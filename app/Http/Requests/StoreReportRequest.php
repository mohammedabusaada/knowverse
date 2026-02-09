<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\ReportReason;
use App\Rules\NoDuplicateReport;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Database\Eloquent\Relations\Relation;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        // Use the MorphMap from your AppServiceProvider to get the full class name
        // This ensures 'post' becomes 'App\Models\Post'
        $modelClass = Relation::getMorphedModel($this->target_type) ?? $this->target_type;

        return [
            'target_type' => ['required', 'in:post,comment,user'],
            'target_id'   => [
                'required', 
                'integer',
                // Custom rule to check database for duplicates
                new NoDuplicateReport($modelClass, (int) $this->target_id),
                // Custom inline check: Prevent self-reporting if target is a user
                function ($attribute, $value, $fail) use ($modelClass) {
                    if ($modelClass === \App\Models\User::class && (int)$value === (int)$this->user()->id) {
                        $fail('You cannot report yourself.');
                    }
                }
            ],
            'reason_type' => ['required', new Enum(ReportReason::class)],
            'reason'      => ['nullable', 'string', 'max:1000'],
        ];
    }
}