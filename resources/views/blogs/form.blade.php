@extends('layouts.app')

@section('title', isset($blog) ? 'Edit Blog Post' : 'Add New Blog Post')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ isset($blog) ? 'Edit Blog Post' : 'Add New Blog Post' }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ isset($blog) ? route('blogs.update', $blog) : route('blogs.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if(isset($blog))
                            @method('PUT')
                        @endif

                        <div class="row">
                            {{-- Main Column (Title, Content, SEO) --}}
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Post Title</label>
                                    <input type="text" 
                                           name="title" 
                                           id="title" 
                                           value="{{ old('title', $blog->title ?? '') }}"
                                           class="form-control @error('title') is-invalid @enderror"
                                           placeholder="Enter post title"
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="summary" class="form-label">Summary / Excerpt</label>
                                    <textarea name="summary" 
                                              id="summary" 
                                              rows="3" 
                                              class="form-control @error('summary') is-invalid @enderror"
                                              placeholder="Briefly describe the article (displayed on post list)..."
                                    >{{ old('summary', $blog->summary ?? '') }}</textarea>
                                    @error('summary')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @include('partials.summernote-field', [
                                    'name' => 'content',
                                    'id' => 'content',
                                    'label' => 'Post Content',
                                    'value' => old('content', $blog->content ?? ''),
                                    'placeholder' => 'Start writing your post content here...',
                                ])

                                {{-- SEO Section --}}
                                <div class="card border mt-4">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0 fw-semibold">
                                            <i class="fas fa-search me-1"></i> Search Engine Optimization (SEO) Settings
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="meta_title" class="form-label">Meta Title</label>
                                            <input type="text" 
                                                   name="meta_title" 
                                                   id="meta_title" 
                                                   value="{{ old('meta_title', $blog->meta_title ?? '') }}"
                                                   class="form-control @error('meta_title') is-invalid @enderror"
                                                   placeholder="Search engine title (defaults to post title)">
                                            @error('meta_title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="meta_description" class="form-label">Meta Description</label>
                                            <textarea name="meta_description" 
                                                      id="meta_description" 
                                                      rows="3" 
                                                      class="form-control @error('meta_description') is-invalid @enderror"
                                                      placeholder="Brief summary for search engine results"
                                            >{{ old('meta_description', $blog->meta_description ?? '') }}</textarea>
                                            @error('meta_description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                            <input type="text" 
                                                   name="meta_keywords" 
                                                   id="meta_keywords" 
                                                   value="{{ old('meta_keywords', $blog->meta_keywords ?? '') }}"
                                                   class="form-control @error('meta_keywords') is-invalid @enderror"
                                                   placeholder="e.g. learning, study tips, backend">
                                            @error('meta_keywords')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Sidebar Column (Image, Publish Status, Tags) --}}
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="image" class="form-label">Featured Image</label>
                                    <input type="file" 
                                           name="image" 
                                           id="image" 
                                           class="form-control @error('image') is-invalid @enderror"
                                           accept="image/*">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Max size: 2MB. Supported formats: JPEG, PNG, JPG, GIF, WEBP.
                                        Recommended aspect ratio: 16:9.
                                    </div>
                                    @if(isset($blog) && $blog->image)
                                        <div class="mt-2">
                                            <img src="{{ \Storage::disk('r2')->url(auth()->user()->organization_id . '/blog_images/' . $blog->image) }}" 
                                                 alt="{{ $blog->title }}" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 100%; max-height: 200px; object-fit: cover;">
                                        </div>
                                    @endif
                                </div>

                                <div class="card border mb-3">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0 fw-semibold">Publish Settings</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check mb-3">
                                            <input type="checkbox" 
                                                   name="is_published" 
                                                   id="is_published" 
                                                   value="1"
                                                   {{ old('is_published', $blog->is_published ?? false) ? 'checked' : '' }}
                                                   class="form-check-input">
                                            <label for="is_published" class="form-check-label">
                                                Published
                                            </label>
                                            <div class="form-text text-muted">
                                                Published posts are visible to readers on the website.
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="published_at" class="form-label">Publication Date</label>
                                            <input type="datetime-local" 
                                                   name="published_at" 
                                                   id="published_at" 
                                                   value="{{ old('published_at', isset($blog) && $blog->published_at ? $blog->published_at->format('Y-m-d\TH:i') : '') }}"
                                                   class="form-control @error('published_at') is-invalid @enderror">
                                            @error('published_at')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text text-muted">
                                                Leave blank to publish immediately upon checking the "Published" box.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="tags" class="form-label">Tags</label>
                                    <input type="text" 
                                           name="tags" 
                                           id="tags" 
                                           value="{{ old('tags', $blog->tags ?? '') }}"
                                           class="form-control @error('tags') is-invalid @enderror"
                                           placeholder="e.g. tutorials, study tips, coding (comma-separated)">
                                    @error('tags')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text text-muted">
                                        Enter tags separated by commas.
                                    </div>
                                </div>

                                @if(isset($blog))
                                    <div class="mb-3">
                                        <label class="form-label">Post Slug</label>
                                        <input type="text" 
                                               value="{{ $blog->slug }}" 
                                               class="form-control" 
                                               readonly>
                                        <div class="form-text">
                                            Slug is automatically generated from the title.
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('blogs.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ isset($blog) ? 'Update Post' : 'Save Post' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
