<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Pelanggaran;
use App\Models\BentukPelanggaran;
use App\Models\Kawasan;

use Exception;
use Helper;
use Log;

class ReferensiController extends Controller
{
    public function OldAPI()
    {
        return response()->json([
            'success' => false,
            'message' => 'Harap update aplikasi untuk dapat melanjutkan penggunaan aplikasi',
        ]);
    }


    public function getJenisPelanggaran(Request $request)
    {
        $pelapor = Helper::pelapor();
        try {
            $jenisPelanggaran = Pelanggaran::select('uuid', 'name', 'keterangan', 'image')->get();
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $jenisPelanggaran,
        ]);
    }

    public function getSingleJenisPelanggaran(Request $request, $uuid)
    {
        $pelapor = Helper::pelapor();
        try {
            $jenisPelanggaran = Pelanggaran::select('uuid', 'name', 'keterangan', 'image')->where('uuid', $uuid)->first();
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $jenisPelanggaran,
        ]);
    }

    public function getBentukPelanggaran(Request $request)
    {
        $pelapor = Helper::pelapor();
        try {
            $bentuk_pelanggaran = BentukPelanggaran::select('uuid', 'bentuk_pelanggaran', 'keterangan', 'image')->get();
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $bentuk_pelanggaran,
        ]);
    }

    public function getBentukByPelanggaran(Request $request, $uuid)
    {
        $pelapor = Helper::pelapor();
        try {
            $bentuk_pelanggaran = BentukPelanggaran::select('uuid', 'bentuk_pelanggaran', 'keterangan', 'image')->where('jenis_pelanggaran', $uuid)->get();
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $bentuk_pelanggaran,
        ]);
    }

    public function getSingleBentukPelanggaran(Request $request, $uuid)
    {
        $pelapor = Helper::pelapor();
        try {
            $bentuk = BentukPelanggaran::select('uuid', 'bentuk_pelanggaran', 'keterangan', 'image')->where('uuid', $uuid)->first();
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $bentuk,
        ]);
    }

    public function getKawasan(Request $request)
    {
        $pelapor = Helper::pelapor();
        try {
            $kawasan = Kawasan::select('uuid', 'kawasan', 'keterangan', 'image')->get();
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $kawasan,
        ]);
    }

    public function getSingleKawasan(Request $request, $uuid)
    {
        $pelapor = Helper::pelapor();
        try {
            $kawasan = Kawasan::select('uuid', 'kawasan', 'keterangan', 'image')->where('uuid', $uuid)->first();
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $kawasan,
        ]);
    }
}
