<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Services\ReportModerationService;
use Illuminate\Http\Request;

class ReportModerationController extends Controller
{
    /**
     * Injecting the moderation service to decouple business logic from the controller.
     */
    public function __construct(
        protected ReportModerationService $moderationService
    ) {
        // Outer Door Defense: Ensure only authorized moderators/admins can access these routes
        $this->middleware(['auth', 'can:manage-reports']);
    }

    /**
     * Display the listing of all reports.
     */
    public function index()
    {
        // Inner Door Defense: Enforce the 'viewAny' policy
        $this->authorize('viewAny', Report::class);

        // Eager load 'reporter' to prevent N+1 query performance issues on the listing page
        $reports = Report::with(['reporter'])
            ->latest()
            ->paginate(20);

        return view('admin.reports.index', compact('reports'));
    }

    /**
     * Display the detailed view of a specific report.
     */
    public function show(Report $report)
    {
        // Inner Door Defense: Enforce the 'view' policy for this specific report
        $this->authorize('view', $report);

        // Eager load related entities required for the detailed view
        $report->load(['reporter', 'resolver', 'target']);

        return view('admin.reports.show', compact('report'));
    }

    /**
     * Resolve the report: applies penalties, hides content, and rewards the reporter.
     */
    public function resolve(Request $request, Report $report)
    {
        // Inner Door Defense: Only authorized personnel can mutate the report state
        $this->authorize('update', $report);

        // 1. Update the report record state
        $report->markAsResolved($request->user());

        // 2. Delegate the heavy lifting (hiding content, notifications) to the Service class
        $this->moderationService->handle($report);

        return $this->respond($request, 'Report resolved successfully. Actions applied.');
    }

    /**
     * Dismiss the report: marks it as closed without taking any punitive actions.
     */
    public function dismiss(Request $request, Report $report)
    {
        // Inner Door Defense
        $this->authorize('update', $report);

        $report->markAsDismissed($request->user());

        return $this->respond($request, 'Report dismissed without action.');
    }

    /**
     * Unified response handler to support both standard web requests and AJAX (fetch) calls.
     */
    protected function respond(Request $request, string $message)
    {
        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'status' => 'success'
            ]);
        }
        
        return redirect()->route('admin.reports.index')->with('success', $message);
    }
}