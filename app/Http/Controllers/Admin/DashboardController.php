<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Report;
use App\Models\User;

class DashboardController extends Controller
{
public function index()
    {
        $totalPosts = Post::count();
        $totalComments = Comment::count();
        $totalReports = Report::count();
        $pendingReports = Report::where('status', 'pending')->count();
        $totalUsers = User::count();

        return view('admin.dashboard', compact(
            'totalPosts',
            'totalComments',
            'totalReports',
            'pendingReports',
            'totalUsers'
        ));
    }
}
