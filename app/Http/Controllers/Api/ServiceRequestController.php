<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ServiceRequest\RejectRequest;
use App\Http\Requests\Api\ServiceRequest\StoreRequest;
use App\Http\Resources\ServiceRequestResource;
use App\Models\BusinessAccount;
use App\Models\ServiceRequest;
use App\Services\ServiceRequest\ServiceRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    public function __construct(
        protected ServiceRequestService $service
    ) {
    }

    public function outgoing(Request $request, BusinessAccount $businessAccount): JsonResponse
    {
        $this->ensureOwnership($businessAccount);

        $requests = $this->service->outgoing($businessAccount, $request->only(['status', 'per_page']));

        return response()->json([
            'message' => __('api.service_requests.outgoing_fetched'),
            'data' => ServiceRequestResource::collection($requests),
        ]);
    }

    public function incoming(Request $request, BusinessAccount $businessAccount): JsonResponse
    {
        $this->ensureOwnership($businessAccount);

        $requests = $this->service->incoming($businessAccount, $request->only(['status', 'per_page']));

        return response()->json([
            'message' => __('api.service_requests.incoming_fetched'),
            'data' => ServiceRequestResource::collection($requests),
        ]);
    }

    public function store(StoreRequest $request, BusinessAccount $businessAccount): JsonResponse
    {
        $this->ensureOwnership($businessAccount);

        $serviceRequest = $this->service->create($businessAccount, $request->validated());

        return response()->json([
            'message' => __('api.service_requests.created'),
            'data' => new ServiceRequestResource($serviceRequest),
        ], 201);
    }

    public function accept(BusinessAccount $businessAccount, ServiceRequest $serviceRequest): JsonResponse
    {
        $this->ensureOwnership($businessAccount);
        $serviceRequest = $this->service->accept($businessAccount, $serviceRequest);

        return response()->json([
            'message' => __('api.service_requests.accepted'),
            'data' => new ServiceRequestResource($serviceRequest),
        ]);
    }

    public function reject(
        RejectRequest $request,
        BusinessAccount $businessAccount,
        ServiceRequest $serviceRequest
    ): JsonResponse {
        $this->ensureOwnership($businessAccount);
        $serviceRequest = $this->service->reject(
            $businessAccount,
            $serviceRequest,
            $request->validated('rejection_reason')
        );

        return response()->json([
            'message' => __('api.service_requests.rejected'),
            'data' => new ServiceRequestResource($serviceRequest),
        ]);
    }

    public function destroy(BusinessAccount $businessAccount, ServiceRequest $serviceRequest): JsonResponse
    {
        $this->ensureOwnership($businessAccount);
        $serviceRequest = $this->service->cancel($businessAccount, $serviceRequest);

        return response()->json([
            'message' => __('api.service_requests.cancelled'),
            'data' => new ServiceRequestResource($serviceRequest),
        ]);
    }

    protected function ensureOwnership(BusinessAccount $businessAccount): void
    {
        abort_if($businessAccount->user_id !== auth('api')->id(), 403, __('api.errors.unauthorized'));
    }

}
