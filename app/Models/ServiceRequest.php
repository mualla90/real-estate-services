<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'service_id',
        'requester_business_account_id',
        'provider_business_account_id',
        'status',
        'quantity',
        'needed_at',
        'message',
        'price_offer',
        'rejection_reason',
        'responded_at',
        'cancellation_reason',
        'cancelled_at',
    ];

    protected $casts = [
        'needed_at' => 'datetime',
        'responded_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'price_offer' => 'decimal:2',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function requesterBusinessAccount()
    {
        return $this->belongsTo(BusinessAccount::class, 'requester_business_account_id');
    }

    public function providerBusinessAccount()
    {
        return $this->belongsTo(BusinessAccount::class, 'provider_business_account_id');
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
