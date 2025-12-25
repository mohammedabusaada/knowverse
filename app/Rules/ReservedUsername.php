<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ReservedUsername implements ValidationRule
{

    /**
     * The list of reserved words that cannot be used as usernames.
     * Adding 'posts', 'tags', etc., prevents users from breaking resource routes.
     */
    protected array $reserved = [
        'search', 'admin', 'dashboard', 'settings', 'login', 
        'register', 'logout', 'api', 'posts', 'comments', 
        'tags', 'profile', 'reputation', 'test-report', 
        'knowverse', 'root', 'help', 'support', 'home'
    ];
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (in_array(strtolower($value), $this->reserved)) {
            $fail('This :attribute is reserved and cannot be used.');
        }
    }
}
