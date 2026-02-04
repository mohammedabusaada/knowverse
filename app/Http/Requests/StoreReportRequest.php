<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Enums\ReportReason;
use Illuminate\Validation\Rules\Enum;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
           'target_type' => ['required', 'in:post,comment,user'],
        'target_id'   => ['required', 'integer'],
        'reason_type' => ['required', new Enum(ReportReason::class)],
        'reason'      => ['nullable', 'string', 'max:1000'],
        ];
    }
}
