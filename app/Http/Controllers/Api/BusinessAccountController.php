<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BusinessAccount\StoreRequest;
use App\Http\Requests\Api\BusinessAccount\UpdateRequest;
use App\Models\BusinessAccount;
use App\Services\BusinessAccount\BusinessAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessAccountController extends Controller
{
    public function __construct(
        protected BusinessAccountService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        $businessAccounts = BusinessAccount::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'message' => __('api.business_accounts.fetched'),
            'data' => $businessAccounts,
        ]);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $businessAccount = $this->service->create(
            $request->user(),
            $request->validated()
        );

        return response()->json([
            'message' => __('api.business_accounts.created'),
            'data' => $businessAccount,
        ], 201);
    }

    public function show(Request $request, BusinessAccount $businessAccount): JsonResponse
    {
        abort_if($businessAccount->user_id !== $request->user()->id, 403);

        return response()->json([
            'message' => __('api.business_accounts.single_fetched'),
            'data' => $businessAccount,
        ]);
    }

    public function update(UpdateRequest $request, BusinessAccount $businessAccount): JsonResponse
    {
        abort_if($businessAccount->user_id !== $request->user()->id, 403);

        $businessAccount = $this->service->update(
            $businessAccount,
            $request->validated()
        );

        return response()->json([
            'message' => __('api.business_accounts.updated'),
            'data' => $businessAccount,
        ]);
    }
}
