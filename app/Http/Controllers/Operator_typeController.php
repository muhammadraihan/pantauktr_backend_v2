<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Operator_type;
use App\Traits\Authorizable;

use DataTables;
use URL;

class Operator_typeController extends Controller
{
    use Authorizable;
 
    public function index()
    {
        $operator = Operator_type::all();
        if (request()->ajax()) {
            $data = Operator_type::latest()->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="' . route('operator-type.edit', $row->uuid) . '"><i class="fal fa-edit"></i></a>
                        <a class="btn btn-danger btn-sm btn-icon waves-effect waves-themed delete-btn" data-url="' . URL::route('operator-type.destroy', $row->uuid) . '" data-id="' . $row->uuid . '" data-token="' . csrf_token() . '" data-toggle="modal" data-target="#modal-delete"><i class="fal fa-trash-alt"></i></a>';
                })
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('operator_type.index');
    }

    public function create()
    {
        return view('operator_type.create');
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

        $operator = new Operator_type();
        $operator->name = $request->name;

        $operator->save();


        toastr()->success('New Operator Added', 'Success');
        return redirect()->route('operator-type.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $operator = Operator_type::uuid($id);
        return view('operator-type.edit', compact('operator'));
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
        $operator = Operator_type::uuid($id);
        $operator->name = $request->name;

        $operator->save();

        toastr()->success('Operator Edited', 'Success');
        return redirect()->route('operator-type.index');
    }

    public function destroy($id)
    {
        $operator = Operator_type::uuid($id);
        $operator->delete();
        toastr()->success('Operator Deleted', 'Success');
        return redirect()->route('operator-type.index');
    }
}
