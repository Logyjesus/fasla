<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'discounted_price' => 'nullable|numeric|min:0|lt:price',
            'quantity' => 'required|integer|min:1',
            'sub_category_id' => 'required|exists:sub_categories,id',

            'colors' => 'nullable|array|min:1|max:5',
            'colors.*'=> 'nullable|string|distinct|max:50',

            'sizes' => 'nullable|array|min:1|max:5',
            'sizes.*' => 'nullable|string|distinct|max:50',

            'images' => 'nullable|array|max:5',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The product name is required.',
            'slug.required' => 'The product slug is required.',
            'slug.unique' => 'This slug is already in use.',
            'price.numeric' => 'The price must be a number.',
            'discounted_price.lt' => 'The discounted price must be less than the original price.',

            'colors.required' => 'At least one color is required.',
            'colors.max' => 'You can add up to 5 colors only.',
            'colors.*.distinct' => 'Each color must be unique.',

            'sizes.required' => 'At least one size is required.',
            'sizes.max' => 'You can add up to 5 sizes only.',
            'sizes.*.distinct' => 'Each size must be unique.',

            'images.required' => 'At least one image is required.',
            'images.max' => 'You can upload up to 5 images only.',
            'images.*.image' => 'Each file must be a valid image.',
            'images.*.mimes' => 'Each image must be of type: jpeg, png, jpg, gif, or svg.',
        ];
    }
}
