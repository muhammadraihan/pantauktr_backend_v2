<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Operator_type;

use Auth;
use DataTables;
use DB;
use File;
use Hash;
use Image;
use Response;
use URL;

class OperatorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $operator = Operator_type::all();
        if (request()->ajax()) {
            $data = Operator_type::latest()->get();

            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        return '
                        <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="'.route('operator.edit',$row->uuid).'"><i class="fal fa-edit"></i></a>
                        <a class="btn btn-danger btn-sm btn-icon waves-effect waves-themed delete-btn" data-url="'.URL::route('operator.destroy',$row->uuid).'" data-id="'.$row->uuid.'" data-token="'.csrf_token().'" data-toggle="modal" data-target="#modal-delete"><i class="fal fa-trash-alt"></i></a>';
                 })
            ->removeColumn('id')
            ->removeColumn('uuid')
            ->rawColumns(['action'])
            ->make(true);
        }

        return view('operator.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('operator.create');
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
            'name' => 'required',
        ];

        $messages = [
            '*.required' => 'Field tidak boleh kosong !',
        ];

        $this->validate($request, $rules, $messages);

        $operator = new Operator_type();
        $operator->name = $request->name;

        $operator->save();        

        
        toastr()->success('New Operator Added','Success');
        return redirect()->route('operator.index');
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
        $operator = Operator_type::uuid($id);
        return view('operator.edit', compact('operator'));
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
            'name' => 'required',
        ];

        $messages = [
            '*.required' => 'Field tidak boleh kosong !',
        ];

        $this->validate($request, $rules, $messages);
          // Saving data
          $operator = Operator_type::uuid($id);
          $operator->name = $request->name;
    
          $operator->save();
    
          toastr()->success('Operator Edited','Success');
          return redirect()->route('operator.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $operator = Operator_type::uuid($id);
        $operator->delete();
        toastr()->success('Operator Deleted','Success');
        return redirect()->route('operator.index');
    }
}
