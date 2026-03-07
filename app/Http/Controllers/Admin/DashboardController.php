<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Post, Comment, Report, User};
use App\Enums\ReportStatus;

class DashboardController extends Controller
{
    /**
     * Provides a high-level administrative overview of the platform's vital signs.
     * Aggregates core metrics including user growth, content volume, and moderation queue status.
     */
    public function index()
    {
        return view('admin.dashboard', [
            'totalPosts'       => Post::count(),
            'totalComments'    => Comment::count(),
            'totalUsers'       => User::count(),
            'totalReports'     => Report::count(),
            'pendingReports'   => Report::pending()->count(),
            'resolvedReports'  => Report::resolved()->count(),
            'dismissedReports' => Report::dismissed()->count(),
        ]);
    }
}
