<?php

namespace App\Http\Controllers;

use App\Models\BentukPelanggaran;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Traits\Authorizable;
use Auth;
use DataTables;
use DB;
use URL;


class BentukPelanggaranController extends Controller
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
            $bentuk_Pelanggaran = BentukPelanggaran::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),
            'id','uuid','bentuk_pelanggaran','created_by'])->get();
            return Datatables::of($bentuk_Pelanggaran)
                ->addIndexColumn()
                ->editColumn('created_by', function ($row) {
                    return $row->users->name;
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="' . route('bentuk_pelanggaran.edit', $row->uuid) . '"><i class="fal fa-edit"></i></a>
                        <a class="btn btn-danger btn-sm btn-icon waves-effect waves-themed delete-btn" data-url="' . URL::route('bentuk_pelanggaran.destroy', $row->uuid) . '" data-id="' . $row->uuid . '" data-token="' . csrf_token() . '" data-toggle="modal" data-target="#modal-delete"><i class="fal fa-trash-alt"></i></a>';
                })
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('bentuk_Pelanggaran.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('bentuk_pelanggaran.create');
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

        $bentuk_Pelanggaran = new BentukPelanggaran();
        $bentuk_Pelanggaran->bentuk_pelanggaran = $request->name;
        $bentuk_Pelanggaran->created_by = Auth::user()->uuid;

        $bentuk_Pelanggaran->save();


        toastr()->success('New Bentuk Pelanggaran Added', 'Success');
        return redirect()->route('bentuk_pelanggaran.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BentukPelanggaran  $bentuk_Pelanggaran
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BentukPelanggaran  $bentuk_Pelanggaran
     * @return \Illuminate\Http\Response
     */
    public function edit($uuid)
    {
        $bentuk_pelanggaran = BentukPelanggaran::uuid($uuid);
        return view('bentuk_pelanggaran.edit',compact('bentuk_pelanggaran'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BentukPelanggaran  $bentuk_Pelanggaran
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
        $bentuk_Pelanggaran = BentukPelanggaran::uuid($uuid);
        $bentuk_Pelanggaran->bentuk_pelanggaran = $request->name;
        $bentuk_Pelanggaran->edited_by = Auth::user()->uuid;

        $bentuk_Pelanggaran->save();

        toastr()->success('Bentuk Pelanggaran Edited', 'Success');
        return redirect()->route('bentuk_pelanggaran.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BentukPelanggaran  $bentuk_Pelanggaran
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        $bentuk_Pelanggaran = BentukPelanggaran::uuid($uuid);
        $bentuk_Pelanggaran->delete();
        toastr()->success('Bentuk Pelanggaran Deleted', 'Success');
        return redirect()->route('bentuk_pelanggaran.index');
    }
}
