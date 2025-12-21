<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreReportRequest;

class ReportController extends Controller
{
  public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(StoreReportRequest $request)
    {
        // Map target type string to actual model class
        $map = [
            'post'    => \App\Models\Post::class,
            'comment' => \App\Models\Comment::class,
            'user'    => \App\Models\User::class,
        ];

        $modelClass = $map[$request->target_type];

        // Validate target existence
        $target = $modelClass::find($request->target_id);

        if (!$target) {
            return back()->withErrors(['target_id' => 'The selected item does not exist.']);
        }

        // Create the report
        $report = Report::create([
            'reporter_id' => $request->user()->id,
            'target_type' => $modelClass,
            'target_id'   => $request->target_id,
            'reason'      => $request->reason,
            'status'      => Report::STATUS_PENDING,
        ]);

        Log::info('A new report has been submitted.', [
            'report_id' => $report->id,
            'target_type' => $request->target_type,
            'target_id' => $request->target_id
        ]);

        return back()->with('success', 'Your report has been submitted successfully.');
    }
}

