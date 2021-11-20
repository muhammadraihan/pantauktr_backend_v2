<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Kawasan extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'kawasan', 'keterangan', 'image', 'created_by', 'edited_by'
    ];

    protected static $logAttributes = ['*'];

    protected static $logName = 'Kawasan';

    protected static $logOnlyDirty = true;

    protected static $submitEmptyLogs = false;

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Data has been {$eventName}";
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'created_by', 'uuid');
    }
}
