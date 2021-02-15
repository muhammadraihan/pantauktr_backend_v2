<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Traits\Uuid;

class Pelapor extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use Uuid;
    
    protected $fillable = [
        'firstname', 'lastname', 'email', 'password', 'provider', 'avatar', 'reward_point', 'last_login_ip', 'last_login_at'
    ];

    protected static $logAttributes = ['*'];

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

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
