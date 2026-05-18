<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $orgId = auth()->user()->organization_id;

        $teachers = Teacher::with(['organizations' => function ($q) use ($orgId) {
                        $q->where('organizations.id', $orgId);
                    }])
                    ->whereHas('organizations', fn($q) => $q->where('organizations.id', $orgId))
                    ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                        $q->where('name', 'like', "%{$request->search}%")
                          ->orWhere('email', 'like', "%{$request->search}%");
                    }))
                    ->latest()
                    ->paginate(20);

        return view('teachers.index', compact('teachers'));
    }

    public function create()
    {
        $organization = Organization::find(auth()->user()->organization_id);
        return view('teachers.create', compact('organization'));
    }

    public function store(Request $request)
    {
        $orgId = auth()->user()->organization_id;

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:teachers,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:lead,contributor',
            'bio'      => 'nullable|string',
            'phone'    => 'nullable|string|max:20',
            'website'  => 'nullable|url|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'bio', 'phone', 'website']);
        $data['password']  = Hash::make($request->password);
        $data['is_active'] = true;

        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')->store('teachers/avatars', 'r2');
        }

        $teacher = Teacher::create($data);

        // Attach to org
        $teacher->organizations()->attach($orgId, [
            'role'       => $request->role,
            'status'     => 'active',
            'invited_by' => auth()->id(),
        ]);

        return redirect()->route('teachers.index')
                         ->with('success', "Teacher {$teacher->name} added successfully.");
    }

    public function show(Teacher $teacher)
    {
        return redirect()->route('teachers.edit', $teacher);
    }

    public function edit(Teacher $teacher)
    {
        $orgId        = auth()->user()->organization_id;
        $organization = Organization::find($orgId);
        $pivot        = $teacher->organizations()->where('organizations.id', $orgId)->first()?->pivot;

        return view('teachers.edit', compact('teacher', 'organization', 'pivot'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $orgId = auth()->user()->organization_id;

        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:teachers,email,' . $teacher->id,
            'password'      => 'nullable|string|min:8|confirmed',
            'role'          => 'required|in:lead,contributor',
            'bio'           => 'nullable|string',
            'phone'         => 'nullable|string|max:20',
            'website'       => 'nullable|url|max:255',
            'is_active'     => 'boolean',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'bio', 'phone', 'website']);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('profile_image')) {
            if ($teacher->profile_image) Storage::disk('r2')->delete($teacher->profile_image);
            $data['profile_image'] = $request->file('profile_image')->store('teachers/avatars', 'r2');
        }

        $teacher->update($data);

        // Update pivot role/status
        $teacher->organizations()->updateExistingPivot($orgId, [
            'role' => $request->role,
        ]);

        return redirect()->route('teachers.index')
                         ->with('success', 'Teacher updated successfully.');
    }

    public function destroy(Teacher $teacher)
    {
        $orgId = auth()->user()->organization_id;

        // Only detach from this org (don't delete teacher if they belong to other orgs)
        $teacher->organizations()->detach($orgId);

        if ($teacher->organizations()->count() === 0) {
            if ($teacher->profile_image) Storage::disk('r2')->delete($teacher->profile_image);
            $teacher->delete();
        }

        return redirect()->route('teachers.index')
                         ->with('success', 'Teacher removed from your organization.');
    }
}
