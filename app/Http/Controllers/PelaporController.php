<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Authorizable;
use App\Models\Pelapor;

use DataTables;
use DB;


class PelaporController extends Controller
{
    use Authorizable;
 
    public function index()
    {
        if (request()->ajax()) {
            DB::statement(DB::raw('set @rownum=0'));
            $pelapor = Pelapor::select([
                DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                'id', 'uuid', 'firstname', 'lastname', 'email', 'provider', 'avatar', 'reward_point', 'last_login_at', 'last_login_ip'
            ])->get();
            return Datatables::of($pelapor)
                ->addIndexColumn()
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->make(true);
        }

        return view('pelapor.index');
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
        //
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
