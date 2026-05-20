<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'service_id',
        'initiator_business_account_id',
        'recipient_business_account_id',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function initiatorBusinessAccount()
    {
        return $this->belongsTo(BusinessAccount::class, 'initiator_business_account_id');
    }

    public function recipientBusinessAccount()
    {
        return $this->belongsTo(BusinessAccount::class, 'recipient_business_account_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }
}

