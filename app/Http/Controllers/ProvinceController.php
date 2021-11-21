<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Province;
use App\Traits\Authorizable;

use DataTables;

class ProvinceController extends Controller
{
    use Authorizable;
  
    public function index()
    {
        $province = Province::all();
        if (request()->ajax()) {
            $data = Province::latest()->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->make(true);
        }

        return view('province.index');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
