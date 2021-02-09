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
        $jenis_laporan = Jenis_laporan::where('name', 'like', 'Pelanggaran')->get();
        $jenis_laporans = Jenis_laporan::where('name', 'like', 'Apresiasi')->get();
        // dd($jenis_laporan);
        if ($request->user()->hasRole('operator'))
        {
            $laporan_ktr = Laporan::where('kota', 'like', $users->city->city_name)
                            ->where('jenis_pelanggaran', '=', 'bb127320-5497-455e-a05d-4e245aa50616')
                            ->count();
            $laporan_tapsban = Laporan::where('kota', 'like', $users->city->city_name)
                                ->where('jenis_pelanggaran', '=', '669d6f1d-2456-4798-b416-b70ecaacbac0')
                                ->count();
            $laporan_pos = Laporan::where('kota', 'like', $users->city->city_name)
                                ->where('jenis_pelanggaran', '=', 'd902a7d6-6e25-42a0-8e66-afe2bfbcf6c6')
                                ->count();
            $laporan_apresiasi = Laporan::where('kota', 'like', $users->city->city_name)
                                ->where('jenis_apresiasi', '=', '3c22c473-3a56-4e7a-81b5-abc8fc86439d')
                                ->count();     
            $laporan_masukan = Laporan::where('kota', 'like', $users->city->city_name)
                                ->where('jenis_apresiasi', '=', '8c771a3a-04a3-4a4a-95cc-6cd00d75e2b3')
                                ->count();
         }else{
            $laporan_ktr = Laporan::where('jenis_pelanggaran', '=', 'bb127320-5497-455e-a05d-4e245aa50616')->count();
            $laporan_tapsban = Laporan::where('jenis_pelanggaran', '=', '669d6f1d-2456-4798-b416-b70ecaacbac0')->count();
            $laporan_pos = Laporan::where('jenis_pelanggaran', '=', 'd902a7d6-6e25-42a0-8e66-afe2bfbcf6c6')->count();
            $laporan_apresiasi = Laporan::where('jenis_apresiasi', '=', '3c22c473-3a56-4e7a-81b5-abc8fc86439d')->count();     
            $laporan_masukan = Laporan::where('jenis_apresiasi', '=', '8c771a3a-04a3-4a4a-95cc-6cd00d75e2b3')->count();
         }
                                         
        $jenis_pelanggaran = [];
        $jenis_apresiasi = [];
        $data_ktr = [];
        $data_tapsban = [];
        $data_pos = [];
        $data_apresiasi = [];
        $data_masukan = [];
        $nampel = [];

        foreach($jenis_laporan as $lp){
            $jenis_pelanggaran[] = $lp->name;
            $data_ktr[] = $laporan_ktr;
            $data_tapsban[] = $laporan_tapsban;
            $data_pos[] = $laporan_pos;

        }

        // foreach($nama_pelanggaran as $np){ 
        //     // dd($np['name']);
        //     $nampel[] = $np['name'];

            
            
        // }

        foreach($jenis_laporans as $ls){
            $jenis_apresiasi[] = $ls->name;
            $data_apresiasi[] = $laporan_apresiasi;
            $data_masukan[] = $laporan_masukan;

        }
        // dd(json_encode($nampel));
        // dd(json_encode($data_ktr));
        // dd(json_encode($jenis_pelanggaran));
        return view('chart.index', compact('jenis_laporan', 'jenis_pelanggaran', 'users', 'data_ktr', 'laporan_ktr','jl', 'lap', 'laporan_tapsban', 'data_tapsban', 
    'jenis_apresiasi', 'jenis_laporans', 'data_pos', 'laporan_pos', 'laporan_apresiasi', 'data_apresiasi', 'laporan_masukan', 'data_masukan'));
    }
}
