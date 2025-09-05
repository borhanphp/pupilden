@extends('layouts.app')

@section('title', 'Questions')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-question-circle"></i> Questions
                            @if(isset($examId))
                                <small class="text-muted">for {{ $questions->first()->exam->title ?? 'Exam' }}</small>
                            @endif
                        </h4>
                        <div>
                            @if(isset($examId))
                                <a href="{{ route('questions.create', $examId) }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Question to Exam
                                </a>
                            @else
                                <a href="{{ route('questions.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add New Question
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
                                    <th>Question</th>
                                    <th>Exam</th>
                                    <th>Type</th>
                                    <th>Marks</th>
                                    <th>Options</th>
                                    <th>Correct Answer</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($questions as $question)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ Str::limit($question->question_text, 80) }}</strong>
                                                <br>
                                                <small class="text-muted">Created {{ $question->created_at->format('M j, Y') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($question->exam)
                                                <a href="{{ route('exams.show', $question->exam) }}" class="text-decoration-none">
                                                    <span class="badge bg-primary">{{ $question->exam->title }}</span>
                                                </a>
                                            @else
                                                <span class="text-muted">No exam</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $question->type_badge_class }}">
                                                {{ $question->type_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $question->marks }} marks</span>
                                        </td>
                                        <td>
                                            @if($question->has_options)
                                                <span class="badge bg-info">{{ count($question->options) }} options</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 200px;" title="{{ $question->correct_answer_display }}">
                                                {{ Str::limit($question->correct_answer_display, 30) }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('questions.edit', $question) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a href="{{ route('questions.show', $question) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <form action="{{ route('questions.duplicate', $question) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Duplicate Question">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('questions.destroy', $question) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this question?')">
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
                                        <td colspan="7" class="text-center text-muted">
                                            <div class="py-4">
                                                <i class="fas fa-question-circle fa-3x mb-3"></i>
                                                <p>No questions found.</p>
                                                @if(isset($examId))
                                                    <a href="{{ route('questions.create', $examId) }}" class="btn btn-primary">
                                                        <i class="fas fa-plus"></i> Add your first question to this exam
                                                    </a>
                                                @else
                                                    <a href="{{ route('questions.create') }}" class="btn btn-primary">
                                                        <i class="fas fa-plus"></i> Add your first question
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
