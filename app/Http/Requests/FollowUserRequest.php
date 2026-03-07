<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Support\Facades\Auth;

/**
 * Validation logic for user-to-user social relationships.
 * Includes governance rules to prevent self-following and ensure session integrity.
 */
class FollowUserRequest extends FormRequest
{
  public function authorize(): bool
    {
         // Governance: User must be authenticated AND cannot initiate a follow relationship with themselves.
        return Auth::check() && $this->route('user')->id !== Auth::id();
    }

    public function rules(): array
    {
        return []; // No payload validation required as the action is target-based via URL
    }
}
