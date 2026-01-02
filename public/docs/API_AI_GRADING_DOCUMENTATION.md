# AI-Powered Exam Grading API

This document describes the AI-powered grading system for short answer questions using **Google Gemini**.

## Quick Start

1. Get your Gemini API key from [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Add to `.env`: `GEMINI_API_KEY=your-api-key`
3. Call the API endpoints to grade answers automatically!

## Setup

### 1. Get Google Gemini API Key
1. Go to [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Sign in with your Google account
3. Click "Create API Key"
4. Copy your API key

### 2. Configure API Key

Add your Google Gemini API key to Laravel configuration:

**Option 1: Environment File (.env)**
```env
GEMINI_API_KEY=your-api-key-here
```

**Option 2: config/services.php**
```php
'gemini' => [
    'api_key' => env('GEMINI_API_KEY'),
],
```

**Option 3: Direct Configuration**
```php
// In config/app.php or wherever you prefer
'gemini_api_key' => env('GEMINI_API_KEY'),
```

## API Endpoints

### Base URL
```
http://your-domain.com/api/student
```

## 1. AI Grade Single Answer

**Endpoint:** `POST /exam-attempts/{attemptId}/ai-grade`

**Description:** Automatically grade a single short answer question using AI.

### Request Body:
```json
{
  "answer_id": 1
}
```

### Example Request (Postman/cURL):
```bash
POST /api/student/exam-attempts/1/ai-grade
Authorization: Bearer {token}
Content-Type: application/json

{
  "answer_id": 1
}
```

### Success Response (200):
```json
{
  "success": true,
  "message": "Answer graded successfully using AI",
  "data": {
    "answer_id": 1,
    "question_id": 2,
    "marks_awarded": 4,
    "total_marks": 5,
    "is_correct": true,
    "ai_feedback": "The answer demonstrates good understanding of the CSS box model concepts.",
    "ai_confidence": 85
  }
}
```

---

## 2. AI Grade Multiple Answers

**Endpoint:** `POST /exam-attempts/{attemptId}/ai-grade-multiple`

**Description:** Automatically grade multiple short answer questions at once.

### Request Body:
```json
{
  "answer_ids": [1, 2, 3]
}
```

### Example Request (Postman/cURL):
```bash
POST /api/student/exam-attempts/1/ai-grade-multiple
Authorization: Bearer {token}
Content-Type: application/json

{
  "answer_ids": [1, 2, 3]
}
```

### Success Response (200):
```json
{
  "success": true,
  "message": "Answers graded successfully using AI",
  "data": {
    "attempt_id": 1,
    "score": 85,
    "total_marks": 100,
    "percentage": 85.00,
    "is_passed": true,
    "graded_answers": [
      {
        "answer_id": 1,
        "question_id": 2,
        "marks_awarded": 4,
        "is_correct": true,
        "ai_feedback": "Good understanding of concepts"
      },
      {
        "answer_id": 2,
        "question_id": 3,
        "marks_awarded": 3,
        "is_correct": true,
        "ai_feedback": "Partially correct"
      }
    ]
  }
}
```

---

## How AI Grading Works

### 1. AI Evaluation Process
- **Question**: The AI receives the original question text
- **Expected Answer**: The correct answer from the question
- **Student's Answer**: The submitted answer
- **Total Marks**: Maximum marks for the question

### 2. AI Response Format
```json
{
  "marks_awarded": 4,
  "is_correct": true,
  "feedback": "Brief feedback on the answer",
  "confidence": 85
}
```

### 3. Features
- ✅ **Intelligent Grading**: Uses Google Gemini Pro for evaluation
- ✅ **Contextual Understanding**: Understands meaning, not just keywords
- ✅ **Partial Credit**: Can award partial marks for partially correct answers
- ✅ **Feedback**: Provides brief feedback on the answer
- ✅ **Confidence Score**: Indicates AI's confidence in the grading

---

## Examples

### Example 1: Single Answer Grading

**Question**: "Explain the CSS box model"
**Expected Answer**: "The CSS box model describes the layout of elements, consisting of content, padding, border, and margin"
**Student's Answer**: "The box model has content, padding, and margin"
**AI Grade**: 3 out of 5 marks

**Response**:
```json
{
  "marks_awarded": 3,
  "is_correct": false,
  "feedback": "Good understanding but missed the border component. More detail needed.",
  "confidence": 90
}
```

### Example 2: Grading Short Essay

**Question**: "Explain the difference between REST and SOAP APIs"
**Expected Answer**: "REST is stateless and uses HTTP methods, SOAP uses XML and can be stateful"
**Student's Answer**: "REST uses HTTP and is simpler, SOAP uses XML and is more complex"
**AI Grade**: 4 out of 5 marks

**Response**:
```json
{
  "marks_awarded": 4,
  "is_correct": true,
  "feedback": "Accurate comparison with good points about REST simplicity.",
  "confidence": 88
}
```

---

## Configuration

### Google Gemini Model Settings

Currently using: **gemini-pro**

You can modify the model in `AIExamGradingController.php`:
```php
'model' => 'gemini-pro',  // or 'gemini-pro-vision', 'gemini-ultra'
```

### Temperature Settings

Currently using: **0.3** (for consistent, reliable grading)

You can adjust in the controller:
```php
'temperature' => 0.3,  // Lower = more consistent
```

---

## Error Handling

### API Key Not Configured
```json
{
  "success": false,
  "message": "AI grading failed: OpenAI API key is not configured"
}
```

### API Request Failed
```json
{
  "success": false,
  "message": "AI grading failed: OpenAI API request failed"
}
```

### Invalid Response
```json
{
  "success": false,
  "message": "AI grading failed: Failed to parse AI response"
}
```

---

## Cost Considerations

### Google Gemini Pricing (as of 2023)
- **Gemini Pro**: Free tier available (60 requests per minute)
- **Paid tier**: Very affordable pricing
- **Free tier limits**: 15 RPM for free tier

### Estimated Costs
- Average short answer: ~200 tokens
- Free tier: 60 requests per minute (with Google AI Studio free tier)
- 1000 answers: Free or very low cost

---

## Best Practices

### 1. Use AI for Open-Ended Questions
- ✅ Conceptual explanations
- ✅ Comparison questions
- ✅ Analysis questions
- ❌ Mathematical calculations (use programmatic grading)
- ❌ Definitions with exact matches

### 2. Set Clear Evaluation Criteria
- Define what constitutes a complete answer
- Specify key points that must be covered
- Set marking rubrics in the question description

### 3. Review AI Grades
- AI grading is a tool, not a replacement for human judgment
- Review AI grades for critical exams
- Allow students to appeal grades

### 4. Quality Control
- Monitor confidence scores
- Flag low-confidence grades for human review
- Compare AI grades with manual grades for calibration

---

## Alternative AI Services

The current implementation uses Google Gemini. You can also use other AI services:

### 1. OpenAI GPT
```php
'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . $apiKey
```

### 2. Anthropic Claude
```php
'https://api.anthropic.com/v1/messages'
```

### 3. Azure OpenAI
```php
'https://{your-resource}.openai.azure.com/openai/deployments/{deployment}/chat/completions'
```

---

## Security Notes

1. **API Key Protection**: Never expose your OpenAI API key
2. **Rate Limiting**: Implement rate limiting to prevent abuse
3. **Data Privacy**: Student answers are sent to OpenAI - ensure compliance with data privacy regulations
4. **Cost Monitoring**: Monitor usage to prevent unexpected costs

---

## Troubleshooting

### Issue: "Google Gemini API key is not configured"
**Solution**: Add `GEMINI_API_KEY` to your `.env` file

### Issue: "Google Gemini API request failed"
**Solution**: 
- Check if API key is valid
- Check if you have sufficient Gemini credits/quota
- Verify network connectivity

### Issue: "Failed to parse AI response"
**Solution**: 
- Lower the temperature for more predictable responses
- Check Gemini API status
- Increase `maxOutputTokens` if responses are truncated

---

## Example Complete Workflow

```bash
# 1. Student submits exam
POST /api/student/exam-attempts/start
Body: { "exam_id": 1 }

# 2. Student submits answers
POST /api/student/exam-attempts/1/submit-answer
Body: { "question_id": 1, "answer_text": "..." }

# 3. AI grade short answers
POST /api/student/exam-attempts/1/ai-grade
Body: { "answer_id": 1 }

# 4. Student submits exam
POST /api/student/exam-attempts/1/submit-exam

# 5. View results
GET /api/student/exam-attempts/1
```

---

## Notes

- AI grading is available only for **short_answer** question types
- MCQ questions are automatically graded (no AI needed)
- AI confidence scores range from 0-100
- All graded answers are automatically calculated in the final score
- AI feedback is optional and can be used for student improvement

