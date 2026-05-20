<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Rules\SubcategoryBelongsToCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceBrowseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'subcategory_id' => [
                'nullable',
                'exists:subcategories,id',
                new SubcategoryBelongsToCategory($request->input('category_id')),
            ],
            'city_id' => ['nullable', Rule::exists('cities', 'id')->where('is_active', true)],
            'service_type' => ['nullable', Rule::in(['sale', 'rent'])],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'price_currency' => ['nullable', Rule::in(['USD', 'SYP'])],
            'search' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'radius_km' => ['nullable', 'numeric', 'min:0.1', 'max:500'],
            'sort' => ['nullable', Rule::in(['latest', 'price_asc', 'price_desc', 'rating_desc'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $perPage = (int) ($validated['per_page'] ?? 15);
        $priceColumn = ($validated['price_currency'] ?? 'USD') === 'SYP' ? 'price_syp' : 'price_usd';
        $favoriteBusinessAccountIds = $this->favoriteBusinessAccountIds($request);

        $services = Service::query()
            ->with(['category', 'subcategory', 'city', 'media', 'dynamicFieldValues.dynamicField'])
            ->when($favoriteBusinessAccountIds !== [], function ($query) use ($favoriteBusinessAccountIds) {
                $query->withExists([
                    'favorites as is_favorited' => fn ($q) => $q->whereIn('business_account_id', $favoriteBusinessAccountIds),
                ]);
            })
            ->approved()
            ->active()
            ->published()
            ->when(isset($validated['category_id']), fn ($q) => $q->where('category_id', $validated['category_id']))
            ->when(isset($validated['subcategory_id']), fn ($q) => $q->where('subcategory_id', $validated['subcategory_id']))
            ->when(isset($validated['city_id']), fn ($q) => $q->where('city_id', $validated['city_id']))
            ->when(isset($validated['service_type']), fn ($q) => $q->where('service_type', $validated['service_type']))
            ->when(isset($validated['min_price']), fn ($q) => $q->where($priceColumn, '>=', $validated['min_price']))
            ->when(isset($validated['max_price']), fn ($q) => $q->where($priceColumn, '<=', $validated['max_price']))
            ->when(! empty($validated['search']), function ($query) use ($validated) {
                $search = $validated['search'];

                $query->where(function ($q) use ($search) {
                    $q->where('title->en', 'like', "%{$search}%")
                        ->orWhere('title->ar', 'like', "%{$search}%")
                        ->orWhere('description->en', 'like', "%{$search}%")
                        ->orWhere('description->ar', 'like', "%{$search}%");
                });
            })
            ->when(
                isset($validated['latitude'], $validated['longitude'], $validated['radius_km']),
                function ($query) use ($validated) {
                    $latitude = (float) $validated['latitude'];
                    $longitude = (float) $validated['longitude'];
                    $radiusKm = (float) $validated['radius_km'];

                    $haversine = '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))';

                    $query->whereNotNull('latitude')
                        ->whereNotNull('longitude')
                        ->whereRaw($haversine . ' <= ?', [$latitude, $longitude, $latitude, $radiusKm]);
                }
            );

        $sort = $validated['sort'] ?? 'latest';
        match ($sort) {
            'price_asc' => $services->orderBy($priceColumn),
            'price_desc' => $services->orderByDesc($priceColumn),
            'rating_desc' => $services->orderByDesc('average_rating')->latest('id'),
            default => $services->ordered(),
        };

        $result = $services->paginate($perPage)->withQueryString();

        return response()->json([
            'message' => __('api.services.fetched'),
            'data' => ServiceResource::collection($result),
        ]);
    }

    public function show(Service $service): JsonResponse
    {
        abort_unless($service->isVisible(), 404);
        $favoriteBusinessAccountIds = $this->favoriteBusinessAccountIds(request());

        $service->increment('views_count');
        $service->load(['category', 'subcategory', 'city', 'media', 'dynamicFieldValues.dynamicField']);
        $service->setAttribute(
            'is_favorited',
            $favoriteBusinessAccountIds !== []
                ? $service->favorites()->whereIn('business_account_id', $favoriteBusinessAccountIds)->exists()
                : false
        );

        return response()->json([
            'message' => __('api.services.single_fetched'),
            'data' => new ServiceResource($service),
        ]);
    }

    protected function favoriteBusinessAccountIds(Request $request): array
    {
        $user = auth('api')->user();
        if (! $user) {
            return [];
        }

        return $user->businessAccounts()
            ->where('status', 'approved')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }
}
