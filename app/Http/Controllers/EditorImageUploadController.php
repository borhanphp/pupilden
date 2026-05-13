<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EditorImageUploadController extends Controller
{
    /**
     * Store an image for rich-text editors (Summernote) under the user's organization.
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $orgId = (string) auth()->user()->organization_id;
        $folder = $orgId.'/editor/content';
        if (! Storage::disk('public')->exists($folder)) {
            Storage::disk('public')->makeDirectory($folder);
        }

        $extension = $request->file('file')->getClientOriginalExtension() ?: 'jpg';
        $filename = uniqid('editor_', true).'.'.$extension;
        $request->file('file')->storeAs($folder, $filename, 'public');

        $url = asset('uploads/'.$folder.'/'.$filename);

        return response()->json(['url' => $url]);
    }
}
