<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Contracts\Auth\CanResetPassword;

use App\Traits\Uuid;

class User extends Authenticatable implements CanResetPassword
{
    use HasRoles;
    use Notifiable;
    use Uuid;
    use LogsActivity;

    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'city_id', 'place_id', 'operator_id', 'last_login_at', 'last_login_ip',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected static $logAttributes = ['name', 'email', 'password', 'avatar'];

    protected static $logName = 'user';

    protected static $logOnlyDirty = true;

    protected static $submitEmptyLogs = false;

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Data has been {$eventName}";
    }

    public function city()
    {
        return $this->belongsTo(Kota::class, 'city_id', 'uuid');
    }

    public function operator()
    {
        return $this->belongsTo(Operator_type::class, 'operator_id', 'uuid');
    }
}
