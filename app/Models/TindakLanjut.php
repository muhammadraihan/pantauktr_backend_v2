<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class TindakLanjut extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'laporan_id', 'keterangan', 'status', 'updated_by'
    ];

    /**
     * Get the laporan that owns the TindakLanjut
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'laporan_id', 'uuid');
    }
}
