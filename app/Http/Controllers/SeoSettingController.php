<?php

namespace App\Http\Controllers;

use App\Models\SeoSetting;
use App\Models\Page;
use Illuminate\Http\Request;

class SeoSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SeoSetting::with(['page'])
            ->whereHas('page', function($q) {
                $q->where('organization_id', auth()->user()->organization_id);
            });

        // Filter by page if provided
        if ($request->has('page_id') && $request->page_id) {
            $query->where('page_id', $request->page_id);
        }

        // Search by meta_title, meta_description, or keywords
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('meta_title', 'like', "%{$search}%")
                  ->orWhere('meta_description', 'like', "%{$search}%")
                  ->orWhere('keywords', 'like', "%{$search}%");
            });
        }

        $seoSettings = $query->orderBy('created_at', 'desc')->get();

        $pages = Page::where('organization_id', auth()->user()->organization_id)
            ->orderBy('title')
            ->get();

        return view('seo-settings.index', compact('seoSettings', 'pages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $pages = Page::where('organization_id', auth()->user()->organization_id)
            ->orderBy('title')
            ->get();

        $pageId = $request->get('page_id');

        return view('seo-settings.create', compact('pages', 'pageId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'page_id' => 'required|exists:pages,id|unique:seo_settings,page_id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'keywords' => 'nullable|string|max:255',
        ]);

        // Verify page belongs to user's organization
        $page = Page::findOrFail($validated['page_id']);
        if ($page->organization_id !== auth()->user()->organization_id) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Unauthorized access.');
        }

        SeoSetting::create($validated);

        return redirect()->route('seo-settings.index')
            ->with('success', 'SEO setting created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(SeoSetting $seoSetting)
    {
        // Verify SEO setting belongs to user's organization
        if ($seoSetting->page->organization_id !== auth()->user()->organization_id) {
            return redirect()->route('seo-settings.index')
                ->with('error', 'Unauthorized access.');
        }

        $seoSetting->load(['page']);
        return view('seo-settings.show', compact('seoSetting'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SeoSetting $seoSetting)
    {
        // Verify SEO setting belongs to user's organization
        if ($seoSetting->page->organization_id !== auth()->user()->organization_id) {
            return redirect()->route('seo-settings.index')
                ->with('error', 'Unauthorized access.');
        }

        $pages = Page::where('organization_id', auth()->user()->organization_id)
            ->orderBy('title')
            ->get();

        return view('seo-settings.edit', compact('seoSetting', 'pages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SeoSetting $seoSetting)
    {
        // Verify SEO setting belongs to user's organization
        if ($seoSetting->page->organization_id !== auth()->user()->organization_id) {
            return redirect()->route('seo-settings.index')
                ->with('error', 'Unauthorized access.');
        }

        $validated = $request->validate([
            'page_id' => 'required|exists:pages,id|unique:seo_settings,page_id,' . $seoSetting->id,
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'keywords' => 'nullable|string|max:255',
        ]);

        // Verify new page belongs to user's organization
        $page = Page::findOrFail($validated['page_id']);
        if ($page->organization_id !== auth()->user()->organization_id) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Unauthorized access.');
        }

        $seoSetting->update($validated);

        return redirect()->route('seo-settings.index')
            ->with('success', 'SEO setting updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SeoSetting $seoSetting)
    {
        // Verify SEO setting belongs to user's organization
        if ($seoSetting->page->organization_id !== auth()->user()->organization_id) {
            return redirect()->route('seo-settings.index')
                ->with('error', 'Unauthorized access.');
        }

        $seoSetting->delete();

        return redirect()->route('seo-settings.index')
            ->with('success', 'SEO setting deleted successfully!');
    }
}
