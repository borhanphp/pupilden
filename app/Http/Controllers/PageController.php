<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::where('organization_id', auth()->user()->organization_id)->get();
        return view('pages.index', compact('pages'));
    }

    public function create()
    {
        return view('pages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'slug' => 'required|unique:pages,slug',
            'type' => 'required'
        ]);

        $validated['organization_id'] = auth()->user()->organization_id;

        Page::create($validated);

        return redirect()->route('pages.index')->with('success', 'Page created!');
    }

    public function edit(Page $page)
    {
        return view('pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required',
            'slug' => 'required',
            'type' => 'required'
        ]);

        $page->update($validated);

        return redirect()->route('pages.index')->with('success', 'Page updated!');
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return back()->with('success', 'Page deleted');
    }
}
