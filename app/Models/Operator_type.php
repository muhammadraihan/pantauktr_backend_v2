<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Operator_type extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'name'
    ];

    protected static $logAttributes = ['*'];

    protected static $logName = 'operatorType';

    protected static $logOnlyDirty = true;

    protected static $submitEmptyLogs = false;

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Data has been {$eventName}";
    }
}
