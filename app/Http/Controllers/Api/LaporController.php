<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Geocoder\Geocoder;
use GuzzleHttp\Client;
use Carbon\Carbon;

use App\Models\Laporan;
use App\Models\Pelapor;

use Auth;
use Config;
use DB;
use Exception;
use File;
use Hash;
use Helper;
use Image;
use Validator;
use Storage;

class LaporController extends Controller
{
    public function __construct()
    {
        Config::set('jwt.user', Pelapor::class);
        Config::set('auth.providers', [
            'users' => [
                'driver' => 'eloquent',
                'model' => Pelapor::class,
            ]
        ]);
    }

    public function lapor(Request $request)
    {
        $pelapor = Helper::pelapor();
        $uniqueCode = Helper::GenerateReportNumber(13);
        $validator = Validator::make($request->all(), [
            'jenis_laporan' => 'required',
            'keterangan' => 'required',
            'photo' => 'required',
            'photo' => 'image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ]);
        }

        $lat = $request->get('lat');
        $lng = $request->get('lng');
        // reverse geocode
        $client = new Client();
        $geocoder = new Geocoder($client);
        $geocoder->setApiKey(config('geocoder.key'));
        $response = $geocoder->getAddressForCoordinates($lat, $lng);
        $address = $response['address_components'];

        $folder = public_path() . '/lampiran' . '/';
        if (!File::exists($folder)) {
            File::makeDirectory($folder, 0775, true, true);
        }
        // request image files and uploading to google cloud storage
        $image = $request->file('photo');
        $filename = $pelapor->uuid . uniqid(mt_rand(), true) . '.' . $image->getClientOriginalExtension();
        $resizeImage = Image::make($image);
        $resizeImage->resize(800, 600, function ($constraint) {
            $constraint->aspectRatio();
        })->encode();

        $googleContent = 'lampiran' . '/' . $filename;
        $disk = Storage::disk('gcs');
        $disk->put($googleContent, (string) $resizeImage);
        $fileUrl = $disk->url(env('GOOGLE_CLOUD_STORAGE_BUCKET') . '/' . $googleContent);

        // begin transaction
        DB::beginTransaction();
        try {
            $laporan = new Laporan;
            $laporan->nomor_laporan = 'KTR' . '-' . $uniqueCode;
            $laporan->jenis_laporan = $request->jenis_laporan;
            if (!empty($request->jenis_pelanggaran)) {
                $laporan->jenis_pelanggaran = $request->jenis_pelanggaran;
            } else {
                $laporan->jenis_apresiasi = $request->jenis_apresiasi;
            }
            $laporan->keterangan = $request->keterangan;
            $laporan->photo = $fileUrl;
            $laporan->lat = $lat;
            $laporan->lng = $lng;
            $laporan->nama_lokasi = $request->nama_lokasi;
            $laporan->detail_lokasi = $request->detail_lokasi;
            $laporan->alamat = $request->alamat;
            foreach ($address as $key => $value) {
                if (array_search('administrative_area_level_4', $value->types) !== false) {
                    $laporan->kelurahan = $value->long_name;
                }
                if (array_search('administrative_area_level_3', $value->types) !== false) {
                    $laporan->kecamatan = $value->long_name;
                }
                if (array_search('administrative_area_level_2', $value->types) !== false) {
                    $laporan->kota = $value->long_name;
                }
                if (array_search('administrative_area_level_1', $value->types) !== false) {
                    $laporan->propinsi = $value->long_name;
                }
                if (array_search('country', $value->types) !== false) {
                    $laporan->negara = $value->long_name;
                }
            }
            $laporan->place_id = $request->place_id;
            $laporan->created_by = $pelapor->uuid;
            $laporan->status = 0;
            $laporan->device_token = $request->device_token;
            $laporan->save();
            // update reward point
            $pelapor = Pelapor::uuid($pelapor->uuid);
            $pelapor->reward_point++;
            $pelapor->save();
        } catch (Exception $e) {
            // catch error and rollback data saving if fails
            DB::rollback();
            // catch error message
            return response()->json([
                'success' => false,
                'messages' => $e->getMessage(),
            ], 500);
        }
        // if no error commit data saving
        DB::commit();
        // return response
        return response()->json([
            'success' => true,
            'messages' => 'Laporan terkirim',
        ], 200);
    }

    public function listLaporan()
    {
        // form response structure
        $response = array(
            'success' => true,
            'data' => array(),
        );
        // get pelapor details based on auth token
        $pelapor = Helper::pelapor();
        // select laporan based on user auth
        $list = Laporan::select('uuid', 'nomor_laporan', 'jenis_pelanggaran', 'jenis_laporan', 'keterangan', 'photo', 'nama_lokasi', 'created_at')
            ->where('created_by', $pelapor->uuid)->get();
        // form response
        for ($i = 0; $i < count($list); $i++) {
            $tanggal = Carbon::parse($list[$i]->created_at)->format('l\\, j F Y H:i:s');
            $response['data'][$i] = array();
            $response['data'][$i]['uuid'] = $list[$i]->uuid;
            $response['data'][$i]['nomor_laporan'] = $list[$i]->nomor_laporan;
            $response['data'][$i]['jenis_laporan'] = $list[$i]->JenisLaporan->name;
            $response['data'][$i]['jenis_pelanggaran'] = $list[$i]->pelanggaran->name;
            $response['data'][$i]['nama_lokasi'] = $list[$i]->nama_lokasi;
            $response['data'][$i]['tanggal'] = $tanggal;
        }
        // return response
        return response()->json($response, 200);
    }

    public function detailLaporan($id)
    {
        $response = array(
            'success' => true,
            'data' => array(),
        );
        // get pelapor details based on auth token
        $pelapor = Helper::pelapor();
        $list = Laporan::select('jenis_pelanggaran', 'jenis_laporan', 'keterangan', 'photo', 'nama_lokasi', 'created_at')
            ->where('created_by', $pelapor->uuid)
            ->where('uuid', $id)
            ->get();

        for ($i = 0; $i < count($list); $i++) {
            $tanggal = Carbon::parse($list[$i]->created_at)->format('l\\, j F Y H:i:s');
            $response['data'][$i] = array();
            $response['data'][$i]['jenis_laporan'] = $list[$i]->JenisLaporan->name;
            $response['data'][$i]['jenis_pelanggaran'] = $list[$i]->pelanggaran->name;
            $response['data'][$i]['keterangan'] = $list[$i]->keterangan;
            $response['data'][$i]['photo'] = url('/') . '/lampiran' . '/' . $pelapor->uuid . '/' . $list[$i]->photo;
            $response['data'][$i]['nama_lokasi'] = $list[$i]->nama_lokasi;
            $response['data'][$i]['tanggal'] = $tanggal;
        }
        // return response
        return response()->json($response, 200);
    }
}
