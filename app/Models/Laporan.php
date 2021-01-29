<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Laporan extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'jenis_pelanggaran', 'keterangan', 'photo', 'lat', 'lng', 'nama_lokasi', 'alamat', 'kelurahan', 'kecamatan', 'kota', 'propinsi', 'negara', 'place_id', 'jenis_laporan', 'jenis_apresiasi', 'created_by'
    ];

    public function userCreate(){
        return $this->belongsTo(Pelapor::class, 'created_by','uuid');
    }

    public function pelanggaran(){
        return $this->belongsTo(Pelanggaran::class, 'jenis_pelanggaran', 'uuid');
    }

    public function jenis_laporan(){
        return $this->belongsTo(Jenis_laporan::class, 'jenis_laporan', 'uuid');
    }

    public function jenis_apresiasi(){
        return $this->belomgsTo(Jenis_apresiasi::class, 'jenis_apresiasi', 'uuid');
    }

    public function kota(){
        return $this->belongsTo(Kota::class, 'kota', 'uuid');
    }
}
