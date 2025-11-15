@extends('layouts.app')

@section('title', 'Themes')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-palette"></i> Themes
                        </h4>
                        <div>
                            <a href="{{ route('themes.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add New Theme
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

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Slug</th>
                                    <th>Available Sections</th>
                                    <th>Status</th>
                                    <th>Organizations</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($themes as $theme)
                                    <tr>
                                        <td>
                                            <strong>{{ $theme->name }}</strong>
                                        </td>
                                        <td>
                                            <code>{{ $theme->slug }}</code>
                                        </td>
                                        <td>
                                            @if($theme->available_sections)
                                                @foreach($theme->available_sections as $section)
                                                    <span class="badge bg-info me-1">{{ $section }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">No sections</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $theme->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $theme->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $theme->organizationThemes->count() }} organizations</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('themes.show', $theme) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ route('themes.edit', $theme) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('themes.destroy', $theme) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this theme?')">
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
                                                <i class="fas fa-palette fa-3x mb-3"></i>
                                                <p>No themes found.</p>
                                                <a href="{{ route('themes.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Create your first theme
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

