#  Code Master - API Documentation

## Base URL
```
http://127.0.0.1:8000/api
```

## Authentication
All protected endpoints require JWT token in Authorization header:
```
Authorization: Bearer {token}
```

---

## 1. Authentication Endpoints

### 1.1 Register
**POST** `/auth/register`

**Request Body:**
```json
{
  "name": "Ahmed Hassan",
  "email": "ahmed@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "Ahmed Hassan",
      "email": "ahmed@example.com",
      "role": "learner"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "bearer"
  }
}
```

---

### 1.2 Login
**POST** `/auth/login`

**Request Body:**
```json
{
  "email": "ahmed@example.com",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Ahmed Hassan",
      "email": "ahmed@example.com",
      "role": "learner"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "bearer"
  }
}
```

---

### 1.3 Logout
**POST** `/auth/logout`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "message": "Successfully logged out"
}
```

---

## 2. Track Endpoints

### 2.1 Get All Tracks
**GET** `/tracks`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Full Stack Development",
      "description": "Learn web development from scratch",
      "creator": {
        "id": 1,
        "name": "Admin User"
      }
    }
  ]
}
```

---

### 2.2 Get Track Details
**GET** `/tracks/{id}`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Full Stack Development",
    "description": "Learn web development from scratch",
    "courses": [
      {
        "id": 1,
        "title": "HTML & CSS Basics",
        "order": 1
      },
      {
        "id": 2,
        "title": "JavaScript Fundamentals",
        "order": 2
      }
    ]
  }
}
```

---

### 2.3 Enroll in Track
**POST** `/tracks/{id}/enroll`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "message": "Enrolled successfully"
}
```

---

### 2.4 Get My Tracks
**GET** `/tracks/my-tracks`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Full Stack Development",
      "courses": [
        {
          "id": 1,
          "title": "HTML & CSS Basics",
          "userProgress": [
            {
              "is_unlocked": true,
              "is_completed": false,
              "total_score": 45
            }
          ]
        }
      ]
    }
  ]
}
```

---

## 3. Course Endpoints

### 3.1 Get Course Details
**GET** `/courses/{id}`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "course": {
      "id": 1,
      "title": "HTML & CSS Basics",
      "description": "Learn HTML and CSS from scratch"
    },
    "topics": [
      {
        "id": 1,
        "title": "Introduction to HTML",
        "type": "article",
        "is_unlocked": true,
        "is_viewed": false
      },
      {
        "id": 2,
        "title": "HTML Tags Explained",
        "type": "video",
        "is_unlocked": false,
        "is_viewed": false
      }
    ]
  }
}
```

---

## 4. Topic Endpoints

### 4.1 Get Topic Details
**GET** `/topics/{id}`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Introduction to HTML",
    "type": "article",
    "content": "HTML stands for HyperText Markup Language...",
    "quiz": {
      "id": 1,
      "title": "HTML Basics Quiz",
      "total_points": 10
    }
  }
}
```

---

### 4.2 Mark Topic as Viewed
**POST** `/topics/{id}/mark-viewed`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "message": "Topic marked as viewed"
}
```

---

## 5. Quiz Endpoints

### 5.1 Get Quiz
**GET** `/quizzes/{id}`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "quiz": {
      "id": 1,
      "title": "HTML Basics Quiz",
      "type": "topic",
      "total_points": 10,
      "pass_percentage": 50
    },
    "questions": [
      {
        "id": 1,
        "question_text": "What does HTML stand for?",
        "points": 5,
        "answers": [
          {
            "id": 1,
            "answer_text": "HyperText Markup Language"
          },
          {
            "id": 2,
            "answer_text": "High Tech Modern Language"
          }
        ]
      }
    ]
  }
}
```

---

### 5.2 Submit Quiz
**POST** `/quizzes/{id}/submit`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "answers": {
    "1": 1,
    "2": 4,
    "3": 7
  }
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Quiz submitted successfully",
  "data": {
    "id": 1,
    "score": 8,
    "max_score": 10,
    "passed": true,
    "percentage": 80,
    "can_retry_at": null
  }
}
```

---

### 5.3 Get Quiz Result
**GET** `/quizzes/attempts/{attemptId}/result`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "score": 8,
    "max_score": 10,
    "passed": true,
    "attempted_at": "2024-02-18T10:30:00.000000Z",
    "answers": [
      {
        "question": {
          "id": 1,
          "question_text": "What does HTML stand for?"
        },
        "answer": {
          "id": 1,
          "answer_text": "HyperText Markup Language"
        },
        "is_correct": true
      }
    ]
  }
}
```

---

## 6. Team Endpoints

### 6.1 Create Team
**POST** `/teams/create`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "name": "Awesome Developers",
  "project_type": "web",
  "max_members": 5
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Team created successfully",
  "data": {
    "id": 1,
    "name": "Awesome Developers",
    "code": "ABC12345",
    "project_type": "web",
    "max_members": 5
  }
}
```

---

### 6.2 Join Team
**POST** `/teams/join`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "code": "ABC12345",
  "track_id": 1
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Joined team successfully"
}
```

---

### 6.3 Get Team Details
**GET** `/teams/{id}`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Awesome Developers",
    "code": "ABC12345",
    "project_type": "web",
    "max_members": 5,
    "members": [
      {
        "user": {
          "id": 1,
          "name": "Ahmed Hassan",
          "email": "ahmed@example.com"
        },
        "track": {
          "id": 1,
          "title": "Full Stack Development"
        }
      }
    ]
  }
}
```

---

### 6.4 Get My Teams
**GET** `/teams/my-teams`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Awesome Developers",
      "code": "ABC12345",
      "members": [...]
    }
  ]
}
```

---

### 6.5 Get Team Progress
**GET** `/teams/{id}/progress`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "team": {
      "id": 1,
      "name": "Awesome Developers"
    },
    "members_progress": [
      {
        "user": {
          "id": 1,
          "name": "Ahmed Hassan"
        },
        "track": {
          "id": 1,
          "title": "Full Stack Development"
        },
        "stats": {
          "courses_completed": 2,
          "topics_viewed": 15,
          "quizzes_passed": 10,
          "total_score": 85
        }
      }
    ]
  }
}
```

---

## 7. Progress Endpoints

### 7.1 Get My Progress
**GET** `/progress`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "total_tracks": 2,
    "total_courses_unlocked": 5,
    "total_courses_completed": 2,
    "total_topics_viewed": 20,
    "total_quizzes_passed": 15,
    "total_score": 140
  }
}
```

---

## 8. Admin Endpoints

**Note:** All admin endpoints require admin role and are protected by `admin` middleware.

### 8.1 Users Management

#### Get All Users
**GET** `/admin/users`

**Headers:** `Authorization: Bearer {admin_token}`

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Ahmed Hassan",
      "email": "ahmed@example.com",
      "stats": {
        "total_tracks": 2,
        "courses_completed": 3,
        "topics_viewed": 25,
        "quizzes_passed": 18,
        "total_score": 160
      }
    }
  ]
}
```

#### Get User Details
**GET** `/admin/users/{id}`

**Headers:** `Authorization: Bearer {admin_token}`

---

### 8.2 Tracks Management

#### Get All Tracks
**GET** `/admin/tracks`

#### Create Track
**POST** `/admin/tracks`

**Request Body:**
```json
{
  "title": "Mobile Development",
  "description": "Learn mobile app development",
  "course_ids": [1, 2, 3]
}
```

#### Update Track
**PUT** `/admin/tracks/{id}`

#### Delete Track
**DELETE** `/admin/tracks/{id}`

---

### 8.3 Courses Management

#### Get All Courses
**GET** `/admin/courses`

#### Create Course
**POST** `/admin/courses`

**Request Body:**
```json
{
  "title": "React Native Basics",
  "description": "Learn React Native",
  "topic_ids": [1, 2, 3]
}
```

#### Update Course
**PUT** `/admin/courses/{id}`

#### Delete Course
**DELETE** `/admin/courses/{id}`

---

### 8.4 Topics Management

#### Get All Topics
**GET** `/admin/topics`

#### Create Topic
**POST** `/admin/topics`

**Request Body:**
```json
{
  "title": "Introduction to React",
  "type": "video",
  "video_url": "https://example.com/video.mp4",
  "video_duration": 1200
}
```

#### Update Topic
**PUT** `/admin/topics/{id}`

#### Delete Topic
**DELETE** `/admin/topics/{id}`

---

### 8.5 Quizzes Management

#### Get All Quizzes
**GET** `/admin/quizzes`

#### Create Quiz
**POST** `/admin/quizzes`

**Request Body:**
```json
{
  "title": "React Basics Quiz",
  "type": "topic",
  "topic_id": 1,
  "total_points": 20,
  "pass_percentage": 60,
  "questions": [
    {
      "question_text": "What is React?",
      "points": 5,
      "answers": [
        {
          "answer_text": "A JavaScript library",
          "is_correct": true
        },
        {
          "answer_text": "A programming language",
          "is_correct": false
        }
      ]
    }
  ]
}
```

#### Update Quiz
**PUT** `/admin/quizzes/{id}`

#### Delete Quiz
**DELETE** `/admin/quizzes/{id}`

---

### 8.6 Videos Management

#### Get All Videos
**GET** `/admin/videos`

#### Upload Video
**POST** `/admin/videos/upload`

**Request (multipart/form-data):**
```
title: "React Tutorial Part 1"
video: [file]
```

**Note:** Maximum file size: 500MB

#### Get Video
**GET** `/admin/videos/{id}`

#### Delete Video
**DELETE** `/admin/videos/{id}`

---

### 8.7 Teams Management

#### Get All Teams
**GET** `/admin/teams`

#### Get Team Details
**GET** `/admin/teams/{id}`

---

## Error Responses

### 401 Unauthorized
```json
{
  "success": false,
  "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
  "success": false,
  "message": "Unauthorized. Admin access required."
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Resource not found"
}
```

### 422 Validation Error
```json
{
  "success": false,
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

### 500 Server Error
```json
{
  "success": false,
  "message": "Internal server error"
}
```