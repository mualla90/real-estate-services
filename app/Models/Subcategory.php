<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Subcategory extends Model
{
    use HasFactory,HasTranslations;
    public array $translatable = [
        'name',
    ];
     protected $fillable = [
        'category_id',
        'name',
        'is_active',
        'sort_order',
    ];
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
      public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function dynamicFields()
    {
        return $this->hasMany(DynamicField::class);
    }
}
