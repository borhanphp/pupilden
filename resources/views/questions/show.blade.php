@extends('layouts.app')

@section('title', 'Question Details')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Question Information</h4>
                        <div class="btn-group" role="group">
                            <a href="{{ route('questions.edit', $question) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit Question
                            </a>
                            <form action="{{ route('questions.duplicate', $question) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-copy"></i> Duplicate
                                </button>
                            </form>
                            <a href="{{ route('questions.index', $question->exam_id) }}" class="btn btn-secondary">
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
                                    <h5 class="card-title mb-0">Question Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Question Text</label>
                                        <div class="border p-3 bg-white rounded">
                                            {{ $question->question_text }}
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Question Type</label>
                                                <div>
                                                    <span class="badge {{ $question->type_badge_class }} fs-6">
                                                        {{ $question->type_label }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Marks</label>
                                                <p class="form-control-plaintext">{{ $question->marks }} marks</p>
                                            </div>
                                        </div>
                                    </div>

                                    @if($question->has_options)
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Answer Options</label>
                                            <div class="border p-3 bg-white rounded">
                                                @foreach($question->formatted_options as $option)
                                                    <div class="mb-2">
                                                        <strong>{{ $option['key'] }}.</strong> {{ $option['value'] }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Correct Answer</label>
                                        <div class="border p-3 bg-success bg-opacity-10 rounded">
                                            <strong>{{ $question->correct_answer_display }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Exam Information</h5>
                                </div>
                                <div class="card-body">
                                    @if($question->exam)
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Exam</label>
                                            <div>
                                                <a href="{{ route('exams.show', $question->exam) }}" class="text-decoration-none">
                                                    <span class="badge bg-primary fs-6">{{ $question->exam->title }}</span>
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Course</label>
                                            <div>
                                                <a href="{{ route('courses.show', $question->exam->course) }}" class="text-decoration-none">
                                                    <span class="badge bg-info fs-6">{{ $question->exam->course->name }}</span>
                                                </a>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Exam Type</label>
                                            <div>
                                                <span class="badge bg-{{ $question->exam->type === 'pre_course' ? 'info' : 'warning' }} fs-6">
                                                    {{ $question->exam->type_label }}
                                                </span>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-muted">No exam assigned</p>
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
                                                <h4 class="text-primary mb-0">{{ $question->marks }}</h4>
                                                <small class="text-muted">Marks</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-success mb-0">{{ $question->answers_count }}</h4>
                                            <small class="text-muted">Answers</small>
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
                                        <p class="form-control-plaintext">{{ $question->created_at->format('M j, Y g:i A') }}</p>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fw-bold">Last Updated</label>
                                        <p class="form-control-plaintext">{{ $question->updated_at->format('M j, Y g:i A') }}</p>
                                    </div>
                                </div>
                            </div>

                            @if($question->creator || $question->updater)
                                <div class="card bg-light mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">User Information</h5>
                                    </div>
                                    <div class="card-body">
                                        @if($question->creator)
                                            <div class="mb-2">
                                                <label class="form-label fw-bold">Created By</label>
                                                <p class="form-control-plaintext">{{ $question->creator->name }}</p>
                                            </div>
                                        @endif
                                        @if($question->updater)
                                            <div class="mb-2">
                                                <label class="form-label fw-bold">Last Updated By</label>
                                                <p class="form-control-plaintext">{{ $question->updater->name }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($question->answers_count > 0)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Student Answers ({{ $question->answers_count }})</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Student</th>
                                                        <th>Answer</th>
                                                        <th>Correct</th>
                                                        <th>Date</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($question->answers->take(10) as $answer)
                                                        <tr>
                                                            <td>{{ $answer->student->name ?? 'Unknown Student' }}</td>
                                                            <td>{{ Str::limit($answer->answer_text ?? 'No answer', 50) }}</td>
                                                            <td>
                                                                <span class="badge bg-{{ $answer->is_correct ? 'success' : 'danger' }}">
                                                                    {{ $answer->is_correct ? 'Correct' : 'Incorrect' }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $answer->created_at->format('M j, Y g:i A') }}</td>
                                                            <td>
                                                                <a href="#" class="btn btn-sm btn-outline-info">
                                                                    <i class="fas fa-eye"></i> View
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted">
                                                                No answers yet
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
