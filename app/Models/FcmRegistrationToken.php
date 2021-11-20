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

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    protected static $logAttributes = ['token', 'revoked'];

    protected static $logName = 'fcm-token';

    protected static $logOnlyDirty = true;

    protected static $submitEmptyLogs = false;

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Data has been {$eventName}";
    }

    public function pelapor()
    {
        return $this->belongsTo(Pelapor::class, 'pelapor_id', 'uuid');
    }
}
