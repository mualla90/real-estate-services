<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Report\StoreRequest;
use App\Http\Resources\ReportResource;
use App\Models\BusinessAccount;
use App\Models\Service;
use App\Services\Report\ReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $service
    ) {
    }

    public function storeForService(
        StoreRequest $request,
        BusinessAccount $businessAccount,
        Service $service
    ): JsonResponse {
        $this->ensureOwnership($businessAccount);

        $report = $this->service->createForService($businessAccount, $service, $request->validated());

        return response()->json([
            'message' => __('api.reports.submitted'),
            'data' => new ReportResource($report),
        ], 201);
    }

    protected function ensureOwnership(BusinessAccount $businessAccount): void
    {
        abort_if($businessAccount->user_id !== auth('api')->id(), 403, __('api.errors.unauthorized'));
    }
}
