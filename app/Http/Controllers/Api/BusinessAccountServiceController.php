<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Service\StoreServiceRequest;
use App\Http\Requests\Api\Service\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\BusinessAccount;
use App\Models\Service;
use App\Services\ServiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessAccountServiceController extends Controller
{
    public function __construct(
        protected ServiceService $serviceService
    ) {
    }

    public function index(Request $request, BusinessAccount $businessAccount): JsonResponse
    {
        $this->ensureOwnership($businessAccount);

        $perPage = (int) $request->input('per_page', 15);

        $services = $businessAccount->services()
            ->with(['category', 'subcategory', 'city', 'media', 'dynamicFieldValues.dynamicField'])
            ->latest('id')
            ->paginate($perPage);

        return response()->json([
            'message' => __('api.services.fetched'),
            'data' => ServiceResource::collection($services),
        ]);
    }

    public function store(StoreServiceRequest $request, BusinessAccount $businessAccount): JsonResponse
    {
        $this->ensureOwnership($businessAccount);
        $this->ensureBusinessAccountApproved($businessAccount);

        $service = $this->serviceService->create(
            $businessAccount,
            $request->validated()
        );

        return response()->json([
            'message' => __('api.services.created'),
            'data' => new ServiceResource($service),
        ], 201);
    }

    public function show(BusinessAccount $businessAccount, Service $service): JsonResponse
    {
        $this->ensureOwnership($businessAccount);
        $this->ensureServiceBelongsToBusinessAccount($businessAccount, $service);

        $service->load(['category', 'subcategory', 'city', 'media', 'dynamicFieldValues.dynamicField']);

        return response()->json([
            'message' => __('api.services.single_fetched'),
            'data' => new ServiceResource($service),
        ]);
    }

    public function update(
        UpdateServiceRequest $request,
        BusinessAccount $businessAccount,
        Service $service
    ): JsonResponse {
        $this->ensureOwnership($businessAccount);
        $this->ensureBusinessAccountApproved($businessAccount);
        $this->ensureServiceBelongsToBusinessAccount($businessAccount, $service);

        $service = $this->serviceService->update(
            $service,
            $request->validated()
        );

        return response()->json([
            'message' => __('api.services.updated'),
            'data' => new ServiceResource($service),
        ]);
    }

    public function destroy(BusinessAccount $businessAccount, Service $service): JsonResponse
    {
        $this->ensureOwnership($businessAccount);
        $this->ensureServiceBelongsToBusinessAccount($businessAccount, $service);

        $this->serviceService->delete($service);

        return response()->json([
            'message' => __('api.services.deleted'),
        ]);
    }

    protected function ensureOwnership(BusinessAccount $businessAccount): void
    {
        abort_if($businessAccount->user_id !== auth('api')->id(), 403, __('api.errors.unauthorized'));
    }

    protected function ensureBusinessAccountApproved(BusinessAccount $businessAccount): void
    {
        abort_if($businessAccount->status !== 'approved', 422, __('api.errors.business_account_not_approved'));
    }

    protected function ensureServiceBelongsToBusinessAccount(
        BusinessAccount $businessAccount,
        Service $service
    ): void {
        abort_if($service->business_account_id !== $businessAccount->id, 404);
    }
}
