<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Post;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreReportRequest;
use App\Enums\ReportStatus;
use App\Enums\ReportTargetType;
use App\Services\ReportModerationService;

class ReportController extends Controller
{
    public function __construct(protected ReportModerationService $moderationService)
    {
        $this->middleware('auth');
    }

    public function store(StoreReportRequest $request)
    {
        // --------------------------------------------------
        // Resolve target model from enum
        // --------------------------------------------------
        $targetType = ReportTargetType::tryFrom($request->target_type);

if (!$targetType) {
    abort(400, 'Invalid report target type.');
}


        $modelClass = match ($targetType) {
            ReportTargetType::POST    => Post::class,
            ReportTargetType::COMMENT => Comment::class,
            ReportTargetType::USER    => User::class,
        };

        // --------------------------------------------------
        // Prevent reporting yourself
        // --------------------------------------------------
        if (
            $targetType === ReportTargetType::USER &&
            (int) $request->target_id === (int) $request->user()->id
        ) {
            return back()->with('status', 'You cannot report yourself.');
        }

        // --------------------------------------------------
        // Validate target existence
        // --------------------------------------------------
        $target = $modelClass::findOrFail($request->target_id);

        // --------------------------------------------------
        // Prevent duplicate pending reports
        // --------------------------------------------------
        $alreadyReported = Report::where('reporter_id', $request->user()->id)
            ->where('target_type', $modelClass)
            ->where('target_id', $request->target_id)
            ->where('status', ReportStatus::PENDING->value)
            ->exists();

        if ($alreadyReported) {
            return back()->with('status', 'You have already reported this item.');
        }

        // --------------------------------------------------
        // Create report
        // --------------------------------------------------
        $report = Report::create([
            'reporter_id' => $request->user()->id,
            'target_type' => $modelClass,
            'target_id'   => $request->target_id,
            'reason_type' => $request->reason_type,
            'reason'      => $request->reason,
            'status'      => ReportStatus::PENDING->value,
        ]);

$this->moderationService->handle($report);


        Log::info('New report submitted', [
            'report_id'   => $report->id,
            'reporter_id' => $request->user()->id,
            'target_type' => $modelClass,
            'target_id'   => $request->target_id,
        ]);



        return back()->with(
            'status',
            'Report submitted successfully. Thank you for helping keep the community safe.'
        );
    }
}
