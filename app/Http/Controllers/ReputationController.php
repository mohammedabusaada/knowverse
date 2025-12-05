<?php

namespace App\Http\Controllers;

use App\Models\User;

class ReputationController extends Controller
{
    public function index(User $user)
    {
        $this->authorize('view', $user);

        $history = $user->reputations()
            ->with('source')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('reputation.index', [
            'user' => $user,
            'history' => $history,
        ]);
    }
}
