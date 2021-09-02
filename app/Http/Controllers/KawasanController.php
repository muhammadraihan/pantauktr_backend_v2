<?php

namespace App\Http\Controllers;

use App\Models\Kawasan;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Traits\Authorizable;
use Auth;
use DataTables;
use DB;
use URL;

class KawasanController extends Controller
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
            $kawasan = Kawasan::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),
            'id','uuid','kawasan','created_by'])->get();
            return Datatables::of($kawasan)
                ->addIndexColumn()
                ->editColumn('created_by', function ($row) {
                    return $row->users->name;
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="' . route('kawasan.edit', $row->uuid) . '"><i class="fal fa-edit"></i></a>
                        <a class="btn btn-danger btn-sm btn-icon waves-effect waves-themed delete-btn" data-url="' . URL::route('kawasan.destroy', $row->uuid) . '" data-id="' . $row->uuid . '" data-token="' . csrf_token() . '" data-toggle="modal" data-target="#modal-delete"><i class="fal fa-trash-alt"></i></a>';
                })
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('kawasan.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('kawasan.create');
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

        $kawasan = new Kawasan();
        $kawasan->kawasan = $request->name;
        $kawasan->created_by = Auth::user()->uuid;

        $kawasan->save();


        toastr()->success('New Kawasan Added', 'Success');
        return redirect()->route('kawasan.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Kawasan  $kawasan
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Kawasan  $kawasan
     * @return \Illuminate\Http\Response
     */
    public function edit($uuid)
    {
        $kawasan = Kawasan::uuid($uuid);
        return view('kawasan.edit',compact('kawasan'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Kawasan  $kawasan
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
        $kawasan = Kawasan::uuid($uuid);
        $kawasan->kawasan = $request->name;
        $kawasan->edited_by = Auth::user()->uuid;

        $kawasan->save();

        toastr()->success('Kawasan Edited', 'Success');
        return redirect()->route('kawasan.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Kawasan  $kawasan
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        $kawasan = Kawasan::uuid($uuid);
        $kawasan->delete();
        toastr()->success('Kawasan Deleted', 'Success');
        return redirect()->route('kawasan.index');
    }
}
