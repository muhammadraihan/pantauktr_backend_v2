<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Laporan;
use App\Models\Pelapor;
use App\Models\Pelanggaran;
use App\Models\Province;
use App\Models\Jenis_apresiasi;
use App\Models\Jenis_laporan;
use App\Models\User;
use App\Models\Kota;
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
                        return $row->jlaporan->name;
                    })
                    ->editColumn('photo', function($row){
                        $url = asset('publiclampiran');
                        return '<img style="width: 150px; height: 150px;"  src="'.$url.'/'.$row->created_by.'/'.$row->photo.'" alt="">';
                    })
                    ->editColumn('created_at',function($row){
                        return Carbon::parse($row->created_at)->format('l\\, j F Y H:i:s');
                    })
            ->removeColumn('id')
            ->removeColumn('uuid')
            ->rawColumns(['photo'])
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
        //
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
        //
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
