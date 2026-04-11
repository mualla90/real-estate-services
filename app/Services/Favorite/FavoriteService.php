<?php

namespace App\Services\Favorite;

use App\Models\BusinessAccount;
use App\Models\Favorite;
use App\Models\Service;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class FavoriteService
{
    public function list(BusinessAccount $businessAccount, int $perPage = 15): LengthAwarePaginator
    {
        return $businessAccount->favorites()
            ->with([
                'service.category',
                'service.subcategory',
                'service.city',
                'service.media',
                'service.dynamicFieldValues.dynamicField',
            ])
            ->latest('id')
            ->paginate($perPage);
    }

    public function add(BusinessAccount $businessAccount, array $data): Favorite
    {
        $service = Service::query()->findOrFail($data['service_id']);
        abort_unless($service->isVisible(), 422, 'Service is not available.');

        return DB::transaction(function () use ($businessAccount, $service, $data) {
            $favorite = Favorite::query()->firstOrCreate(
                [
                    'business_account_id' => $businessAccount->id,
                    'service_id' => $service->id,
                ],
                [
                    'note' => $data['note'] ?? null,
                ]
            );

            if ($favorite->wasRecentlyCreated === false && array_key_exists('note', $data)) {
                $favorite->update(['note' => $data['note']]);
            }

            return $favorite->fresh([
                'service.category',
                'service.subcategory',
                'service.city',
                'service.media',
                'service.dynamicFieldValues.dynamicField',
            ]);
        });
    }

    public function remove(BusinessAccount $businessAccount, Service $service): void
    {
        $favorite = Favorite::query()
            ->where('business_account_id', $businessAccount->id)
            ->where('service_id', $service->id)
            ->first();

        abort_if(! $favorite, 404, 'Favorite not found.');

        $favorite->delete();
    }
}

