<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        //$this->authorize('view users');
        $users = User::with('roles')->get();
        return view('users.list', compact('users'));
    }

    public function create()
    {
        //$this->authorize('create users');
        $formType = 'create';
        $roles = Role::all();
        $user = new User();
        return view('users.form', compact('roles', 'user', 'formType'));
    }

    public function store(Request $request)
    {
        //$this->authorize('create users');
        $request->validate([
            'username' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id'
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            if ($request->has('roles')) {
                $roles = array_map('intval', $request->roles);
                $user->syncRoles($roles);
            }

            DB::commit();
            return redirect()->route('users.index')->with('success', 'User created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating user: ' . $e->getMessage());
        }
    }

    public function edit(User $user)
    {
       // $this->authorize('edit users');
        $formType = 'edit';
        $roles = Role::all();
        return view('users.form', compact('user', 'roles', 'formType'));
    }

    public function update(Request $request, User $user)
    {
       // $this->authorize('edit users');
        $request->validate([
            'username' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id'
        ]);

        DB::beginTransaction();
        try {
            $user->update([
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email
            ]);

            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($request->password)]);
            }
            $user->roles()->detach();
            if ($request->has('roles')) {
                $roles = array_map('intval', $request->roles);
                $user->syncRoles($roles);
            } else {
                $user->syncRoles([]);
            }

            DB::commit();
            return redirect()->route('users.index')->with('success', 'User updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating user: ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        $this->authorize('delete users');
        try {
            $user->roles()->detach();
            $user->delete();
            return redirect()->route('users.index')->with('success', 'User deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }
}
