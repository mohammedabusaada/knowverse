<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class FollowTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Ensure only logged-in users can follow tags
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            // While the ID is usually in the URL, if you ever pass it in the body:
            'tag_id' => ['sometimes', 'exists:tags,id'],
        ];
    }
}