@extends('layouts.app')

@section('title', 'Edit Page')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Page</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('pages.update', $page) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="title" 
                                   id="title" 
                                   value="{{ old('title', $page->title) }}"
                                   class="form-control @error('title') is-invalid @enderror"
                                   placeholder="About Us"
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="slug" 
                                   id="slug" 
                                   value="{{ old('slug', $page->slug) }}"
                                   class="form-control @error('slug') is-invalid @enderror"
                                   placeholder="about-us"
                                   required>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">URL-friendly version of the title (e.g., about-us, contact)</div>
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Page Type <span class="text-danger">*</span></label>
                            <select name="type" 
                                    id="type" 
                                    class="form-select @error('type') is-invalid @enderror"
                                    required>
                                <option value="">Select type</option>
                                <option value="home" {{ old('type', $page->type) == 'home' ? 'selected' : '' }}>Home</option>
                                <option value="about" {{ old('type', $page->type) == 'about' ? 'selected' : '' }}>About</option>
                                <option value="contact" {{ old('type', $page->type) == 'contact' ? 'selected' : '' }}>Contact</option>
                                <option value="custom" {{ old('type', $page->type) == 'custom' ? 'selected' : '' }}>Custom</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="is_active" 
                                       id="is_active"
                                       {{ old('is_active', $page->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('pages.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Page
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

