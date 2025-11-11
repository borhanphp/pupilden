# Exam Attempts API Documentation

This document provides API endpoints for managing exam attempts by students.

## Base URL
```
http://your-domain.com/api/student
```

## Authentication
All endpoints require student authentication. Include the bearer token in the Authorization header:
```
Authorization: Bearer {your_token}
```

---

## 1. Start Exam Attempt

**Endpoint:** `POST /exam-attempts/start`

**Description:** Creates a new exam attempt for a student.

### Request Body:
```json
{
  "exam_id": 1
}
```

### Example Request (Postman/cURL):
```bash
POST /api/student/exam-attempts/start
Authorization: Bearer {token}
Content-Type: application/json

{
  "exam_id": 1
}
```

### Success Response (201):
```json
{
  "success": true,
  "message": "Exam attempt started successfully",
  "data": {
    "attempt_id": 1,
    "exam": {
      "id": 1,
      "title": "Final Exam - Web Development",
      "description": "This is the final exam for the course",
      "type": "final",
      "pass_mark": 70,
      "duration": 90
    },
    "questions": [
      {
        "id": 1,
        "type": "mcq",
        "question_text": "What is HTML?",
        "marks": 2,
        "options": [
          "HyperText Markup Language",
          "High Tech Modern Language",
          "Hyper Transfer Markup Language"
        ]
      },
      {
        "id": 2,
        "type": "short_answer",
        "question_text": "Explain CSS box model",
        "marks": 5,
        "options": null
      }
    ],
    "attempted_at": "2023-09-06T10:00:00.000000Z"
  }
}
```

### Error Response (400):
```json
{
  "success": false,
  "message": "You already have an in-progress attempt for this exam",
  "data": {
    "attempt_id": 1,
    "attempted_at": "2023-09-06T10:00:00.000000Z"
  }
}
```

---

## 2. Submit Answer

**Endpoint:** `POST /exam-attempts/{attemptId}/submit-answer`

**Description:** Submits an answer for a specific question in an exam attempt.

### Request Body:
```json
{
  "question_id": 1,
  "answer_text": "HyperText Markup Language"
}
```

### Example Request (Postman/cURL):
```bash
POST /api/student/exam-attempts/1/submit-answer
Authorization: Bearer {token}
Content-Type: application/json

{
  "question_id": 1,
  "answer_text": "HyperText Markup Language"
}
```

### Success Response (200):
```json
{
  "success": true,
  "message": "Answer submitted successfully",
  "data": {
    "answer_id": 1,
    "question_id": 1,
    "is_correct": true,
    "marks_awarded": 2,
    "total_marks": 2
  }
}
```

---

## 3. Submit Exam

**Endpoint:** `POST /exam-attempts/{attemptId}/submit-exam`

**Description:** Submits the entire exam and calculates the final score.

### Example Request (Postman/cURL):
```bash
POST /api/student/exam-attempts/1/submit-exam
Authorization: Bearer {token}
```

### Success Response (200):
```json
{
  "success": true,
  "message": "Exam submitted successfully",
  "data": {
    "attempt_id": 1,
    "score": 85,
    "total_marks": 100,
    "percentage": 85.00,
    "is_passed": true,
    "pass_mark": 70,
    "status": "Passed",
    "submitted_at": "2023-09-06T10:30:00.000000Z",
    "answers": [
      {
        "question_id": 1,
        "question_text": "What is HTML?",
        "your_answer": "HyperText Markup Language",
        "correct_answer": "HyperText Markup Language",
        "is_correct": true,
        "marks_awarded": 2,
        "total_marks": 2
      },
      {
        "question_id": 2,
        "question_text": "Explain CSS box model",
        "your_answer": "The CSS box model describes the layout of elements",
        "correct_answer": "CSS box model describes content, padding, border, and margin",
        "is_correct": false,
        "marks_awarded": 0,
        "total_marks": 5
      }
    ]
  }
}
```

---

## 4. Get Attempt History

**Endpoint:** `GET /exam-attempts/history`

**Description:** Retrieves all exam attempts for the authenticated student.

### Query Parameters:
- `per_page` (optional): Number of items per page (default: 15)
- `exam_id` (optional): Filter by specific exam ID

### Example Request (Postman/cURL):
```bash
GET /api/student/exam-attempts/history
Authorization: Bearer {token}

GET /api/student/exam-attempts/history?exam_id=1&per_page=10
Authorization: Bearer {token}
```

### Success Response (200):
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "exam": {
          "id": 1,
          "title": "Final Exam - Web Development",
          "type": "final",
          "course": {
            "id": 1,
            "name": "Web Development"
          }
        },
        "score": 85,
        "total_marks": 100,
        "percentage": 85.00,
        "is_passed": true,
        "status": "Passed",
        "attempted_at": "2023-09-06T10:00:00.000000Z",
        "submitted_at": "2023-09-06T10:30:00.000000Z",
        "created_at": "2023-09-06T10:00:00.000000Z"
      }
    ],
    "total": 1,
    "per_page": 15,
    "last_page": 1
  }
}
```

---

## 5. Get Attempt Details

**Endpoint:** `GET /exam-attempts/{attemptId}`

**Description:** Retrieves detailed information about a specific exam attempt.

### Example Request (Postman/cURL):
```bash
GET /api/student/exam-attempts/1
Authorization: Bearer {token}
```

### Success Response (200):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "exam": {
      "id": 1,
      "title": "Final Exam - Web Development",
      "description": "This is the final exam for the course",
      "type": "final",
      "pass_mark": 70,
      "duration": 90
    },
    "score": 85,
    "total_marks": 100,
    "percentage": 85.00,
    "is_passed": true,
    "status": "Passed",
    "attempted_at": "2023-09-06T10:00:00.000000Z",
    "submitted_at": "2023-09-06T10:30:00.000000Z",
    "reviewed_at": "2023-09-06T10:30:00.000000Z",
    "answers": [
      {
        "question_id": 1,
        "question": {
          "id": 1,
          "question_text": "What is HTML?",
          "type": "mcq",
          "correct_answer": "HyperText Markup Language",
          "options": [
            "HyperText Markup Language",
            "High Tech Modern Language",
            "Hyper Transfer Markup Language"
          ],
          "marks": 2
        },
        "your_answer": "HyperText Markup Language",
        "is_correct": true,
        "marks_awarded": 2
      }
    ]
  }
}
```

---

## Postman Collection Example

### Import Collection
Create a new Postman collection with these settings:

**Collection Variables:**
- `base_url`: `http://your-domain.com/api/student`
- `token`: Your bearer token

### Example Collection Structure:

1. **Start Exam Attempt**
   - Method: `POST`
   - URL: `{{base_url}}/exam-attempts/start`
   - Headers:
     - `Authorization`: `Bearer {{token}}`
     - `Content-Type`: `application/json`
   - Body (raw JSON):
     ```json
     {
       "exam_id": 1
     }
     ```

2. **Submit Answer**
   - Method: `POST`
   - URL: `{{base_url}}/exam-attempts/1/submit-answer`
   - Headers:
     - `Authorization`: `Bearer {{token}}`
     - `Content-Type`: `application/json`
   - Body (raw JSON):
     ```json
     {
       "question_id": 1,
       "answer_text": "HyperText Markup Language"
     }
     ```

3. **Submit Exam**
   - Method: `POST`
   - URL: `{{base_url}}/exam-attempts/1/submit-exam`
   - Headers:
     - `Authorization`: `Bearer {{token}}`

4. **Get Attempt History**
   - Method: `GET`
   - URL: `{{base_url}}/exam-attempts/history`
   - Headers:
     - `Authorization`: `Bearer {{token}}`

5. **Get Attempt Details**
   - Method: `GET`
   - URL: `{{base_url}}/exam-attempts/1`
   - Headers:
     - `Authorization`: `Bearer {{token}}`

6. **Update Answer Marks (Manual Grading)**
   - Method: `POST`
   - URL: `{{base_url}}/exam-attempts/1/update-marks`
   - Headers:
     - `Authorization`: `Bearer {{token}}`
     - `Content-Type`: `application/json`
   - Body (raw JSON):
     ```json
     {
       "answers": [
         {
           "answer_id": 1,
           "marks_awarded": 4,
           "is_correct": true
         }
       ]
     }
     ```

---

## Error Responses

### Validation Error (422):
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "exam_id": ["The exam id field is required."]
  }
}
```

### Unauthorized (403):
```json
{
  "success": false,
  "message": "You must be enrolled in this course to take the exam"
}
```

### Not Found (404):
```json
{
  "success": false,
  "message": "Attempt not found"
}
```

### Server Error (500):
```json
{
  "success": false,
  "message": "Error starting exam attempt: Internal server error"
}
```

---

## Workflow Example

### Complete Exam Flow:

1. **Student starts exam:**
   ```bash
   POST /api/student/exam-attempts/start
   Body: { "exam_id": 1 }
   Response: Returns attempt_id and questions
   ```

2. **Student answers each question:**
   ```bash
   POST /api/student/exam-attempts/1/submit-answer
   Body: {
     "question_id": 1,
     "answer_text": "HyperText Markup Language"
   }
   ```

3. **Student submits exam:**
   ```bash
   POST /api/student/exam-attempts/1/submit-exam
   Response: Returns score, percentage, pass/fail status
   ```

4. **View results:**
   ```bash
   GET /api/student/exam-attempts/1
   Response: Returns detailed attempt with all answers
   ```

---

## 6. Update Answer Marks (Manual Grading)

**Endpoint:** `POST /exam-attempts/{attemptId}/update-marks`

**Description:** Manually grade short answer questions and update marks for exam answers.

### Request Body:
```json
{
  "answers": [
    {
      "answer_id": 1,
      "marks_awarded": 4,
      "is_correct": true
    },
    {
      "answer_id": 2,
      "marks_awarded": 3,
      "is_correct": true
    }
  ]
}
```

### Example Request (Postman/cURL):
```bash
POST /api/student/exam-attempts/1/update-marks
Authorization: Bearer {token}
Content-Type: application/json

{
  "answers": [
    {
      "answer_id": 1,
      "marks_awarded": 4,
      "is_correct": true
    }
  ]
}
```

### Success Response (200):
```json
{
  "success": true,
  "message": "Answer marks updated successfully",
  "data": {
    "attempt_id": 1,
    "score": 85,
    "total_marks": 100,
    "percentage": 85.00,
    "is_passed": true,
    "updated_answers": [
      {
        "answer_id": 1,
        "question_id": 2,
        "marks_awarded": 4,
        "is_correct": true
      }
    ]
  }
}
```

---

## Notes

- Each student can only have one in-progress attempt per exam
- **MCQ questions** are automatically graded upon submission
- **Short answer questions** require manual grading - marks remain 0 until graded
- Scores are calculated automatically and updated when marks are changed
- Pass/fail status is only determined when all answers are graded
- Use the `update-marks` endpoint to manually grade short answer questions
- All timestamps are returned in ISO 8601 format (UTC)

