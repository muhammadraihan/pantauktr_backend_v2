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

use Auth;
use DataTables;
use DB;
use File;
use Hash;
use Image;
use Response;
use URL;
class ChartController extends Controller
{
    public function index(Request $request, User $uuid)
    {
        $users = Auth::user($uuid);
        $jl = Jenis_laporan::all();
        $lap = Laporan::all();
        $nama_pelanggaran = Pelanggaran::select('name')->get()->toArray();
        $jenis_laporan_pelanggaran = Jenis_laporan::where('name', 'like', 'Pelanggaran')->get();
        $jenis_laporan_apresiasi = Jenis_laporan::where('name', 'like', 'Apresiasi')->get();
     
        $year = DB::table('laporans')
                    ->select( DB::raw("DATE_FORMAT(created_at, '%Y') tahun"))
                    ->groupBy('tahun')
                    ->get();
        $month = DB::table('laporans')
                    ->select( DB::raw("DATE_FORMAT(created_at, '%m') bulan"))
                    ->groupBy('bulan')
                    ->get();

        if ($request->user()->hasRole('operator'))
        {
            $laporan_pelanggaran = DB::table('laporans')
                            ->join('pelanggarans','pelanggarans.uuid','=','laporans.jenis_pelanggaran')
                            ->select('laporans.jenis_pelanggaran','pelanggarans.name')
                            ->whereYear('laporans.created_at', $request['tahun'])
                            ->whereMonth('laporans.created_at', $request['bulan'])
                            ->where('laporans.kota','like',$users->city->city_name)
                            ->get();

            $lapPelanggaran = $laporan_pelanggaran->groupBy('name');
            $arrPelanggaran = array();
            foreach ($lapPelanggaran as $key => $value) {
                $arrPelanggaran[$key] = count($value);
            }

            $laporan_apresiasi = DB::table('laporans')
                            ->join('jenis_apresiasis','jenis_apresiasis.uuid','=','laporans.jenis_apresiasi')
                            ->select('laporans.jenis_apresiasi','jenis_apresiasis.name')
                            ->whereYear('laporans.created_at', $request['tahun'])
                            ->whereMonth('laporans.created_at', $request['bulan'])
                            ->where('laporans.kota','like',$users->city->city_name)
                            ->get();

            $lapApresiasi = $laporan_apresiasi->groupBy('name');
            $arrApresiasi = array();
            foreach ($lapApresiasi as $key => $value) {
                $arrApresiasi[$key] = count($value);
            }
         }else{
            $laporan_pelanggaran = DB::table('laporans')
                            ->join('pelanggarans','pelanggarans.uuid','=','laporans.jenis_pelanggaran')
                            ->select('laporans.jenis_pelanggaran','pelanggarans.name')
                            ->whereYear('laporans.created_at', $request['tahun'])
                            ->whereMonth('laporans.created_at', $request['bulan'])
                            ->get();

            $lapPelanggaran = $laporan_pelanggaran->groupBy('name');
            $arrPelanggaran = array();

            foreach ($lapPelanggaran as $key => $value) {
                $arrPelanggaran[$key] = count($value);
            }

            $laporan_apresiasi = DB::table('laporans')
                            ->join('jenis_apresiasis','jenis_apresiasis.uuid','=','laporans.jenis_apresiasi')
                            ->select('laporans.jenis_apresiasi','jenis_apresiasis.name')
                            ->whereYear('laporans.created_at', $request['tahun'])
                            ->whereMonth('laporans.created_at', $request['bulan'])
                            ->get();

            $lapApresiasi = $laporan_apresiasi->groupBy('name');
            $arrApresiasi = array();
            
            foreach ($lapApresiasi as $key => $value) {
                $arrApresiasi[$key] = count($value);
            }
         }
                                         
        $jenis_pelanggaran = [];
        $jenis_apresiasi = [];
        $data_pelanggaran = [];
        $data_apresiasi = [];

        foreach($laporan_pelanggaran as $lp){
            $jenis_pelanggaran[] = $lp->name;
            $data_pelanggaran[] = $laporan_pelanggaran;
        }

        foreach($laporan_apresiasi as $ls){
            $jenis_apresiasi[] = $ls->name;
            $data_apresiasi[] = $laporan_apresiasi;
        }
        return view('chart.index', compact('users','jl','lap','nama_pelanggaran','jenis_laporan_pelanggaran','jenis_laporan_apresiasi',
                    'laporan_pelanggaran','lapPelanggaran','arrPelanggaran','laporan_apresiasi','lapApresiasi','arrApresiasi','jenis_pelanggaran','jenis_apresiasi',
                    'data_pelanggaran','data_apresiasi','year', 'month'));
    }

    public function bulan(Request $request, User $uuid){
        $users = Auth::user($uuid);
        if ($request->user()->hasRole('operator'))
        {
            $laporan_pelanggaran = DB::table('laporans')
                                ->join('pelanggarans','pelanggarans.uuid','=','laporans.jenis_pelanggaran')
                                ->select('laporans.jenis_pelanggaran','pelanggarans.name')
                                ->whereYear('laporans.created_at', $request['tahun'])
                                ->whereMonth('laporans.created_at', $request['bulan'])
                                ->where('laporans.kota','like',$users->city->city_name)
                                ->get();

            $lapPelanggaran = $laporan_pelanggaran->groupBy('name');
            $arrPelanggaran = array();
            foreach ($lapPelanggaran as $key => $value) {
            $arrPelanggaran[$key] = count($value);
            }

            $laporan_apresiasi = DB::table('laporans')
                                ->join('jenis_apresiasis','jenis_apresiasis.uuid','=','laporans.jenis_apresiasi')
                                ->select('laporans.jenis_apresiasi','jenis_apresiasis.name')
                                ->whereYear('laporans.created_at', $request['tahun'])
                                ->whereMonth('laporans.created_at', $request['bulan'])
                                ->where('laporans.kota','like',$users->city->city_name)
                                ->get();

            $lapApresiasi = $laporan_apresiasi->groupBy('name');
            $arrApresiasi = array();
            foreach ($lapApresiasi as $key => $value) {
            $arrApresiasi[$key] = count($value);
            }
        }else{
            $laporan_pelanggaran = DB::table('laporans')
                            ->join('pelanggarans','pelanggarans.uuid','=','laporans.jenis_pelanggaran')
                            ->select('laporans.jenis_pelanggaran','pelanggarans.name')
                            ->whereYear('laporans.created_at', $request['tahun'])
                            ->whereMonth('laporans.created_at', $request['bulan'])
                            ->get();
            $lapPelanggaran = $laporan_pelanggaran->groupBy('name');
            $arrPelanggaran = array();
            foreach ($lapPelanggaran as $key => $value) {
                $arrPelanggaran[$key] = count($value);
            }

            $laporan_apresiasi = DB::table('laporans')
                            ->join('jenis_apresiasis','jenis_apresiasis.uuid','=','laporans.jenis_apresiasi')
                            ->select('laporans.jenis_apresiasi','jenis_apresiasis.name')
                            ->whereYear('laporans.created_at', $request['tahun'])
                            ->whereMonth('laporans.created_at', $request['bulan'])
                            ->get();

            $lapApresiasi = $laporan_apresiasi->groupBy('name');
            $arrApresiasi = array();
            foreach ($lapApresiasi as $key => $value) {
                $arrApresiasi[$key] = count($value);
            }
        }
                                        
            $jenis_pelanggaran = [];
            $jenis_apresiasi = [];
            $data_pelanggaran = [];
            $data_apresiasi = [];

            foreach($laporan_pelanggaran as $lp){
                $jenis_pelanggaran[] = $lp->name;
                $data_pelanggaran[] = $laporan_pelanggaran;
            }

            foreach($laporan_apresiasi as $ls){
                $jenis_apresiasi[] = $ls->name;
                $data_apresiasi[] = $laporan_apresiasi;
            }
        return response()->json([$arrPelanggaran, $arrApresiasi]);
    }
}
