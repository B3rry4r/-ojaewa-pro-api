<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'category' => 'required|string|in:beauty,brand,school,music',
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
            'music_category' => 'nullable|string|in:dj,artist,producer',
            'youtube' => 'nullable|string|max:255',
            'spotify' => 'nullable|string|max:255',
            'store_status' => 'nullable|string|in:pending,approved,deactivated',
            'subscription_status' => 'nullable|string|in:active,expired',
            'subscription_ends_at' => 'nullable|date'
        ];

        // Conditional validations based on offering_type
        if ($this->offering_type === 'providing_service') {
            $rules['service_list'] = 'required|json';
            $rules['professional_title'] = 'required|string|max:100';
        }

        if ($this->offering_type === 'selling_product') {
            $rules['product_list'] = 'required|json';
            $rules['business_certificates'] = 'required|json';
        }

        // Conditional validations based on category
        if ($this->category === 'school') {
            $rules['school_type'] = 'required|string|in:fashion,music,catering,beauty';
            $rules['school_biography'] = 'required|string|max:1000';
            $rules['classes_offered'] = 'required|json';
        }

        if ($this->category === 'music') {
            $rules['music_category'] = 'required|string|in:dj,artist,producer';
            $rules['identity_document'] = 'required|string|max:255';
            
            // At least one of youtube or spotify is required
            if (empty($this->youtube) && empty($this->spotify)) {
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
