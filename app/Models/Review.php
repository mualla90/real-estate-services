<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'service_id',
        'reviewer_business_account_id',
        'service_request_id',
        'rating',
        'comment',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function reviewerBusinessAccount()
    {
        return $this->belongsTo(BusinessAccount::class, 'reviewer_business_account_id');
    }

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }
}

