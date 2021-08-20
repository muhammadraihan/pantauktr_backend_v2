<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jenis_apresiasi;
use App\Models\Jenis_laporan;
use App\Models\Pelanggaran;
use App\Models\BentukPelanggaran;
use App\Models\BentukApresiasi;
use App\Models\Kawasan;

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

    public function getBentukPelanggaran(Request $request){
        $bentuk_pelanggaran = BentukPelanggaran::select('id','uuid','bentuk_pelanggaran')->get();
        return response()->json([
            'succes'=>true,
            'data' => $bentuk_pelanggaran,
        ],200);
    }

    public function getBentukApresiasi(Request $request){
        $bentuk_apresiasi = BentukApresiasi::select('id','uuid','bentuk_apresiasi')->get();
        return response()->json([
            'succes'=>true,
            'data' => $bentuk_apresiasi,
        ],200);
    }

    public function getKawasan(Request $request){
        $kawasan = Kawasan::select('id','uuid','kawasan')->get();
        return response()->json([
            'succes'=>true,
            'data' => $kawasan,
        ],200);
    }
}
