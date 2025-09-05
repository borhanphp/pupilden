@extends('layouts.app')

@section('title', isset($courseCategory) ? 'Edit Course Category' : 'Add New Course Category')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ isset($courseCategory) ? 'Edit Course Category' : 'Add New Course Category' }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ isset($courseCategory) ? route('course-categories.update', $courseCategory) : route('course-categories.store') }}" method="POST">
                        @csrf
                        @if(isset($courseCategory))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Category Name</label>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           value="{{ old('name', $courseCategory->name ?? '') }}"
                                           class="form-control @error('name') is-invalid @enderror"
                                           placeholder="Enter category name"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" 
                                              id="description" 
                                              class="form-control @error('description') is-invalid @enderror"
                                              rows="4"
                                              placeholder="Enter category description">{{ old('description', $courseCategory->description ?? '') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="icon" class="form-label">Icon Class</label>
                                    <input type="text" 
                                           name="icon" 
                                           id="icon" 
                                           value="{{ old('icon', $courseCategory->icon ?? '') }}"
                                           class="form-control @error('icon') is-invalid @enderror"
                                           placeholder="fas fa-book">
                                    <div class="form-text">
                                        Enter FontAwesome icon class (e.g., fas fa-book, fas fa-code)
                                    </div>
                                    @error('icon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               name="is_active" 
                                               id="is_active" 
                                               value="1"
                                               {{ old('is_active', $courseCategory->is_active ?? true) ? 'checked' : '' }}
                                               class="form-check-input">
                                        <label for="is_active" class="form-check-label">
                                            Active Category
                                        </label>
                                    </div>
                                    <div class="form-text">
                                        Inactive categories won't be visible to students
                                    </div>
                                </div>

                                @if(isset($courseCategory))
                                    <div class="mb-3">
                                        <label class="form-label">Category Slug</label>
                                        <input type="text" 
                                               value="{{ $courseCategory->slug }}" 
                                               class="form-control" 
                                               readonly>
                                        <div class="form-text">
                                            Slug is automatically generated from the name
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('course-categories.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ isset($courseCategory) ? 'Update Category' : 'Create Category' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
