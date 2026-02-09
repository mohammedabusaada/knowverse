<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReportStatusRequest extends FormRequest
{
    /**
     * Verify that the user is an admin before processing the request
     */
    public function authorize(): bool
    {
        // Use the permission defined in the Policy or ensure the user is an admin
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * No validation rules are required because the action is defined by the PATCH route
     */
    public function rules(): array
    {
        return [];
    }
}