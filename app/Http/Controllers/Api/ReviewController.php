<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Review\StoreRequest;
use App\Http\Resources\ReviewResource;
use App\Models\BusinessAccount;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Services\Review\ReviewService;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    public function __construct(
        protected ReviewService $service
    ) {
    }

    public function index(Service $service): JsonResponse
    {
        $reviews = $this->service->listForService($service, 15);

        return response()->json([
            'message' => __('api.reviews.fetched'),
            'data' => ReviewResource::collection($reviews),
        ]);
    }

    public function store(
        StoreRequest $request,
        BusinessAccount $businessAccount,
        ServiceRequest $serviceRequest
    ): JsonResponse {
        $this->ensureOwnership($businessAccount);
        $review = $this->service->create($businessAccount, $serviceRequest, $request->validated());

        return response()->json([
            'message' => __('api.reviews.created'),
            'data' => new ReviewResource($review->load('reviewerBusinessAccount')),
        ], 201);
    }

    protected function ensureOwnership(BusinessAccount $businessAccount): void
    {
        abort_if($businessAccount->user_id !== auth('api')->id(), 403, __('api.errors.unauthorized'));
    }

}
