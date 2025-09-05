<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    /**
     * Display a listing of questions for a specific exam
     */
    public function index(Request $request, $examId = null)
    {
        try {
            $query = Question::with(['exam', 'creator', 'updater', 'answers']);

            if ($examId) {
                // Get questions for a specific exam
                $exam = Exam::whereHas('course', function($q) {
                    $q->where('organization_id', auth()->user()->organization_id);
                })->findOrFail($examId);
                
                $query->where('exam_id', $examId);
            } else {
                // Get all questions for the organization
                $query->whereHas('exam.course', function($q) {
                    $q->where('organization_id', auth()->user()->organization_id);
                });
            }

            $questions = $query->orderBy('created_at', 'desc')->get();

            if ($request->ajax()) {
                return response()->json(['questions' => $questions]);
            }

            return view('questions.index', compact('questions', 'examId'));

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Error retrieving questions: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error retrieving questions: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new question
     */
    public function create(Request $request, $examId = null)
    {
        try {
            $exams = Exam::whereHas('course', function($q) {
                $q->where('organization_id', auth()->user()->organization_id);
            })
            ->where('is_published', true)
            ->orderBy('title')
            ->get();

            $exam = null;
            if ($examId) {
                $exam = Exam::whereHas('course', function($q) {
                    $q->where('organization_id', auth()->user()->organization_id);
                })->findOrFail($examId);
            }

            return view('questions.form', compact('exams', 'examId', 'exam'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading form: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created question
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'exam_id' => 'required|exists:exams,id',
                'type' => 'required|in:mcq,short_answer',
                'question_text' => 'required|string',
                'options' => 'required_if:type,mcq|array|min:2|max:6',
                'options.*' => 'required|string|max:500',
                'correct_answer' => 'required|string|max:1000',
                'marks' => 'required|integer|min:1|max:100'
            ]);

            // Verify exam belongs to organization
            $exam = Exam::whereHas('course', function($q) {
                $q->where('organization_id', auth()->user()->organization_id);
            })->findOrFail($request->exam_id);

            $questionData = [
                'exam_id' => $request->exam_id,
                'type' => $request->type,
                'question_text' => $request->question_text,
                'correct_answer' => $request->correct_answer,
                'marks' => $request->marks,
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id,
            ];

            // Add options for MCQ questions
            if ($request->type === 'mcq' && $request->has('options')) {
                $questionData['options'] = $request->options;
            }

            $question = Question::create($questionData);

            return redirect()->route('questions.index', $request->exam_id)
                ->with('success', 'Question created successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating question: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified question
     */
    public function show(Question $question)
    {
        try {
            // Verify question belongs to organization
            $question->load(['exam.course', 'creator', 'updater', 'answers']);
            
            if ($question->exam->course->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Unauthorized');
            }

            return view('questions.show', compact('question'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error retrieving question: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified question
     */
    public function edit(Question $question)
    {
        try {
            // Verify question belongs to organization
            if ($question->exam->course->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Unauthorized');
            }

            $exams = Exam::whereHas('course', function($q) {
                $q->where('organization_id', auth()->user()->organization_id);
            })
            ->where('is_published', true)
            ->orderBy('title')
            ->get();

            return view('questions.form', compact('question', 'exams'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading form: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified question
     */
    public function update(Request $request, Question $question)
    {
        try {
            // Verify question belongs to organization
            if ($question->exam->course->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Unauthorized');
            }

            $request->validate([
                'exam_id' => 'required|exists:exams,id',
                'type' => 'required|in:mcq,short_answer',
                'question_text' => 'required|string',
                'options' => 'required_if:type,mcq|array|min:2|max:6',
                'options.*' => 'required|string|max:500',
                'correct_answer' => 'required|string|max:1000',
                'marks' => 'required|integer|min:1|max:100'
            ]);

            // Verify exam belongs to organization
            $exam = Exam::whereHas('course', function($q) {
                $q->where('organization_id', auth()->user()->organization_id);
            })->findOrFail($request->exam_id);

            $questionData = [
                'exam_id' => $request->exam_id,
                'type' => $request->type,
                'question_text' => $request->question_text,
                'correct_answer' => $request->correct_answer,
                'marks' => $request->marks,
                'updated_by' => auth()->user()->id,
            ];

            // Add options for MCQ questions
            if ($request->type === 'mcq' && $request->has('options')) {
                $questionData['options'] = $request->options;
            } else {
                $questionData['options'] = null;
            }

            $question->update($questionData);

            return redirect()->route('questions.index', $request->exam_id)
                ->with('success', 'Question updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating question: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified question
     */
    public function destroy(Question $question)
    {
        try {
            // Verify question belongs to organization
            if ($question->exam->course->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Unauthorized');
            }

            $examId = $question->exam_id;
            $question->delete();

            return redirect()->route('questions.index', $examId)
                ->with('success', 'Question deleted successfully');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting question: ' . $e->getMessage());
        }
    }

    /**
     * Duplicate a question
     */
    public function duplicate(Question $question)
    {
        try {
            // Verify question belongs to organization
            if ($question->exam->course->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Unauthorized');
            }

            $newQuestion = $question->replicate();
            $newQuestion->question_text = $question->question_text . ' (Copy)';
            $newQuestion->created_by = auth()->user()->id;
            $newQuestion->updated_by = auth()->user()->id;
            $newQuestion->save();

            return redirect()->route('questions.index', $question->exam_id)
                ->with('success', 'Question duplicated successfully');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error duplicating question: ' . $e->getMessage());
        }
    }
}