<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\ReservedUsername;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Manages identity refinement and data integrity for user profiles.
 * Implements input preprocessing and strict uniqueness constraints.
 */
class ProfileUpdateRequest extends FormRequest
{
    /**
     * Ensure the user is authenticated before evaluating validation rules.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'full_name'           => ['required', 'string', 'max:255'],
            'academic_title'      => ['nullable', 'string', 'max:255'],
            'bio'                 => ['nullable', 'string', 'max:2000'],
            'profile_picture'     => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'public_follow_lists' => ['boolean'], 
            
            'username' => [
                'required', 'string', 'max:50', 'alpha_dash:ascii',
                Rule::unique('users')->ignore($this->user()->id),
                new ReservedUsername(), // Prevent hijacking system-reserved slugs
            ],
            
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }

/**
     * Data Normalization: Pre-process inputs before they hit the validator.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            // Cast HTML checkbox strings into proper boolean values for database storage
            'public_follow_lists' => $this->boolean('public_follow_lists'),
        ]);
    }
}