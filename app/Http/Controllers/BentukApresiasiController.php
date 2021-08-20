<?php

namespace App\Http\Controllers;

use App\Models\BentukApresiasi;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Traits\Authorizable;
use Auth;
use DataTables;
use DB;
use URL;

class BentukApresiasiController extends Controller
{
    use Authorizable;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            DB::statement(DB::raw('set @rownum=0'));
            $bentuk_apresiasi = BentukApresiasi::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),
            'id','uuid','bentuk_apresiasi','created_by'])->get();
            return Datatables::of($bentuk_apresiasi)
                ->addIndexColumn()
                ->editColumn('created_by', function ($row) {
                    return $row->users->name;
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="' . route('bentuk_apresiasi.edit', $row->uuid) . '"><i class="fal fa-edit"></i></a>
                        <a class="btn btn-danger btn-sm btn-icon waves-effect waves-themed delete-btn" data-url="' . URL::route('bentuk_apresiasi.destroy', $row->uuid) . '" data-id="' . $row->uuid . '" data-token="' . csrf_token() . '" data-toggle="modal" data-target="#modal-delete"><i class="fal fa-trash-alt"></i></a>';
                })
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('bentuk_apresiasi.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('bentuk_apresiasi.create');
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
            'name' => 'required'
        ];

        $messages = [
            '*.required' => 'Field tidak boleh kosong !',
        ];

        $this->validate($request, $rules, $messages);

        $bentuk_apresiasi = new BentukApresiasi();
        $bentuk_apresiasi->bentuk_apresiasi = $request->name;
        $bentuk_apresiasi->created_by = Auth::user()->uuid;

        $bentuk_apresiasi->save();


        toastr()->success('New Bentuk Apreasiasi Added', 'Success');
        return redirect()->route('bentuk_apresiasi.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bentuk_Apresiasi  $bentuk_Apresiasi
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Bentuk_Apresiasi  $bentuk_Apresiasi
     * @return \Illuminate\Http\Response
     */
    public function edit($uuid)
    {
        $bentuk_apresiasi = BentukApresiasi::uuid($uuid);
        return view('bentuk_apresiasi.edit',compact('bentuk_apresiasi'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bentuk_Apresiasi  $bentuk_Apresiasi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$uuid)
    {
        $rules = [
            'name' => 'required',
        ];

        $messages = [
            '*.required' => 'Field tidak boleh kosong !',
        ];

        $this->validate($request, $rules, $messages);
        // Saving data
        $bentuk_apresiasi = BentukApresiasi::uuid($uuid);
        $bentuk_apresiasi->bentuk_apresiasi = $request->name;
        $bentuk_apresiasi->edited_by = Auth::user()->uuid;

        $bentuk_apresiasi->save();

        toastr()->success('Bentuk Apresiasi Edited', 'Success');
        return redirect()->route('bentuk_apresiasi.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bentuk_Apresiasi  $bentuk_Apresiasi
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        $bentuk_apresiasi = BentukApresiasi::uuid($uuid);
        $bentuk_apresiasi->delete();
        toastr()->success('Bentuk Apreasiasi Deleted', 'Success');
        return redirect()->route('bentuk_apresiasi.index');
    }
}
