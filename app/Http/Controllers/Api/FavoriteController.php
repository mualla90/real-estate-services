<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Favorite\StoreRequest;
use App\Http\Resources\FavoriteResource;
use App\Models\BusinessAccount;
use App\Models\Service;
use App\Services\Favorite\FavoriteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function __construct(
        protected FavoriteService $service
    ) {
    }

    public function index(Request $request, BusinessAccount $businessAccount): JsonResponse
    {
        $this->ensureOwnership($businessAccount);

        $perPage = (int) $request->input('per_page', 15);
        $favorites = $this->service->list($businessAccount, $perPage);

        return response()->json([
            'message' => __('api.favorites.fetched'),
            'data' => FavoriteResource::collection($favorites),
        ]);
    }

    public function store(StoreRequest $request, BusinessAccount $businessAccount): JsonResponse
    {
        $this->ensureOwnership($businessAccount);

        $favorite = $this->service->add($businessAccount, $request->validated());

        return response()->json([
            'message' => __('api.favorites.added'),
            'data' => new FavoriteResource($favorite),
        ], 201);
    }

    public function destroy(BusinessAccount $businessAccount, Service $service): JsonResponse
    {
        $this->ensureOwnership($businessAccount);

        $this->service->remove($businessAccount, $service);

        return response()->json([
            'message' => __('api.favorites.removed'),
        ]);
    }

    protected function ensureOwnership(BusinessAccount $businessAccount): void
    {
        abort_if($businessAccount->user_id !== auth('api')->id(), 403, __('api.errors.unauthorized'));
    }
}
