<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Post;
use App\Models\Comment;
use App\Models\User;
use App\Http\Requests\StoreReportRequest;
use App\Enums\ReportStatus;
use App\Enums\ReportTargetType;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(StoreReportRequest $request)
    {
        // 1. Determine Model Class
        $modelClass = match (ReportTargetType::from($request->target_type)) {
            ReportTargetType::POST    => Post::class,
            ReportTargetType::COMMENT => Comment::class,
            ReportTargetType::USER    => User::class,
        };

        // 2. Create the Report
        // The StoreReportRequest has already checked for duplicates!
        Report::create([
            'reporter_id' => $request->user()->id,
            'target_type' => $modelClass,
            'target_id'   => $request->target_id,
            'reason_type' => $request->reason_type,
            'reason'      => $request->reason,
            'status'      => ReportStatus::PENDING->value,
        ]);

        return back()->with('status', 'Report submitted. An admin will review it shortly.');
    }
}