<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jenis_apresiasi;
use App\Models\Jenis_laporan;
use App\Models\Pelanggaran;

class ReferensiController extends Controller
{
    public function getJenisLaporan(Request $request)
    {
        $jenisLaporan = Jenis_laporan::select('id','uuid','name')->get();
        return response()->json([
            'success' => true,
            'data' => $jenisLaporan,
        ],200);
    }

    public function getJenisPelanggaran(Request $request)
    {
        $jenisPelanggaran = Pelanggaran::select('id','uuid','name')->get();
        return response()->json([
            'success' => true,
            'data' => $jenisPelanggaran,
        ],200);
    }

    public function getJenisApresiasi(Request $request)
    {
        $jenisApresiasi = Jenis_apresiasi::select('id','uuid','name')->get();
        return response()->json([
            'success' => true,
            'data' => $jenisApresiasi,
        ],200);
    }
}
