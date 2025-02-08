<?php

namespace App\Http\Requests;

use App\Models\Seller;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $admin = Seller::where('slug', $this->route('admin'))->first();
        return [
            'name' => 'sometimes|string',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('sellers', 'email')->ignore($admin?->id),
            ],
            'password' => 'sometimes|string|min:6|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email is invalid',
            'email.unique' => 'Email is already taken',
            'password.min' => 'Password must be at least 6 characters',
            'confirm_password.same' => 'Password and confirm password must match',
        ];
    }
}