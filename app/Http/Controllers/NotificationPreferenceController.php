<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\NotificationPreference;

class NotificationPreferenceController extends Controller
{
    public function edit()
    {
        return view('settings.notifications', [
            'categories' => config('notification-preferences.categories'),
            'preferences' => Auth::user()
                ->notificationPreferences
                ->keyBy('type'),
        ]);
    }
    public function update(Request $request)
    {
        $user = Auth::user();

        foreach (config('notification-preferences.categories') as $type => $config) {
            NotificationPreference::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'type'    => $type,
                ],
                [

                    'enabled' => $request->boolean("preferences.$type"),
                ]
            );
        }

        return redirect()
            ->route('settings.notifications')
            ->with('success', 'Preferences updated successfully.');
    }
}
