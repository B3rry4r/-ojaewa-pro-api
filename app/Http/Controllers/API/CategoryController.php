<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\BusinessProfile;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Get categories by type.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'type' => ['required', Rule::in(['market', 'beauty', 'brand', 'school', 'sustainability', 'music'])],
        ]);
        
        $categories = Category::ofType($request->type)
            ->topLevel()
            ->orderBy('order')
            ->with(['children' => function ($query) {
                $query->orderBy('order');
            }])
            ->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $categories,
        ]);
    }
    
    /**
     * Get children of a specific category.
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function children(int $id): JsonResponse
    {
        $category = Category::findOrFail($id);
        
        $children = $category->children()
            ->orderBy('order')
            ->get();
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'parent' => $category,
                'children' => $children,
            ],
        ]);
    }
    
    /**
     * Get items for a specific category by type and slug.
     * 
     * @param string $type
     * @param string $slug
     * @return JsonResponse
     */
    public function items(string $type, string $slug): JsonResponse
    {
        // Validate type
        if (!in_array($type, ['market', 'beauty', 'brand', 'school', 'sustainability', 'music'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid category type',
            ], 400);
        }
        
        $category = Category::where('type', $type)
            ->where('slug', $slug)
            ->firstOrFail();
        
        // For now, return IDs as specified in requirements
        // This can be expanded to return actual items based on category type
        $items = $this->getCategoryItems($category);
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'category' => $category,
                'items' => $items,
            ],
        ]);
    }
    
    /**
     * Get items for a category (placeholder implementation).
     * 
     * @param Category $category
     * @return array
     */
    private function getCategoryItems(Category $category): array
    {
        $items = [];
        
        switch ($category->type) {
            case 'market':
                // For market categories, return product IDs
                // This would need proper category-product relationships in the future
                $items = Product::where('status', 'approved')
                    ->limit(20)
                    ->pluck('id')
                    ->toArray();
                break;
                
            case 'beauty':
            case 'brand':
            case 'school':
            case 'sustainability':
            case 'music':
                // For business profile categories, return business profile IDs
                $items = BusinessProfile::where('category', $category->type)
                    ->where('status', 'approved')
                    ->limit(20)
                    ->pluck('id')
                    ->toArray();
                break;
                
            default:
                // For other types, could return blog IDs or other relevant content
                $items = Blog::where('is_published', true)
                    ->limit(20)
                    ->pluck('id')
                    ->toArray();
                break;
        }
        
        return $items;
    }
}
