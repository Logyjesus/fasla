<?php

namespace App\Http\Requests;

use App\Models\Seller;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSellerRequest extends FormRequest
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
        $seller = Seller::where('slug', $this->route('seller'))->first();
        return [
            'name' => 'sometimes|string',
            'store_name' => 'sometimes|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($seller?->id),
            ],
            'address' => 'sometimes|string',
            'phone' => 'sometimes|string|max:255',
            'password' => 'sometimes|string|min:6|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'store_name.required' => 'Store name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email is invalid',
            'email.unique' => 'Email is already taken',
            'address.string' => 'Address must be a string',
            'phone.required' => 'Phone is required',
            'phone.max' => 'Phone number must not exceed 255 characters',
            'password.min' => 'Password must be at least 6 characters',
            'confirm_password.same' => 'Password and confirm password must match',
        ];
    }
}
