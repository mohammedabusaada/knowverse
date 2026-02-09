<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\ReservedUsername;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Standard enterprise check: ensure the user is editing their own profile
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'full_name'           => ['required', 'string', 'max:255'],
            'academic_title'      => ['nullable', 'string', 'max:255'],
            'bio'                 => ['nullable', 'string', 'max:2000'],
            'profile_picture'     => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            
            // Privacy Toggle
            'public_follow_lists' => ['boolean'], 
            
            'username' => [
                'required', 
                'string', 
                'max:50', 
                'alpha_dash:ascii',
                Rule::unique('users')->ignore($this->user()->id),
                new ReservedUsername(),
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
     * Prepare data for validation (handling the checkbox).
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'public_follow_lists' => $this->boolean('public_follow_lists'),
        ]);
    }
}