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
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BusinessAccountServiceController extends Controller
{
    public function __construct(
        protected ServiceService $serviceService
    ) {
    }

    public function index(BusinessAccount $businessAccount): AnonymousResourceCollection
    {
        $this->ensureOwnership($businessAccount);

        $services = $businessAccount->services()
            ->latest('id')
            ->paginate(15);

        return ServiceResource::collection($services);
    }

    public function store(StoreServiceRequest $request, BusinessAccount $businessAccount): ServiceResource
    {
        $this->ensureOwnership($businessAccount);
        $this->ensureBusinessAccountApproved($businessAccount);

        $service = $this->serviceService->create(
            $businessAccount,
            $request->validated()
        );

        return new ServiceResource($service);
    }

    public function show(BusinessAccount $businessAccount, Service $service): ServiceResource
    {
        $this->ensureOwnership($businessAccount);
        $this->ensureServiceBelongsToBusinessAccount($businessAccount, $service);

        return new ServiceResource($service);
    }

    public function update(
        UpdateServiceRequest $request,
        BusinessAccount $businessAccount,
        Service $service
    ): ServiceResource {
        $this->ensureOwnership($businessAccount);
        $this->ensureBusinessAccountApproved($businessAccount);
        $this->ensureServiceBelongsToBusinessAccount($businessAccount, $service);

        $service = $this->serviceService->update(
            $service,
            $request->validated()
        );

        return new ServiceResource($service);
    }

    public function destroy(BusinessAccount $businessAccount, Service $service): JsonResponse
    {
        $this->ensureOwnership($businessAccount);
        $this->ensureServiceBelongsToBusinessAccount($businessAccount, $service);

        $this->serviceService->delete($service);

        return response()->json([
            'message' => 'Service deleted successfully.',
        ]);
    }

    protected function ensureOwnership(BusinessAccount $businessAccount): void
    {
        abort_if($businessAccount->user_id !== auth('api')->id(), 403, 'Unauthorized.');
    }

    protected function ensureBusinessAccountApproved(BusinessAccount $businessAccount): void
    {
        abort_if($businessAccount->status !== 'approved', 422, 'Business account is not approved.');
    }

    protected function ensureServiceBelongsToBusinessAccount(
        BusinessAccount $businessAccount,
        Service $service
    ): void {
        abort_if($service->business_account_id !== $businessAccount->id, 404);
    }
}
