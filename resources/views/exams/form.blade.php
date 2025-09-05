@extends('layouts.app')

@section('title', isset($exam) ? 'Edit Exam' : 'Add New Exam')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ isset($exam) ? 'Edit Exam' : 'Add New Exam' }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ isset($exam) ? route('exams.update', $exam) : route('exams.store') }}" method="POST">
                        @csrf
                        @if(isset($exam))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="course_id" class="form-label">Course</label>
                                    <select name="course_id" 
                                            id="course_id" 
                                            class="form-select @error('course_id') is-invalid @enderror"
                                            {{ isset($courseId) ? 'readonly' : '' }}>
                                        <option value="">Select a course</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" 
                                                    {{ old('course_id', $exam->course_id ?? $courseId ?? '') == $course->id ? 'selected' : '' }}>
                                                {{ $course->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('course_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="title" class="form-label">Exam Title</label>
                                    <input type="text" 
                                           name="title" 
                                           id="title" 
                                           value="{{ old('title', $exam->title ?? '') }}"
                                           class="form-control @error('title') is-invalid @enderror"
                                           placeholder="Enter exam title"
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="type" class="form-label">Exam Type</label>
                                            <select name="type" 
                                                    id="type" 
                                                    class="form-select @error('type') is-invalid @enderror">
                                                <option value="pre_course" {{ old('type', $exam->type ?? '') == 'pre_course' ? 'selected' : '' }}>Pre-Course Assessment</option>
                                                <option value="final_exam" {{ old('type', $exam->type ?? '') == 'final_exam' ? 'selected' : '' }}>Final Exam</option>
                                            </select>
                                            @error('type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="pass_mark" class="form-label">Pass Mark (%)</label>
                                            <input type="number" 
                                                   name="pass_mark" 
                                                   id="pass_mark" 
                                                   value="{{ old('pass_mark', $exam->pass_mark ?? '') }}"
                                                   class="form-control @error('pass_mark') is-invalid @enderror"
                                                   placeholder="e.g., 70"
                                                   min="0"
                                                   max="100"
                                                   required>
                                            @error('pass_mark')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               name="is_published" 
                                               id="is_published" 
                                               value="1"
                                               {{ old('is_published', $exam->is_published ?? false) ? 'checked' : '' }}
                                               class="form-check-input">
                                        <label for="is_published" class="form-check-label">
                                            Publish Exam
                                        </label>
                                    </div>
                                    <div class="form-text">Published exams are available to students</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Exam Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Exam Types</label>
                                            <ul class="list-unstyled small">
                                                <li><strong>Pre-Course Assessment:</strong> Taken before starting the course</li>
                                                <li><strong>Final Exam:</strong> Taken after completing the course</li>
                                            </ul>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Pass Mark</label>
                                            <p class="form-control-plaintext small">
                                                The minimum percentage score required to pass the exam (0-100%)
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="card bg-light mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Next Steps</h5>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled small">
                                            <li><i class="fas fa-check text-success me-2"></i>Create exam questions</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Set up exam settings</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Publish when ready</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Monitor student attempts</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ isset($exam) ? route('exams.index', $exam->course_id) : route('exams.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ isset($exam) ? 'Update Exam' : 'Create Exam' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
