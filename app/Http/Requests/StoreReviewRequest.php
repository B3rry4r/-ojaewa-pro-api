<?php

namespace App\Http\Requests;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        
        // Must be authenticated
        if (!$user) {
            return false;
        }
        
        // Validate ownership if it's an order review
        if ($this->input('reviewable_type') === Product::class) {
            return true; // Anyone can review a product
        } elseif ($this->input('reviewable_type') === Order::class) {
            // Only the owner of the order can review it
            $orderId = $this->input('reviewable_id');
            return Order::where('id', $orderId)
                        ->where('user_id', $user->id)
                        ->exists();
        }
        
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reviewable_id' => 'required|integer',
            'reviewable_type' => ['required', Rule::in([Product::class, Order::class])],
            'rating' => 'required|integer|min:1|max:5',
            'headline' => 'required|string|max:100',
            'body' => 'required|string|max:1000',
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
            'reviewable_id.required' => 'The item ID to review is required',
            'reviewable_type.required' => 'The type of item to review is required',
            'reviewable_type.in' => 'The review type must be valid',
            'rating.required' => 'Rating is required',
            'rating.min' => 'Rating must be at least 1 star',
            'rating.max' => 'Rating cannot exceed 5 stars',
            'headline.required' => 'Review headline is required',
            'headline.max' => 'Review headline cannot exceed 100 characters',
            'body.required' => 'Review body is required',
            'body.max' => 'Review body cannot exceed 1000 characters',
        ];
    }
}
