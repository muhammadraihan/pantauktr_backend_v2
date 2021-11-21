<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;

class Role extends \Spatie\Permission\Models\Role
{
    use LogsActivity;

    protected $fillable = [
        'name', 'guard_name',
    ];

    protected static $logAttributes = ['name', 'guard_name'];

    protected static $logName = 'role';

    protected static $logOnlyDirty = true;

    protected static $submitEmptyLogs = false;

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Data has been {$eventName}";
    }
}
