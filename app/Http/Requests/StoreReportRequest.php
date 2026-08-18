<?php

namespace App\Http\Requests;

use App\Enums\ReportReason;
use App\Rules\NoDuplicateReport;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

/**
 * Orchestrates the reporting system logic.
 * Handles polymorphic target resolution and prevents redundant or illogical reports.
 */
class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $modelClass = Relation::getMorphedModel($this->target_type) ?? $this->target_type;

        return [
            'target_type' => ['required', 'in:post,comment,user'],
            'target_id' => [
                'required',
                'integer',
                new NoDuplicateReport($modelClass, (int) $this->target_id),

                function ($attribute, $value, $fail) use ($modelClass) {
                    $user = $this->user();

                    // Self-Reporting Constraint for Users
                    if ($modelClass === \App\Models\User::class) {
                        if ((int) $value === (int) $user->id) {
                            $fail('You cannot report your own profile.');
                        }

                        $targetUser = \App\Models\User::find($value);
                        if ($targetUser && $targetUser->is_banned) {
                            $fail('This account is already suspended and under review.');
                        }
                    }
                    // Self-Reporting Constraint for Posts and Comments
                    elseif (in_array($modelClass, [\App\Models\Post::class, \App\Models\Comment::class])) {
                        $target = $modelClass::find($value);
                        if ($target && (int) $target->user_id === (int) $user->id) {
                            // Automatically adapt message (e.g., "You cannot report your own post.")
                            $fail('You cannot report your own '.strtolower(class_basename($modelClass)).'.');
                        }
                    }
                },
            ],
            'reason_type' => ['required', new Enum(ReportReason::class)],
            'reason' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
