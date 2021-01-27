<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Spatie\Geocoder\Geocoder;
use GuzzleHttp\Client;
use Carbon\Carbon;

use App\Models\Laporan;

use Auth;
use Config;
use DB;
use Exception;
use File;
use Hash;
use Helper;
use Image;
use Validator;

class LaporController extends Controller
{
    public function lapor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis_laporan' => 'required',
            'keterangan' => 'required',
            'photo' => 'required',
            'photo' => 'image|mimes:jpeg,png,jpg,gif,svg',
          ]);

        if($validator->fails()) {
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
        $response = $geocoder->getAddressForCoordinates($lat,$lng);
        $address = $response['address_components'];

        $folder = public_path().'/lampiran'.'/'.$pelapor->uuid.'/';
        if (!File::exists($folder)) {
        File::makeDirectory($folder, 0775, true, true);
        }
        // request image files and uploading to server
        $image = $request->file('photo');
        $filename = uniqid(mt_rand(),true).'.'.$image->getClientOriginalExtension();
        $resizeImage = Image::make($image);
        $resizeImage->resize(800,600, function($constraint){
        $constraint->aspectRatio();
        })->save($folder.$filename);

        // begin transaction
        DB::beginTransaction();
        try {
        $laporan = new Laporan;
        $laporan->jenis_laporan = $request->jenis_laporan;
        if(!empty($request->jenis_pelanggaran)){
            $laporan->jenis_pelanggaran = $request->jenis_pelanggaran;    
        }else{
            $laporan->jenis_apresiasi = $request->jenis_apresiasi;
        }
        $laporan->keterangan = $request->keterangan;
        $laporan->photo = $filename;
        $laporan->lat = $lat;
        $laporan->lng = $lng;
        $laporan->nama_lokasi = $request->nama_lokasi;
        $laporan->alamat = $response['formatted_address'];
        foreach ($address as $key => $value) {
            if ( array_search('administrative_area_level_4', $value->types) !== false ) {
            $laporan->kelurahan = $value->long_name;
            }
            if ( array_search('administrative_area_level_3', $value->types) !== false ) {
            $laporan->kecamatan = $value->long_name;
            }
            if ( array_search('administrative_area_level_2', $value->types) !== false ) {
            $laporan->kota = $value->long_name;
            }
            if ( array_search('administrative_area_level_1', $value->types) !== false ) {
            $laporan->propinsi = $value->long_name;
            }
            if ( array_search('country', $value->types) !== false ) {
            $laporan->negara = $value->long_name;
            }
        }
        $laporan->place_id = $request->place_id;
        $laporan->created_by = $pelapor->uuid;
        $laporan->save();
        } catch (Exception $e) {
        // catch error and rollback data saving if fails
        DB::rollback();
        // catch error message
        return response()->json([
            'success' => false,
            'messages' => $e->getMessage(),
        ],500);
        }
        // if no error commit data saving
        DB::commit();
        // return response
        return response()->json([
        'success' => true,
        'messages' => 'Laporan terkirim',
        ],200);

    }
}
