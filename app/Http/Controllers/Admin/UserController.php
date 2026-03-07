<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Manages the scholar directory and enforces administrative governance.
 * Includes capabilities for account auditing, suspension, and permanent record eradication.
 */
class UserController extends Controller
{
/**
     * Enforce strict access control: Only System Administrators can manage user accounts.
     * Moderators are restricted to content oversight and cannot modify user entities.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!$request->user()->isAdmin()) {
                abort(403, 'Unauthorized access. Only System Administrators can manage user accounts.');
            }
            return $next($request);
        });
    }

    /**
     * Display a paginated list of all registered users with optional search filtering.
     */
    public function index(Request $request)
    {
        // Eager load the 'role' relationship and count aggregates to prevent N+1 query performance issues
        $query = User::with('role')->withCount(['posts', 'allComments'])->latest();

        // Apply search constraints if a query string is provided
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%");
            });
        }

        // Paginate results and preserve the search query in pagination links
        $users = $query->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Display detailed information for a specific user.
     */
    public function show(User $user)
    {
        // Inner Door Defense: Ensure the viewer has clearance
        $this->authorize('view', $user);

        // Eager load recent activity or specific relations if needed for the show view
        $user->loadCount(['posts', 'allComments', 'followers', 'following']);
        
        return view('admin.users.show', compact('user'));
    }

/**
     * Orchestrates user suspension (banning).
     * Implements a safety check to prevent administrative self-lockout.
     */
    public function toggleBan(User $user)
    {
        // 🚀 Inner Door Defense: Ensure only admins can mutate another user's state
        $this->authorize('update', $user);

        // Safely check if the currently authenticated user is trying to ban themselves
        if (Auth::id() === $user->id) {
            return back()->with('error', 'Operation aborted: You cannot ban your own active session account.');
        }

        // Toggle the ban status and define the appropriate feedback message
        if ($user->is_banned) {
            $user->update(['banned_at' => null]);
            $status = 'User access restored successfully.';
        } else {
            $user->update(['banned_at' => now()]);
            $status = 'User has been banned and suspended from the platform.';
        }

        return back()->with('success', $status);
    }

/**
     * Permanently expunges a user record from the infrastructure.
     * Executes a Hard Delete to comply with data privacy regulations (GDPR).
     */
    public function destroy(User $user)
    {

        // CRITICAL SECURITY: Enforce the strict 'delete' policy (Admins ONLY, no Moderators)
        $this->authorize('delete', $user);

        // Fail-safe mechanism: Prevent the currently authenticated admin from deleting their own account
        if (Auth::id() === $user->id) {
            return back()->with('error', 'Operation aborted: You cannot delete your own active session account.');
        }

        $user->forceDelete();
        
        return back()->with('success', 'User account has been permanently removed from the system.');
    }
}