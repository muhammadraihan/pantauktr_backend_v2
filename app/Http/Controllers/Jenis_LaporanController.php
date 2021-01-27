<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jenis_laporan;
use App\Models\Operator_type;

use Auth;
use DataTables;
use DB;
use File;
use Hash;
use Image;
use Response;
use URL;

class Jenis_LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jenis_laporan = Jenis_laporan::all();
        if (request()->ajax()) {
            $data = Jenis_laporan::latest()->get();

            return Datatables::of($data)
                    ->addIndexColumn()
                    ->editColumn('created_by',function($row){
                        return $row->userCreate->name;
                    })
                    ->editColumn('edited_by',function($row){
                        if($row->edited_by != null){
                        return $row->userEdit->name;
                        }else{
                            return null;
                        }
                    })
                    ->addColumn('action', function($row){
                        return '
                        <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="'.route('jenis_laporan.edit',$row->uuid).'"><i class="fal fa-edit"></i></a>
                        <a class="btn btn-danger btn-sm btn-icon waves-effect waves-themed delete-btn" data-url="'.URL::route('jenis_laporan.destroy',$row->uuid).'" data-id="'.$row->uuid.'" data-token="'.csrf_token().'" data-toggle="modal" data-target="#modal-delete"><i class="fal fa-trash-alt"></i></a>';
                 })
            ->removeColumn('id')
            ->removeColumn('uuid')
            ->rawColumns(['action'])
            ->make(true);
        }

        return view('jenis_laporan.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('jenis_laporan.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|min:2|alpha',
        ];

        $messages = [
            '*.required' => 'Field tidak boleh kosong !',
        ];

        $this->validate($request, $rules, $messages);

        $jenis_laporan = new Jenis_laporan();
        $jenis_laporan->name = $request->name;
        $jenis_laporan->created_by = Auth::user()->uuid;

        $jenis_laporan->save();        

        
        toastr()->success('New Jenis Laporan Added','Success');
        return redirect()->route('jenis_laporan.index');
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
        $jenis_laporan = Jenis_laporan::uuid($id);
        return view('jenis_laporan.edit', compact('jenis_laporan'));
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
        $rules = [
            'name' => 'required|min:2|alpha',
        ];

        $messages = [
            '*.required' => 'Field tidak boleh kosong !',
        ];

        $this->validate($request, $rules, $messages);
          // Saving data
          $jenis_laporan = Jenis_laporan::uuid($id);
          $jenis_laporan->name = $request->name;
          $jenis_laporan->edited_by = Auth::user()->uuid;
    
          $jenis_laporan->save();
    
          toastr()->success('Jenis Laporan Edited','Success');
          return redirect()->route('jenis_laporan.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $jenis_laporan = Jenis_laporan::uuid($id);
        $jenis_laporan->delete();
        toastr()->success('Jenis Laporan Deleted','Success');
        return redirect()->route('jenis_laporan.index');
    }
}
