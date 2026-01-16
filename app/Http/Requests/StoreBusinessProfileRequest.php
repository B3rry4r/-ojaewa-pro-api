<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * StoreBusinessProfileRequest
 * 
 * Business profiles are registered under these category types:
 * - school → Educational institutions
 * - art → Artists, galleries, studios
 * - afro_beauty_services → Beauty service providers
 * 
 * All business directory categories have 2 levels (leaf categories only).
 */
class StoreBusinessProfileRequest extends FormRequest
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
        $rules = [
            // category_id should be a leaf category from business directory types
            'category_id' => [
                'required',
                'exists:categories,id',
                Rule::exists('categories', 'id')->whereIn('type', Category::BUSINESS_TYPES),
            ],
            'subcategory_id' => 'nullable|exists:categories,id',
            // Legacy category field for backward compatibility
            'category' => ['required', 'string', Rule::in(['school', 'afro_beauty_services'])],
            'country' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'business_email' => 'required|email|max:100',
            'business_phone_number' => 'required|string|max:20',
            'website_url' => 'nullable|url|max:255',
            'instagram' => 'nullable|string|max:100',
            'facebook' => 'nullable|string|max:100',
            'identity_document' => 'nullable|string|max:255',
            'business_name' => 'required|string|max:100',
            'business_description' => 'required|string|max:1000',
            'business_logo' => 'nullable|string|max:255',
            'offering_type' => 'nullable|string|in:selling_product,providing_service',
            'product_list' => 'nullable|json',
            'service_list' => 'nullable|json',
            'business_certificates' => 'nullable|json',
            'professional_title' => 'nullable|string|max:100',
            'school_type' => 'nullable|string|in:fashion,music,catering,beauty',
            'school_biography' => 'nullable|string|max:1000',
            'classes_offered' => 'nullable|json',
            'youtube' => 'nullable|string|max:255',
            'spotify' => 'nullable|string|max:255',
            'store_status' => 'nullable|string|in:pending,approved,deactivated',
            'subscription_status' => 'nullable|string|in:active,expired',
            'subscription_ends_at' => 'nullable|date'
        ];

        // Conditional validations based on offering_type
        // NOTE: Files (business_certificates, identity_document, business_logo) are uploaded 
        // AFTER business creation via POST /api/business/{id}/upload endpoint.
        // Therefore, they are NOT required at creation time.
        if ($this->offering_type === 'providing_service') {
            $rules['service_list'] = 'required|json';
            $rules['professional_title'] = 'required|string|max:100';
        }

        if ($this->offering_type === 'selling_product') {
            $rules['product_list'] = 'required|json';
            // business_certificates uploaded after creation via /api/business/{id}/upload
        }

        // Conditional validations based on category
        if ($this->category === 'school') {
            $rules['school_type'] = 'required|string|in:fashion,music,catering,beauty';
            $rules['school_biography'] = 'required|string|max:1000';
            $rules['classes_offered'] = 'required|json';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'Please select a business category',
            'category_id.exists' => 'The selected category is invalid or not a business category',
            'category.in' => 'Category must be one of: school, art, afro_beauty_services',
        ];
    }
}
