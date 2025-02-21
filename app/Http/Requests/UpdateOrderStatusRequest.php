<?php

namespace App\Http\Requests;

use App\Enum\OrderStatusEnum;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
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
        return [
            'status' => ['required', new Enum(OrderStatusEnum::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'The order status field is required.',
            'status.enum' => 'The selected order status is invalid.',
        ];
    }
}