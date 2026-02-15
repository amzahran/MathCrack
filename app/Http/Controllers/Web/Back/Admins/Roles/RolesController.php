<?php

namespace App\Http\Controllers\Web\Back\Admins\Roles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Gate;

class RolesController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('show roles')) {
            return view('themes/default/back.permission-denied');
        }

        $roles=Role::where('id', '!=', 1)->get();
        $permissions=Permission::all();

        $groupedPermissions = $permissions->groupBy(function ($permission) {
            $parts = explode(' ', $permission->name);
            return end($parts);
        });

        return view('themes/default/back.admins.roles.roles-list', ['roles' => $roles, 'groupedPermissions' => $groupedPermissions]);
    }

    public function store(Request $request)
    {
        if (!Gate::allows('add roles')) {
            return view('themes/default/back.permission-denied');
        }

        $permissions = $request->input('permissions');
        $roleName = $request->input('name');

        $role = Role::where('name', $roleName)->first();

        if ($role) {
            return redirect()->back()->with('error', __('l.Role already exist '));
        }

        $role = Role::create(['name' => $roleName]);
        $role->syncPermissions($permissions);

        return redirect()->back()->with('success', __('l.Role added successfully'));
    }

    public function delete(Request $request)
    {
        if (!Gate::allows('delete roles')) {
            return view('themes/default/back.permission-denied');
        }

        $encryptedId=$request->id;
        $id=decrypt($encryptedId);

        $role = Role::findById($id);

        if ($id == 1 || $id == 2) {
            return redirect()->back()->with('error', __('l.can not delete Root role '));
        }
        if (!$role) {
            return redirect()->back()->with('error', __('l.Role dose not exist '));
        }

        $role->delete();

        return redirect()->back()->with('success', __('l.Role deleted successfully'));
    }

    public function edit(Request $request)
    {
        if (!Gate::allows('edit roles')) {
            return view('themes/default/back.permission-denied');
        }

        $encryptedId=$request->id;
        $id=decrypt($encryptedId);

        if ($id == 1 || $id == 2) {
            return redirect()->back()->with('error', __('l.can not edit Root role '));
        }

        $role = Role::findById($id);

        $permissions=Permission::all();
        $groupedPermissions = $permissions->groupBy(function ($permission) {
            $parts = explode(' ', $permission->name);
            return end($parts);
        });

        return view('themes/default/back.admins.roles.roles-edit', ['role' => $role, 'groupedPermissions' => $groupedPermissions]);
    }

    public function update(Request $request)
    {
        if (!Gate::allows('edit roles')) {
            return view('themes/default/back.permission-denied');
        }

        $encryptedId = $request->input('id');
        $id = decrypt($encryptedId);

        if ($id == 1 || $id == 2) {
            return redirect()->back()->with('error', __('l.can not edit Root role '));
        }

        $role = Role::findById($id);

        $name = $request->input('name');
        $permissions = $request->input('permissions');

        $role->name = $name;
        $role->save();

        $role->permissions()->detach();

        if (!empty($permissions)) {
            $role->syncPermissions($permissions);
        }

        return redirect()->back()->with('success', __('l.Role updated successfully'));
    }

}