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
        // dd($nama_pelanggaran);
        $jenis_laporan_pelanggaran = Jenis_laporan::where('name', 'like', 'Pelanggaran')->get();
        $jenis_laporan_apresiasi = Jenis_laporan::where('name', 'like', 'Apresiasi')->get();
        // dd($jenis_laporan);
        
        if ($request->user()->hasRole('operator'))
        {
            // dd($users->city->city_name);
            $laporan_pelanggaran = DB::table('laporans')
                            ->join('pelanggarans','pelanggarans.uuid','=','laporans.jenis_pelanggaran')
                            ->select('laporans.jenis_pelanggaran','pelanggarans.name')
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
                            ->where('laporans.kota','like',$users->city->city_name)
                            ->get();

            $lapApresiasi = $laporan_apresiasi->groupBy('name');
            $arrApresiasi = array();
            foreach ($lapApresiasi as $key => $value) {
                $arrApresiasi[$key] = count($value);
            }
            // dd($arrPelanggaran,$arrApresiasi);
            // dd($laporan_ktr->groupBy('jenis_pelanggaran'));
         }else{
            // $laporan_ktr = Laporan::where('jenis_pelanggaran', '=', 'bb127320-5497-455e-a05d-4e245aa50616')->count();
            // $laporan_tapsban = Laporan::where('jenis_pelanggaran', '=', '669d6f1d-2456-4798-b416-b70ecaacbac0')->count();
            // $laporan_pos = Laporan::where('jenis_pelanggaran', '=', 'd902a7d6-6e25-42a0-8e66-afe2bfbcf6c6')->count();
            // $laporan_apresiasi = Laporan::where('jenis_apresiasi', '=', '3c22c473-3a56-4e7a-81b5-abc8fc86439d')->count();     
            // $laporan_masukan = Laporan::where('jenis_apresiasi', '=', '8c771a3a-04a3-4a4a-95cc-6cd00d75e2b3')->count();
            $laporan_pelanggaran = DB::table('laporans')
                            ->join('pelanggarans','pelanggarans.uuid','=','laporans.jenis_pelanggaran')
                            ->select('laporans.jenis_pelanggaran','pelanggarans.name')
                            // ->where('laporans.kota','like',$users->city->city_name)
                            ->get();
            $lapPelanggaran = $laporan_pelanggaran->groupBy('name');
            $arrPelanggaran = array();
            foreach ($lapPelanggaran as $key => $value) {
                $arrPelanggaran[$key] = count($value);
            }

            $laporan_apresiasi = DB::table('laporans')
                            ->join('jenis_apresiasis','jenis_apresiasis.uuid','=','laporans.jenis_apresiasi')
                            ->select('laporans.jenis_apresiasi','jenis_apresiasis.name')
                            // ->where('laporans.kota','like',$users->city->city_name)
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

        foreach($jenis_laporan_pelanggaran as $lp){
            $jenis_pelanggaran[] = $lp->name;
            $data_pelanggaran[] = $laporan_pelanggaran;
        }
        // dd($arrPelanggaran);

        foreach($jenis_laporan_apresiasi as $ls){
            $jenis_apresiasi[] = $ls->name;
            $data_apresiasi[] = $laporan_apresiasi;
        }
        // dd($data_apresiasi);
        // dd(json_encode($nampel));
        // dd(json_encode($data_ktr));
        // dd(json_encode($jenis_pelanggaran));
        if(!empty($this->province)){
            $this->kota = Kota::where('province_code', $this->province)->get();
        }
        return view('chart.index', compact('users','jl','lap','nama_pelanggaran','jenis_laporan_pelanggaran','jenis_laporan_apresiasi',
                    'laporan_pelanggaran','lapPelanggaran','arrPelanggaran','laporan_apresiasi','lapApresiasi','arrApresiasi','jenis_pelanggaran','jenis_apresiasi',
                    'data_pelanggaran','data_apresiasi'))->withProvince(Province::orderBy('province_name')->get());
    }
}
