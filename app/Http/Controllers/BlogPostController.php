<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BlogPostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = BlogPost::where('organization_id', auth()->user()->organization_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('blogs.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('blogs.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'summary' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|string|max:500',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
        ]);

        try {
            $orgId = auth()->user()->organization_id;
            
            // Generate unique slug for this organization
            $slug = Str::slug($request->title);
            $originalSlug = $slug;
            $count = 1;
            while (BlogPost::where('organization_id', $orgId)->where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }

            // Set publication timestamp
            $publishedAt = $request->published_at ? Carbon::parse($request->published_at) : null;
            if ($request->has('is_published') && !$publishedAt) {
                $publishedAt = now();
            }

            $data = [
                'organization_id' => $orgId,
                'title' => $request->title,
                'slug' => $slug,
                'content' => $request->content,
                'summary' => $request->summary,
                'is_published' => $request->has('is_published'),
                'published_at' => $publishedAt,
                'tags' => $request->tags,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id,
            ];

            // Handle featured image upload
            if ($request->hasFile('image')) {
                $folder = $orgId . '/blog_images';
                $imageName = time() . '.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->storeAs($folder, $imageName, 'r2');
                $data['image'] = $imageName;
            }

            BlogPost::create($data);

            return redirect()->route('blogs.index')
                ->with('success', 'Blog post created successfully.');
        } catch (\Exception $e) {
            return redirect()->route('blogs.index')
                ->with('error', 'Error creating blog post: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BlogPost $blog)
    {
        // View not strictly required for admin, but can redirect to show view
        return view('blogs.show', compact('blog'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlogPost $blog)
    {
        // Enforce organization check
        if ($blog->organization_id !== auth()->user()->organization_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('blogs.form', compact('blog'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlogPost $blog)
    {
        // Enforce organization check
        $orgId = auth()->user()->organization_id;
        if ($blog->organization_id !== $orgId) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'summary' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|string|max:500',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
        ]);

        try {
            // Re-generate slug if title changed
            $slug = $blog->slug;
            if ($blog->title !== $request->title) {
                $slug = Str::slug($request->title);
                $originalSlug = $slug;
                $count = 1;
                while (BlogPost::where('organization_id', $orgId)
                    ->where('slug', $slug)
                    ->where('id', '!=', $blog->id)
                    ->exists()) {
                    $slug = $originalSlug . '-' . $count++;
                }
            }

            // Set publication timestamp
            $publishedAt = $request->published_at ? Carbon::parse($request->published_at) : null;
            if ($request->has('is_published') && !$publishedAt) {
                $publishedAt = $blog->published_at ?? now();
            }

            $data = [
                'title' => $request->title,
                'slug' => $slug,
                'content' => $request->content,
                'summary' => $request->summary,
                'is_published' => $request->has('is_published'),
                'published_at' => $publishedAt,
                'tags' => $request->tags,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
                'updated_by' => auth()->user()->id,
            ];

            // Handle featured image upload
            if ($request->hasFile('image')) {
                $folder = $orgId . '/blog_images';
                if ($blog->image) {
                    Storage::disk('r2')->delete($folder . '/' . $blog->image);
                }

                $imageName = time() . '.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->storeAs($folder, $imageName, 'r2');
                $data['image'] = $imageName;
            }

            $blog->update($data);

            return redirect()->route('blogs.index')
                ->with('success', 'Blog post updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('blogs.index')
                ->with('error', 'Error updating blog post: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlogPost $blog)
    {
        // Enforce organization check
        if ($blog->organization_id !== auth()->user()->organization_id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Delete image if exists
            if ($blog->image) {
                $folder = auth()->user()->organization_id . '/blog_images';
                Storage::disk('r2')->delete($folder . '/' . $blog->image);
            }

            $blog->delete();

            return redirect()->route('blogs.index')
                ->with('success', 'Blog post deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('blogs.index')
                ->with('error', 'Error deleting blog post: ' . $e->getMessage());
        }
    }
}
