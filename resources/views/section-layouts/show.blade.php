@extends('layouts.app')

@section('title', 'Section Layout Details')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-th-large"></i> Section Layout Details
                        </h4>
                        <div>
                            <a href="{{ route('section-layouts.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                            <a href="{{ route('section-layouts.edit', $sectionLayout) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Basic Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">ID</th>
                                    <td>{{ $sectionLayout->id }}</td>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td><strong>{{ $sectionLayout->name }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Slug</th>
                                    <td><code>{{ $sectionLayout->slug }}</code></td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $sectionLayout->created_at->format('F j, Y, g:i a') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ $sectionLayout->updated_at->format('F j, Y, g:i a') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Layout Configuration</h5>
                            @if($sectionLayout->layout_config)
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <pre class="mb-0" style="max-height: 400px; overflow-y: auto;">{{ json_encode($sectionLayout->layout_config, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No layout configuration set.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="mb-3">Sections Using This Layout</h5>
                            @if($sectionLayout->sections->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Section ID</th>
                                                <th>Page</th>
                                                <th>Section Type</th>
                                                <th>Title</th>
                                                <th>Order</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($sectionLayout->sections as $section)
                                                <tr>
                                                    <td>{{ $section->id }}</td>
                                                    <td>
                                                        <a href="{{ route('pages.edit', $section->page) }}">
                                                            {{ $section->page->title }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">{{ $section->section_type }}</span>
                                                    </td>
                                                    <td>{{ $section->title ?? 'Untitled' }}</td>
                                                    <td>{{ $section->order }}</td>
                                                    <td>
                                                        <span class="badge {{ $section->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                            {{ $section->is_active ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> No sections are using this layout yet.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('section-layouts.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                                <div>
                                    <a href="{{ route('section-layouts.edit', $sectionLayout) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('section-layouts.destroy', $sectionLayout) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this layout?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

