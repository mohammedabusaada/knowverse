<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Services\ReportModerationService;
use Illuminate\Http\Request;

class ReportModerationController extends Controller
{
    public function __construct(
        protected ReportModerationService $moderationService
    ) {
        $this->middleware(['auth', 'can:manage-reports']);
    }

    /**
     * Display the listing of reports.
     */
    public function index()
    {
        // We eager load 'reporter' and 'target' to keep the page fast (prevent N+1)
        $reports = Report::with(['reporter'])
            ->latest()
            ->paginate(20);

        return view('admin.reports.index', compact('reports'));
    }

    /**
     * Display a specific report.
     */
    public function show(Report $report)
    {
        $report->load(['reporter', 'resolver', 'target']);

        return view('admin.reports.show', compact('report'));
    }

    /**
     * Resolve the report (Hides content + Rewards/Penalizes).
     */
    public function resolve(Request $request, Report $report)
    {
        // 1. Update the report record itself
        $report->markAsResolved($request->user());

        // 2. Run the logic to hide the Post/Comment and send notifications
        $this->moderationService->handle($report);

        return $this->respond($request, 'Report resolved and actions applied.');
    }

    /**
     * Dismiss the report (No action taken).
     */
    public function dismiss(Request $request, Report $report)
    {
        $report->markAsDismissed($request->user());

        return $this->respond($request, 'Report dismissed without action.');
    }

    /**
     * Unified response handler for AJAX and Redirects.
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