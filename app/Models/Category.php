<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use LaravelLang\Models\HasTranslations;

class Category extends Model
{
    use HasFactory,HasTranslations;
    public array $translatable = [
        'name',
        'description'
    ];
    protected $fillable = [
        'name',
        'description',
        'is_active',
        'sort_order',
    ];
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }
}
