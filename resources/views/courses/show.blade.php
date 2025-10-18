@extends('layouts.app')

@section('title', 'Course Details')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Course Information</h4>
                        <div class="btn-group" role="group">
                            <a href="{{ route('videos.index', $course->id) }}" class="btn btn-info">
                                <i class="fas fa-video"></i> Manage Videos
                            </a>
                            <a href="{{ route('exams.index', $course->id) }}" class="btn btn-warning">
                                <i class="fas fa-clipboard-list"></i> Manage Exams
                            </a>
                            <a href="{{ route('courses.edit', $course) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit Course
                            </a>
                            <a href="{{ route('courses.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Basic Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Course Name</label>
                                        <h5 class="mb-0">{{ $course->name }}</h5>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Slug</label>
                                        <p class="form-control-plaintext"><code>{{ $course->slug }}</code></p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Description</label>
                                        <p class="form-control-plaintext">
                                            {{ $course->description ?: 'No description provided' }}
                                        </p>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Category</label>
                                                <div>
                                                    @if($course->courseCategory)
                                                        <span class="badge bg-primary fs-6">{{ $course->courseCategory->name }}</span>
                                                    @else
                                                        <span class="text-muted">No category</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Sub-Category</label>
                                                <div>
                                                    @if($course->courseSubCategory)
                                                        <span class="badge bg-info fs-6">{{ $course->courseSubCategory->name }}</span>
                                                    @else
                                                        <span class="text-muted">No sub-category</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Level</label>
                                                <p class="form-control-plaintext">{{ $course->level ?: 'Not specified' }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Duration</label>
                                                <p class="form-control-plaintext">{{ $course->duration ?: 'Not specified' }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Language</label>
                                                <p class="form-control-plaintext">{{ $course->language ?: 'Not specified' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Tags</label>
                                                <p class="form-control-plaintext">{{ $course->tags ?: 'No tags' }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Keywords</label>
                                                <p class="form-control-plaintext">{{ $course->keywords ?: 'No keywords' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Course Image</h5>
                                </div>
                                <div class="card-body text-center">
                                    @if($course->image)
                                        <img src="{{ asset('uploads/' . auth()->user()->organization_id . '/course_images/' . $course->image) }}" 
                                             alt="{{ $course->name }}" 
                                             class="img-fluid rounded" 
                                             style="max-width: 100%; height: auto;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center" 
                                             style="height: 200px;">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        </div>
                                        <p class="text-muted mt-2">No image uploaded</p>
                                    @endif
                                </div>
                            </div>

                            <div class="card bg-light mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Status & Statistics</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Price</label>
                                        <div>
                                            @if($course->price > 0)
                                                <span class="text-success fw-bold fs-5">${{ number_format($course->price, 2) }}</span>
                                            @else
                                                <span class="badge bg-success fs-6">Free</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status</label>
                                        <div>
                                            @if($course->is_published)
                                                <span class="badge bg-success">Published</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Draft</span>
                                            @endif
                                            @if($course->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Flags</label>
                                        <div>
                                            @if($course->is_featured)
                                                <span class="badge bg-warning text-dark">Featured</span>
                                            @endif
                                            @if($course->is_archived)
                                                <span class="badge bg-secondary">Archived</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Enrolled Students</label>
                                        <div>
                                            <span class="badge bg-info fs-6">{{ $course->students->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card bg-light mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Timestamps</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <label class="form-label fw-bold">Created</label>
                                        <p class="form-control-plaintext">{{ $course->created_at->format('M j, Y g:i A') }}</p>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fw-bold">Last Updated</label>
                                        <p class="form-control-plaintext">{{ $course->updated_at->format('M j, Y g:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($course->students->count() > 0)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Enrolled Students</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Student Name</th>
                                                        <th>Username</th>
                                                        <th>Email</th>
                                                        <th>Enrolled Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($course->students->take(10) as $student)
                                                        <tr>
                                                            <td>{{ $student->name }}</td>
                                                            <td>{{ $student->username }}</td>
                                                            <td>{{ $student->email }}</td>
                                                            <td>{{ $student->pivot?->created_at?->format('M j, Y') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @if($course->students->count() > 10)
                                            <div class="text-center mt-2">
                                                <small class="text-muted">Showing 10 of {{ $course->students->count() }} students</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
