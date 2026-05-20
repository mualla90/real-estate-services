<?php

namespace App\Services\Review;

use App\Models\BusinessAccount;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ReviewService
{
    public function listForService(Service $service, int $perPage = 15): LengthAwarePaginator
    {
        abort_unless($service->isVisible(), 404);

        return $service->reviews()
            ->with('reviewerBusinessAccount')
            ->latest('id')
            ->paginate($perPage);
    }

    public function create(BusinessAccount $reviewerBusinessAccount, ServiceRequest $serviceRequest, array $data): Review
    {
        $this->ensureBusinessAccountApproved($reviewerBusinessAccount);

        abort_if($serviceRequest->requester_business_account_id !== $reviewerBusinessAccount->id, 403, 'Unauthorized.');
        abort_if($serviceRequest->status !== 'accepted', 422, __('api.errors.review_requires_accepted_request'));

        $alreadyReviewed = Review::query()
            ->where('service_request_id', $serviceRequest->id)
            ->exists();

        abort_if($alreadyReviewed, 422, __('api.errors.request_already_reviewed'));

        return DB::transaction(function () use ($reviewerBusinessAccount, $serviceRequest, $data) {
            $review = Review::query()->create([
                'service_id' => $serviceRequest->service_id,
                'service_request_id' => $serviceRequest->id,
                'reviewer_business_account_id' => $reviewerBusinessAccount->id,
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null,
            ]);

            $this->refreshServiceRatingStats($serviceRequest->service);

            return $review->load('reviewerBusinessAccount');
        });
    }

    public function ensureBusinessAccountApproved(BusinessAccount $businessAccount): void
    {
        abort_if($businessAccount->status !== 'approved', 422, __('api.errors.business_account_not_approved'));
    }

    protected function refreshServiceRatingStats(Service $service): void
    {
        $count = $service->reviews()->count();
        $average = $count > 0 ? (float) $service->reviews()->avg('rating') : 0;

        $service->update([
            'review_count' => $count,
            'average_rating' => round($average, 2),
        ]);
    }
}
