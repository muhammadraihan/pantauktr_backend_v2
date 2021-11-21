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

    protected $hidden = [
        'password', 'id',
    ];

    protected static $logAttributes = ['firstname', 'lastname', 'name', 'email', 'password', 'avatar'];

    protected static $logName = 'pelapor';

    protected static $logOnlyDirty = true;

    protected static $submitEmptyLogs = false;

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

    public function routeNotificationForFcm()
    {
        $device_tokens = $this->DeviceToken->where('revoked', 0)
            ->pluck('token')
            ->toArray();
        return $device_tokens;
    }
}
