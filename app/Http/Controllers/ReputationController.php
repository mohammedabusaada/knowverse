<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ReputationController extends Controller
{
    /**
     * Display a paginated audit log of the scholar's reputation ledger.
     */
    public function index(User $user)
    {
        // Soft Gating: Check permissions without throwing an exception
        $canView = Auth::check() && Auth::id() === $user->id;

        // Fetch data only if authorized, otherwise return an empty paginator
        $history = $canView
            ? $user->reputations()->with('source')->orderByDesc('created_at')->paginate(20)
            : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);

        return view('reputation.index', [
            'user'      => $user,
            'history'   => $history,
            'isPrivate' => !$canView, // Pass the state to the view
        ]);
    }
}