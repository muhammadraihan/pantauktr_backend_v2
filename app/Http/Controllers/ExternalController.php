<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\External_link;
use App\Traits\Authorizable;


use DataTables;

use URL;

class ExternalController extends Controller
{
    use Authorizable;

    public function index()
    {
        $external = External_link::all();
        if (request()->ajax()) {
            $data = External_link::latest()->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="' . route('external-link.edit', $row->uuid) . '"><i class="fal fa-edit"></i></a>
                        <a class="btn btn-danger btn-sm btn-icon waves-effect waves-themed delete-btn" data-url="' . URL::route('external-link.destroy', $row->uuid) . '" data-id="' . $row->uuid . '" data-token="' . csrf_token() . '" data-toggle="modal" data-target="#modal-delete"><i class="fal fa-trash-alt"></i></a>';
                })
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('external.index');
    }

    public function create()
    {
        return view('external.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|min:2',
            'description' => 'required',
            'link' => 'required'
        ];

        $messages = [
            '*.required' => 'Field tidak boleh kosong !',
            '*.min' => 'Nama tidak boleh kurang dari 2 karakter !',
        ];

        $this->validate($request, $rules, $messages);

        $external = new External_link();
        $external->title = $request->title;
        $external->description = $request->description;
        $external->link = $request->link;

        $external->save();


        toastr()->success('New Link Added', 'Success');
        return redirect()->route('external-link.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $external = External_link::uuid($id);
        return view('external.edit', compact('external'));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'title' => 'required|min:2',
            'description' => 'required|min:10',
            'link' => 'required'
        ];

        $messages = [
            '*.required' => 'Field tidak boleh kosong !',
            '*.min' => 'Nama tidak boleh kurang dari 2 karakter !',
        ];

        $this->validate($request, $rules, $messages);

        $external = External_link::uuid($id);
        $external->title = $request->title;
        $external->description = $request->description;
        $external->link = $request->link;

        $external->save();


        toastr()->success('New Link Added', 'Success');
        return redirect()->route('external-link.index');
    }

    public function destroy($id)
    {
        $external = External_link::uuid($id);
        $external->delete();
        toastr()->success('link Deleted', 'Success');
        return redirect()->route('external-link.index');
    }
}
