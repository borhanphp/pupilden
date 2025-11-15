@extends('layouts.app')

@section('title', 'Organization Themes')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-palette"></i> Organization Themes
                        </h4>
                        <div>
                            <a href="{{ route('organization-themes.create') }}" class="btn btn-primary">
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

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Search -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" class="d-flex gap-2">
                                <input type="text" name="search" class="form-control" placeholder="Search by theme name..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                                @if(request('search'))
                                    <a href="{{ route('organization-themes.index') }}" class="btn btn-outline-secondary">
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
                                    <th>Theme</th>
                                    <th>Organization</th>
                                    <th>Custom Settings</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($organizationThemes as $organizationTheme)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $organizationTheme->theme->name ?? 'N/A' }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $organizationTheme->theme->slug ?? '' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $organizationTheme->organization->name ?? 'N/A' }}</strong>
                                            </div>
                                        </td>
                                        <td>
                                            @if($organizationTheme->custom_settings)
                                                <span class="badge bg-info">Customized</span>
                                            @else
                                                <span class="badge bg-secondary">Default</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $organizationTheme->created_at->format('M j, Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $organizationTheme->updated_at->format('M j, Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('organization-themes.show', $organizationTheme) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ route('organization-themes.edit', $organizationTheme) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('organization-themes.destroy', $organizationTheme) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this organization theme?')">
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
                                                <p>No organization themes found.</p>
                                                <a href="{{ route('organization-themes.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Create your first organization theme
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

