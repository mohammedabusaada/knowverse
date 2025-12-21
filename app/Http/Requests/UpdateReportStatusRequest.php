<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateReportStatusRequest extends FormRequest
{
     public function authorize(): bool
    {
    return $this->user()?->role?->name === 'admin';
    
    }

    public function rules(): array
    {
        return [
            'status' => 'required|string|in:pending,reviewed,dismissed',
        ];
    }
}
