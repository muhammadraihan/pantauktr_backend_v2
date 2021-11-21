<?php

namespace App\Http\Controllers;

use App\Traits\Authorizable;
use App\Models\Permission;
use App\Models\Role;
use DataTables;
use DB;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    use Authorizable;

    public function index()
    {
        if (request()->ajax()) {
            DB::statement(DB::raw('set @rownum=0'));
            $permissions = Permission::select([
                DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                'name', 'created_at'
            ]);
            return DataTables::of($permissions)
                ->editColumn('created_at', function ($permission) {
                    return $permission->created_at->format('l \\, jS F Y h:i:s A');
                })->make();
        }
        return view('permissions.index');
    }

    public function create()
    {
        return view('permissions.create');
    }

    public function store(Request $request)
    {
        $role = Role::where('id', 1)->first();
        if ($request->permission_type == 'basic') {
            $this->validate($request, [
                'name' => 'required|min:3|max:255|unique:permissions,name',
            ]);

            $permission = new Permission();
            $permission->name = $request->name;
            $permission->save();
            $permission->syncRoles($role);
            toastr()->success('Basic Permission Added', 'Success');
            return redirect()->route('permissions.index');
        } elseif ($request->permission_type == 'crud') {
            $this->validate($request, [
                'resource' => 'required|min:3|max:255|unique:permissions,name',
            ]);
            $crud = $request->input('action');
            if ($crud != null) {
                foreach ($crud as $action) {
                    $slug = strtolower($action) . '_' . strtolower($request->resource);
                    $permission = new Permission();
                    $permission->name = $slug;
                    $permission->save();
                    $permission->syncRoles($role);
                }
                toastr()->success('Resource Permission Added', 'Success');
                return redirect()->route('permissions.index');
            }
            else {
                toastr()->error('Please choose one of the CRUD Actions', 'Error');
                return redirect()->route('permissions.create')->withInput();
            }
        }
        else {
            toastr()->error('Please choose one of the options', 'Error');
            return redirect()->route('permissions.create')->withInput();
        }
    }

    public function show(Permission $permission)
    {
        //
    }

    public function edit(Permission $permission)
    {
        //
    }

    public function update(Request $request, Permission $permission)
    {
        //
    }

    public function destroy(Permission $permission)
    {
        //
    }
}
