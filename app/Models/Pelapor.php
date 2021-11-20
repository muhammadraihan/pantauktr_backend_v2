<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Notifications\Notifiable;
use App\Traits\Uuid;
use Arr;
use Laravel\Passport\HasApiTokens;

class Pelapor extends Authenticatable implements CanResetPassword
{
    use HasFactory;
    use Notifiable;
    use Uuid;
    use LogsActivity;
    use HasApiTokens;

    protected $fillable = [
        'firstname', 'lastname', 'email', 'password', 'provider', 'avatar', 'reward_point', 'last_login_ip', 'last_login_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'id',
    ];

    /**
     * The attibutes for logging the event change
     *
     * @var array
     */
    protected static $logAttributes = ['firstname', 'lastname', 'name', 'email', 'password', 'avatar'];

    /**
     * Logging name
     *
     * @var string
     */
    protected static $logName = 'pelapor';

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

    public function LinkedSocialAccounts()
    {
        return $this->hasMany(LinkedSocialAccount::class, 'pelapor_uuid', 'uuid');
    }

    public function DeviceToken()
    {
        return $this->hasMany(FcmRegistrationToken::class, 'pelapor_id', 'uuid');
    }

    /**
     * Specifies the user's FCM tokens
     *
     * @return string|array
     */
    public function routeNotificationForFcm()
    {
        $device_tokens = $this->DeviceToken->where('revoked', 0)
            ->pluck('token')
            ->toArray();
        return $device_tokens;
    }
}
