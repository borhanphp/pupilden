<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TeacherAuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $teacher = Teacher::where('email', $request->email)->first();

        if (!$teacher || !Hash::check($request->password, $teacher->password)) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
        }

        if (!$teacher->is_active) {
            return response()->json(['success' => false, 'message' => 'Your account is inactive. Please contact admin.'], 403);
        }

        $token = $teacher->createToken('teacher-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'token'   => $token,
                'teacher' => $this->formatTeacher($teacher),
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true, 'message' => 'Logged out successfully']);
    }

    public function me(Request $request)
    {
        $teacher = $request->user()->load('activeOrganizations');
        return response()->json(['success' => true, 'data' => $this->formatTeacher($teacher)]);
    }

    public function updateProfile(Request $request)
    {
        $teacher = $request->user();

        $validator = Validator::make($request->all(), [
            'name'          => 'sometimes|string|max:255',
            'bio'           => 'sometimes|nullable|string',
            'website'       => 'sometimes|nullable|url|max:255',
            'phone'         => 'sometimes|nullable|string|max:20',
            'profile_image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'password'      => 'sometimes|nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->only(['name', 'bio', 'website', 'phone']);

        if ($request->hasFile('profile_image')) {
            if ($teacher->profile_image) {
                Storage::disk('r2')->delete($teacher->profile_image);
            }
            $path = $request->file('profile_image')->store('teachers/avatars', 'r2');
            $data['profile_image'] = $path;
        }

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $teacher->update($data);

        return response()->json(['success' => true, 'message' => 'Profile updated', 'data' => $this->formatTeacher($teacher->fresh())]);
    }

    private function formatTeacher(Teacher $teacher): array
    {
        return [
            'id'                => $teacher->id,
            'name'              => $teacher->name,
            'email'             => $teacher->email,
            'bio'               => $teacher->bio,
            'website'           => $teacher->website,
            'phone'             => $teacher->phone,
            'profile_image_url' => $teacher->profile_image_url,
            'is_active'         => $teacher->is_active,
            'organizations'     => $teacher->relationLoaded('activeOrganizations')
                ? $teacher->activeOrganizations->map(fn($org) => [
                    'id'   => $org->id,
                    'name' => $org->name,
                    'slug' => $org->slug,
                    'role' => $org->pivot->role,
                    'logo' => $org->settings?->logo
                        ? Storage::disk('r2')->url($org->settings->logo)
                        : null,
                ])
                : [],
        ];
    }
}
