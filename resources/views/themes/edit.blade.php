@extends('layouts.app')

@section('title', 'Edit Theme')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Theme</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('themes.update', $theme) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Theme Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name', $theme->name) }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="Modern Theme"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="slug" 
                                   id="slug" 
                                   value="{{ old('slug', $theme->slug) }}"
                                   class="form-control @error('slug') is-invalid @enderror"
                                   placeholder="modern-theme"
                                   required>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">URL-friendly version of the theme name (e.g., modern-theme)</div>
                        </div>

                        <div class="mb-3">
                            <label for="preview_image" class="form-label">Preview Image URL</label>
                            <input type="text" 
                                   name="preview_image" 
                                   id="preview_image" 
                                   value="{{ old('preview_image', $theme->preview_image) }}"
                                   class="form-control @error('preview_image') is-invalid @enderror"
                                   placeholder="https://example.com/preview.jpg">
                            @error('preview_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">URL to the theme preview image</div>
                        </div>

                        <div class="mb-3">
                            <label for="available_sections" class="form-label">Available Sections</label>
                            <textarea name="available_sections" 
                                      id="available_sections" 
                                      rows="4"
                                      class="form-control @error('available_sections') is-invalid @enderror"
                                      placeholder='["hero", "about", "features", "testimonials", "contact"] or hero, about, features'>{{ old('available_sections', $theme->available_sections ? (is_array($theme->available_sections) ? json_encode($theme->available_sections, JSON_PRETTY_PRINT) : $theme->available_sections) : '') }}</textarea>
                            @error('available_sections')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Enter as JSON array (e.g., ["hero", "about", "features"]) or comma-separated values (e.g., hero, about, features)
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="is_active" 
                                       id="is_active"
                                       {{ old('is_active', $theme->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('themes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Theme
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

