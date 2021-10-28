<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

class LinkedSocialAccount extends Model
{
    use Uuid;

    protected $fillable = [
        'provider_name',
        'provider_id',
        'pelapor_uuid',
    ];
    public function pelapor()
    {
        return $this->belongsTo(Pelapor::class, 'pelapor_uuid', 'uuid');
    }
}
