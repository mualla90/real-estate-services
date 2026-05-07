<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class BusinessAccount extends Model implements HasMedia
{
    use HasFactory,HasTranslations,SoftDeletes,InteractsWithMedia;
    public array $translatable = [
        'name',
        'description'
    ];
    protected $fillable = [
        'user_id',
        'activity_type_id',
        'city_id',
        'license_number',
        'name',
        'phone',
        'email',
        'description',
        'address',
        'latitude',
        'longitude',
        'status',
        'rejection_reason',
        'reviewed_by_admin_id',
        'reviewed_at',
    ];
    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'reviewed_at' => 'datetime',
        ];
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function activityType()
    {
        return $this->belongsTo(ActivityType::class);
    }


    public function reviewedByAdmin()
    {
        return $this->belongsTo(Admin::class, 'reviewed_by_admin_id');
    }
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function outgoingServiceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'requester_business_account_id');
    }

    public function incomingServiceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'provider_business_account_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'reviewer_business_account_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'reporter_business_account_id');
    }

    public function initiatedConversations()
    {
        return $this->hasMany(Conversation::class, 'initiator_business_account_id');
    }

    public function receivedConversations()
    {
        return $this->hasMany(Conversation::class, 'recipient_business_account_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_business_account_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images');
        $this->addMediaCollection('documents');
    }
}
