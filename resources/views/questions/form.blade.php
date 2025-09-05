@extends('layouts.app')

@section('title', isset($question) ? 'Edit Question' : 'Add New Question')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ isset($question) ? 'Edit Question' : 'Add New Question' }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ isset($question) ? route('questions.update', $question) : route('questions.store') }}" method="POST">
                        @csrf
                        @if(isset($question))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="exam_id" class="form-label">Exam</label>
                                    <select name="exam_id" 
                                            id="exam_id" 
                                            class="form-select @error('exam_id') is-invalid @enderror"
                                            {{ isset($examId) ? 'readonly' : '' }}>
                                        <option value="">Select an exam</option>
                                        @foreach($exams as $exam)
                                            <option value="{{ $exam->id }}" 
                                                    {{ old('exam_id', $question->exam_id ?? $examId ?? '') == $exam->id ? 'selected' : '' }}>
                                                {{ $exam->title }} ({{ $exam->course->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('exam_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="type" class="form-label">Question Type</label>
                                    <select name="type" 
                                            id="type" 
                                            class="form-select @error('type') is-invalid @enderror"
                                            onchange="toggleOptions()">
                                        <option value="mcq" {{ old('type', $question->type ?? '') == 'mcq' ? 'selected' : '' }}>Multiple Choice Question (MCQ)</option>
                                        <option value="short_answer" {{ old('type', $question->type ?? '') == 'short_answer' ? 'selected' : '' }}>Short Answer</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="question_text" class="form-label">Question Text</label>
                                    <textarea name="question_text" 
                                              id="question_text" 
                                              rows="4"
                                              class="form-control @error('question_text') is-invalid @enderror"
                                              placeholder="Enter your question here..."
                                              required>{{ old('question_text', $question->question_text ?? '') }}</textarea>
                                    @error('question_text')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div id="options-section" style="display: none;">
                                    <label class="form-label">Answer Options</label>
                                    <div id="options-container">
                                        @if(isset($question) && $question->type === 'mcq' && $question->options)
                                            @foreach($question->options as $index => $option)
                                                <div class="input-group mb-2">
                                                    <span class="input-group-text">{{ chr(65 + $index) }}</span>
                                                    <input type="text" 
                                                           name="options[]" 
                                                           value="{{ old('options.' . $index, $option) }}"
                                                           class="form-control @error('options.' . $index) is-invalid @enderror"
                                                           placeholder="Option {{ chr(65 + $index) }}">
                                                    @if($index >= 2)
                                                        <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="input-group mb-2">
                                                <span class="input-group-text">A</span>
                                                <input type="text" 
                                                       name="options[]" 
                                                       value="{{ old('options.0', '') }}"
                                                       class="form-control @error('options.0') is-invalid @enderror"
                                                       placeholder="Option A">
                                            </div>
                                            <div class="input-group mb-2">
                                                <span class="input-group-text">B</span>
                                                <input type="text" 
                                                       name="options[]" 
                                                       value="{{ old('options.1', '') }}"
                                                       class="form-control @error('options.1') is-invalid @enderror"
                                                       placeholder="Option B">
                                            </div>
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addOption()">
                                        <i class="fas fa-plus"></i> Add Option
                                    </button>
                                    <small class="form-text text-muted">Minimum 2 options, maximum 6 options</small>
                                </div>

                                <div class="mb-3">
                                    <label for="correct_answer" class="form-label">Correct Answer</label>
                                    <input type="text" 
                                           name="correct_answer" 
                                           id="correct_answer" 
                                           value="{{ old('correct_answer', $question->correct_answer ?? '') }}"
                                           class="form-control @error('correct_answer') is-invalid @enderror"
                                           placeholder="Enter the correct answer"
                                           required>
                                    @error('correct_answer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        For MCQ: Enter the exact text of the correct option<br>
                                        For Short Answer: Enter the expected answer
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="marks" class="form-label">Marks</label>
                                    <input type="number" 
                                           name="marks" 
                                           id="marks" 
                                           value="{{ old('marks', $question->marks ?? 1) }}"
                                           class="form-control @error('marks') is-invalid @enderror"
                                           placeholder="1"
                                           min="1"
                                           max="100"
                                           required>
                                    @error('marks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Question Types</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Multiple Choice (MCQ)</label>
                                            <ul class="list-unstyled small">
                                                <li>• Provide 2-6 answer options</li>
                                                <li>• Students select one correct answer</li>
                                                <li>• Automatic grading</li>
                                            </ul>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Short Answer</label>
                                            <ul class="list-unstyled small">
                                                <li>• Students type their answer</li>
                                                <li>• Manual grading required</li>
                                                <li>• Flexible answer format</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="card bg-light mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Tips</h5>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled small">
                                            <li><i class="fas fa-lightbulb text-warning me-2"></i>Keep questions clear and concise</li>
                                            <li><i class="fas fa-lightbulb text-warning me-2"></i>Use appropriate marks allocation</li>
                                            <li><i class="fas fa-lightbulb text-warning me-2"></i>Test questions before publishing</li>
                                            <li><i class="fas fa-lightbulb text-warning me-2"></i>Consider question difficulty</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ isset($question) ? route('questions.index', $question->exam_id) : route('questions.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ isset($question) ? 'Update Question' : 'Create Question' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let optionCount = {{ isset($question) && $question->type === 'mcq' && $question->options ? count($question->options) : 2 }};

        function toggleOptions() {
            const type = document.getElementById('type').value;
            const optionsSection = document.getElementById('options-section');
            
            if (type === 'mcq') {
                optionsSection.style.display = 'block';
            } else {
                optionsSection.style.display = 'none';
            }
        }

        function addOption() {
            if (optionCount >= 6) {
                alert('Maximum 6 options allowed');
                return;
            }
            
            const container = document.getElementById('options-container');
            const optionLetter = String.fromCharCode(65 + optionCount);
            
            const optionDiv = document.createElement('div');
            optionDiv.className = 'input-group mb-2';
            optionDiv.innerHTML = `
                <span class="input-group-text">${optionLetter}</span>
                <input type="text" 
                       name="options[]" 
                       class="form-control" 
                       placeholder="Option ${optionLetter}">
                <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            
            container.appendChild(optionDiv);
            optionCount++;
        }

        function removeOption(button) {
            if (optionCount <= 2) {
                alert('Minimum 2 options required');
                return;
            }
            
            button.parentElement.remove();
            optionCount--;
            
            // Update option letters
            const options = document.querySelectorAll('#options-container .input-group-text');
            options.forEach((option, index) => {
                option.textContent = String.fromCharCode(65 + index);
            });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleOptions();
        });
    </script>
@endsection
