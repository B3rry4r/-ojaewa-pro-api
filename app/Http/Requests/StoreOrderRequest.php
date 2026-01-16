<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Validate the items array is present and has at least one item
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id,status,approved',
            'items.*.quantity' => 'required|integer|min:1',

            // Shipping fields (optional but recommended)
            'shipping_name' => 'nullable|string|max:255',
            'shipping_phone' => 'nullable|string|max:50',
            'shipping_address' => 'nullable|string|max:255',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_state' => 'nullable|string|max:100',
            'shipping_country' => 'nullable|string|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'items.required' => 'You must include at least one item in your order',
            'items.*.product_id.required' => 'Each item must specify a product ID',
            'items.*.product_id.exists' => 'One or more selected products do not exist or are not approved',
            'items.*.quantity.required' => 'Each item must specify a quantity',
            'items.*.quantity.min' => 'Quantity must be at least 1 for each item',
        ];
    }
}
