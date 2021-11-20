<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Authorizable;

use App\Models\Jenis_apresiasi;

use Auth;
use DataTables;
use URL;

class Jenis_ApresiasiController extends Controller
{

    use Authorizable;

    public function index()
    {
        $jenis_apresiasi = Jenis_apresiasi::all();
        if (request()->ajax()) {
            $data = Jenis_apresiasi::latest()->get();

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
                        <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="' . route('jenis_apresiasi.edit', $row->uuid) . '"><i class="fal fa-edit"></i></a>
                        <a class="btn btn-danger btn-sm btn-icon waves-effect waves-themed delete-btn" data-url="' . URL::route('jenis_apresiasi.destroy', $row->uuid) . '" data-id="' . $row->uuid . '" data-token="' . csrf_token() . '" data-toggle="modal" data-target="#modal-delete"><i class="fal fa-trash-alt"></i></a>';
                })
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('jenis_apresiasi.index');
    }

    public function create()
    {
        return view('jenis_apresiasi.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|min:2'
        ];

        $messages = [
            '*.required' => 'Field tidak boleh kosong !',
            '*.min' => 'Nama tidak boleh kurang dari 2 karakter !',
        ];

        $this->validate($request, $rules, $messages);

        $jenis_apresiasi = new Jenis_apresiasi();
        $jenis_apresiasi->name = $request->name;
        $jenis_apresiasi->created_by = Auth::user()->uuid;

        $jenis_apresiasi->save();


        toastr()->success('New Jenis Apresiasi Added', 'Success');
        return redirect()->route('jenis_apresiasi.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $jenis_apresiasi = Jenis_apresiasi::uuid($id);
        return view('jenis_apresiasi.edit', compact('jenis_apresiasi'));
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
        $jenis_apresiasi = Jenis_apresiasi::uuid($id);
        $jenis_apresiasi->name = $request->name;
        $jenis_apresiasi->edited_by = Auth::user()->uuid;

        $jenis_apresiasi->save();

        toastr()->success('Jenis Apresiasi Edited', 'Success');
        return redirect()->route('jenis_apresiasi.index');
    }

    public function destroy($id)
    {
        $jenis_apresiasi = Jenis_apresiasi::uuid($id);
        $jenis_apresiasi->delete();
        toastr()->success('Jenis Apresiasi Deleted', 'Success');
        return redirect()->route('jenis_apresiasi.index');
    }
}
