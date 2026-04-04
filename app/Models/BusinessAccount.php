<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class BusinessAccount extends Model
{
    use HasFactory,HasTranslations,SoftDeletes;
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
}
