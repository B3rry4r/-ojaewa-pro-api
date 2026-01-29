<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\BusinessProfile;
use App\Models\SustainabilityInitiative;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

/**
 * CategoryController
 * 
 * FINAL LOCKED MODEL - Category Types and Entity Mapping:
 * ========================================================
 * 
 * PRODUCT CATALOGS (return Products):
 * - textiles (3 levels: Group → Leaf)
 * - shoes_bags (3 levels: Group → Leaf)
 * - afro_beauty_products (3 levels: Group → Leaf)
 * 
 * BUSINESS DIRECTORIES (return BusinessProfiles) - 2 levels:
 * - school (2 levels: Leaf only)
 * 
 * INITIATIVES (return SustainabilityInitiatives) - 2 levels:
 * - sustainability (2 levels: Leaf only)
 * 
 * AFRO BEAUTY: Single products tree (no services)
 * - afro_beauty_products → Products
 */
class CategoryController extends Controller
{
    /**
     * Get ALL categories grouped by type with full tree structure.
     * Useful for registration forms to let users select categories.
     * Also includes form options for fabrics, styles, and tribes.
     * 
     * GET /api/categories/all
     * 
     * @return JsonResponse
     */
    public function allCategoriesTree(): JsonResponse
    {
        $result = [];
        
        foreach (Category::TYPES as $type) {
            $result[$type] = Category::where('type', $type)
                ->whereNull('parent_id')
                ->orderBy('order')
                ->with(['children' => function ($query) {
                    $query->orderBy('order')->with(['children' => function ($q) {
                        $q->orderBy('order');
                    }]);
                }])
                ->get();
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $result,
            'form_options' => [
                'fabrics' => Product::select('fabric_type')
                    ->whereNotNull('fabric_type')
                    ->where('fabric_type', '!=', '')
                    ->distinct()
                    ->orderBy('fabric_type')
                    ->pluck('fabric_type')
                    ->values(),
                'styles' => Product::select('style')
                    ->whereNotNull('style')
                    ->where('style', '!=', '')
                    ->distinct()
                    ->orderBy('style')
                    ->pluck('style')
                    ->values(),
                'tribes' => Product::select('tribe')
                    ->whereNotNull('tribe')
                    ->where('tribe', '!=', '')
                    ->distinct()
                    ->orderBy('tribe')
                    ->pluck('tribe')
                    ->values(),
            ],
            'meta' => [
                'type_mapping' => [
                    'product_catalogs' => [
                        'types' => Category::PRODUCT_TYPES,
                        'returns' => 'Products',
                        'description' => 'Use category_id when creating products',
                    ],
                    'business_directories' => [
                        'types' => Category::BUSINESS_TYPES,
                        'returns' => 'BusinessProfiles',
                        'description' => 'Use category_id when registering businesses',
                    ],
                    'initiatives' => [
                        'types' => Category::INITIATIVE_TYPES,
                        'returns' => 'SustainabilityInitiatives',
                        'description' => 'Use category_id when creating initiatives',
                    ],
                ],
                'afro_beauty_tabs' => [
                    'tab_1_products' => 'afro_beauty_products',
                ],
                'depth_rules' => [
                    'textiles' => '3 levels (Group → Leaf)',
                    'shoes_bags' => '3 levels (Group → Leaf)',
                    'afro_beauty_products' => '3 levels (Group → Leaf)',
                    'art' => '2 levels (Leaf only)',
                    'school' => '2 levels (Leaf only)',
                    'sustainability' => '2 levels (Leaf only)',
                ],
                'form_option_usage' => [
                    'textiles' => [
                        'required' => ['fabric_type', 'style', 'tribe', 'size'],
                        'description' => 'Textiles products require fabric, style, tribe, and size',
                    ],
                    'shoes_bags' => [
                        'required' => ['size'],
                        'optional' => ['fabric_type'],
                        'description' => 'Shoes & Bags only require size; fabric_type is optional',
                    ],
                    'afro_beauty_products' => [
                        'required' => [],
                        'description' => 'Afro beauty products do not require apparel fields',
                    ],
                    'art' => [
                        'required' => [],
                        'description' => 'Art products do not require apparel fields',
                    ],
                ],
                'types' => Category::TYPES,
            ],
        ]);
    }

    /**
     * Get categories by type.
     * 
     * GET /api/categories?type={type}
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'type' => ['required', Rule::in(Category::TYPES)],
        ]);
        
        $type = $request->type;
        
        $categories = Category::ofType($type)
            ->topLevel()
            ->orderBy('order')
            ->with(['children' => function ($query) {
                $query->orderBy('order');
            }])
            ->get();
        
        // Determine what entity type this category returns
        $entityType = $this->getEntityTypeForType($type);
        
        return response()->json([
            'status' => 'success',
            'data' => $categories,
            'meta' => [
                'type' => $type,
                'returns' => $entityType,
                'total_count' => $categories->count(),
            ],
        ]);
    }
    
    /**
     * Get children of a specific category.
     * 
     * GET /api/categories/{id}/children
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
                'entity_type' => $category->getEntityType(),
            ],
        ]);
    }
    
    /**
     * Get items for a specific category by type and slug.
     * Returns full objects with pagination for products, businesses, or sustainability initiatives.
     * 
     * GET /api/categories/{type}/{slug}/items
     * 
     * @param Request $request
     * @param string $type
     * @param string $slug
     * @return JsonResponse
     */
    public function items(Request $request, string $type, string $slug): JsonResponse
    {
        // Validate type
        if (!in_array($type, Category::TYPES)) { 
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid category type. Valid types: ' . implode(', ', Category::TYPES),
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
                    'parent_id' => $category->parent_id,
                    'entity_type' => $category->getEntityType(),
                ],
                'items' => $items,
            ],
        ]);
    }
    
    /**
     * Get paginated items for a category.
     * Uses the category's type to determine which entity to return.
     * 
     * @param Category $category
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private function getCategoryItems(Category $category, int $perPage)
    {
        // Include this category and all descendants for inclusive filtering
        $categoryIds = $category->getSelfAndDescendantIds();

        // PRODUCT TYPES: textiles, shoes_bags, afro_beauty_products
        if ($category->returnsProducts()) {
            return Product::whereIn('category_id', $categoryIds)
                ->where('status', 'approved')
                ->with([
                    'category:id,name,slug,parent_id,type',
                    'sellerProfile:id,business_name,business_email,city,state'
                ])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
        }
        
        // BUSINESS TYPES: school
        if ($category->returnsBusinesses()) {
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
        }
        
        // INITIATIVE TYPES: sustainability
        if ($category->returnsInitiatives()) {
            return SustainabilityInitiative::whereIn('category_id', $categoryIds)
                ->where('status', 'active')
                ->with([
                    'admin:id,firstname,lastname',
                    'categoryRelation:id,name,slug,parent_id,type'
                ])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
        }
        
        // Fallback: return empty paginated collection
        return Product::whereRaw('1 = 0')->paginate($perPage);
    }
    
    /**
     * Get the entity type string for a given category type
     */
    private function getEntityTypeForType(string $type): string
    {
        if (in_array($type, Category::PRODUCT_TYPES)) {
            return 'products';
        }
        if (in_array($type, Category::BUSINESS_TYPES)) {
            return 'businesses';
        }
        if (in_array($type, Category::INITIATIVE_TYPES)) {
            return 'initiatives';
        }
        return 'unknown';
    }
}
