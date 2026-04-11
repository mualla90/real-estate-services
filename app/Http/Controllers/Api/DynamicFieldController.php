<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DynamicField;
use App\Rules\SubcategoryBelongsToCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DynamicFieldController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'subcategory_id' => [
                'nullable',
                'exists:subcategories,id',
                new SubcategoryBelongsToCategory($request->input('category_id')),
            ],
        ]);

        $categoryId = (int) $request->input('category_id');
        $subcategoryId = $request->input('subcategory_id');
        $locale = app()->getLocale();

        $fields = DynamicField::query()
            ->where('status', 'active')
            ->where(function ($query) use ($categoryId, $subcategoryId) {
                $query->where(function ($q) use ($categoryId) {
                    $q->where('category_id', $categoryId)
                        ->whereNull('subcategory_id');
                });

                if ($subcategoryId) {
                    $query->orWhere(function ($q) use ($subcategoryId) {
                        $q->whereNull('category_id')
                            ->where('subcategory_id', $subcategoryId);
                    });

                    $query->orWhere(function ($q) use ($categoryId, $subcategoryId) {
                        $q->where('category_id', $categoryId)
                            ->where('subcategory_id', $subcategoryId);
                    });
                }
            })
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'message' => __('api.dynamic_fields.fetched'),
            'data' => $fields->map(function (DynamicField $field) use ($locale) {
                return [
                    'id' => $field->id,
                    'name' => $field->getTranslation('name', $locale),
                    'name_translations' => $field->getTranslations('name'),
                    'field_key' => $field->field_key,
                    'field_type' => $field->field_type,
                    'is_required' => $field->is_required,
                    'options' => $field->options,
                    'sort_order' => $field->sort_order,
                ];
            }),
        ]);
    }
}
