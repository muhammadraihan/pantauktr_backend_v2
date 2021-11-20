<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jenis_laporan;
use App\Traits\Authorizable;

use Auth;
use DataTables;
use URL;

class Jenis_LaporanController extends Controller
{
    use Authorizable;

    public function index()
    {
        $jenis_laporan = Jenis_laporan::all();
        if (request()->ajax()) {
            $data = Jenis_laporan::latest()->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('created_by', function ($row) {
                    return $row->userCreate->name;
                })
                ->editColumn('edited_by', function ($row) {
                    if ($row->edited_by != null) {
                        return $row->userEdit->name;
                    } else {
                        return null;
                    }
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="' . route('jenis_laporan.edit', $row->uuid) . '"><i class="fal fa-edit"></i></a>
                        <a class="btn btn-danger btn-sm btn-icon waves-effect waves-themed delete-btn" data-url="' . URL::route('jenis_laporan.destroy', $row->uuid) . '" data-id="' . $row->uuid . '" data-token="' . csrf_token() . '" data-toggle="modal" data-target="#modal-delete"><i class="fal fa-trash-alt"></i></a>';
                })
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('jenis_laporan.index');
    }

    public function create()
    {
        return view('jenis_laporan.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|min:2',
        ];

        $messages = [
            '*.required' => 'Field tidak boleh kosong !',
        ];

        $this->validate($request, $rules, $messages);

        $jenis_laporan = new Jenis_laporan();
        $jenis_laporan->name = $request->name;
        $jenis_laporan->created_by = Auth::user()->uuid;

        $jenis_laporan->save();


        toastr()->success('New Jenis Laporan Added', 'Success');
        return redirect()->route('jenis_laporan.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $jenis_laporan = Jenis_laporan::uuid($id);
        return view('jenis_laporan.edit', compact('jenis_laporan'));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required|min:2',
        ];

        $messages = [
            '*.required' => 'Field tidak boleh kosong !',
        ];

        $this->validate($request, $rules, $messages);
        $jenis_laporan = Jenis_laporan::uuid($id);
        $jenis_laporan->name = $request->name;
        $jenis_laporan->edited_by = Auth::user()->uuid;

        $jenis_laporan->save();

        toastr()->success('Jenis Laporan Edited', 'Success');
        return redirect()->route('jenis_laporan.index');
    }

    public function destroy($id)
    {
        $jenis_laporan = Jenis_laporan::uuid($id);
        $jenis_laporan->delete();
        toastr()->success('Jenis Laporan Deleted', 'Success');
        return redirect()->route('jenis_laporan.index');
    }
}
