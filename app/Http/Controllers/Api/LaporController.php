<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Geocoder\Laravel\Facades\Geocoder;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\Laporan;
use App\Models\Pelapor;

use DB;
use Exception;
use Helper;
use Log;

class LaporController extends Controller
{
    public function lapor(Request $request)
    {
        // get pelapor data by token
        $pelapor = Helper::pelapor();
        // generate report number
        $uniqueCode = Helper::GenerateReportNumber(13);
        // get lat lng request data
        $lat = $request->get('lat');
        $lng = $request->get('lng');
        // reverse geocode
        $result = Geocoder::reverse($lat, $lng)->get();
        /**
         * This is the step of black magic 
         * to solving city name distinction in google geocode api.
         * 1. Filter result by admin level 2 name
         * 2. Get first filtered data (somehow the first data is the most accurate). why? ask google :)
         * 3. If filtered data null, get the original result if not pass the data
         * 4. Get admin levels collection to get property
         * 5. Done.
         */
        $map = $result->filter(function ($value, $key) {
            return str_contains($value->getAdminLevels()->get(2)->getName(), 'Kota');
        });
        // get first data
        $filter_result = $map->first();
        // check filtered data not null
        if ($filter_result != null) {
            // get admin levels data
            $admin_levels = $filter_result->getAdminLevels();
            $propinsi = $admin_levels->get(1)->getName();
            $kota = $admin_levels->get(2)->getName();
            $kecamatan = $admin_levels->get(3)->getName();
            $kelurahan = $admin_levels->get(4)->getName();
            $negara = $filter_result->getCountry()->getName();
        } else {
            $admin_levels = $result->first()->getAdminLevels();
            $propinsi = $admin_levels->get(1)->getName();
            $kota = $admin_levels->get(2)->getName();
            $kecamatan = $admin_levels->get(3)->getName();
            $kelurahan = $admin_levels->get(4)->getName();
            $negara = $result->first()->getCountry()->getName();
        }
        // begin transaction
        DB::beginTransaction();
        try {
            $laporan = new Laporan;
            $laporan->nomor_laporan = 'KTR' . '-' . $uniqueCode;
            $laporan->jenis_pelanggaran = $request->jenis_pelanggaran;
            $laporan->bentuk_pelanggaran = $request->bentuk_pelanggaran;
            $laporan->photo = $request->image_url;
            $laporan->kawasan = $request->kawasan;
            $laporan->lat = $lat;
            $laporan->lng = $lng;
            $laporan->nama_lokasi = $request->nama_lokasi;
            $laporan->detail_lokasi = $request->detail_lokasi;
            $laporan->alamat = $request->alamat;
            $laporan->kelurahan = $kelurahan;
            $laporan->kecamatan = $kecamatan;
            $laporan->kota = $kota;
            $laporan->propinsi = $propinsi;
            $laporan->negara = $negara;
            $laporan->place_id = $request->place_id;
            $laporan->created_by = $pelapor->uuid;
            $laporan->status = 0;
            $laporan->device_token = $request->device_token;
            $laporan->created_at = $request->report_date;
            $laporan->save();
            // update reward point
            $pelapor = Pelapor::uuid($pelapor->uuid);
            $pelapor->reward_point++;
            $pelapor->save();
        } catch (Exception $e) {
            // catch error and rollback data saving if fails
            DB::rollback();
            // log message to local an slack
            Log::stack(['stack', 'slack'])->error('Error post laporan', [
                'user' => $pelapor->email,
                'agent' => $request->header('User-Agent'),
                'origin' => env('APP_URL'),
                'error' => $e->getMessage(),
            ]);
            // catch error message
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        // if no error commit data saving
        DB::commit();
        // return response
        return response()->json([
            'success' => true,
            'message' => 'Laporan terkirim',
        ]);
    }

    public function listLaporan(Request $request)
    {
        // form response structure
        $response = array(
            'success' => true,
            'data' => array(),
        );
        try {
            // get pelapor details based on auth token
            $pelapor = Helper::pelapor();
            // select laporan based on user auth
            $list = Laporan::select('uuid', 'jenis_pelanggaran', 'bentuk_pelanggaran', 'kawasan', 'nama_lokasi', 'status', 'created_at')
                ->where('created_by', $pelapor->uuid)->orderByDesc('created_at')->get();
            // form response
            for ($i = 0; $i < count($list); $i++) {
                // parsing carbon to locale config
                $tanggalBuat = Carbon::parse($list[$i]->created_at)->translatedFormat('d/m/y H:i');
                // change status code to human readable for good sake
                $status = '';
                switch ($list[$i]->status) {
                    case 0:
                        $status = 'Laporan Diterima';
                        break;
                    case 1:
                        $status = 'Laporan Ditindaklanjuti';
                        break;
                    case 2:
                        $status = 'Laporan Selesai';
                        break;
                    default:
                        $status = 'Laporan Diterima';
                        break;
                }
                $response['data'][$i] = array();
                $response['data'][$i]['uuid'] = $list[$i]->uuid;
                $response['data'][$i]['jenis_pelanggaran'] = $list[$i]->jenis_pelanggaran == null ? "" : $list[$i]->pelanggaran->name;
                $response['data'][$i]['icon_pelanggaran'] =  $list[$i]->pelanggaran->image;
                $response['data'][$i]['bentuk_pelanggaran'] = $list[$i]->bentuk_pelanggaran == null ? "" : $list[$i]->BentukPelanggaran->bentuk_pelanggaran;
                $response['data'][$i]['kawasan'] = $list[$i]->kawasan == null ? "" : $list[$i]->Kawasan->kawasan;
                $response['data'][$i]['lokasi'] = $list[$i]->nama_lokasi;
                $response['data'][$i]['tanggal_laporan'] = $tanggalBuat;
                $response['data'][$i]['kode_status'] = $list[$i]->status;
                $response['data'][$i]['status'] = $status;
            }
        } catch (Exception $e) {
            // log message to local an slack
            Log::stack(['stack', 'slack'])->error('Error get list laporan', [
                'user' => $pelapor->email,
                'agent' => $request->header('User-Agent'),
                'origin' => env('APP_URL'),
                'error' => $e->getMessage(),
            ]);
            // catch error message
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        // return response
        return response()->json($response);
    }

    public function detailLaporan(Request $request, $id)
    {
        try {
            // get pelapor details based on auth token
            $pelapor = Helper::pelapor();
            $detailLaporan = Laporan::select('uuid', 'nomor_laporan', 'jenis_pelanggaran', 'bentuk_pelanggaran', 'kawasan', 'photo', 'nama_lokasi', 'detail_lokasi', 'status', 'created_at', 'updated_at')
                ->where('created_by', $pelapor->uuid)
                ->where('uuid', $id)
                ->first();
            // change status code to human readable for good sake
            $status = '';
            switch ($detailLaporan->status) {
                case 0:
                    $status = 'Laporan Diterima';
                    break;
                case 1:
                    $status = 'Laporan Ditindaklanjuti';
                    break;
                case 2:
                    $status = 'Laporan Selesai';
                    break;
                default:
                    $status = 'Laporan Diterima';
                    break;
            }
            // parsing carbon to locale config
            $tanggalBuat = Carbon::parse($detailLaporan->created_at)->translatedFormat('d/m/y H:i');
            $tanggalUbah = Carbon::parse($detailLaporan->updated_at)->translatedFormat('l\\, j F Y H:i:s');
        } catch (Exception $e) {
            // log message to local an slack
            Log::stack(['stack', 'slack'])->error('Error get detail laporan', [
                'user' => $pelapor->email,
                'agent' => $request->header('User-Agent'),
                'origin' => env('APP_URL'),
                'error' => $e->getMessage(),
            ]);
            // catch error message
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        // return response
        return response()->json([
            'success' => true,
            'detail' => [
                'uuid' => $detailLaporan->uuid,
                'nomor_laporan' => $detailLaporan->nomor_laporan,
                'jenis_pelanggaran' => $detailLaporan->jenis_pelanggaran == null ? "" : $detailLaporan->pelanggaran->name,
                'bentuk_pelanggaran' => $detailLaporan->bentuk_pelanggaran == null ? "" : $detailLaporan->BentukPelanggaran->bentuk_pelanggaran,
                'kawasan' => $detailLaporan->kawasan == null ? "" : $detailLaporan->Kawasan->kawasan,
                'lokasi' => $detailLaporan->nama_lokasi,
                'detail_lokasi' => $detailLaporan->detail_lokasi,
                'photo' => $detailLaporan->photo,
                'kode_status' => $detailLaporan->status,
                'status' => $status,
                'tanggal_laporan' => $tanggalBuat,
            ],
        ]);
    }
}
