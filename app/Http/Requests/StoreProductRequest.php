<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * StoreProductRequest
 * 
 * Products are created under these category types:
 * - textiles (3 levels: Group → Leaf)
 * - shoes_bags (3 levels: Group → Leaf)
 * - afro_beauty_products (2 levels: Leaf only)
 * 
 * The category_id should be a leaf category from one of these types.
 */
class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if the user is authenticated and has a seller profile
        $user = $this->user();
        return $user && $user->sellerProfile;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            // category_id should be a leaf category from product catalog types
            'category_id' => [
                'required',
                'exists:categories,id',
                Rule::exists('categories', 'id')->whereIn('type', Category::PRODUCT_TYPES),
            ],
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'image' => 'nullable|string|max:2000', // URL for now, will be file upload later
            'processing_time_type' => 'required|in:normal,quick_quick',
            'fabric_type' => 'nullable|string|max:100',
            'processing_days' => 'required|integer|min:1|max:30',
            'price' => 'required|numeric|min:0.01',
        ];

        // Category-sensitive requirements
        $category = Category::find($this->input('category_id'));
        $categoryType = $category?->type;

        // Textiles & Shoes/Bags require apparel attributes
        if (in_array($categoryType, ['textiles', 'shoes_bags'])) {
            $rules['gender'] = 'nullable|in:male,female,unisex';
            $rules['style'] = 'required|string|max:100';
            $rules['tribe'] = 'required|string|max:100';
            $rules['size'] = 'required|string|max:50';
            $rules['fabric_type'] = $categoryType === 'textiles'
                ? 'required|string|max:100'
                : 'nullable|string|max:100';
        } else {
            // Afro Beauty products and Art products do not require apparel-specific fields
            $rules['gender'] = 'nullable|in:male,female,unisex';
            $rules['style'] = 'nullable|string|max:100';
            $rules['tribe'] = 'nullable|string|max:100';
            $rules['size'] = 'nullable|string|max:50';
            $rules['fabric_type'] = 'nullable|string|max:100';
        }

        return $rules;
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
            'category_id.required' => 'Please select a product category',
            'category_id.exists' => 'The selected category is invalid or not a product category. Valid types: textiles, shoes_bags, afro_beauty_products',
            'price.min' => 'Price must be at least 0.01',
        ];
    }
}
