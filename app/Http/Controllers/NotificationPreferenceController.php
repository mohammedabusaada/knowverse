<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\NotificationPreference;
use App\Enums\NotificationType;

class NotificationPreferenceController extends Controller
{
    /**
     * Renders the settings interface for fine-grained notification control.
     */
    public function edit()
    {
        return view('settings.notifications', [
            'user' => Auth::user(),
            // Pass the logical grouping mapping defined in the Enum for UI organization
            'categories' => NotificationType::grouped(),
            'preferences' => Auth::user()->notificationPreferences->keyBy('type'),
        ]);
    }

    /**
     * Synchronizes user toggle states with the database.
     * Employs updateOrCreate to handle both existing preferences and first-time toggles seamlessly.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $inputPreferences = $request->input('preferences', []);

        // Iterate strictly through defined Enum cases to prevent invalid type injection
        foreach (NotificationType::cases() as $typeCase) {
            
            // Architectural constraint: Mandatory system alerts cannot be opted-out of
            if ($typeCase->isMandatory()) {
                continue;
            }

            $typeValue = $typeCase->value;

            // Upsert mechanism: Updates if exists, creates if missing
            NotificationPreference::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'type'    => $typeValue,
                ],
                [
                    // Boolean casting based on HTML checkbox payload presence
                    'enabled' => isset($inputPreferences[$typeValue]) && $inputPreferences[$typeValue] == '1',
                ]
            );
        }

        return redirect()
            ->route('settings.notifications')
            ->with('success', 'Notification preferences updated successfully.');
    }
}