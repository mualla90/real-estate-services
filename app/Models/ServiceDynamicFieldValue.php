<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceDynamicFieldValue extends Model
{
    protected $fillable = [
        'service_id',
        'dynamic_field_id',
        'field_type',
        'value_text',
        'value_number',
        'value_json',
    ];

    protected $casts = [
        'value_number' => 'decimal:2',
        'value_json' => 'array',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function dynamicField()
    {
        return $this->belongsTo(DynamicField::class);
    }
}

