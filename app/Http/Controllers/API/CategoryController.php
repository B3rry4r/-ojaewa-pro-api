<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\BusinessProfile;
use App\Models\Blog;
use App\Models\SustainabilityInitiative;
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
     * Returns full objects with pagination for products, businesses, or sustainability initiatives.
     * 
     * @param Request $request
     * @param string $type
     * @param string $slug
     * @return JsonResponse
     */
    public function items(Request $request, string $type, string $slug): JsonResponse
    {
        // Validate type
        if (!in_array($type, ['market', 'beauty', 'brand', 'school', 'sustainability', 'music'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid category type',
            ], 400);
        }
        
        // Validate pagination parameters
        $request->validate([
            'per_page' => 'integer|min:1|max:50',
            'page' => 'integer|min:1',
        ]);
        
        $category = Category::where('type', $type)
            ->where('slug', $slug)
            ->firstOrFail();
        
        $perPage = $request->input('per_page', 15);
        
        // Get paginated items based on category type
        $items = $this->getCategoryItems($category, $perPage);
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'category' => [
                    'id' => $category->id,
                    'type' => $category->type,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                ],
                'items' => $items,
            ],
        ]);
    }
    
    /**
     * Get paginated items for a category.
     * Returns full objects (products, businesses, or sustainability initiatives).
     * 
     * @param Category $category
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private function getCategoryItems(Category $category, int $perPage)
    {
        // Include this category and all descendants for inclusive filtering
        $categoryIds = $category->getSelfAndDescendantIds();

        switch ($category->type) {
            case 'market':
                // For market categories, return products filtered by category hierarchy
                return Product::whereIn('category_id', $categoryIds)
                    ->where('status', 'approved')
                    ->with([
                        'category:id,name,slug,parent_id,type',
                        'sellerProfile:id,business_name,business_email,city,state'
                    ])
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage);
                
            case 'beauty':
            case 'brand':
            case 'school':
            case 'music':
                // For business categories, return business profiles filtered by category/subcategory hierarchy
                return BusinessProfile::where(function ($query) use ($categoryIds) {
                        $query->whereIn('category_id', $categoryIds)
                              ->orWhereIn('subcategory_id', $categoryIds);
                    })
                    ->where('store_status', 'approved')
                    ->with([
                        'user:id,firstname,lastname',
                        'categoryRelation:id,name,slug,parent_id,type',
                        'subcategoryRelation:id,name,slug,parent_id,type'
                    ])
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage);
                
            case 'sustainability':
                // For sustainability categories, return initiatives filtered by category hierarchy
                return SustainabilityInitiative::whereIn('category_id', $categoryIds)
                    ->where('status', 'active')
                    ->with([
                        'admin:id,firstname,lastname',
                        'categoryRelation:id,name,slug,parent_id,type'
                    ])
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage);
                
            default:
                // Fallback: return empty paginated collection
                return collect([])->paginate($perPage);
        }
    }
}
