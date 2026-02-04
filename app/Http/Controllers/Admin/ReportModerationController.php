<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateReportStatusRequest;
use App\Models\Report;
use App\Services\ReportModerationService;
use Illuminate\Support\Facades\Log;

class ReportModerationController extends Controller
{
    protected ReportModerationService $moderationService;

    public function __construct(ReportModerationService $moderationService)
    {
        $this->middleware(['auth', 'can:manage-reports']);
        $this->moderationService = $moderationService;
    }

    public function index()
    {
        $reports = Report::with(['reporter', 'reviewer'])
            ->latest()
            ->paginate(20);

        return view('admin.reports.index', compact('reports'));
    }

    public function show(Report $report)
    {
        $report->load(['reporter', 'reviewer', 'target']);

        return view('admin.reports.show', compact('report'));
    }

    public function review(UpdateReportStatusRequest $request, Report $report)
    {
        // 1️⃣ Mark as reviewed
        $report->markReviewed($request->user());

        // 2️⃣ Apply moderation logic
        $this->moderationService->handle($report);

        Log::info('Report reviewed & moderation applied.', [
            'report_id' => $report->id,
            'reviewed_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Report reviewed and moderation applied.');
    }

    public function dismiss(UpdateReportStatusRequest $request, Report $report)
    {
        $report->update([
            'status' => \App\Enums\ReportStatus::DISMISSED,
            'reviewed_by' => $request->user()->id,
        ]);

        Log::info('Report dismissed by admin.', [
            'report_id' => $report->id,
        ]);

        return back()->with('success', 'Report dismissed.');
    }
}
