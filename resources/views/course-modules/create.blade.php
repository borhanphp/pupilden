@extends('layouts.app')

@section('title', 'Create Course Module')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-plus"></i> Create Course Module
                        </h4>
                        <a href="{{ route('course-modules.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Modules
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('course-modules.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="course_id" class="form-label">Course <span class="text-danger">*</span></label>
                                    <select class="form-select @error('course_id') is-invalid @enderror" id="course_id" name="course_id" required>
                                        <option value="">Select Course</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" {{ old('course_id', $selectedCourseId) == $course->id ? 'selected' : '' }}>
                                                {{ $course->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('course_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="name" class="form-label">Module Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @include('partials.summernote-field', [
                                    'value' => old('description'),
                                ])

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="order" class="form-label">Order</label>
                                            <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                                   id="order" name="order" value="{{ old('order') }}" min="0">
                                            @error('order')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="image" class="form-label">Module Image</label>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                           id="image" name="image" accept="image/*">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Max size: 2MB. Supported formats: JPEG, PNG, JPG, GIF</div>
                                </div>

                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Duration Settings</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="duration_value" class="form-label">Duration Value</label>
                                            <input type="number" class="form-control @error('duration_value') is-invalid @enderror" 
                                                   id="duration_value" name="duration_value" value="{{ old('duration_value') }}" min="0">
                                            @error('duration_value')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="duration_type" class="form-label">Duration Type</label>
                                            <select class="form-select @error('duration_type') is-invalid @enderror" id="duration_type" name="duration_type">
                                                <option value="">Select Type</option>
                                                <option value="0" {{ old('duration_type') == '0' ? 'selected' : '' }}>Minutes</option>
                                                <option value="1" {{ old('duration_type') == '1' ? 'selected' : '' }}>Hours</option>
                                                <option value="2" {{ old('duration_type') == '2' ? 'selected' : '' }}>Days</option>
                                            </select>
                                            @error('duration_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('course-modules.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Module
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

