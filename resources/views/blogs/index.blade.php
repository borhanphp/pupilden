@extends('layouts.app')

@section('title', 'Blog Posts')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-newspaper"></i> Blog Posts
                        </h4>
                        <a href="{{ route('blogs.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Post
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Cover Image</th>
                                    <th>Title</th>
                                    <th>Tags</th>
                                    <th>Status</th>
                                    <th>Published At</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($posts as $post)
                                    <tr>
                                        <td>
                                            @if($post->image)
                                                <img src="{{ \Storage::disk('r2')->url(auth()->user()->organization_id . '/blog_images/' . $post->image) }}" 
                                                     alt="{{ $post->title }}" 
                                                     class="img-thumbnail" 
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $post->title }}</strong>
                                            </div>
                                            <small class="text-muted">{{ Str::limit(strip_tags($post->summary), 60) }}</small>
                                        </td>
                                        <td>
                                            @if($post->tags)
                                                @foreach(explode(',', $post->tags) as $tag)
                                                    <span class="badge bg-secondary mb-1">{{ trim($tag) }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($post->is_published)
                                                <span class="badge bg-success">Published</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Draft</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $post->published_at ? $post->published_at->format('Y-m-d H:i') : '—' }}
                                        </td>
                                        <td>
                                            {{ $post->created_at->format('Y-m-d H:i') }}
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('blogs.edit', $post) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('blogs.destroy', $post) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this blog post?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            <div class="py-4">
                                                <i class="fas fa-newspaper fa-3x mb-3"></i>
                                                <p>No blog posts found.</p>
                                                <a href="{{ route('blogs.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Write your first post
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
