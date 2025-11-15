@extends('layouts.app')

@section('title', 'SEO Settings')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-search"></i> SEO Settings
                        </h4>
                        <div>
                            <a href="{{ route('seo-settings.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add New SEO Setting
                            </a>
                        </div>
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

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <form method="GET" class="d-flex gap-2">
                                <select name="page_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Pages</option>
                                    @foreach($pages as $page)
                                        <option value="{{ $page->id }}" {{ request('page_id') == $page->id ? 'selected' : '' }}>
                                            {{ $page->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                        <div class="col-md-8">
                            <form method="GET" class="d-flex gap-2">
                                <input type="text" name="search" class="form-control" placeholder="Search by title, description, or keywords..." value="{{ request('search') }}">
                                @if(request('page_id'))
                                    <input type="hidden" name="page_id" value="{{ request('page_id') }}">
                                @endif
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                                @if(request('search') || request('page_id'))
                                    <a href="{{ route('seo-settings.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Page</th>
                                    <th>Meta Title</th>
                                    <th>Meta Description</th>
                                    <th>Keywords</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($seoSettings as $seoSetting)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $seoSetting->page->title ?? 'N/A' }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $seoSetting->page->slug ?? '' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                                                {{ $seoSetting->meta_title ?? '-' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis;">
                                                {{ Str::limit($seoSetting->meta_description ?? '-', 50) }}
                                            </div>
                                        </td>
                                        <td>
                                            <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                                                {{ $seoSetting->keywords ?? '-' }}
                                            </div>
                                        </td>
                                        <td>
                                            <small>{{ $seoSetting->created_at->format('M j, Y') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('seo-settings.show', $seoSetting) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ route('seo-settings.edit', $seoSetting) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('seo-settings.destroy', $seoSetting) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this SEO setting?')">
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
                                        <td colspan="6" class="text-center text-muted">
                                            <div class="py-4">
                                                <i class="fas fa-search fa-3x mb-3"></i>
                                                <p>No SEO settings found.</p>
                                                <a href="{{ route('seo-settings.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Create your first SEO setting
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

