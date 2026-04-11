<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class DynamicField extends Model
{
    use HasTranslations;

    public array $translatable = [
        'name',
    ];

    protected $fillable = [
        'admin_id',
        'category_id',
        'subcategory_id',
        'name',
        'field_key',
        'field_type',
        'is_required',
        'options',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'name' => 'array',
        'options' => 'array',
        'is_required' => 'boolean',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function serviceValues()
    {
        return $this->hasMany(ServiceDynamicFieldValue::class);
    }
}

