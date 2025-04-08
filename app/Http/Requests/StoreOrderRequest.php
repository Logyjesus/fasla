<?php

namespace App\Http\Requests;

use App\Enum\OrderStatusEnum;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'shipping_address' => 'required|string',

            'order_items' => 'required|array|min:1',
            'order_items.*.product_id' => 'required|exists:products,id',
            'order_items.*.quantity' => 'required|integer|min:1',
            'order_items.*.price' => 'required|numeric|min:0',
            'order_items.*.color' => 'nullable|string',
            'order_items.*.size' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'User ID is required',
            'user_id.exists' => 'User ID is not valid',
            'shipping_address.required' => 'Shipping Address is required',
            'order_items.required' => 'Order Items is required',
            'order_items.array' => 'Order Items must be an array',
            'order_items.min' => 'Order Items must have at least 1 item',
            'order_items.*.product_id.required' => 'Product ID is required for each order item',
            'order_items.*.product_id.exists' => 'Product ID is not valid for each order item',
            'order_items.*.quantity.required' => 'Quantity is required for each order item',
            'order_items.*.quantity.integer' => 'Quantity must be an integer for each order item',
            'order_items.*.quantity.min' => 'Quantity must be at least 1 for each order item',
            'order_items.*.price.required' => 'Price is required for each order item',
            'order_items.*.price.numeric' => 'Price must be a number for each order item',
            'order_items.*.price.min' => 'Price must be at least 0 for each order item',
            'order_items.*.color.string' => 'Color must be a valid string',
        ];
    }
}
