<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Laporan;
use App\Models\Pelapor;
use App\Models\Pelanggaran;
use App\Models\Province;
use App\Models\Jenis_apresiasi;
use App\Models\Jenis_laporan;
use App\Models\TindakLanjut;
use App\Models\Operator_type;
use App\Models\User;
use App\Models\Kota;
use App\Libraries\Firebase;
use App\Libraries\Push;
use Carbon\Carbon;

use Auth;
use DataTables;
use DB;
use File;
use Hash;
use Image;
use Response;
use URL;
use PDF;
use Helper;


class LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, User $uuid)
    {
        $users = Auth::user($uuid);
        $kota = [];
        $year = DB::table('laporans')
                    ->select( DB::raw("DATE_FORMAT(created_at, '%Y') tahun"))
                    ->groupBy('tahun')
                    ->get();
        $month = DB::table('laporans')
                    ->select( DB::raw("DATE_FORMAT(created_at, '%m') bulan"))
                    ->groupBy('bulan')
                    ->get();

        if (request()->ajax()) {
          DB::statement(DB::raw('set @rownum=0'));
          if ($request->user()->hasRole('operator')){
            $userss = Laporan::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),
            'id','uuid','jenis_pelanggaran', 'jenis_laporan', 'jenis_apresiasi', 'keterangan','photo', 'lat', 'lng', 'nama_lokasi', 'alamat', 'kelurahan', 'kecamatan', 'kota', 'propinsi', 'negara', 'place_id', 'created_by','created_at'])
            ->where('kota', 'like', $users->city->city_name)
            ->whereYear('created_at', (int)$request['tahun'])
            ->whereMonth('created_at', (int)$request['bulan'])
            ->get();
          }else{
            $userss = Laporan::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),
            'id','uuid','jenis_pelanggaran','jenis_laporan', 'jenis_apresiasi', 'keterangan','photo', 'lat', 'lng', 'nama_lokasi', 'alamat', 'kelurahan', 'kecamatan', 'kota', 'propinsi', 'negara', 'place_id', 'created_by','created_at'])
            ->whereYear('created_at', $request['tahun'])
            ->whereMonth('created_at', $request['bulan'])
            ->get();
          }
            return Datatables::of($userss) 
                    ->addIndexColumn()
                    ->editColumn('created_by',function($row){
                        return $row->userCreate->name ?? null;
                    })
                    ->editColumn('jenis_pelanggaran',function($row){
                        return $row->pelanggaran->name ?? null;
                        
                    })
                    ->editColumn('jenis_apresiasi',function($row){
                        return $row->japresiasi->name ?? null;
                    })
                    ->editColumn('jenis_laporan',function($row){
                        return $row->jenislaporan->name ;
                    })
                    ->editColumn('photo', function($row){
                        $url = asset('publiclampiran');
                        return '<img style="width: 150px; height: 150px;"  src="'.$url.'/'.$row->created_by.'/'.$row->photo.'" alt="">';
                    })
                    ->editColumn('created_at',function($row){
                        return Carbon::parse($row->created_at)->format('l\\, j F Y H:i:s');
                    })
                    ->addColumn('action', function($row){
                        return '
                        <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="'.route('tindaklanjut.index',$row->uuid).'"><i class="fal fa-edit"></i></a>';
                 })
            ->removeColumn('id')
            ->removeColumn('uuid')
            ->rawColumns(['photo','action'])
            ->make(true);
        }

        if(!empty($this->province)){
            $this->kota = Kota::where('province_code', $this->province)->get();
        }

        return view('laporan.index', compact('users','kota','year','month'));
    }

    public function bulans(Request $request, User $uuid){
        $users = Auth::user($uuid);

        if ($request->user()->hasRole('operator')){
            $userss = Laporan::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),
            'id','uuid','jenis_pelanggaran', 'jenis_laporan', 'jenis_apresiasi', 'keterangan','photo', 'lat', 'lng', 'nama_lokasi', 'alamat', 'kelurahan', 'kecamatan', 'kota', 'propinsi', 'negara', 'place_id', 'created_by','created_at'])
                ->where('kota', 'like', $users->city->city_name)
                ->get();
          }else{
            $userss = Laporan::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),
            'id','uuid','jenis_pelanggaran','jenis_laporan', 'jenis_apresiasi', 'keterangan','photo', 'lat', 'lng', 'nama_lokasi', 'alamat', 'kelurahan', 'kecamatan', 'kota', 'propinsi', 'negara', 'place_id', 'created_by','created_at'])->get();
          }
        return response()->json($userss);
    }

    public function cetakpelanggaran(Request $request, User $uuid){

        $users = Auth::user($uuid);
        if ($request->user()->hasRole('operator')){
            $cetak = DB::table('laporans')
                ->join('pelanggarans','pelanggarans.uuid','=','laporans.jenis_pelanggaran')
                ->join('jenis_laporans', 'jenis_laporans.uuid', '=', 'laporans.jenis_laporan')
                ->select('laporans.jenis_pelanggaran','pelanggarans.name as nama_pelanggaran', 'laporans.jenis_laporan', 'jenis_laporans.name as nama_laporan','laporans.keterangan', 'laporans.nama_lokasi', 'laporans.alamat', 'laporans.kota',
                'laporans.propinsi', 'laporans.negara', 'laporans.place_id')
                ->where('laporans.kota','like',$users->city->city_name)
                ->get();
        }else{
            $cetak = DB::table('laporans')
                ->join('pelanggarans','pelanggarans.uuid','=','laporans.jenis_pelanggaran')
                ->join('jenis_laporans', 'jenis_laporans.uuid', '=', 'laporans.jenis_laporan')
                ->select('laporans.jenis_pelanggaran','pelanggarans.name as nama_pelanggaran', 'laporans.jenis_laporan', 'jenis_laporans.name as nama_laporan','laporans.keterangan', 'laporans.nama_lokasi', 'laporans.alamat', 'laporans.kota',
                'laporans.propinsi', 'laporans.negara', 'laporans.place_id')
                ->get();
        }
        $pdf = PDF::loadview('laporan.laporan_pelanggaran_pdf', compact('cetak'))->setPaper('a4', 'landscape');
        return $pdf->download('laporan-jenis-pelanggaran.pdf', compact('cetak'));
    }

    public function cetakapresiasi(Request $request, User $uuid){
        $users = Auth::user($uuid);
        if ($request->user()->hasRole('operator')){
            $cetak = DB::table('laporans')
                ->join('jenis_apresiasis','jenis_apresiasis.uuid','=','laporans.jenis_apresiasi')
                ->join('jenis_laporans', 'jenis_laporans.uuid', '=', 'laporans.jenis_laporan')
                ->select('laporans.jenis_apresiasi','jenis_apresiasis.name as nama_apresiasi', 'laporans.jenis_laporan', 'jenis_laporans.name as nama_laporan','laporans.keterangan', 'laporans.nama_lokasi', 'laporans.alamat', 'laporans.kota',
                'laporans.propinsi', 'laporans.negara', 'laporans.place_id')
                ->where('laporans.kota','like',$users->city->city_name)
                ->get();
        }else{
            $cetak = DB::table('laporans')
                ->join('jenis_apresiasis','jenis_apresiasis.uuid','=','laporans.jenis_apresiasi')
                ->join('jenis_laporans', 'jenis_laporans.uuid', '=', 'laporans.jenis_laporan')
                ->select('laporans.jenis_apresiasi','jenis_apresiasis.name as nama_apresiasi', 'laporans.jenis_laporan', 'jenis_laporans.name as nama_laporan','laporans.keterangan', 'laporans.nama_lokasi', 'laporans.alamat', 'laporans.kota',
                'laporans.propinsi', 'laporans.negara', 'laporans.place_id')
                ->get();
        }
        $pdf = PDF::loadview('laporan.laporan_apresiasi_pdf', compact('cetak'))->setPaper('a4', 'landscape');
        return $pdf->download('laporan-jenis-apresiasi.pdf', compact('cetak'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function tindaklanjut($id)
    {
        $idlaporan = Laporan::uuid($id);
        // dd($idlaporan);
        return view('laporan.tindak_lanjut',compact('idlaporan'));
    }

    public function storetindaklanjut(Request $request)
    {
        $rules = [
            'keterangan' => 'required|min:2',
            'status' => 'required'
        ];

        $messages = [
            '*.required' => 'Field tidak boleh kosong !',
        ];

        $this->validate($request, $rules, $messages);
        $laporan = Laporan::uuid($request->laporan_id);
        $laporan->status = $request->status;
        $laporan->save();
          // Saving data
        $tindaklanjut = new TindakLanjut();
        $tindaklanjut->laporan_id = $request->laporan_id;
        $tindaklanjut->keterangan = $request->keterangan;
        $tindaklanjut->status = $request->status;
        $tindaklanjut->updated_by = Auth::user()->uuid;
    
        $tindaklanjut->save();
        $this->sendNotifToAndroid($tindaklanjut);

        toastr()->success('Tindak Lanjut Updated','Success');
        return redirect()->route('laporan.index');
    }

    public function sendNotifToAndroid($messages)
    {
        //token from env
        $token = "AAAA7vtgV4o:APA91bGc7FLESkwOnW1Mne4tcyZwENKSyQoirOny555Np4TU-F8wpr99KGughY2UNV-INUyspE-g2M9iRwZ1g-82m6oCLEpbU5fEtW80IuqpFIH2W11oLWDjt3fnZP_Xyyt5f6vCW8jS";  
        //token from device
        // $token = Laporan::uuid($messages->laporan_id)->pluck('token_device');
        $from = "d0IckHe9o1l3aPayOh553P:APA91bGyzUPnsqCjQ6xDTBW_ZUG1TQ-JL3wu053nmgWlT3le1vEen_s6Ty8R9kiUd2M2Vr8RwNLgCAcu3G8-xbrVG5VxQoMosxPdAogSaIPZ7k0xX-fnDgjTrnIsFJcnKIf5_qJ5TGWR";
        
        $msg = collect(array
              (
                'body'  => $messages->status,
                'title' => "Status Laporan",
                'receiver' => 'erw',
                'icon'  => "https://image.flaticon.com/icons/png/512/270/270014.png",/*Default Icon*/
                'sound' => 'mySound'/*Default sound*/
              ))->toJson();

        $fields = (object)array
                (
                    
                    'to'        => $from,
                    'notification'  => array(
                        "data" => $msg,
                    )
                );

        $headers = array
                (
                    'Authorization: key=' . $token,
                    'Content-Type: application/json'
                );
        //#Send Reponse To FireBase Server 
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        return $result;
        curl_close( $ch );
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
