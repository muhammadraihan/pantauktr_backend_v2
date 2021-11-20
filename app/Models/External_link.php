<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class External_link extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'title', 'description', 'link'
    ];

    protected static $logAttributes = ['*'];

    protected static $logName = 'externalLink';

    protected static $logOnlyDirty = true;

    protected static $submitEmptyLogs = false;
    
    public function getDescriptionForEvent(string $eventName): string
    {
        return "Data has been {$eventName}";
    }
}
