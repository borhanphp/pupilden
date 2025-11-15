<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $themes = Theme::orderBy('name')->get();
        return view('themes.index', compact('themes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('themes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:themes,slug',
            'preview_image' => 'nullable|string|max:255',
            'available_sections' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Parse available_sections if provided as JSON string
        if ($request->has('available_sections') && $request->available_sections) {
            $decoded = json_decode($request->available_sections, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $validated['available_sections'] = $decoded;
            } else {
                // If not valid JSON, try splitting by comma
                $sections = array_map('trim', explode(',', $request->available_sections));
                $validated['available_sections'] = array_filter($sections);
            }
        } else {
            $validated['available_sections'] = null;
        }

        $validated['is_active'] = $request->has('is_active') ? true : false;

        Theme::create($validated);

        return redirect()->route('themes.index')->with('success', 'Theme created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Theme $theme)
    {
        $theme->load(['organizationThemes.organization']);
        return view('themes.show', compact('theme'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Theme $theme)
    {
        return view('themes.edit', compact('theme'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Theme $theme)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:themes,slug,' . $theme->id,
            'preview_image' => 'nullable|string|max:255',
            'available_sections' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Parse available_sections if provided as JSON string
        if ($request->has('available_sections') && $request->available_sections) {
            $decoded = json_decode($request->available_sections, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $validated['available_sections'] = $decoded;
            } else {
                // If not valid JSON, try splitting by comma
                $sections = array_map('trim', explode(',', $request->available_sections));
                $validated['available_sections'] = array_filter($sections);
            }
        } else {
            $validated['available_sections'] = null;
        }

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $theme->update($validated);

        return redirect()->route('themes.index')->with('success', 'Theme updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Theme $theme)
    {
        $theme->delete();
        return redirect()->route('themes.index')->with('success', 'Theme deleted successfully!');
    }
}
