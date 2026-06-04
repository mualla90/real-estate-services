<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    protected $table = 'app_notifications';

    protected $fillable = [
        'type',
        'title',
        'message',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function adminUrl(): ?string
    {
        $data = $this->data ?? [];

        return match ($this->type) {
            'business_account_pending_review',
            'business_account_approved',
            'business_account_rejected' => ! empty($data['business_account_id'])
                ? route('admin.business-accounts.show', ['businessAccount' => $data['business_account_id']])
                : route('admin.business-accounts.index'),

            'service_pending_review',
            'service_approved',
            'service_rejected' => ! empty($data['service_id'])
                ? route('admin.services.review.show', ['service' => $data['service_id']])
                : route('admin.services.review.index'),

            'category_created',
            'category_updated' => ! empty($data['category_id'])
                ? route('admin.categories.edit', ['category' => $data['category_id']])
                : route('admin.categories.index'),

            'subcategory_created',
            'subcategory_updated' => ! empty($data['subcategory_id'])
                ? route('admin.subcategories.edit', ['subcategory' => $data['subcategory_id']])
                : route('admin.subcategories.index'),

            default => null,
        };
    }
}
