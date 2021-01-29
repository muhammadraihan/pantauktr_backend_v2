<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Laporan;
use App\Models\Pelapor;
use App\Models\Pelanggaran;
use App\Models\Province;
use App\Models\Jenis_laporan;
use App\Models\Jenis_apresiasi;
use App\Models\User;

use Auth;
use DataTables;
use DB;
use File;
use Hash;
use Image;
use Response;
use URL;

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
        // dd($users);
        if (request()->ajax()) {
          DB::statement(DB::raw('set @rownum=0'));
          if ($request->user()->hasRole('operator')){
            $userss = User::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),
            'id','uuid','jenis_pelanggaranan','keterangan','photo', 'lat', 'lng', 'nama_lokasi', 'alamat', 'kelurahan', 'kecamatan', 'kota', 'propinsi', 'negara', 'created_by'])->where('kota', 'like', $users->city_id);
          }else{
            $userss = User::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),
            'id','uuid','jenis_pelanggaranan','keterangan','photo', 'lat', 'lng', 'nama_lokasi', 'alamat', 'kelurahan', 'kecamatan', 'kota', 'propinsi', 'negara', 'created_by'])->get();
          }


            return Datatables::of($data)
                    ->addIndexColumn()
                    ->editColumn('created_by',function($row){
                        return $row->userCreate->name;
                    })
                    ->editColumn('jenis_pelanggaran',function($row){
                        if($row->jenis_pelanggaran != null){
                        return $row->jenis_pelanggaran->name;
                        }else{
                            return null;
                        }
                    })
                    ->editColumn('jenis_apresiasi',function($row){
                        return $row->jenis_apresiasi->name;
                    })
                    ->editColumn('jenis_laporan',function($row){
                        return $row->jenis_laporan->name;
                    })
                    ->editColumn('kota',function($row){
                        return $row->kota->city_name;
                    })
            ->removeColumn('id')
            ->removeColumn('uuid')
            ->make(true);
        }

        return view('laporan.index');
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
