<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Support\Facades\Auth;
class FollowUserRequest extends FormRequest
{
  public function authorize(): bool
    {
         // المستخدم يجب أن يكون مسجل، ولا يمكن متابعة نفسه
        return Auth::check() && $this->route('user')->id !== Auth::id();
    }

    public function rules(): array
    {
        return [];
    }
}
