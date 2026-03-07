<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\ReportReason;
use App\Rules\NoDuplicateReport;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Orchestrates the reporting system logic.
 * Handles polymorphic target resolution and prevents redundant or illogical reports.
 */
class StoreReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Require authentication to submit reports
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Resolve the fully qualified class name using the MorphMap definition
        $modelClass = Relation::getMorphedModel($this->target_type) ?? $this->target_type;

        return [
            'target_type' => ['required', 'in:post,comment,user'],
            'target_id'   => [
                'required', 
                'integer',
                // Idempotency: Prevent duplicate pending reports from the same scholar
                new NoDuplicateReport($modelClass, (int) $this->target_id),
                
                function ($attribute, $value, $fail) use ($modelClass) {
                    // Self-Reporting Constraint
                    if ($modelClass === \App\Models\User::class && (int)$value === (int)$this->user()->id) {
                        $fail('You cannot report your own profile.');
                    }
                    
                    // State Verification: Block reports on accounts already under administrative suspension
                    if ($modelClass === \App\Models\User::class) {
                        $targetUser = \App\Models\User::find($value);
                        if ($targetUser && $targetUser->is_banned) {
                            $fail('This account is already suspended and under review.');
                        }
                    }
                }
            ],
            'reason_type' => ['required', new Enum(ReportReason::class)],
            'reason'      => ['nullable', 'string', 'max:1000'],
        ];
    }
}