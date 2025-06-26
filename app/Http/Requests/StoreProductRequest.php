<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female,unisex',
            'style' => 'required|string|max:100',
            'tribe' => 'required|string|max:100',
            'description' => 'required|string|max:1000',
            'image' => 'nullable|string|max:2000', // URL for now, will be file upload later
            'size' => 'required|string|max:50',
            'processing_time_type' => 'required|in:normal,quick_quick',
            'processing_days' => 'required|integer|min:1|max:30',
            'price' => 'required|numeric|min:0.01',
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
        ];
    }
}
