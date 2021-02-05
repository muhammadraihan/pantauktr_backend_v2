<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Kota extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'city_name', 'city_code','province_code'
    ];
}
