<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\SectionLayout;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index(Page $page)
    {
        // Verify page belongs to user's organization
        if ($page->organization_id !== auth()->user()->organization_id) {
            return redirect()->route('pages.index')->with('error', 'Unauthorized access.');
        }

        $sections = $page->sections()->orderBy('order')->get();
        $layouts = SectionLayout::all();

        return view('sections.index', compact('page', 'sections', 'layouts'));
    }

    public function store(Request $request, Page $page)
    {
        // Verify page belongs to user's organization
        if ($page->organization_id !== auth()->user()->organization_id) {
            return redirect()->route('pages.index')->with('error', 'Unauthorized access.');
        }

        $validated = $request->validate([
            'section_type' => 'required',
            'layout_id' => 'nullable|exists:section_layouts,id',
            'title' => 'nullable|string',
        ]);

        // Ensure page_id is set
        if (!$page || !$page->id) {
            return back()->with('error', 'Invalid page specified.');
        }

        $validated['page_id'] = $page->id;
        $maxOrder = $page->sections()->max('order');
        $validated['order'] = ($maxOrder !== null) ? $maxOrder + 1 : 0;
        $validated['is_active'] = true; // Default to active

        try {
            PageSection::create($validated);
            return back()->with('success', 'Section added!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error creating section: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, PageSection $section)
    {
        $validated = $request->validate([
            'title' => 'nullable|string',
            'layout_id' => 'nullable|exists:section_layouts,id',
            'is_active' => 'boolean',
            'order' => 'integer',
        ]);

        $section->update($validated);

        return back()->with('success', 'Section updated!');
    }

    public function destroy(PageSection $section)
    {
        $section->delete();
        return back()->with('success', 'Section removed');
    }
}

