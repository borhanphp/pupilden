<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PageSection;
use App\Models\SectionContent;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function index(PageSection $section)
    {
        $contents = $section->contents()->orderBy('block_index')->get();

        return view('contents.index', compact('section', 'contents'));
    }

    public function store(Request $request, PageSection $section)
    {
        $validated = $request->validate([
            'key' => 'required|string',
            'value' => 'nullable',
            'block_index' => 'required|integer',
            'style' => 'nullable|string'
        ]);

        $validated['section_id'] = $section->id;

        // Parse style JSON if provided
        if ($request->has('style') && $request->style) {
            $decoded = json_decode($request->style, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $validated['style'] = $decoded;
            } else {
                $validated['style'] = null;
            }
        } else {
            $validated['style'] = null;
        }

        SectionContent::create($validated);

        return back()->with('success', 'Content added!');
    }

    public function update(Request $request, SectionContent $content)
    {
        $validated = $request->validate([
            'value' => 'nullable',
            'key' => 'required|string',
            'block_index' => 'required|integer',
            'style' => 'nullable|string'
        ]);

        // Parse style JSON if provided
        if ($request->has('style') && $request->style) {
            $decoded = json_decode($request->style, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $validated['style'] = $decoded;
            } else {
                $validated['style'] = $content->style; // Keep existing if invalid
            }
        } else {
            $validated['style'] = null;
        }

        $content->update($validated);

        return back()->with('success', 'Content updated!');
    }

    public function destroy(SectionContent $content)
    {
        $content->delete();
        return back()->with('success', 'Content deleted');
    }
}

