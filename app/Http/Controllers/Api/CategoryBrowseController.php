<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryBrowseController extends Controller
{
    public function categories(Request $request): JsonResponse
    {
        $request->validate([
            'include_subcategories' => ['nullable', 'boolean'],
        ]);

        $locale = app()->getLocale();
        $includeSubcategories = $request->boolean('include_subcategories', true);

        $categories = Category::query()
            ->where('is_active', true)
            ->withCount(['subcategories as active_subcategories_count' => function ($query) {
                $query->where('is_active', true);
            }])
            ->when($includeSubcategories, function ($query) {
                $query->with(['subcategories' => function ($subquery) {
                    $subquery
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->orderByDesc('id');
                }]);
            })
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'message' => __('api.categories.fetched'),
            'data' => $categories->map(function (Category $category) use ($locale, $includeSubcategories) {
                return [
                    'id' => $category->id,
                    'name' => $category->getTranslation('name', $locale),
                    'name_translations' => $category->getTranslations('name'),
                    'description' => $category->description
                        ? $category->getTranslation('description', $locale)
                        : null,
                    'description_translations' => $category->description
                        ? $category->getTranslations('description')
                        : null,
                    'sort_order' => $category->sort_order,
                    'active_subcategories_count' => (int) ($category->active_subcategories_count ?? 0),
                    'subcategories' => $includeSubcategories
                        ? $category->subcategories->map(function (Subcategory $subcategory) use ($locale) {
                            return [
                                'id' => $subcategory->id,
                                'category_id' => $subcategory->category_id,
                                'name' => $subcategory->getTranslation('name', $locale),
                                'name_translations' => $subcategory->getTranslations('name'),
                                'sort_order' => $subcategory->sort_order,
                            ];
                        })->values()
                        : null,
                ];
            })->values(),
        ]);
    }

    public function subcategories(Request $request): JsonResponse
    {
        $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
        ]);

        $locale = app()->getLocale();

        $subcategories = Subcategory::query()
            ->where('is_active', true)
            ->whereHas('category', function ($query) {
                $query->where('is_active', true);
            })
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('category_id', $request->integer('category_id'));
            })
            ->with(['category' => function ($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'message' => __('api.subcategories.fetched'),
            'data' => $subcategories->map(function (Subcategory $subcategory) use ($locale) {
                return [
                    'id' => $subcategory->id,
                    'category_id' => $subcategory->category_id,
                    'name' => $subcategory->getTranslation('name', $locale),
                    'name_translations' => $subcategory->getTranslations('name'),
                    'sort_order' => $subcategory->sort_order,
                    'category' => $subcategory->category ? [
                        'id' => $subcategory->category->id,
                        'name' => $subcategory->category->getTranslation('name', $locale),
                        'name_translations' => $subcategory->category->getTranslations('name'),
                    ] : null,
                ];
            })->values(),
        ]);
    }
}
