<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FaqController extends Controller
{
    /**
     * Get all FAQs, optionally filtered by category.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Faq::query();

        if ($request->has('category') && $request->category) {
            $query->byCategory($request->category);
        }

        $faqs = $query->latest()->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $faqs,
        ]);
    }

    /**
     * Get all available FAQ categories.
     */
    public function categories(): JsonResponse
    {
        $categories = Faq::getCategories();

        return response()->json([
            'status' => 'success',
            'data' => $categories,
        ]);
    }

    /**
     * Search FAQs by question or answer.
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:3',
        ]);

        $query = $request->query('query');
        
        $faqs = Faq::where(function ($q) use ($query) {
                $q->where('question', 'LIKE', "%{$query}%")
                  ->orWhere('answer', 'LIKE', "%{$query}%");
            })
            ->latest()
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $faqs,
        ]);
    }

    /**
     * Get a specific FAQ by ID.
     */
    public function show(int $id): JsonResponse
    {
        $faq = Faq::find($id);

        if (!$faq) {
            return response()->json([
                'status' => 'error',
                'message' => 'FAQ not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $faq,
        ]);
    }
}
