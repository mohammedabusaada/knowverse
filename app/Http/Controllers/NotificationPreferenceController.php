<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\NotificationPreference;
use App\Enums\NotificationType;

class NotificationPreferenceController extends Controller
{
    public function edit()
    {
        return view('settings.notifications', [
            'user' => Auth::user(),
            // Grouped cases from Enum: [ 'posts' => [CASE, CASE], 'votes' => [...] ]
            'categories' => NotificationType::grouped(),
            'preferences' => Auth::user()->notificationPreferences->keyBy('type'),
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $inputPreferences = $request->input('preferences', []);

        // Iterate through all cases in the Enum to ensure we handle all types
        foreach (NotificationType::cases() as $typeCase) {
            // Skip mandatory types; users shouldn't be able to toggle them
            if ($typeCase->isMandatory()) {
                continue;
            }

            $typeValue = $typeCase->value;

            NotificationPreference::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'type'    => $typeValue,
                ],
                [
                    // If the key exists in input, it's enabled (true), otherwise false
                    'enabled' => isset($inputPreferences[$typeValue]) && $inputPreferences[$typeValue] == '1',
                ]
            );
        }

        return redirect()
            ->route('settings.notifications')
            ->with('success', 'Notification preferences updated successfully.');
    }
}