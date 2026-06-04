<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Service extends Model implements HasMedia
{
    use SoftDeletes, HasTranslations, InteractsWithMedia;

    public array $translatable = [
        'title',
        'description',
    ];

    protected $fillable = [
        'business_account_id',
        'category_id',
        'subcategory_id',
        'city_id',
        'title',
        'description',
        'service_type',
        'price',
        'currency',
        'price_usd',
        'price_syp',
        'address',
        'latitude',
        'longitude',
        'status',
        'rejection_reason',
        'reviewed_by_admin_id',
        'reviewed_at',
        'published_at',
        'average_rating',
        'review_count',
        'views_count',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'price' => 'decimal:2',
        'price_usd' => 'decimal:2',
        'price_syp' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'reviewed_at' => 'datetime',
        'published_at' => 'datetime',
        'average_rating' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function businessAccount()
    {
        return $this->belongsTo(BusinessAccount::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function reviewedByAdmin()
    {
        return $this->belongsTo(Admin::class, 'reviewed_by_admin_id');
    }

    public function dynamicFieldValues()
    {
        return $this->hasMany(ServiceDynamicFieldValue::class);
    }

    public function requests()
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'rejected');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at');
    }

    // public function scopeOfBusinessAccount(Builder $query, int $businessAccountId): Builder
    // {
    //     return $query->where('business_account_id', $businessAccountId);
    // }

    // public function scopeOfCategory(Builder $query, int $categoryId): Builder
    // {
    //     return $query->where('category_id', $categoryId);
    // }

    // public function scopeOfSubcategory(Builder $query, int $subcategoryId): Builder
    // {
    //     return $query->where('subcategory_id', $subcategoryId);
    // }

    // public function scopeOfCity(Builder $query, int $cityId): Builder
    // {
    //     return $query->where('city_id', $cityId);
    // }

    // public function scopeOfType(Builder $query, string $serviceType): Builder
    // {
    //     return $query->where('service_type', $serviceType);
    // }

    // public function scopeVisible(Builder $query): Builder
    // {
    //     return $query
    //         ->where('status', 'approved')
    //         ->where('is_active', true)
    //         ->whereNotNull('published_at');
    // }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('sort_order')
            ->latest('id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isVisible(): bool
    {
        return $this->isApproved()
            && $this->is_active
            && ! is_null($this->published_at);
    }

    public function getPreferredPrice(?string $currency = 'USD'): ?string
    {
        return strtoupper((string) $currency) === 'SYP'
            ? $this->price_syp
            : $this->price_usd;
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('main_image')
            ->useDisk('public')
            ->singleFile();

        $this->addMediaCollection('gallery')->useDisk('public');
    }
}
