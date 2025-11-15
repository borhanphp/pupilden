<?php

namespace App\Http\Controllers;

use App\Models\SectionLayout;
use Illuminate\Http\Request;

class SectionLayoutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sectionLayouts = SectionLayout::orderBy('name')->get();
        return view('section-layouts.index', compact('sectionLayouts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('section-layouts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:section_layouts,slug',
            'layout_config' => 'required|string',
        ]);

        // Parse layout_config JSON
        if ($request->has('layout_config') && $request->layout_config) {
            $decoded = json_decode($request->layout_config, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $validated['layout_config'] = $decoded;
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Invalid JSON format for layout config. Please check your JSON syntax.');
            }
        }

        SectionLayout::create($validated);

        return redirect()->route('section-layouts.index')->with('success', 'Section layout created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(SectionLayout $sectionLayout)
    {
        $sectionLayout->load(['sections.page']);
        return view('section-layouts.show', compact('sectionLayout'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SectionLayout $sectionLayout)
    {
        return view('section-layouts.edit', compact('sectionLayout'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SectionLayout $sectionLayout)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:section_layouts,slug,' . $sectionLayout->id,
            'layout_config' => 'required|string',
        ]);

        // Parse layout_config JSON
        if ($request->has('layout_config') && $request->layout_config) {
            $decoded = json_decode($request->layout_config, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $validated['layout_config'] = $decoded;
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Invalid JSON format for layout config. Please check your JSON syntax.');
            }
        }

        $sectionLayout->update($validated);

        return redirect()->route('section-layouts.index')->with('success', 'Section layout updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SectionLayout $sectionLayout)
    {
        $sectionLayout->delete();
        return redirect()->route('section-layouts.index')->with('success', 'Section layout deleted successfully!');
    }
}
