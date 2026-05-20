<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Admin extends Authenticatable
{
    use HasFactory,Notifiable,SoftDeletes,HasRoles;
    protected $guard_name = 'admin';

    
    protected $fillable=[
        'name',
        'email',
        'password',
        'is_active',
        'last_login_at',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
     protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }
     public function reviewedBusinessAccounts()
    {
        return $this->hasMany(BusinessAccount::class, 'reviewed_by_admin_id');
    }

    public function appNotifications()
    {
        return $this->morphMany(AppNotification::class, 'notifiable');
    }
}
