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
        $pelapor = Helper::pelapor();
        $uniqueCode = Helper::GenerateReportNumber(13);
        $lat = $request->get('lat');
        $lng = $request->get('lng');
        $result = Geocoder::reverse($lat, $lng)->get();
        $map = $result->filter(function ($value, $key) {
            return str_contains($value->getAdminLevels()->get(2)->getName(), 'Kota');
        });
        $filter_result = $map->first();
        if ($filter_result != null) {
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
            $pelapor = Pelapor::uuid($pelapor->uuid);
            $pelapor->reward_point++;
            $pelapor->save();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'Laporan terkirim',
        ]);
    }

    public function listLaporan(Request $request)
    {
        $response = array(
            'success' => true,
            'data' => array(),
        );
        try {
            $pelapor = Helper::pelapor();
            $list = Laporan::select('uuid', 'jenis_pelanggaran', 'bentuk_pelanggaran', 'kawasan', 'nama_lokasi', 'status', 'created_at')
                ->where('created_by', $pelapor->uuid)->orderByDesc('created_at')->get();
            for ($i = 0; $i < count($list); $i++) {
                $tanggalBuat = Carbon::parse($list[$i]->created_at)->translatedFormat('d/m/y H:i');
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
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        return response()->json($response);
    }

    public function detailLaporan(Request $request, $id)
    {
        try {
            $pelapor = Helper::pelapor();
            $detailLaporan = Laporan::select('uuid', 'nomor_laporan', 'jenis_pelanggaran', 'bentuk_pelanggaran', 'kawasan', 'photo', 'nama_lokasi', 'detail_lokasi', 'status', 'created_at', 'updated_at')
                ->where('created_by', $pelapor->uuid)
                ->where('uuid', $id)
                ->first();
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
            $tanggalBuat = Carbon::parse($detailLaporan->created_at)->translatedFormat('d/m/y H:i');
            $tanggalUbah = Carbon::parse($detailLaporan->updated_at)->translatedFormat('l\\, j F Y H:i:s');
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
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
