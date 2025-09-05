@extends('layouts.app')

@section('title', 'Exams')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-clipboard-list"></i> Exams
                            @if(isset($courseId))
                                <small class="text-muted">for {{ $exams->first()->course->name ?? 'Course' }}</small>
                            @endif
                        </h4>
                        <div>
                            @if(isset($courseId))
                                <a href="{{ route('exams.create', $courseId) }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Exam to Course
                                </a>
                            @else
                                <a href="{{ route('exams.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add New Exam
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Course</th>
                                    <th>Type</th>
                                    <th>Pass Mark</th>
                                    <th>Questions</th>
                                    <th>Attempts</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($exams as $exam)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $exam->title }}</strong>
                                                <br>
                                                <small class="text-muted">Created {{ $exam->created_at->format('M j, Y') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($exam->course)
                                                <a href="{{ route('courses.show', $exam->course) }}" class="text-decoration-none">
                                                    <span class="badge bg-primary">{{ $exam->course->name }}</span>
                                                </a>
                                            @else
                                                <span class="text-muted">No course</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $exam->type === 'pre_course' ? 'info' : 'warning' }}">
                                                {{ $exam->type_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $exam->pass_mark }}%</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $exam->questions_count }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">{{ $exam->attempts_count }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $exam->status_badge_class }}">
                                                {{ $exam->status_text }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('exams.edit', $exam) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a href="{{ route('exams.show', $exam) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <form action="{{ route('exams.toggle-published', $exam) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-{{ $exam->is_published ? 'warning' : 'success' }}" 
                                                            title="{{ $exam->is_published ? 'Unpublish' : 'Publish' }}">
                                                        <i class="fas fa-{{ $exam->is_published ? 'eye-slash' : 'eye' }}"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('exams.destroy', $exam) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this exam?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">
                                            <div class="py-4">
                                                <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                                                <p>No exams found.</p>
                                                @if(isset($courseId))
                                                    <a href="{{ route('exams.create', $courseId) }}" class="btn btn-primary">
                                                        <i class="fas fa-plus"></i> Add your first exam to this course
                                                    </a>
                                                @else
                                                    <a href="{{ route('exams.create') }}" class="btn btn-primary">
                                                        <i class="fas fa-plus"></i> Add your first exam
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
