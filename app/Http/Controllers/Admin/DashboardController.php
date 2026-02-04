<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Report;
use App\Models\User;
use App\Enums\ReportStatus;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPosts = Post::count();
        $totalComments = Comment::count();
        $totalUsers = User::count();

        $totalReports = Report::count();
        $pendingReports = Report::where('status', ReportStatus::PENDING)->count();
        $reviewedReports = Report::where('status', ReportStatus::REVIEWED)->count();
        $dismissedReports = Report::where('status', ReportStatus::DISMISSED)->count();

        return view('admin.dashboard', compact(
            'totalPosts',
            'totalComments',
            'totalUsers',
            'totalReports',
            'pendingReports',
            'reviewedReports',
            'dismissedReports'
        ));
    }
}
