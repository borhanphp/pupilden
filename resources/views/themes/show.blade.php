@extends('layouts.app')

@section('title', 'Theme Details')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-palette"></i> Theme Details
                        </h4>
                        <div>
                            <a href="{{ route('themes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                            <a href="{{ route('themes.edit', $theme) }}" class="btn btn-primary">
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
                                    <td>{{ $theme->id }}</td>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td><strong>{{ $theme->name }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Slug</th>
                                    <td><code>{{ $theme->slug }}</code></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge {{ $theme->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $theme->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Preview Image</th>
                                    <td>
                                        @if($theme->preview_image)
                                            <img src="{{ $theme->preview_image }}" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                                            <br>
                                            <small><a href="{{ $theme->preview_image }}" target="_blank">{{ $theme->preview_image }}</a></small>
                                        @else
                                            <span class="text-muted">No preview image</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $theme->created_at->format('F j, Y, g:i a') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ $theme->updated_at->format('F j, Y, g:i a') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Available Sections</h5>
                            @if($theme->available_sections && count($theme->available_sections) > 0)
                                <div class="card bg-light">
                                    <div class="card-body">
                                        @foreach($theme->available_sections as $section)
                                            <span class="badge bg-info me-2 mb-2">{{ $section }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No sections configured for this theme.
                                </div>
                            @endif

                            <h5 class="mb-3 mt-4">Organizations Using This Theme</h5>
                            @if($theme->organizationThemes->count() > 0)
                                <div class="list-group">
                                    @foreach($theme->organizationThemes as $orgTheme)
                                        <div class="list-group-item">
                                            <strong>{{ $orgTheme->organization->name ?? 'N/A' }}</strong>
                                            <br>
                                            <small class="text-muted">Assigned {{ $orgTheme->created_at->format('M j, Y') }}</small>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> No organizations are using this theme yet.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('themes.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                                <div>
                                    <a href="{{ route('themes.edit', $theme) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('themes.destroy', $theme) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this theme?')">
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

