<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use LaravelLang\Models\HasTranslations;

class City extends Model
{
    use HasFactory,HasTranslations;
    public array $translatable = [
        'name',
    ];
    protected $fillable=[
        'name',
        'is_active',
        'sort_order'
    ];
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
    public function businessAccounts()
    {
        return $this->hasMany(BusinessAccount::class);
    }

}
