<?php

namespace App\Http\Controllers;

use App\Traits\Authorizable;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    use Authorizable;

    public function index()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        return view('roles.index', compact('roles', 'permissions'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
      $this->validate($request,
      [
        'name'  =>  'required|unique:roles,name'
      ]);

      $role = new Role();
      $role->name = $request->name;
      $role->save();

      toastr()->success('New Role Added','Success');
      return redirect()->back();
    }

    public function show(Role $role)
    {
        //
    }

    public function edit(Role $role)
    {
        //
    }

    public function update(Request $request, $id)
    {
        if ($role = Role::findOrFail($id)) {
            if ($role->name === 'superadmin') {
                $role->syncPermissions(Permission::all());
                return redirect()->route('roles.index');
            }
            $permissions = $request->get('permissions', []);
            $role->syncPermissions($permissions);
            toastr()->success($role->name . ' Role Permission has been updated', 'Success');
        } else {
            toastr()->error('Role with id' . $id . 'not found', 'Error');
        }
        return redirect()->route('roles.index');
    }

    public function destroy(Role $role)
    {
        //
    }
}
