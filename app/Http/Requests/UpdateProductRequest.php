<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Get the product being updated
        $product = Product::find($this->route('product'));
        
        // Check if product exists and belongs to the authenticated user's seller profile
        $user = $this->user();
        return $user && $product && $user->sellerProfile && $product->seller_profile_id === $user->sellerProfile->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'gender' => 'sometimes|in:male,female,unisex',
            'style' => 'sometimes|string|max:100',
            'tribe' => 'sometimes|string|max:100',
            'description' => 'sometimes|string|max:1000',
            'image' => 'nullable|string|max:2000', // URL for now, will be file upload later
            'size' => 'sometimes|string|max:50',
            'processing_time_type' => 'sometimes|in:normal,quick_quick',
            'processing_days' => 'sometimes|integer|min:1|max:30',
            'price' => 'sometimes|numeric|min:0.01',
            'status' => 'sometimes|in:pending,approved,rejected',
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
            'gender.in' => 'Gender must be male, female, or unisex',
            'processing_time_type.in' => 'Processing time type must be normal or quick_quick',
            'processing_days.min' => 'Processing days must be at least 1',
            'processing_days.max' => 'Processing days cannot exceed 30',
            'price.min' => 'Price must be at least 0.01',
            'status.in' => 'Status must be pending, approved, or rejected',
        ];
    }
}
