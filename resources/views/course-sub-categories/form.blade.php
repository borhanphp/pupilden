@extends('layouts.app')

@section('title', isset($courseSubCategory) ? 'Edit Course Sub-Category' : 'Add New Course Sub-Category')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ isset($courseSubCategory) ? 'Edit Course Sub-Category' : 'Add New Course Sub-Category' }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ isset($courseSubCategory) ? route('course-sub-categories.update', $courseSubCategory) : route('course-sub-categories.store') }}" method="POST">
                        @csrf
                        @if(isset($courseSubCategory))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="course_category_id" class="form-label">Parent Category</label>
                                    <select name="course_category_id" 
                                            id="course_category_id" 
                                            class="form-select @error('course_category_id') is-invalid @enderror"
                                            required>
                                        <option value="">Select a parent category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                    {{ old('course_category_id', $courseSubCategory->course_category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('course_category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="name" class="form-label">Sub-Category Name</label>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           value="{{ old('name', $courseSubCategory->name ?? '') }}"
                                           class="form-control @error('name') is-invalid @enderror"
                                           placeholder="Enter sub-category name"
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
                                              placeholder="Enter sub-category description">{{ old('description', $courseSubCategory->description ?? '') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               name="is_active" 
                                               id="is_active" 
                                               value="1"
                                               {{ old('is_active', $courseSubCategory->is_active ?? true) ? 'checked' : '' }}
                                               class="form-check-input">
                                        <label for="is_active" class="form-check-label">
                                            Active Sub-Category
                                        </label>
                                    </div>
                                    <div class="form-text">
                                        Inactive sub-categories won't be visible to students
                                    </div>
                                </div>

                                @if(isset($courseSubCategory))
                                    <div class="mb-3">
                                        <label class="form-label">Sub-Category Slug</label>
                                        <input type="text" 
                                               value="{{ $courseSubCategory->slug }}" 
                                               class="form-control" 
                                               readonly>
                                        <div class="form-text">
                                            Slug is automatically generated from the name
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Created</label>
                                        <input type="text" 
                                               value="{{ $courseSubCategory->created_at->format('M j, Y g:i A') }}" 
                                               class="form-control" 
                                               readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Last Updated</label>
                                        <input type="text" 
                                               value="{{ $courseSubCategory->updated_at->format('M j, Y g:i A') }}" 
                                               class="form-control" 
                                               readonly>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('course-sub-categories.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ isset($courseSubCategory) ? 'Update Sub-Category' : 'Create Sub-Category' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
