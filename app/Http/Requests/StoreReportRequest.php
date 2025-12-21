<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'target_type' => 'required|string|in:post,comment,user',
            'target_id'   => 'required|integer',
            'reason'      => 'nullable|string|max:2000',
        ];
    }
}
