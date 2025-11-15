@extends('layouts.app')

@section('title', 'Section Layouts')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-th-large"></i> Section Layouts
                        </h4>
                        <div>
                            <a href="{{ route('section-layouts.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add New Layout
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

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Slug</th>
                                    <th>Layout Config</th>
                                    <th>Sections Using</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sectionLayouts as $layout)
                                    <tr>
                                        <td>
                                            <strong>{{ $layout->name }}</strong>
                                        </td>
                                        <td>
                                            <code>{{ $layout->slug }}</code>
                                        </td>
                                        <td>
                                            @if($layout->layout_config)
                                                <span class="badge bg-info">Configured</span>
                                            @else
                                                <span class="text-muted">No config</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $layout->sections->count() }} sections</span>
                                        </td>
                                        <td>
                                            <small>{{ $layout->created_at->format('M j, Y') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('section-layouts.show', $layout) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ route('section-layouts.edit', $layout) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('section-layouts.destroy', $layout) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this layout?')">
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
                                                <i class="fas fa-th-large fa-3x mb-3"></i>
                                                <p>No section layouts found.</p>
                                                <a href="{{ route('section-layouts.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Create your first layout
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

