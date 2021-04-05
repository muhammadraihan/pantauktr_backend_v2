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

    public function userCreate()
    {
        return $this->belongsTo(User::class, 'created_by', 'uuid');
    }

    public function pelanggaran()
    {
        return $this->belongsTo(Pelanggaran::class, 'jenis_pelanggaran', 'uuid');
    }

    public function JenisLaporan()
    {
        return $this->belongsTo(Jenis_laporan::class, 'jenis_laporan', 'id');
    }

    public function japresiasi()
    {
        return $this->belongsTo(Jenis_apresiasi::class, 'jenis_apresiasi', 'uuid');
    }
}
