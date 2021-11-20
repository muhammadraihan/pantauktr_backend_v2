<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class FcmRegistrationToken extends Model
{
    use HasFactory;
    use Uuid;
    use LogsActivity;

    protected $fillable = [
        'pelapor_id', 'token', 'revoked',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * The attibutes for logging the event change
     *
     * @var array
     */
    protected static $logAttributes = ['token', 'revoked'];

    /**
     * Logging name
     *
     * @var string
     */
    protected static $logName = 'fcm-token';

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

    /**
     * Get the pelapor that owns the FcmRegistrationToken
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pelapor()
    {
        return $this->belongsTo(Pelapor::class, 'pelapor_id', 'uuid');
    }
}
