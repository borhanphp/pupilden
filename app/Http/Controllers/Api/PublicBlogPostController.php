<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\BlogPost;
use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicBlogPostController extends BaseController
{
    /**
     * Validate domain and get organization ID
     */
    private function validateDomain(Request $request)
    {
        if (!$request->has('domain_name')) {
            return ['error' => 'Domain name is required'];
        }
        
        $domainName = $request->domain_name;
        if (in_array($domainName, ['localhost', '127.0.0.1', '::1'])) {
            $domain = Domain::where('is_active', true)->first();
        } else {
            $domain = Domain::where('domain_name', $domainName)->first();
        }
        if (!$domain) {
            return ['error' => 'Domain not found'];
        }

        return ['organization_id' => $domain->organization_id];
    }

    /**
     * Get all published blog posts for the organization
     */
    public function index(Request $request)
    {
        try {
            $domainValidation = $this->validateDomain($request);
            if (isset($domainValidation['error'])) {
                return $this->error($domainValidation['error'], ['error' => $domainValidation['error']]);
            }
            
            $organizationId = $domainValidation['organization_id'];
            
            $query = BlogPost::with(['creator'])
                ->where('organization_id', $organizationId)
                ->where('is_published', true)
                ->where(function($q) {
                    $q->whereNull('published_at')
                      ->orWhere('published_at', '<=', now()->addHours(15));
                });

            // Filter by search
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%")
                      ->orWhere('summary', 'like', "%{$search}%")
                      ->orWhere('tags', 'like', "%{$search}%");
                });
            }

            // Filter by tag
            if ($request->has('tag')) {
                $tag = $request->tag;
                $query->where('tags', 'like', "%{$tag}%");
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'published_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            if (in_array($sortBy, ['title', 'published_at', 'created_at'])) {
                // If sorting by published_at, fall back to created_at if null
                if ($sortBy === 'published_at') {
                    $query->orderByRaw('COALESCE(published_at, created_at) ' . $sortOrder);
                } else {
                    $query->orderBy($sortBy, $sortOrder);
                }
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Pagination
            $perPage = $request->get('per_page', 9);
            $posts = $query->paginate($perPage);

            // Transform data
            $posts->getCollection()->transform(function ($post) use ($organizationId) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'summary' => $post->summary,
                    'content' => $post->content,
                    'image_url' => $post->image ? Storage::disk('r2')->url($organizationId . '/blog_images/' . $post->image) : null,
                    'tags' => $post->tags ? array_map('trim', explode(',', $post->tags)) : [],
                    'published_at' => ($post->published_at ?? $post->created_at)->format('Y-m-d H:i:s'),
                    'author' => $post->creator ? [
                        'name' => $post->creator->name,
                        'designation' => $post->creator->designation,
                    ] : null,
                    'meta_title' => $post->meta_title,
                    'meta_description' => $post->meta_description,
                    'meta_keywords' => $post->meta_keywords,
                ];
            });

            return $this->success('Blog posts retrieved successfully', [
                'posts' => $posts->items(),
                'pagination' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->error('Error retrieving blog posts', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get details of a single blog post by slug
     */
    public function show(Request $request, $slug)
    {
        try {
            $domainValidation = $this->validateDomain($request);
            if (isset($domainValidation['error'])) {
                return $this->error($domainValidation['error'], ['error' => $domainValidation['error']]);
            }
            
            $organizationId = $domainValidation['organization_id'];

            $post = BlogPost::with(['creator'])
                ->where('organization_id', $organizationId)
                ->where('slug', $slug)
                ->where('is_published', true)
                ->where(function($q) {
                    $q->whereNull('published_at')
                      ->orWhere('published_at', '<=', now()->addHours(15));
                })
                ->firstOrFail();

            $postData = [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'summary' => $post->summary,
                'content' => $post->content,
                'image_url' => $post->image ? Storage::disk('r2')->url($organizationId . '/blog_images/' . $post->image) : null,
                'tags' => $post->tags ? array_map('trim', explode(',', $post->tags)) : [],
                'published_at' => ($post->published_at ?? $post->created_at)->format('Y-m-d H:i:s'),
                'author' => $post->creator ? [
                    'name' => $post->creator->name,
                    'designation' => $post->creator->designation,
                ] : null,
                'meta_title' => $post->meta_title,
                'meta_description' => $post->meta_description,
                'meta_keywords' => $post->meta_keywords,
            ];

            return $this->success('Blog post details retrieved successfully', ['post' => $postData]);
        } catch (\Exception $e) {
            return $this->error('Blog post not found', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get featured blog posts
     */
    public function featured(Request $request)
    {
        try {
            $domainValidation = $this->validateDomain($request);
            if (isset($domainValidation['error'])) {
                return $this->error($domainValidation['error'], ['error' => $domainValidation['error']]);
            }
            
            $organizationId = $domainValidation['organization_id'];
            $limit = $request->get('limit', 3);

            $posts = BlogPost::with(['creator'])
                ->where('organization_id', $organizationId)
                ->where('is_published', true)
                ->where(function($q) {
                    $q->whereNull('published_at')
                      ->orWhere('published_at', '<=', now()->addHours(15));
                })
                ->orderByRaw('COALESCE(published_at, created_at) DESC')
                ->limit($limit)
                ->get();

            $posts->transform(function ($post) use ($organizationId) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'summary' => $post->summary,
                    'image_url' => $post->image ? Storage::disk('r2')->url($organizationId . '/blog_images/' . $post->image) : null,
                    'tags' => $post->tags ? array_map('trim', explode(',', $post->tags)) : [],
                    'published_at' => ($post->published_at ?? $post->created_at)->format('Y-m-d H:i:s'),
                    'author' => $post->creator ? [
                        'name' => $post->creator->name,
                        'designation' => $post->creator->designation,
                    ] : null,
                ];
            });

            return $this->success('Featured blog posts retrieved successfully', ['posts' => $posts]);
        } catch (\Exception $e) {
            return $this->error('Error retrieving featured blog posts', ['error' => $e->getMessage()]);
        }
    }
}
