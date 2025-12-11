<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateReportStatusRequest;
use App\Models\Report;
use Illuminate\Support\Facades\Log;


class ReportModerationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:manage-reports']);
    }

    public function index(Request $request)
    {
        $reports = Report::with(['reporter', 'reviewer'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at')
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
        $report->update([
            'status'      => Report::STATUS_REVIEWED,
            'reviewed_by' => $request->user()->id,
        ]);

        Log::info('Report marked as reviewed.', ['report_id' => $report->id]);

        return back()->with('success', 'The report has been marked as reviewed.');
    }

    public function dismiss(UpdateReportStatusRequest $request, Report $report)
    {
        $report->update([
            'status'      => Report::STATUS_DISMISSED,
            'reviewed_by' => $request->user()->id,
        ]);

        Log::info('Report dismissed by admin.', ['report_id' => $report->id]);

        return back()->with('success', 'The report has been dismissed.');
    }
}
