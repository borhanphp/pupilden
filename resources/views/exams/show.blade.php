@extends('layouts.app')

@section('title', 'Exam Details')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Exam Information</h4>
                        <div class="btn-group" role="group">
                            <a href="{{ route('questions.index', $exam->id) }}" class="btn btn-info">
                                <i class="fas fa-question-circle"></i> Manage Questions
                            </a>
                            <a href="{{ route('exams.edit', $exam) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit Exam
                            </a>
                            <form action="{{ route('exams.toggle-published', $exam) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-{{ $exam->is_published ? 'warning' : 'success' }}">
                                    <i class="fas fa-{{ $exam->is_published ? 'eye-slash' : 'eye' }}"></i> 
                                    {{ $exam->is_published ? 'Unpublish' : 'Publish' }}
                                </button>
                            </form>
                            <a href="{{ route('exams.index', $exam->course_id) }}" class="btn btn-secondary">
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
                                    <h5 class="card-title mb-0">Exam Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Title</label>
                                        <h5 class="mb-0">{{ $exam->title }}</h5>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Exam Type</label>
                                                <div>
                                                    <span class="badge bg-{{ $exam->type === 'pre_course' ? 'info' : 'warning' }} fs-6">
                                                        {{ $exam->type_label }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Pass Mark</label>
                                                <p class="form-control-plaintext">{{ $exam->pass_mark }}%</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Status</label>
                                                <div>
                                                    <span class="badge {{ $exam->status_badge_class }} fs-6">
                                                        {{ $exam->status_text }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Questions Count</label>
                                                <p class="form-control-plaintext">{{ $exam->questions_count }} questions</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Course Information</h5>
                                </div>
                                <div class="card-body">
                                    @if($exam->course)
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Course</label>
                                            <div>
                                                <a href="{{ route('courses.show', $exam->course) }}" class="text-decoration-none">
                                                    <span class="badge bg-primary fs-6">{{ $exam->course->name }}</span>
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Course Category</label>
                                            <div>
                                                @if($exam->course->courseCategory)
                                                    <span class="badge bg-info fs-6">{{ $exam->course->courseCategory->name }}</span>
                                                @else
                                                    <span class="text-muted">No category</span>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-muted">No course assigned</p>
                                    @endif
                                </div>
                            </div>

                            <div class="card bg-light mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Statistics</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="border-end">
                                                <h4 class="text-primary mb-0">{{ $exam->questions_count }}</h4>
                                                <small class="text-muted">Questions</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-success mb-0">{{ $exam->attempts_count }}</h4>
                                            <small class="text-muted">Attempts</small>
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
                                        <p class="form-control-plaintext">{{ $exam->created_at->format('M j, Y g:i A') }}</p>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fw-bold">Last Updated</label>
                                        <p class="form-control-plaintext">{{ $exam->updated_at->format('M j, Y g:i A') }}</p>
                                    </div>
                                </div>
                            </div>

                            @if($exam->creator || $exam->updater)
                                <div class="card bg-light mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">User Information</h5>
                                    </div>
                                    <div class="card-body">
                                        @if($exam->creator)
                                            <div class="mb-2">
                                                <label class="form-label fw-bold">Created By</label>
                                                <p class="form-control-plaintext">{{ $exam->creator->name }}</p>
                                            </div>
                                        @endif
                                        @if($exam->updater)
                                            <div class="mb-2">
                                                <label class="form-label fw-bold">Last Updated By</label>
                                                <p class="form-control-plaintext">{{ $exam->updater->name }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($exam->questions_count > 0)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="card-title mb-0">Questions ({{ $exam->questions_count }})</h5>
                                            <a href="#" class="btn btn-sm btn-primary">
                                                <i class="fas fa-plus"></i> Add Question
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Question</th>
                                                        <th>Type</th>
                                                        <th>Points</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($exam->questions as $question)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ Str::limit($question->question_text ?? 'No question text', 50) }}</td>
                                                            <td>
                                                                <span class="badge bg-info">{{ $question->question_type ?? 'Unknown' }}</span>
                                                            </td>
                                                            <td>{{ $question->points ?? 1 }}</td>
                                                            <td>
                                                                <div class="btn-group btn-group-sm">
                                                                    <a href="#" class="btn btn-outline-primary">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <a href="#" class="btn btn-outline-info">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted">
                                                                No questions added yet. 
                                                                <a href="#" class="text-decoration-none">Add your first question</a>
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
                    @endif

                    @if($exam->attempts_count > 0)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Recent Attempts ({{ $exam->attempts_count }})</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Student</th>
                                                        <th>Score</th>
                                                        <th>Status</th>
                                                        <th>Date</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($exam->attempts->take(5) as $attempt)
                                                        <tr>
                                                            <td>{{ $attempt->student->name ?? 'Unknown Student' }}</td>
                                                            <td>{{ $attempt->score ?? 0 }}%</td>
                                                            <td>
                                                                <span class="badge bg-{{ ($attempt->score ?? 0) >= $exam->pass_mark ? 'success' : 'danger' }}">
                                                                    {{ ($attempt->score ?? 0) >= $exam->pass_mark ? 'Passed' : 'Failed' }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $attempt->created_at->format('M j, Y g:i A') }}</td>
                                                            <td>
                                                                <a href="#" class="btn btn-sm btn-outline-info">
                                                                    <i class="fas fa-eye"></i> View
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted">
                                                                No attempts yet
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
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
