<?php

namespace App\Http\Controllers;

use App\Models\SectionContent;
use App\Models\PageSection;
use Illuminate\Http\Request;

class SectionContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SectionContent::with(['section.page']);

        // Filter by section if provided
        if ($request->has('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        // Search by key or value
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                  ->orWhere('value', 'like', "%{$search}%");
            });
        }

        $sectionContents = $query->orderBy('section_id')->orderBy('block_index')->get();

        $sections = PageSection::with('page')->orderBy('page_id')->orderBy('order')->get();

        return view('section-contents.index', compact('sectionContents', 'sections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $sections = PageSection::with('page')->orderBy('page_id')->orderBy('order')->get();
        $sectionId = $request->get('section_id');

        return view('section-contents.create', compact('sections', 'sectionId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'section_id' => 'required|exists:page_sections,id',
            'key' => 'required|string|max:255',
            'value' => 'nullable',
            'block_index' => 'required|integer|min:0',
            'style' => 'nullable|string'
        ]);

        // Parse style JSON if provided
        if ($request->has('style') && $request->style) {
            $decoded = json_decode($request->style, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $validated['style'] = $decoded;
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Invalid JSON format for style. Please check your JSON syntax.');
            }
        } else {
            $validated['style'] = null;
        }

        SectionContent::create($validated);

        return redirect()->route('section-contents.index')
            ->with('success', 'Section content created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(SectionContent $sectionContent)
    {
        $sectionContent->load(['section.page']);
        return view('section-contents.show', compact('sectionContent'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SectionContent $sectionContent)
    {
        $sections = PageSection::with('page')->orderBy('page_id')->orderBy('order')->get();
        return view('section-contents.edit', compact('sectionContent', 'sections'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SectionContent $sectionContent)
    {
        $validated = $request->validate([
            'section_id' => 'required|exists:page_sections,id',
            'key' => 'required|string|max:255',
            'value' => 'nullable',
            'block_index' => 'required|integer|min:0',
            'style' => 'nullable|string'
        ]);

        // Parse style JSON if provided
        if ($request->has('style') && $request->style) {
            $decoded = json_decode($request->style, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $validated['style'] = $decoded;
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Invalid JSON format for style. Please check your JSON syntax.');
            }
        } else {
            $validated['style'] = null;
        }

        $sectionContent->update($validated);

        return redirect()->route('section-contents.index')
            ->with('success', 'Section content updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SectionContent $sectionContent)
    {
        $sectionContent->delete();
        return redirect()->route('section-contents.index')
            ->with('success', 'Section content deleted successfully!');
    }
}
