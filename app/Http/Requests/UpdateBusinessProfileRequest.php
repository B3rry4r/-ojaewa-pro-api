<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBusinessProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Will implement proper authorization in the controller
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
            'category_id' => 'sometimes|nullable|exists:categories,id',
            'subcategory_id' => 'sometimes|nullable|exists:categories,id',
            'category' => 'sometimes|required|string|in:beauty,brand,school,music',
            'country' => 'sometimes|required|string|max:100',
            'state' => 'sometimes|required|string|max:100',
            'city' => 'sometimes|required|string|max:100',
            'address' => 'sometimes|required|string|max:255',
            'business_email' => 'sometimes|required|email|max:100',
            'business_phone_number' => 'sometimes|required|string|max:20',
            'website_url' => 'sometimes|nullable|url|max:255',
            'instagram' => 'sometimes|nullable|string|max:100',
            'facebook' => 'sometimes|nullable|string|max:100',
            'identity_document' => 'sometimes|nullable|string|max:255',
            'business_name' => 'sometimes|required|string|max:100',
            'business_description' => 'sometimes|required|string|max:1000',
            'business_logo' => 'sometimes|nullable|string|max:255',
            'offering_type' => 'sometimes|nullable|string|in:selling_product,providing_service',
            'product_list' => 'sometimes|nullable|json',
            'service_list' => 'sometimes|nullable|json',
            'business_certificates' => 'sometimes|nullable|json',
            'professional_title' => 'sometimes|nullable|string|max:100',
            'school_type' => 'sometimes|nullable|string|in:fashion,music,catering,beauty',
            'school_biography' => 'sometimes|nullable|string|max:1000',
            'classes_offered' => 'sometimes|nullable|json',
            'music_category' => 'sometimes|nullable|string|in:dj,artist,producer',
            'youtube' => 'sometimes|nullable|string|max:255',
            'spotify' => 'sometimes|nullable|string|max:255',
        ];

        // Fetch the existing business profile
        $businessProfile = \App\Models\BusinessProfile::findOrFail($this->route('id'));

        // Get the category from the request or from the existing profile
        $category = $this->category ?? $businessProfile->category;
        $offering_type = $this->offering_type ?? $businessProfile->offering_type;

        // Conditional validations based on offering_type
        if ($offering_type === 'providing_service') {
            if ($this->has('service_list')) {
                $rules['service_list'] = 'required|json';
            }
            if ($this->has('professional_title')) {
                $rules['professional_title'] = 'required|string|max:100';
            }
        }

        if ($offering_type === 'selling_product') {
            if ($this->has('product_list')) {
                $rules['product_list'] = 'required|json';
            }
            // business_certificates uploaded after creation via /api/business/{id}/upload
        }

        // Conditional validations based on category
        if ($category === 'school') {
            if ($this->has('school_type')) {
                $rules['school_type'] = 'required|string|in:fashion,music,catering,beauty';
            }
            if ($this->has('school_biography')) {
                $rules['school_biography'] = 'required|string|max:1000';
            }
            if ($this->has('classes_offered')) {
                $rules['classes_offered'] = 'required|json';
            }
        }

        if ($category === 'music') {
            if ($this->has('music_category')) {
                $rules['music_category'] = 'required|string|in:dj,artist,producer';
            }
            // identity_document uploaded after creation via /api/business/{id}/upload
            
            // At least one of youtube or spotify is required for music businesses
            if (($this->has('youtube') || $this->has('spotify')) && 
                empty($this->youtube) && empty($this->spotify) &&
                empty($businessProfile->youtube) && empty($businessProfile->spotify)) {
                $rules['youtube'] = 'required_without:spotify|string|max:255';
                $rules['spotify'] = 'required_without:youtube|string|max:255';
            }
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
            'youtube.required_without' => 'At least one music platform link (YouTube or Spotify) is required for music businesses.',
            'spotify.required_without' => 'At least one music platform link (YouTube or Spotify) is required for music businesses.',
        ];
    }
}
