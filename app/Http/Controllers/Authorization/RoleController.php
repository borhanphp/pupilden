<?php

namespace App\Http\Controllers\Authorization;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
class RoleController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $this->authorize('view roles');
        $roles = Role::with('permissions')->get();
        return view('roles.list', compact('roles'));
    }

    public function create()
    {
        $this->authorize('create roles');
        $formType = 'create';
        $permissions = Permission::orderBy('head', 'asc')->get()->groupBy('head');
        return view('roles.form', compact('formType', 'permissions'));
    }

    public function store(RoleRequest $request)
    {
        $this->authorize('create roles');
        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $request->name,
                'guard_name' => $request->guard_name,
            ]);

            if ($request->has('permissions')) {
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $role->syncPermissions($permissions);
            }

            DB::commit();
            return redirect()->route('roles.index')->with('success', 'Role created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating role: ' . $e->getMessage());
        }
    }

    public function show(Role $role)
    {
        $this->authorize('view roles');
        return view('roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $this->authorize('edit roles');
        $formType = 'edit';
        $permissions = Permission::orderBy('head', 'asc')->get()->groupBy('head');
        return view('roles.form', compact('role', 'formType', 'permissions'));
    }

    public function update(RoleRequest $request, Role $role)
    {
        $this->authorize('edit roles');
        DB::beginTransaction();
        try {
            $role->update([
                'name' => $request->name,
                'guard_name' => $request->guard_name,
            ]);

            if ($request->has('permissions')) {
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $role->syncPermissions($permissions);
            } else {
                $role->syncPermissions([]);
            }

            DB::commit();
            return redirect()->route('roles.index')->with('success', 'Role updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating role: ' . $e->getMessage());
        }
    }

    public function destroy(Role $role)
    {
        $this->authorize('delete roles');
        DB::beginTransaction();
        try {
            $role->permissions()->detach();
            $role->delete();
            DB::commit();
            return redirect()->route('roles.index')->with('success', 'Role deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting role: ' . $e->getMessage());
        }
    }
}
