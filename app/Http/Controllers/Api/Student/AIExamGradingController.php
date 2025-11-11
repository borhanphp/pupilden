<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Question;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class AIExamGradingController extends Controller
{
    /**
     * Auto-grade short answer questions using AI
     */
    public function aiGradeAnswer(Request $request, $attemptId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'answer_id' => 'required|exists:answers,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $student = auth('student')->user();

            // Get the answer
            $answer = Answer::where('id', $request->answer_id)
                ->where('attempt_id', $attemptId)
                ->firstOrFail();

            // Verify the attempt belongs to the student
            if ($answer->attempt->student_id !== $student->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Get the question
            $question = $answer->question;

            // Check if it's a short answer question
            if ($question->type !== 'short_answer') {
                return response()->json([
                    'success' => false,
                    'message' => 'AI grading is only available for short answer questions'
                ], 400);
            }

            // Grade using AI
            $gradingResult = $this->gradeWithAI($question, $answer->answer_text);

            // Update the answer
            $answer->update([
                'marks_awarded' => $gradingResult['marks_awarded'],
                'is_correct' => $gradingResult['is_correct'],
                'updated_by' => $student->id
            ]);

            // Recalculate exam score
            $answer->attempt->calculateScore();

            return response()->json([
                'success' => true,
                'message' => 'Answer graded successfully using AI',
                'data' => [
                    'answer_id' => $answer->id,
                    'question_id' => $question->id,
                    'marks_awarded' => $gradingResult['marks_awarded'],
                    'total_marks' => $question->marks,
                    'is_correct' => $gradingResult['is_correct'],
                    'ai_feedback' => $gradingResult['feedback'] ?? null,
                    'ai_confidence' => $gradingResult['confidence'] ?? null
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error grading answer with AI: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Grade multiple answers at once
     */
    public function aiGradeMultipleAnswers(Request $request, $attemptId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'answer_ids' => 'required|array',
                'answer_ids.*' => 'required|exists:answers,id'
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

            // Get all answers
            $answers = Answer::whereIn('id', $request->answer_ids)
                ->where('attempt_id', $attemptId)
                ->with('question')
                ->get();

            $gradedAnswers = [];

            foreach ($answers as $answer) {
                // Only grade short answer questions
                if ($answer->question->type === 'short_answer') {
                    $gradingResult = $this->gradeWithAI($answer->question, $answer->answer_text);
                    
                    $answer->update([
                        'marks_awarded' => $gradingResult['marks_awarded'],
                        'is_correct' => $gradingResult['is_correct'],
                        'updated_by' => $student->id
                    ]);

                    $gradedAnswers[] = [
                        'answer_id' => $answer->id,
                        'question_id' => $answer->question_id,
                        'marks_awarded' => $gradingResult['marks_awarded'],
                        'is_correct' => $gradingResult['is_correct'],
                        'ai_feedback' => $gradingResult['feedback'] ?? null
                    ];
                }
            }

            // Recalculate exam score
            $attempt->calculateScore();

            return response()->json([
                'success' => true,
                'message' => 'Answers graded successfully using AI',
                'data' => [
                    'attempt_id' => $attempt->id,
                    'score' => $attempt->score,
                    'total_marks' => $attempt->total_marks,
                    'percentage' => $attempt->percentage,
                    'is_passed' => $attempt->is_passed,
                    'graded_answers' => $gradedAnswers
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error grading answers with AI: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Grade answer using AI (Google Gemini)
     */
    private function gradeWithAI($question, $studentAnswer)
    {
        // Check if Gemini API key is configured
        $apiKey = config('services.gemini.api_key') ?? config('app.gemini_api_key') ?? env('GEMINI_API_KEY');
        
        if (!$apiKey) {
            throw new \Exception('Google Gemini API key is not configured');
        }

        // Create grading prompt
        $prompt = "You are an exam grading assistant. Grade the following answer.

        Question: {$question->question_text}

        Expected Answer: {$question->correct_answer}

        Student's Answer: {$studentAnswer}

        Total Marks: {$question->marks}

        Please evaluate the student's answer and provide:
        1. A score out of {$question->marks} marks
        2. Whether the answer is correct (true/false)
        3. Brief feedback on the answer

        Respond in JSON format:
        {
            \"marks_awarded\": <number>,
            \"is_correct\": <true/false>,
            \"feedback\": \"<brief feedback>\",
            \"confidence\": <0-100>
        }";

        try {
            // Call Google Gemini API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . $apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.3,
                    'maxOutputTokens' => 500,
                    'topP' => 0.8,
                    'topK' => 40
                ]
            ]);

            $responseData = $response->json();

            if ($response->failed()) {
                throw new \Exception('Google Gemini API request failed: ' . json_encode($responseData));
            }

            // Extract text from response
            $content = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            if (empty($content)) {
                throw new \Exception('Empty response from Gemini API');
            }

            // Parse JSON response
            $gradingData = json_decode($content, true);

            if (!$gradingData) {
                throw new \Exception('Failed to parse AI response');
            }

            // Validate and return grading result
            return [
                'marks_awarded' => min($gradingData['marks_awarded'], $question->marks),
                'is_correct' => $gradingData['is_correct'] ?? false,
                'feedback' => $gradingData['feedback'] ?? '',
                'confidence' => $gradingData['confidence'] ?? 50
            ];

        } catch (\Exception $e) {
            throw new \Exception('AI grading failed: ' . $e->getMessage());
        }
    }

    /**
     * Alternative: Use OpenAI API directly with better error handling
     */
    private function gradeWithAIFallback($question, $studentAnswer)
    {
        try {
            // If AI grading fails, fall back to basic keyword matching
            $studentAnswerLower = strtolower($studentAnswer);
            $correctAnswerLower = strtolower($question->correct_answer);

            // Calculate similarity
            similar_text($studentAnswerLower, $correctAnswerLower, $similarity);

            // Award marks based on similarity
            $marksAwarded = min(($similarity / 100) * $question->marks, $question->marks);
            $marksAwarded = round($marksAwarded);

            return [
                'marks_awarded' => (int)$marksAwarded,
                'is_correct' => $similarity >= 80,
                'feedback' => "AI grading unavailable. Basic evaluation based on keyword matching.",
                'confidence' => min($similarity, 100)
            ];

        } catch (\Exception $e) {
            return [
                'marks_awarded' => 0,
                'is_correct' => false,
                'feedback' => 'Unable to grade this answer automatically.',
                'confidence' => 0
            ];
        }
    }
}

