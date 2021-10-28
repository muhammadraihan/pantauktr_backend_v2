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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'city_id', 'place_id', 'operator_id', 'last_login_at', 'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attibutes for logging the event change
     *
     * @var array
     */
    protected static $logAttributes = ['name', 'email', 'password', 'avatar'];

    /**
     * Logging name
     *
     * @var string
     */
    protected static $logName = 'user';

    /**
     * Logging only the changed attributes
     *
     * @var boolean
     */
    protected static $logOnlyDirty = true;

    /**
     * Prevent save logs items that have no changed attribute
     *
     * @var boolean
     */
    protected static $submitEmptyLogs = false;

    /**
     * Custom logging description
     *
     * @param string $eventName
     * @return string
     */
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
