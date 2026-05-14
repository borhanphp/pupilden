@extends('layouts.app')

@section('title', isset($course) ? 'Edit Course' : 'Add New Course')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ isset($course) ? 'Edit Course' : 'Add New Course' }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ isset($course) ? route('courses.update', $course) : route('courses.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if(isset($course))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Course Name</label>
                                            <input type="text" 
                                                   name="name" 
                                                   id="name" 
                                                   value="{{ old('name', $course->name ?? '') }}"
                                                   class="form-control @error('name') is-invalid @enderror"
                                                   placeholder="Enter course name"
                                                   required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Price</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" 
                                                       name="price" 
                                                       id="price" 
                                                       value="{{ old('price', $course->price ?? '') }}"
                                                       class="form-control @error('price') is-invalid @enderror"
                                                       placeholder="0.00"
                                                       step="0.01"
                                                       min="0">
                                            </div>
                                            @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="course_category_id" class="form-label">Category</label>
                                            <select name="course_category_id" 
                                                    id="course_category_id" 
                                                    class="form-select @error('course_category_id') is-invalid @enderror">
                                                <option value="">Select a category</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" 
                                                            {{ old('course_category_id', $course->course_category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('course_category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="course_sub_category_id" class="form-label">Sub-Category</label>
                                            <select name="course_sub_category_id" 
                                                    id="course_sub_category_id" 
                                                    class="form-select @error('course_sub_category_id') is-invalid @enderror">
                                                <option value="">Select a sub-category</option>
                                                @foreach($subCategories as $subCategory)
                                                    <option value="{{ $subCategory->id }}" 
                                                            {{ old('course_sub_category_id', $course->course_sub_category_id ?? '') == $subCategory->id ? 'selected' : '' }}>
                                                        {{ $subCategory->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('course_sub_category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="level" class="form-label">Level</label>
                                            <select name="level" 
                                                    id="level" 
                                                    class="form-select @error('level') is-invalid @enderror">
                                                <option value="">Select level</option>
                                                <option value="Beginner" {{ old('level', $course->level ?? '') == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                                                <option value="Intermediate" {{ old('level', $course->level ?? '') == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                                                <option value="Advanced" {{ old('level', $course->level ?? '') == 'Advanced' ? 'selected' : '' }}>Advanced</option>
                                            </select>
                                            @error('level')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="duration" class="form-label">Duration</label>
                                            <input type="text" 
                                                   name="duration" 
                                                   id="duration" 
                                                   value="{{ old('duration', $course->duration ?? '') }}"
                                                   class="form-control @error('duration') is-invalid @enderror"
                                                   placeholder="e.g., 10 hours, 6 weeks">
                                            @error('duration')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="language" class="form-label">Language</label>
                                            <input type="text" 
                                                   name="language" 
                                                   id="language" 
                                                   value="{{ old('language', $course->language ?? '') }}"
                                                   class="form-control @error('language') is-invalid @enderror"
                                                   placeholder="e.g., English, Spanish">
                                            @error('language')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                @include('partials.summernote-field', [
                                    'value' => old('description', $course->description ?? ''),
                                    'placeholder' => 'Enter course description',
                                ])

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="tags" class="form-label">Tags</label>
                                            <input type="text" 
                                                   name="tags" 
                                                   id="tags" 
                                                   value="{{ old('tags', $course->tags ?? '') }}"
                                                   class="form-control @error('tags') is-invalid @enderror"
                                                   placeholder="e.g., programming, web development, javascript">
                                            @error('tags')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="keywords" class="form-label">Keywords</label>
                                            <input type="text" 
                                                   name="keywords" 
                                                   id="keywords" 
                                                   value="{{ old('keywords', $course->keywords ?? '') }}"
                                                   class="form-control @error('keywords') is-invalid @enderror"
                                                   placeholder="SEO keywords">
                                            @error('keywords')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="image" class="form-label">Course Image</label>
                                    <input type="file" 
                                           name="image" 
                                           id="image" 
                                           class="form-control @error('image') is-invalid @enderror"
                                           accept="image/*">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Max size: 2MB. Supported formats: JPEG, PNG, JPG, GIF
                                        Image size: 1050x600 or 7:4 aspect ratio
                                    </div>
                                    @if(isset($course) && $course->image)
                                        <div class="mt-2">
                                            <img src="{{ \Storage::disk('r2')->url(auth()->user()->organization_id . '/course_images/' . $course->image) }}" 
                                                 alt="{{ $course->name }}" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 200px;">
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               name="is_published" 
                                               id="is_published" 
                                               value="1"
                                               {{ old('is_published', $course->is_published ?? false) ? 'checked' : '' }}
                                               class="form-check-input">
                                        <label for="is_published" class="form-check-label">
                                            Published
                                        </label>
                                    </div>
                                    <div class="form-text">Published courses are visible to students</div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               name="is_active" 
                                               id="is_active" 
                                               value="1"
                                               {{ old('is_active', $course->is_active ?? true) ? 'checked' : '' }}
                                               class="form-check-input">
                                        <label for="is_active" class="form-check-label">
                                            Active
                                        </label>
                                    </div>
                                    <div class="form-text">Active courses can be enrolled in</div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               name="is_featured" 
                                               id="is_featured" 
                                               value="1"
                                               {{ old('is_featured', $course->is_featured ?? false) ? 'checked' : '' }}
                                               class="form-check-input">
                                        <label for="is_featured" class="form-check-label">
                                            Featured
                                        </label>
                                    </div>
                                    <div class="form-text">Featured courses appear prominently</div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               name="is_archived" 
                                               id="is_archived" 
                                               value="1"
                                               {{ old('is_archived', $course->is_archived ?? false) ? 'checked' : '' }}
                                               class="form-check-input">
                                        <label for="is_archived" class="form-check-label">
                                            Archived
                                        </label>
                                    </div>
                                    <div class="form-text">Archived courses are hidden from students</div>
                                </div>

                                @if(isset($course))
                                    <div class="mb-3">
                                        <label class="form-label">Course Slug</label>
                                        <input type="text" 
                                               value="{{ $course->slug }}" 
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
                            <a href="{{ route('courses.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ isset($course) ? 'Update Course' : 'Create Course' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
