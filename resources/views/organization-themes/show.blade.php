@extends('layouts.app')

@section('title', 'Organization Theme Details')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-palette"></i> Organization Theme Details
                        </h4>
                        <div>
                            <a href="{{ route('organization-themes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                            <a href="{{ route('organization-themes.edit', $organizationTheme) }}" class="btn btn-primary">
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
                                    <td>{{ $organizationTheme->id }}</td>
                                </tr>
                                <tr>
                                    <th>Theme</th>
                                    <td>
                                        <strong>{{ $organizationTheme->theme->name ?? 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $organizationTheme->theme->slug ?? '' }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Organization</th>
                                    <td>
                                        <strong>{{ $organizationTheme->organization->name ?? 'N/A' }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Custom Settings</th>
                                    <td>
                                        @if($organizationTheme->custom_settings)
                                            <span class="badge bg-info">Customized</span>
                                        @else
                                            <span class="badge bg-secondary">Default</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $organizationTheme->created_at->format('F j, Y, g:i a') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ $organizationTheme->updated_at->format('F j, Y, g:i a') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Custom Settings</h5>
                            @if($organizationTheme->custom_settings)
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <pre class="mb-0" style="max-height: 400px; overflow-y: auto;">{{ json_encode($organizationTheme->custom_settings, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No custom settings configured. Using default theme settings.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="mb-3">Theme Information</h5>
                            @if($organizationTheme->theme)
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <table class="table table-sm">
                                                    <tr>
                                                        <th width="40%">Theme Name</th>
                                                        <td>{{ $organizationTheme->theme->name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Slug</th>
                                                        <td><code>{{ $organizationTheme->theme->slug }}</code></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Status</th>
                                                        <td>
                                                            <span class="badge {{ $organizationTheme->theme->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                                {{ $organizationTheme->theme->is_active ? 'Active' : 'Inactive' }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    @if($organizationTheme->theme->preview_image)
                                                        <tr>
                                                            <th>Preview Image</th>
                                                            <td>
                                                                <img src="{{ asset('storage/' . $organizationTheme->theme->preview_image) }}" 
                                                                     alt="Preview" 
                                                                     class="img-thumbnail" 
                                                                     style="max-width: 200px;">
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                @if($organizationTheme->theme->available_sections)
                                                    <h6>Available Sections</h6>
                                                    <ul class="list-group">
                                                        @foreach($organizationTheme->theme->available_sections as $section)
                                                            <li class="list-group-item">{{ $section }}</li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> Theme information not available.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('organization-themes.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                                <div>
                                    <a href="{{ route('organization-themes.edit', $organizationTheme) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('organization-themes.destroy', $organizationTheme) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this organization theme?')">
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

