<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Favorite extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'business_account_id',
        'service_id',
        'note',
    ];

    public function businessAccount()
    {
        return $this->belongsTo(BusinessAccount::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}

