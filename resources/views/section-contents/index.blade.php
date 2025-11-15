@extends('layouts.app')

@section('title', 'Section Contents')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-edit"></i> Section Contents
                        </h4>
                        <div>
                            <a href="{{ route('section-contents.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add New Content
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
                                <select name="section_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Sections</option>
                                    @foreach($sections as $section)
                                        <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                            {{ $section->page->title ?? 'N/A' }} - {{ $section->title ?? 'Section ' . $section->id }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                        <div class="col-md-8">
                            <form method="GET" class="d-flex gap-2">
                                <input type="text" name="search" class="form-control" placeholder="Search by key or value..." value="{{ request('search') }}">
                                @if(request('section_id'))
                                    <input type="hidden" name="section_id" value="{{ request('section_id') }}">
                                @endif
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                                @if(request('search') || request('section_id'))
                                    <a href="{{ route('section-contents.index') }}" class="btn btn-outline-secondary">
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
                                    <th>Section</th>
                                    <th>Block Index</th>
                                    <th>Key</th>
                                    <th>Value</th>
                                    <th>Style</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sectionContents as $content)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $content->section->page->title ?? 'N/A' }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $content->section->title ?? 'Section ' . $content->section_id }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $content->block_index }}</span>
                                        </td>
                                        <td>
                                            <code>{{ $content->key }}</code>
                                        </td>
                                        <td>
                                            <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis;">
                                                {{ Str::limit($content->value, 50) }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($content->style)
                                                <span class="badge bg-info">Has styles</span>
                                            @else
                                                <span class="text-muted">No styles</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('section-contents.show', $content) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ route('section-contents.edit', $content) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('section-contents.destroy', $content) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this content?')">
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
                                                <i class="fas fa-edit fa-3x mb-3"></i>
                                                <p>No section contents found.</p>
                                                <a href="{{ route('section-contents.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Create your first content
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

