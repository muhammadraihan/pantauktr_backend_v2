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

use Exception;
use Helper;
use Log;

class ReferensiController extends Controller
{
    /**
     * Method for v2 api routes
     *
     * @return json
     */
    public function OldAPI()
    {
        return response()->json([
            'success' => false,
            'message' => 'Harap update aplikasi untuk dapat melanjutkan penggunaan aplikasi',
        ]);
    }

    public function getJenisLaporan(Request $request)
    {
        $pelapor = Helper::pelapor();
        try {
            $jenisLaporan = Jenis_laporan::select('id', 'uuid', 'name')->get();
        } catch (Exception $e) {
            // log message to local an slack
            Log::stack(['stack', 'slack'])->error('Error get jenis laporan', [
                'user' => $pelapor->email,
                'agent' => $request->header('User-Agent'),
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $jenisLaporan,
        ], 200);
    }

    public function getJenisPelanggaran(Request $request)
    {
        $pelapor = Helper::pelapor();
        try {
            $jenisPelanggaran = Pelanggaran::select('id', 'uuid', 'name', 'keterangan')->get();
        } catch (Exception $e) {
            // log message to local an slack
            Log::stack(['stack', 'slack'])->error('Error get jenis pelanggaran', [
                'user' => $pelapor->email,
                'agent' => $request->header('User-Agent'),
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $jenisPelanggaran,
        ], 200);
    }

    public function getJenisApresiasi(Request $request)
    {
        $pelapor = Helper::pelapor();
        try {
            $jenisApresiasi = Jenis_apresiasi::select('id', 'uuid', 'name')->get();
        } catch (Exception $e) {
            // log message to local an slack
            Log::stack(['stack', 'slack'])->error('Error get jenis apresiasi', [
                'user' => $pelapor->email,
                'agent' => $request->header('User-Agent'),
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $jenisApresiasi,
        ], 200);
    }

    public function getBentukPelanggaran(Request $request)
    {
        $pelapor = Helper::pelapor();
        try {
            $bentuk_pelanggaran = BentukPelanggaran::select('id', 'uuid', 'bentuk_pelanggaran', 'keterangan')->get();
        } catch (Exception $e) {
            // log message to local an slack
            Log::stack(['stack', 'slack'])->error('Error get bentuk pelanggaran', [
                'user' => $pelapor->email,
                'agent' => $request->header('User-Agent'),
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $bentuk_pelanggaran,
        ], 200);
    }

    public function getKawasan(Request $request)
    {
        $pelapor = Helper::pelapor();
        try {
            $kawasan = Kawasan::select('id', 'uuid', 'kawasan', 'keterangan')->get();
        } catch (Exception $e) {
            // log message to local an slack
            Log::stack(['stack', 'slack'])->error('Error get kawasan', [
                'user' => $pelapor->email,
                'agent' => $request->header('User-Agent'),
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $kawasan,
        ], 200);
    }
}
