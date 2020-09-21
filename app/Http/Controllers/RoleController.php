<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use App\Models\PermissionGroup;

use Auth;
use DB;

class RoleController extends Controller
{
    public function index()
    {
        $this->authorize('view role', Auth::user());
        $roles = Role::all();
        $data['roles'] = $roles;
        $data['title'] = "List Role";
        return view('authorization.role.index', $data);
    }

    public function create()
    {
        $this->authorize('add role', Auth::user());
        $permissionGroups = PermissionGroup::with('permissions')->get();
        $data['title'] = "Create Role";
        $data['permissionGroups'] = $permissionGroups;
        return view('authorization.role.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('add role', Auth::user());
        try
        {
            DB::transaction(function () use ($request)
            {
                $role = Role::create(['name' => $request->name]);

                $permissionsArray = [];
                if (isset($request->permissions))
                {
                    $permissionsArray = $request->permissions;
                }
                $permissions = Permission::whereIn('id', $permissionsArray)->get();
                $role->syncPermissions($permissions);
            });

            return redirect()->route('role-list')->withSuccess('Create role success');
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
			if (isset($e->errorInfo[2]))
			{
				$message = $e->errorInfo[2];
			}
			return redirect()->back()->withError($message);
        }
    }

    public function edit($id)
    {
        $this->authorize('edit role', Auth::user());
        $role = Role::with('permissions')->find($id);
        $permissionGroups = PermissionGroup::with('permissions')->get();
        $data['title'] = "Edit Role";
        $data['role'] = $role;
        $data['permissionGroups'] = $permissionGroups;
        return view('authorization.role.edit', $data);
    }

    public function update($id, Request $request)
    {
        $this->authorize('edit role', Auth::user());
        try
        {
            DB::transaction(function () use ($request, $id)
            {
                $role = Role::find($id);
                $role->name = $request->name;
                $role->save();

                $permissionsArray = [];
                if (isset($request->permissions))
                {
                    $permissionsArray = $request->permissions;
                }
                $permissions = Permission::whereIn('id', $permissionsArray)->get();
                $role->syncPermissions($permissions);
            });

            return redirect()->back()->withSuccess('Update role success');
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
			if (isset($e->errorInfo[2]))
			{
				$message = $e->errorInfo[2];
			}
			return redirect()->back()->withError($message);
        }
    }

    public function delete($id)
    {
        $this->authorize('delete role', Auth::user());
        $role = Role::find($id);
        foreach ($role->permissions as $permission )
        {
            $permission->removeRole($role);
        }
        $role->delete();

        return redirect()->back()->withSuccess('Delete role success');
    }
}
