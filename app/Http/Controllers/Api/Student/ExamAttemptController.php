<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ExamAttemptController extends Controller
{
    /**
     * Start an exam attempt
     */
    public function start(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'exam_id' => 'required|exists:exams,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $student = auth('student')->user();
            $examId = $request->exam_id;

            // Get the exam
            $exam = Exam::whereHas('course', function($q) use ($student) {
                $q->where('organization_id', $student->organization_id);
            })
            ->where('is_published', true)
            ->findOrFail($examId);

            // Check if student is enrolled in the course
            if (!$exam->course->isEnrolledBy($student->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be enrolled in this course to take the exam'
                ], 403);
            }

            // Check if already has an in-progress attempt
            $existingAttempt = ExamAttempt::where('exam_id', $examId)
                ->where('student_id', $student->id)
                ->whereNull('submitted_at')
                ->first();

            if ($existingAttempt) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have an in-progress attempt for this exam',
                    'data' => [
                        'attempt_id' => $existingAttempt->id,
                        'attempted_at' => $existingAttempt->attempted_at?->toISOString()
                    ]
                ], 400);
            }

            // Create new attempt
            $attempt = ExamAttempt::create([
                'exam_id' => $examId,
                'student_id' => $student->id,
                'attempted_at' => now(),
                'created_by' => $student->id,
                'updated_by' => $student->id
            ]);

            // Load exam with questions
            $attempt->load(['exam', 'exam.questions']);

            return response()->json([
                'success' => true,
                'message' => 'Exam attempt started successfully',
                'data' => [
                    'attempt_id' => $attempt->id,
                    'exam' => [
                        'id' => $exam->id,
                        'title' => $exam->title,
                        'description' => $exam->description,
                        'type' => $exam->type,
                        'pass_mark' => $exam->pass_mark,
                        'duration' => $exam->duration,
                    ],
                    'questions' => $exam->questions->map(function($question) {
                        return [
                            'id' => $question->id,
                            'type' => $question->type,
                            'question_text' => $question->question_text,
                            'marks' => $question->marks,
                            'options' => $question->type === 'mcq' ? $question->options : null
                        ];
                    }),
                    'attempted_at' => $attempt->attempted_at->toISOString()
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error starting exam attempt: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit an answer for a question
     */
    public function submitAnswer(Request $request, $attemptId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'question_id' => 'required|exists:questions,id',
                'answer_text' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $student = auth('student')->user();

            // Get the attempt
            $attempt = ExamAttempt::where('id', $attemptId)
                ->where('student_id', $student->id)
                ->firstOrFail();

            // Check if already submitted
            if ($attempt->submitted_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'This exam has already been submitted'
                ], 400);
            }

            // Get the question
            $question = Question::where('exam_id', $attempt->exam_id)
                ->findOrFail($request->question_id);

            // Check if answer already exists
            $existingAnswer = Answer::where('attempt_id', $attemptId)
                ->where('question_id', $request->question_id)
                ->first();

            // For MCQ questions, auto-grade the answer
            // For short answer questions, marks will be 0 until manually graded
            $isCorrect = null;
            $marksAwarded = 0;
            
            if ($question->type === 'mcq') {
                // Auto-grade MCQ questions
                $isCorrect = $this->checkAnswerCorrectness($question, $request->answer_text);
                $marksAwarded = $isCorrect ? $question->marks : 0;
            }
            // For short_answer questions, marks_awarded will remain 0 until teacher grades it

            if ($existingAnswer) {
                // Update existing answer
                $existingAnswer->update([
                    'answer_text' => $request->answer_text,
                    'is_correct' => $isCorrect,
                    'marks_awarded' => $marksAwarded,
                    'updated_by' => $student->id
                ]);
                $answer = $existingAnswer;
            } else {
                // Create new answer
                $answer = Answer::create([
                    'attempt_id' => $attemptId,
                    'question_id' => $request->question_id,
                    'answer_text' => $request->answer_text,
                    'is_correct' => $isCorrect,
                    'marks_awarded' => $marksAwarded,
                    'created_by' => $student->id,
                    'updated_by' => $student->id
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Answer submitted successfully',
                'data' => [
                    'answer_id' => $answer->id,
                    'question_id' => $question->id,
                    'is_correct' => $answer->is_correct,
                    'marks_awarded' => $answer->marks_awarded,
                    'total_marks' => $question->marks
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error submitting answer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit the entire exam
     */
    public function submitExam(Request $request, $attemptId)
    {
        try {
            $student = auth('student')->user();

            // Get the attempt
            $attempt = ExamAttempt::with(['exam', 'answers'])
                ->where('id', $attemptId)
                ->where('student_id', $student->id)
                ->firstOrFail();

            // Check if already submitted
            if ($attempt->submitted_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'This exam has already been submitted'
                ], 400);
            }

            // Calculate score
            $totalScore = $attempt->calculateScore();
            
            // Mark as submitted
            $attempt->markAsSubmitted();

            // Reload attempt with relationships
            $attempt->load(['exam', 'answers.question']);

            return response()->json([
                'success' => true,
                'message' => 'Exam submitted successfully',
                'data' => [
                    'attempt_id' => $attempt->id,
                    'score' => $attempt->score,
                    'total_marks' => $attempt->total_marks,
                    'percentage' => $attempt->percentage,
                    'is_passed' => $attempt->is_passed,
                    'pass_mark' => $attempt->exam->pass_mark,
                    'status' => $attempt->status_text,
                    'submitted_at' => $attempt->submitted_at->toISOString(),
                    'answers' => $attempt->answers->map(function($answer) {
                        return [
                            'question_id' => $answer->question_id,
                            'question_text' => $answer->question->question_text,
                            'your_answer' => $answer->answer_text,
                            'correct_answer' => $answer->question->correct_answer,
                            'is_correct' => $answer->is_correct,
                            'marks_awarded' => $answer->marks_awarded,
                            'total_marks' => $answer->question->marks
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error submitting exam: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get student's attempt history
     */
    public function history(Request $request)
    {
        try {
            $student = auth('student')->user();
            
            $perPage = $request->get('per_page', 15);
            $examId = $request->get('exam_id');

            $query = ExamAttempt::with(['exam', 'exam.course'])
                ->where('student_id', $student->id);

            if ($examId) {
                $query->where('exam_id', $examId);
            }

            $attempts = $query->orderBy('created_at', 'desc')
                ->paginate($perPage);

            $attempts->getCollection()->transform(function ($attempt) {
                return [
                    'id' => $attempt->id,
                    'exam' => [
                        'id' => $attempt->exam->id,
                        'title' => $attempt->exam->title,
                        'type' => $attempt->exam->type,
                        'course' => [
                            'id' => $attempt->exam->course->id,
                            'name' => $attempt->exam->course->name
                        ]
                    ],
                    'score' => $attempt->score,
                    'total_marks' => $attempt->total_marks,
                    'percentage' => $attempt->percentage,
                    'is_passed' => $attempt->is_passed,
                    'status' => $attempt->status_text,
                    'attempted_at' => $attempt->attempted_at?->toISOString(),
                    'submitted_at' => $attempt->submitted_at?->toISOString(),
                    'created_at' => $attempt->created_at->toISOString()
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $attempts
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving attempt history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get details of a specific attempt
     */
    public function show($attemptId)
    {
        try {
            $student = auth('student')->user();

            $attempt = ExamAttempt::with(['exam', 'exam.questions', 'answers.question'])
                ->where('id', $attemptId)
                ->where('student_id', $student->id)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $attempt->id,
                    'exam' => [
                        'id' => $attempt->exam->id,
                        'title' => $attempt->exam->title,
                        'description' => $attempt->exam->description,
                        'type' => $attempt->exam->type,
                        'pass_mark' => $attempt->exam->pass_mark,
                        'duration' => $attempt->exam->duration,
                    ],
                    'score' => $attempt->score,
                    'total_marks' => $attempt->total_marks,
                    'percentage' => $attempt->percentage,
                    'is_passed' => $attempt->is_passed,
                    'status' => $attempt->status_text,
                    'attempted_at' => $attempt->attempted_at?->toISOString(),
                    'submitted_at' => $attempt->submitted_at?->toISOString(),
                    'reviewed_at' => $attempt->reviewed_at?->toISOString(),
                    'answers' => $attempt->answers->map(function($answer) {
                        return [
                            'question_id' => $answer->question_id,
                            'question' => [
                                'id' => $answer->question->id,
                                'question_text' => $answer->question->question_text,
                                'type' => $answer->question->type,
                                'correct_answer' => $answer->question->correct_answer,
                                'options' => $answer->question->options,
                                'marks' => $answer->question->marks
                            ],
                            'your_answer' => $answer->answer_text,
                            'is_correct' => $answer->is_correct,
                            'marks_awarded' => $answer->marks_awarded
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving attempt details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update answer marks (for manual grading)
     */
    public function updateAnswerMarks(Request $request, $attemptId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'answers' => 'required|array',
                'answers.*.answer_id' => 'required|exists:answers,id',
                'answers.*.marks_awarded' => 'required|integer|min:0',
                'answers.*.is_correct' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $student = auth('student')->user();

            // Get the attempt
            $attempt = ExamAttempt::where('id', $attemptId)
                ->where('student_id', $student->id)
                ->firstOrFail();

            // Update each answer
            $updatedAnswers = [];
            foreach ($request->answers as $answerData) {
                $answer = Answer::where('id', $answerData['answer_id'])
                    ->where('attempt_id', $attemptId)
                    ->firstOrFail();

                $answer->update([
                    'marks_awarded' => $answerData['marks_awarded'],
                    'is_correct' => $answerData['is_correct'] ?? null
                ]);

                $updatedAnswers[] = $answer;
            }

            // Recalculate exam score
            $attempt->calculateScore();

            return response()->json([
                'success' => true,
                'message' => 'Answer marks updated successfully',
                'data' => [
                    'attempt_id' => $attempt->id,
                    'score' => $attempt->score,
                    'total_marks' => $attempt->total_marks,
                    'percentage' => $attempt->percentage,
                    'is_passed' => $attempt->is_passed,
                    'updated_answers' => array_map(function($answer) {
                        return [
                            'answer_id' => $answer->id,
                            'question_id' => $answer->question_id,
                            'marks_awarded' => $answer->marks_awarded,
                            'is_correct' => $answer->is_correct
                        ];
                    }, $updatedAnswers)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating answer marks: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if answer is correct
     */
    private function checkAnswerCorrectness($question, $answerText)
    {
        if ($question->type === 'mcq') {
            // For MCQ, check if answer matches the correct option
            return strtolower(trim($answerText)) === strtolower(trim($question->correct_answer));
        } else {
            // For short answer, compare the text
            return strtolower(trim($answerText)) === strtolower(trim($question->correct_answer));
        }
    }
}
