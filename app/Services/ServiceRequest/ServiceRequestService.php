<?php

namespace App\Services\ServiceRequest;

use App\Models\BusinessAccount;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Services\Notification\NotificationService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ServiceRequestService
{
    public function __construct(
        protected NotificationService $notifications
    ) {
    }

    public function outgoing(BusinessAccount $businessAccount, array $filters = []): LengthAwarePaginator
    {
        $perPage = (int) ($filters['per_page'] ?? 15);

        return $businessAccount->outgoingServiceRequests()
            ->with($this->relations())
            ->when(! empty($filters['status']), fn ($q) => $q->where('status', $filters['status']))
            ->latest('id')
            ->paginate($perPage);
    }

    public function incoming(BusinessAccount $businessAccount, array $filters = []): LengthAwarePaginator
    {
        $perPage = (int) ($filters['per_page'] ?? 15);

        return $businessAccount->incomingServiceRequests()
            ->with($this->relations())
            ->when(! empty($filters['status']), fn ($q) => $q->where('status', $filters['status']))
            ->latest('id')
            ->paginate($perPage);
    }

    public function create(BusinessAccount $requesterBusinessAccount, array $data): ServiceRequest
    {
        $this->ensureBusinessAccountApproved($requesterBusinessAccount);

        $service = Service::query()->findOrFail($data['service_id']);
        abort_unless($service->isVisible(), 422, __('api.errors.service_unavailable_for_requests'));

        $providerBusinessAccountId = $service->business_account_id;
        abort_if($providerBusinessAccountId === $requesterBusinessAccount->id, 422, __('api.errors.cannot_request_own_service'));

        $providerAccount = BusinessAccount::query()->findOrFail($providerBusinessAccountId);
        $this->ensureBusinessAccountApproved($providerAccount);

        $existingPending = ServiceRequest::query()
            ->where('service_id', $service->id)
            ->where('requester_business_account_id', $requesterBusinessAccount->id)
            ->where('status', 'pending')
            ->exists();

        abort_if($existingPending, 422, __('api.errors.duplicate_pending_request'));

        $serviceRequest = DB::transaction(function () use ($service, $requesterBusinessAccount, $providerBusinessAccountId, $data) {
            return ServiceRequest::query()->create([
                'service_id' => $service->id,
                'requester_business_account_id' => $requesterBusinessAccount->id,
                'provider_business_account_id' => $providerBusinessAccountId,
                'status' => 'pending',
                'quantity' => $data['quantity'] ?? 1,
                'needed_at' => $data['needed_at'] ?? null,
                'message' => $data['message'] ?? null,
                'price_offer' => $data['price_offer'] ?? null,
            ])->load($this->relations());
        });

        $providerUser = $providerAccount->user;
        if ($providerUser) {
            $this->notifications->notifyUser(
                $providerUser,
                'new_service_request',
                __('api.notification_text.new_service_request_title'),
                __('api.notification_text.new_service_request_message'),
                [
                    'service_request_id' => $serviceRequest->id,
                    'service_id' => $service->id,
                    'requester_business_account_id' => $requesterBusinessAccount->id,
                ]
            );
        }

        return $serviceRequest;
    }

    public function accept(BusinessAccount $providerBusinessAccount, ServiceRequest $serviceRequest): ServiceRequest
    {
        $this->ensureCanRespond($providerBusinessAccount, $serviceRequest);

        return DB::transaction(function () use ($serviceRequest) {
            $serviceRequest->update([
                'status' => 'accepted',
                'rejection_reason' => null,
                'responded_at' => now(),
            ]);

            return $serviceRequest->fresh()->load($this->relations());
        });
    }

    public function reject(
        BusinessAccount $providerBusinessAccount,
        ServiceRequest $serviceRequest,
        string $rejectionReason
    ): ServiceRequest {
        $this->ensureCanRespond($providerBusinessAccount, $serviceRequest);

        return DB::transaction(function () use ($serviceRequest, $rejectionReason) {
            $serviceRequest->update([
                'status' => 'rejected',
                'rejection_reason' => $rejectionReason,
                'responded_at' => now(),
            ]);

            return $serviceRequest->fresh()->load($this->relations());
        });
    }

    public function cancel(BusinessAccount $requesterBusinessAccount, ServiceRequest $serviceRequest): ServiceRequest
    {
        abort_if($serviceRequest->requester_business_account_id !== $requesterBusinessAccount->id, 403, __('api.errors.unauthorized'));
        abort_if($serviceRequest->status !== 'pending', 422, __('api.errors.only_pending_requests_can_be_cancelled'));

        return DB::transaction(function () use ($serviceRequest) {
            $serviceRequest->update([
                'status' => 'cancelled',
                'cancellation_reason' => 'Cancelled by requester.',
                'cancelled_at' => now(),
            ]);

            return $serviceRequest->fresh()->load($this->relations());
        });
    }

    public function ensureBusinessAccountApproved(BusinessAccount $businessAccount): void
    {
        abort_if($businessAccount->status !== 'approved', 422, __('api.errors.business_account_not_approved'));
    }

    public function ensureCanRespond(BusinessAccount $providerBusinessAccount, ServiceRequest $serviceRequest): void
    {
        abort_if($serviceRequest->provider_business_account_id !== $providerBusinessAccount->id, 403, __('api.errors.unauthorized'));
        abort_if($serviceRequest->status !== 'pending', 422, __('api.errors.only_pending_requests_can_be_updated'));
    }

    protected function relations(): array
    {
        return [
            'service.category',
            'service.subcategory',
            'service.city',
            'service.media',
            'service.dynamicFieldValues.dynamicField',
            'requesterBusinessAccount',
            'providerBusinessAccount',
        ];
    }
}
