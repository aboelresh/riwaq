# 🚀  Code Master - API Reference Guide

## 📋 جدول المحتويات

1. [معلومات عامة](#معلومات-عامة)
2. [Authentication (المصادقة)](#1-authentication-المصادقة)
3. [Tracks (المسارات)](#2-tracks-المسارات)
4. [Courses (الكورسات)](#3-courses-الكورسات)
5. [Topics (الموضوعات)](#4-topics-الموضوعات)
6. [Quizzes (الاختبارات)](#5-quizzes-الاختبارات)
7. [Teams (الفرق)](#6-teams-الفرق)
8. [Progress (التقدم)](#7-progress-التقدم)
9. [Admin - Users](#8-admin---users)
10. [Admin - Tracks](#9-admin---tracks)
11. [Admin - Courses](#10-admin---courses)
12. [Admin - Topics](#11-admin---topics)
13. [Admin - Quizzes](#12-admin---quizzes)
14. [Admin - Videos](#13-admin---videos)
15. [Admin - Teams](#14-admin---teams)
16. [Error Handling](#error-handling)

---

## معلومات عامة

### Base URL
```
http://127.0.0.1:8000/api
```

### Authentication
كل الـ Endpoints (ما عدا Register و Login) محتاجة Token في الـ Header:
```http
Authorization: Bearer {your_token_here}
```

### Response Format
كل الـ Responses بتيجي بالشكل دا:

**Success Response:**
```json
{
    "success": true,
    "message": "Operation successful",
    "data": { ... }
}
```

**Error Response:**
```json
{
    "success": false,
    "message": "Error message",
    "errors": { ... }
}
```

---

## 1. Authentication (المصادقة)

### 1.1 Register (تسجيل حساب جديد)

**Endpoint:**
```http
POST /auth/register
```

**Headers:**
```http
Content-Type: application/json
```

**Request Body:**
```json
{
    "name": "Ahmed Hassan",
    "email": "ahmed@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Success Response (201):**
```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "Ahmed Hassan",
            "email": "ahmed@example.com",
            "role": "learner",
            "created_at": "2024-02-20T10:00:00.000000Z"
        },
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "token_type": "bearer"
    }
}
```

**Validation Rules:**
- `name`: required, string
- `email`: required, email, unique
- `password`: required, min:8
- `password_confirmation`: required, same as password

**Error Response (422):**
```json
{
    "success": false,
    "errors": {
        "email": ["The email has already been taken."],
        "password": ["The password must be at least 8 characters."]
    }
}
```

---

### 1.2 Login (تسجيل الدخول)

**Endpoint:**
```http
POST /auth/login
```

**Headers:**
```http
Content-Type: application/json
```

**Request Body:**
```json
{
    "email": "ahmed@example.com",
    "password": "password123"
}
```

**Success Response (200):**
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
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "token_type": "bearer"
    }
}
```

**Error Response (401):**
```json
{
    "success": false,
    "message": "Invalid credentials"
}
```

**💡 Frontend Implementation:**
```javascript
// Save token
localStorage.setItem('token', response.data.token);
localStorage.setItem('user', JSON.stringify(response.data.user));

// Use in all requests
const token = localStorage.getItem('token');
axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
```

---

### 1.3 Logout (تسجيل الخروج)

**Endpoint:**
```http
POST /auth/logout
```

**Headers:**
```http
Authorization: Bearer {token}
Content-Type: application/json
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Successfully logged out"
}
```

**💡 Frontend Implementation:**
```javascript
// Clear storage
localStorage.removeItem('token');
localStorage.removeItem('user');
delete axios.defaults.headers.common['Authorization'];
```

---

## 2. Tracks (المسارات)

### 2.1 Get All Tracks (عرض كل المسارات المتاحة)

**Endpoint:**
```http
GET /tracks
```

**Headers:**
```http
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Web Development Track",
            "description": "Learn web development from scratch",
            "creator": {
                "id": 1,
                "name": "Admin User"
            },
            "created_at": "2024-01-15T10:00:00.000000Z"
        },
        {
            "id": 2,
            "title": "Mobile Development Track",
            "description": "Learn mobile app development",
            "creator": {
                "id": 1,
                "name": "Admin User"
            },
            "created_at": "2024-01-16T10:00:00.000000Z"
        }
    ]
}
```

**💡 Use Case:**
- عرض المسارات المتاحة للتسجيل
- صفحة "Browse Tracks"

---

### 2.2 Get Track Details (تفاصيل مسار معين)

**Endpoint:**
```http
GET /tracks/{id}
```

**Headers:**
```http
Authorization: Bearer {token}
```

**Example:**
```http
GET /tracks/1
```

**Success Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "title": "Web Development Track",
        "description": "Learn web development from scratch",
        "courses": [
            {
                "id": 1,
                "title": "HTML & CSS Basics",
                "description": "Learn HTML and CSS",
                "order": 1
            },
            {
                "id": 2,
                "title": "JavaScript Fundamentals",
                "description": "Learn JavaScript",
                "order": 2
            }
        ],
        "creator": {
            "id": 1,
            "name": "Admin User"
        }
    }
}
```

**Error Response (404):**
```json
{
    "success": false,
    "message": "Track not found"
}
```

**💡 Use Case:**
- عرض تفاصيل المسار قبل التسجيل
- عرض الكورسات المتاحة في المسار

---

### 2.3 Enroll in Track (الانضمام لمسار)

**Endpoint:**
```http
POST /tracks/{id}/enroll
```

**Headers:**
```http
Authorization: Bearer {token}
```

**Example:**
```http
POST /tracks/1/enroll
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Enrolled successfully"
}
```

**Error Responses:**

**400 - Already Enrolled:**
```json
{
    "success": false,
    "message": "Already enrolled in this track"
}
```

**404 - Track Not Found:**
```json
{
    "success": false,
    "message": "Track not found"
}
```

**💡 What Happens:**
1. المستخدم يتسجل في الـ Track
2. أول كورس في المسار يُفتح تلقائياً
3. أول موضوع في أول كورس يُفتح تلقائياً

**💡 Frontend Action After Success:**
```javascript
// Show success message
alert('Enrolled successfully! You can now start learning.');

// Redirect to track courses
navigate(`/tracks/${trackId}/courses`);

// Or refresh dashboard
loadDashboard();
```

---

### 2.4 Get My Tracks (المسارات المسجل فيها)

**Endpoint:**
```http
GET /tracks/my-tracks
```

**Headers:**
```http
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Web Development Track",
            "description": "Learn web development from scratch",
            "courses": [
                {
                    "id": 1,
                    "title": "HTML & CSS Basics",
                    "order": 1,
                    "userProgress": [
                        {
                            "is_unlocked": true,
                            "is_completed": false,
                            "total_score": 25,
                            "max_possible_score": 50,
                            "started_at": "2024-02-15T10:00:00.000000Z"
                        }
                    ]
                },
                {
                    "id": 2,
                    "title": "JavaScript Fundamentals",
                    "order": 2,
                    "userProgress": [
                        {
                            "is_unlocked": false,
                            "is_completed": false,
                            "total_score": 0,
                            "max_possible_score": 60
                        }
                    ]
                }
            ],
            "enrolled_at": "2024-02-15T09:00:00.000000Z"
        }
    ]
}
```

**💡 Use Case:**
- Dashboard الرئيسية
- عرض تقدم المستخدم في كل مسار
- حساب النسبة المئوية: `(total_score / max_possible_score) * 100`

**💡 Frontend Display:**
```javascript
// Progress percentage
const progress = (course.total_score / course.max_possible_score) * 100;

// Display
<ProgressBar value={progress} />
<span>{progress.toFixed(0)}% Complete</span>

// Lock icon
{!course.is_unlocked && <LockIcon />}
```

---

## 3. Courses (الكورسات)

### 3.1 Get Course Details (تفاصيل كورس معين)

**Endpoint:**
```http
GET /courses/{id}
```

**Headers:**
```http
Authorization: Bearer {token}
```

**Example:**
```http
GET /courses/1
```

**Success Response (200):**
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
                "is_viewed": false,
                "order": 1,
                "quiz": {
                    "id": 1,
                    "title": "HTML Basics Quiz",
                    "total_points": 10
                }
            },
            {
                "id": 2,
                "title": "HTML Tags Explained",
                "type": "video",
                "is_unlocked": false,
                "is_viewed": false,
                "order": 2,
                "quiz": {
                    "id": 2,
                    "title": "HTML Tags Quiz",
                    "total_points": 10
                }
            },
            {
                "id": 3,
                "title": "HTML Forms",
                "type": "article",
                "is_unlocked": false,
                "is_viewed": false,
                "order": 3,
                "quiz": {
                    "id": 3,
                    "title": "Forms Quiz",
                    "total_points": 10
                }
            }
        ]
    }
}
```

**Error Responses:**

**403 - Course Locked:**
```json
{
    "success": false,
    "message": "This course is locked. Complete the previous course first."
}
```

**404 - Not Found:**
```json
{
    "success": false,
    "message": "Course not found"
}
```

**💡 Frontend Display:**
```javascript
// Topic status
topics.map(topic => ({
    ...topic,
    status: topic.is_viewed ? 'completed' : 
            topic.is_unlocked ? 'available' : 'locked',
    icon: topic.type === 'video' ? '📹' : '📄'
}));

// Lock indicator
{!topic.is_unlocked && (
    <div className="locked">
        🔒 Complete previous topic first
    </div>
)}
```

---

## 4. Topics (الموضوعات)

### 4.1 Get Topic Details (عرض محتوى موضوع)

**Endpoint:**
```http
GET /topics/{id}
```

**Headers:**
```http
Authorization: Bearer {token}
```

**Example:**
```http
GET /topics/1
```

**Success Response - Article (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "title": "Introduction to HTML",
        "type": "article",
        "content": "<h1>What is HTML?</h1><p>HTML stands for HyperText Markup Language...</p>",
        "quiz": {
            "id": 1,
            "title": "HTML Basics Quiz",
            "total_points": 10,
            "pass_percentage": 50
        }
    }
}
```

**Success Response - Video (200):**
```json
{
    "success": true,
    "data": {
        "id": 2,
        "title": "HTML Tags Explained",
        "type": "video",
        "video_url": "http://127.0.0.1:8000/storage/videos/html-tags-abc123.mp4",
        "video_duration": 900,
        "quiz": {
            "id": 2,
            "title": "HTML Tags Quiz",
            "total_points": 10,
            "pass_percentage": 50
        }
    }
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "This topic is locked."
}
```

**💡 Frontend Display:**

**Article:**
```javascript
// Render HTML safely (use DOMPurify)
<div dangerouslySetInnerHTML={{ __html: DOMPurify.sanitize(content) }} />
```

**Video:**
```javascript
// Video duration display
const minutes = Math.floor(video_duration / 60);
const seconds = video_duration % 60;

<video src={video_url} controls />
<span>Duration: {minutes}:{seconds}</span>
```

---

### 4.2 Mark Topic as Viewed (تعليم الموضوع كمُشاهد)

**Endpoint:**
```http
POST /topics/{id}/mark-viewed
```

**Headers:**
```http
Authorization: Bearer {token}
```

**Example:**
```http
POST /topics/1/mark-viewed
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Topic marked as viewed"
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "This topic is locked."
}
```

**💡 When to Call:**
- بعد ما المستخدم يقرأ المقال كامل
- أو بعد ما يشاهد الفيديو
- قبل ما ياخد الكويز

**💡 Frontend Implementation:**
```javascript
// Button click handler
const handleMarkViewed = async () => {
    await api.post(`/topics/${topicId}/mark-viewed`);
    
    // Update UI
    setIsViewed(true);
    
    // Enable quiz button
    setCanTakeQuiz(true);
    
    // Show success toast
    toast.success('Topic marked as completed!');
};
```

---

## 5. Quizzes (الاختبارات)

### 5.1 Get Quiz (عرض أسئلة الاختبار)

**Endpoint:**
```http
GET /quizzes/{id}
```

**Headers:**
```http
Authorization: Bearer {token}
```

**Example:**
```http
GET /quizzes/1
```

**Success Response (200):**
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
                    },
                    {
                        "id": 3,
                        "answer_text": "Home Tool Markup Language"
                    },
                    {
                        "id": 4,
                        "answer_text": "Hyperlinks and Text Markup Language"
                    }
                ]
            },
            {
                "id": 2,
                "question_text": "Which tag is used for paragraphs?",
                "points": 5,
                "answers": [
                    {
                        "id": 5,
                        "answer_text": "<p>"
                    },
                    {
                        "id": 6,
                        "answer_text": "<para>"
                    },
                    {
                        "id": 7,
                        "answer_text": "<paragraph>"
                    }
                ]
            }
        ]
    }
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "Cannot take quiz at this time. Please wait before retrying.",
    "can_retry_at": "2024-02-20T11:30:00.000000Z"
}
```

**⚠️ Important Notes:**
- الإجابات الصحيحة **مش موجودة** في الـ Response
- المستخدم لازم يختار إجابة واحدة لكل سؤال
- كل سؤال له نقاط محددة

**💡 Frontend Implementation:**
```javascript
// Store selected answers
const [answers, setAnswers] = useState({});

// When user selects an answer
const handleSelectAnswer = (questionId, answerId) => {
    setAnswers({
        ...answers,
        [questionId]: answerId
    });
};

// Form structure
<form onSubmit={handleSubmit}>
    {questions.map(q => (
        <div key={q.id}>
            <h3>{q.question_text} ({q.points} points)</h3>
            {q.answers.map(a => (
                <label key={a.id}>
                    <input
                        type="radio"
                        name={`question_${q.id}`}
                        value={a.id}
                        onChange={() => handleSelectAnswer(q.id, a.id)}
                        required
                    />
                    {a.answer_text}
                </label>
            ))}
        </div>
    ))}
    <button type="submit">Submit Quiz</button>
</form>
```

---

### 5.2 Submit Quiz (إرسال إجابات الاختبار)

**Endpoint:**
```http
POST /quizzes/{id}/submit
```

**Headers:**
```http
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "answers": {
        "1": 1,
        "2": 5
    }
}
```

**Format:**
- Key: `question_id` (as string)
- Value: `answer_id` (as number)

**Success Response - Passed (200):**
```json
{
    "success": true,
    "message": "Quiz submitted successfully",
    "data": {
        "id": 1,
        "score": 10,
        "max_score": 10,
        "passed": true,
        "percentage": 100,
        "can_retry_at": null,
        "attempted_at": "2024-02-20T10:30:00.000000Z"
    }
}
```

**Success Response - Failed (200):**
```json
{
    "success": true,
    "message": "Quiz submitted successfully",
    "data": {
        "id": 2,
        "score": 3,
        "max_score": 10,
        "passed": false,
        "percentage": 30,
        "can_retry_at": "2024-02-20T11:30:00.000000Z",
        "attempted_at": "2024-02-20T10:30:00.000000Z"
    }
}
```

**Error Response (400):**
```json
{
    "success": false,
    "message": "You must wait before retrying this quiz."
}
```

**💡 What Happens When Passed:**
1. الموضوع التالي يُفتح تلقائياً (لو topic quiz)
2. النقاط تُضاف للـ total score
3. المستخدم يقدر يكمل تعلم

**💡 What Happens When Failed:**
1. `can_retry_at` = الوقت الحالي + ساعة
2. الموضوع التالي يبقى مقفول
3. المستخدم لازم ينتظر ساعة ويعيد

**💡 Frontend Display:**
```javascript
// Success screen
if (result.passed) {
    return (
        <div className="success">
            <h1>🎉 Congratulations!</h1>
            <p>You scored {result.score}/{result.max_score}</p>
            <p>{result.percentage}%</p>
            <button onClick={continueToNextTopic}>
                Continue to Next Topic →
            </button>
        </div>
    );
}

// Fail screen
return (
    <div className="fail">
        <h1>😔 Not Quite There</h1>
        <p>You scored {result.score}/{result.max_score}</p>
        <p>{result.percentage}% (Need 50% to pass)</p>
        <p>You can retry after: {formatDate(result.can_retry_at)}</p>
        <Countdown targetDate={result.can_retry_at} />
    </div>
);
```

---

### 5.3 Get Quiz Result (عرض نتيجة محاولة سابقة)

**Endpoint:**
```http
GET /quizzes/attempts/{attemptId}/result
```

**Headers:**
```http
Authorization: Bearer {token}
```

**Example:**
```http
GET /quizzes/attempts/1/result
```

**Success Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "score": 8,
        "max_score": 10,
        "passed": true,
        "percentage": 80,
        "attempted_at": "2024-02-20T10:30:00.000000Z",
        "answers": [
            {
                "question": {
                    "id": 1,
                    "question_text": "What does HTML stand for?",
                    "points": 5
                },
                "answer": {
                    "id": 1,
                    "answer_text": "HyperText Markup Language"
                },
                "is_correct": true
            },
            {
                "question": {
                    "id": 2,
                    "question_text": "Which tag is used for paragraphs?",
                    "points": 5
                },
                "answer": {
                    "id": 6,
                    "answer_text": "<para>"
                },
                "is_correct": false
            }
        ]
    }
}
```

**💡 Use Case:**
- عرض نتائج المحاولات السابقة
- Review الإجابات
- **لا تعرض الإجابة الصحيحة** (لمنع الغش)

---

## 6. Teams (الفرق)

### 6.1 Create Team (إنشاء فريق جديد)

**Endpoint:**
```http
POST /teams/create
```

**Headers:**
```http
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "name": "Awesome Developers",
    "project_type": "web",
    "max_members": 5
}
```

**Validation Rules:**
- `name`: required, string, max:255
- `project_type`: required, in:web,mobile
- `max_members`: required, integer, min:2, max:10

**Success Response (201):**
```json
{
    "success": true,
    "message": "Team created successfully",
    "data": {
        "id": 1,
        "name": "Awesome Developers",
        "code": "ABC12345",
        "project_type": "web",
        "max_members": 5,
        "created_at": "2024-02-20T10:00:00.000000Z"
    }
}
```

**⚠️ Important:**
- الـ `code` يتولد تلقائياً (8 أحرف فريدة)
- لازم تعرض الـ code للمستخدم عشان يشاركه مع فريقه

**💡 Frontend Display:**
```javascript
// After team creation
<div className="team-created">
    <h2>🎉 Team Created Successfully!</h2>
    <div className="team-code">
        <h3>Share this code with your team:</h3>
        <div className="code-display">{team.code}</div>
        <button onClick={() => copyToClipboard(team.code)}>
            📋 Copy Code
        </button>
    </div>
    <p>Your team members can use this code to join.</p>
</div>
```

---

### 6.2 Join Team (الانضمام لفريق)

**Endpoint:**
```http
POST /teams/join
```

**Headers:**
```http
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "code": "ABC12345",
    "track_id": 1
}
```

**Validation Rules:**
- `code`: required, string, exists in teams
- `track_id`: required, integer, exists in tracks

**Success Response (200):**
```json
{
    "success": true,
    "message": "Joined team successfully"
}
```

**Error Responses:**

**400 - Team Full:**
```json
{
    "success": false,
    "message": "Team is full"
}
```

**400 - Already Member:**
```json
{
    "success": false,
    "message": "Already a member of this team"
}
```

**404 - Invalid Code:**
```json
{
    "success": false,
    "message": "Invalid team code"
}
```

**💡 Frontend Implementation:**
```javascript
// Join form
<form onSubmit={handleJoinTeam}>
    <input
        type="text"
        placeholder="Enter team code"
        value={code}
        onChange={(e) => setCode(e.target.value.toUpperCase())}
        maxLength={8}
        required
    />
    <select value={trackId} onChange={(e) => setTrackId(e.target.value)}>
        <option value="">Select your track</option>
        {myTracks.map(track => (
            <option value={track.id}>{track.title}</option>
        ))}
    </select>
    <button type="submit">Join Team</button>
</form>
```

---

### 6.3 Get My Teams (الفرق المنضم لها)

**Endpoint:**
```http
GET /teams/my-teams
```

**Headers:**
```http
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
    "success": true,
    "data": [
        {
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
                        "email": "ahmed@test.com"
                    },
                    "track": {
                        "id": 1,
                        "title": "Web Development Track"
                    }
                },
                {
                    "user": {
                        "id": 2,
                        "name": "Sara Ali",
                        "email": "sara@test.com"
                    },
                    "track": {
                        "id": 1,
                        "title": "Web Development Track"
                    }
                }
            ]
        }
    ]
}
```

**💡 Frontend Display:**
```javascript
// Team card
<div className="team-card">
    <h3>{team.name}</h3>
    <div className="team-info">
        <span>Code: {team.code}</span>
        <span>Type: {team.project_type}</span>
        <span>Members: {team.members.length}/{team.max_members}</span>
    </div>
    <div className="members-list">
        {team.members.map(member => (
            <div className="member">
                <span>{member.user.name}</span>
                <span className="track">{member.track.title}</span>
            </div>
        ))}
    </div>
</div>
```

---

### 6.4 Get Team Details (تفاصيل فريق معين)

**Endpoint:**
```http
GET /teams/{id}
```

**Headers:**
```http
Authorization: Bearer {token}
```

**Example:**
```http
GET /teams/1
```

**Success Response (200):**
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
                    "email": "ahmed@test.com",
                    "profile_photo": null
                },
                "track": {
                    "id": 1,
                    "title": "Web Development Track"
                }
            }
        ],
        "created_at": "2024-02-15T10:00:00.000000Z"
    }
}
```

---

### 6.5 Get Team Progress (تقدم أعضاء الفريق)

**Endpoint:**
```http
GET /teams/{id}/progress
```

**Headers:**
```http
Authorization: Bearer {token}
```

**Example:**
```http
GET /teams/1/progress
```

**Success Response (200):**
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
                    "name": "Ahmed Hassan",
                    "email": "ahmed@test.com"
                },
                "track": {
                    "id": 1,
                    "title": "Web Development Track"
                },
                "stats": {
                    "courses_completed": 2,
                    "topics_viewed": 15,
                    "quizzes_passed": 10,
                    "total_score": 85
                }
            },
            {
                "user": {
                    "id": 2,
                    "name": "Sara Ali",
                    "email": "sara@test.com"
                },
                "track": {
                    "id": 1,
                    "title": "Web Development Track"
                },
                "stats": {
                    "courses_completed": 3,
                    "topics_viewed": 20,
                    "quizzes_passed": 15,
                    "total_score": 120
                }
            }
        ]
    }
}
```

**💡 Frontend Display:**
```javascript
// Leaderboard view
const sortedMembers = members_progress.sort((a, b) => 
    b.stats.total_score - a.stats.total_score
);

<div className="leaderboard">
    {sortedMembers.map((member, index) => (
        <div className="member-card" key={member.user.id}>
            <div className="rank">{index + 1}</div>
            <div className="member-info">
                <h3>{member.user.name}</h3>
                <p>{member.track.title}</p>
            </div>
            <div className="stats">
                <div>Courses: {member.stats.courses_completed}</div>
                <div>Topics: {member.stats.topics_viewed}</div>
                <div>Quizzes: {member.stats.quizzes_passed}</div>
                <div className="score">Score: {member.stats.total_score}</div>
            </div>
        </div>
    ))}
</div>
```

---

## 7. Progress (التقدم)

### 7.1 Get My Progress (إحصائيات المستخدم)

**Endpoint:**
```http
GET /progress
```

**Headers:**
```http
Authorization: Bearer {token}
```

**Success Response (200):**
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

**💡 Frontend Display:**
```javascript
// Dashboard stats cards
<div className="stats-grid">
    <StatCard
        icon="📚"
        label="Enrolled Tracks"
        value={progress.total_tracks}
        color="blue"
    />
    <StatCard
        icon="✅"
        label="Completed Courses"
        value={progress.total_courses_completed}
        color="green"
    />
    <StatCard
        icon="👀"
        label="Topics Viewed"
        value={progress.total_topics_viewed}
        color="purple"
    />
    <StatCard
        icon="🎯"
        label="Quizzes Passed"
        value={progress.total_quizzes_passed}
        color="yellow"
    />
    <StatCard
        icon="⭐"
        label="Total Score"
        value={progress.total_score}
        color="red"
    />
</div>
```

---

## 8. Admin - Users

**⚠️ كل الـ Admin Endpoints محتاجة:**
```http
Authorization: Bearer {admin_token}
```

**⚠️ لو User عادي حاول يدخل:**
```json
{
    "success": false,
    "message": "Unauthorized. Admin access required."
}
```

---

### 8.1 Get All Users (عرض كل المتعلمين)

**Endpoint:**
```http
GET /admin/users
```

**Headers:**
```http
Authorization: Bearer {admin_token}
```

**Success Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Ahmed Hassan",
            "email": "ahmed@test.com",
            "profile_photo": null,
            "created_at": "2024-01-15T10:00:00.000000Z",
            "stats": {
                "total_tracks": 2,
                "courses_completed": 3,
                "topics_viewed": 25,
                "quizzes_passed": 18,
                "total_score": 160
            }
        },
        {
            "id": 2,
            "name": "Sara Ali",
            "email": "sara@test.com",
            "profile_photo": null,
            "created_at": "2024-01-16T10:00:00.000000Z",
            "stats": {
                "total_tracks": 1,
                "courses_completed": 2,
                "topics_viewed": 15,
                "quizzes_passed": 12,
                "total_score": 95
            }
        }
    ]
}
```

**💡 Frontend Display:**
```javascript
// Data table
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Tracks</th>
            <th>Courses</th>
            <th>Score</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        {users.map(user => (
            <tr key={user.id}>
                <td>{user.name}</td>
                <td>{user.email}</td>
                <td>{user.stats.total_tracks}</td>
                <td>{user.stats.courses_completed}</td>
                <td>{user.stats.total_score}</td>
                <td>
                    <button onClick={() => viewDetails(user.id)}>
                        View Details
                    </button>
                </td>
            </tr>
        ))}
    </tbody>
</table>
```

---

### 8.2 Get User Details (تفاصيل متعلم معين)

**Endpoint:**
```http
GET /admin/users/{id}
```

**Headers:**
```http
Authorization: Bearer {admin_token}
```

**Example:**
```http
GET /admin/users/1
```

**Success Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Ahmed Hassan",
        "email": "ahmed@test.com",
        "profile_photo": null,
        "created_at": "2024-01-15T10:00:00.000000Z",
        "tracks": [
            {
                "id": 1,
                "title": "Web Development Track",
                "enrolled_at": "2024-01-15T10:30:00.000000Z"
            }
        ],
        "courseProgress": [
            {
                "course": {
                    "id": 1,
                    "title": "HTML & CSS Basics"
                },
                "is_unlocked": true,
                "is_completed": true,
                "total_score": 45,
                "max_possible_score": 50
            }
        ],
        "topicProgress": [
            {
                "topic": {
                    "id": 1,
                    "title": "Introduction to HTML"
                },
                "is_unlocked": true,
                "is_viewed": true
            }
        ],
        "quizAttempts": [
            {
                "quiz": {
                    "id": 1,
                    "title": "HTML Basics Quiz"
                },
                "score": 10,
                "max_score": 10,
                "passed": true,
                "attempted_at": "2024-01-15T11:00:00.000000Z"
            }
        ]
    }
}
```

---

## 9. Admin - Tracks

### 9.1 Get All Tracks (Admin)

**Endpoint:**
```http
GET /admin/tracks
```

**Headers:**
```http
Authorization: Bearer {admin_token}
```

**Success Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Web Development Track",
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
            ],
            "created_by": 1,
            "created_at": "2024-01-10T10:00:00.000000Z"
        }
    ]
}
```

---

### 9.2 Create Track (إنشاء مسار جديد)

**Endpoint:**
```http
POST /admin/tracks
```

**Headers:**
```http
Authorization: Bearer {admin_token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "title": "Mobile Development Track",
    "description": "Learn mobile app development with React Native",
    "course_ids": [3, 4, 5]
}
```

**Validation Rules:**
- `title`: required, string, max:255
- `description`: nullable, string
- `course_ids`: nullable, array
- `course_ids.*`: exists:courses,id

**Success Response (201):**
```json
{
    "success": true,
    "message": "Track created successfully",
    "data": {
        "id": 2,
        "title": "Mobile Development Track",
        "description": "Learn mobile app development with React Native",
        "courses": [
            {
                "id": 3,
                "title": "React Native Basics",
                "order": 1
            },
            {
                "id": 4,
                "title": "React Native Navigation",
                "order": 2
            },
            {
                "id": 5,
                "title": "React Native State Management",
                "order": 3
            }
        ],
        "created_by": 1,
        "created_at": "2024-02-20T10:00:00.000000Z"
    }
}
```

**⚠️ Important:**
- ترتيب الـ `course_ids` في الـ array هو اللي بيحدد ترتيب الكورسات في المسار
- أول course في الـ array يبقى `order = 1`

---

### 9.3 Update Track (تحديث مسار)

**Endpoint:**
```http
PUT /admin/tracks/{id}
```

**Headers:**
```http
Authorization: Bearer {admin_token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "title": "Updated Track Title",
    "description": "Updated description",
    "course_ids": [1, 3, 2]
}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Track updated successfully",
    "data": {
        "id": 1,
        "title": "Updated Track Title",
        "description": "Updated description",
        "courses": [
            {
                "id": 1,
                "title": "HTML & CSS Basics",
                "order": 1
            },
            {
                "id": 3,
                "title": "Advanced CSS",
                "order": 2
            },
            {
                "id": 2,
                "title": "JavaScript Fundamentals",
                "order": 3
            }
        ]
    }
}
```

**⚠️ Note:**
- الـ `course_ids` الجديدة بتستبدل القديمة كلها (sync)
- لو عايز تضيف course جديد، لازم تبعت كل الـ IDs القديمة + الجديد

---

### 9.4 Delete Track (حذف مسار)

**Endpoint:**
```http
DELETE /admin/tracks/{id}
```

**Headers:**
```http
Authorization: Bearer {admin_token}
```

**Example:**
```http
DELETE /admin/tracks/1
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Track deleted successfully"
}
```

**⚠️ Warning:**
- حذف الـ Track بيحذف كل الـ user enrollments (cascade)
- تأكد قبل الحذف!

---

## 10. Admin - Courses

### 10.1 Get All Courses

**Endpoint:**
```http
GET /admin/courses
```

**Headers:**
```http
Authorization: Bearer {admin_token}
```

**Success Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "HTML & CSS Basics",
            "description": "Learn HTML and CSS",
            "topics": [
                {
                    "id": 1,
                    "title": "Introduction to HTML",
                    "order": 1
                }
            ],
            "created_by": 1,
            "created_at": "2024-01-10T10:00:00.000000Z"
        }
    ]
}
```

---

### 10.2 Create Course

**Endpoint:**
```http
POST /admin/courses
```

**Headers:**
```http
Authorization: Bearer {admin_token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "title": "React Fundamentals",
    "description": "Learn React from scratch",
    "topic_ids": [10, 11, 12]
}
```

**Success Response (201):**
```json
{
    "success": true,
    "message": "Course created successfully",
    "data": {
        "id": 6,
        "title": "React Fundamentals",
        "description": "Learn React from scratch",
        "topics": [
            {
                "id": 10,
                "title": "React Introduction",
                "order": 1
            },
            {
                "id": 11,
                "title": "React Components",
                "order": 2
            },
            {
                "id": 12,
                "title": "React Hooks",
                "order": 3
            }
        ]
    }
}
```

---

### 10.3 Update Course

**Endpoint:**
```http
PUT /admin/courses/{id}
```

**Headers:**
```http
Authorization: Bearer {admin_token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "title": "Updated Course Title",
    "description": "Updated description",
    "topic_ids": [10, 12, 11, 13]
}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Course updated successfully",
    "data": { ... }
}
```

---

### 10.4 Delete Course

**Endpoint:**
```http
DELETE /admin/courses/{id}
```

**Headers:**
```http
Authorization: Bearer {admin_token}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Course deleted successfully"
}
```

---

## 11. Admin - Topics

### 11.1 Get All Topics

**Endpoint:**
```http
GET /admin/topics
```

**Headers:**
```http
Authorization: Bearer {admin_token}
```

**Success Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Introduction to HTML",
            "type": "article",
            "created_by": 1,
            "created_at": "2024-01-10T10:00:00.000000Z"
        },
        {
            "id": 2,
            "title": "HTML Tags Video",
            "type": "video",
            "video_url": "http://127.0.0.1:8000/storage/videos/file.mp4",
            "video_duration": 900,
            "created_by": 1,
            "created_at": "2024-01-11T10:00:00.000000Z"
        }
    ]
}
```

---

### 11.2 Create Topic - Article

**Endpoint:**
```http
POST /admin/topics
```

**Headers:**
```http
Authorization: Bearer {admin_token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "title": "Advanced CSS Techniques",
    "type": "article",
    "content": "<h1>Advanced CSS</h1><p>Learn advanced CSS techniques...</p>"
}
```

**Validation Rules:**
- `title`: required, string, max:255
- `type`: required, in:article,video
- `content`: required_if:type,article
- `video_url`: required_if:type,video
- `video_duration`: nullable, integer (seconds)

**Success Response (201):**
```json
{
    "success": true,
    "message": "Topic created successfully",
    "data": {
        "id": 15,
        "title": "Advanced CSS Techniques",
        "type": "article",
        "content": "<h1>Advanced CSS</h1><p>Learn advanced CSS techniques...</p>",
        "created_by": 1,
        "created_at": "2024-02-20T10:00:00.000000Z"
    }
}
```

---

### 11.3 Create Topic - Video

**Request Body:**
```json
{
    "title": "React Hooks Explained",
    "type": "video",
    "video_url": "http://127.0.0.1:8000/storage/videos/react-hooks.mp4",
    "video_duration": 1200
}
```

**Success Response (201):**
```json
{
    "success": true,
    "message": "Topic created successfully",
    "data": {
        "id": 16,
        "title": "React Hooks Explained",
        "type": "video",
        "video_url": "http://127.0.0.1:8000/storage/videos/react-hooks.mp4",
        "video_duration": 1200,
        "created_by": 1
    }
}
```

---

### 11.4 Update Topic

**Endpoint:**
```http
PUT /admin/topics/{id}
```

**Headers:**
```http
Authorization: Bearer {admin_token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "title": "Updated Title",
    "type": "article",
    "content": "<h1>Updated Content</h1>"
}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Topic updated successfully",
    "data": { ... }
}
```

---

### 11.5 Delete Topic

**Endpoint:**
```http
DELETE /admin/topics/{id}
```

**Headers:**
```http
Authorization: Bearer {admin_token}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Topic deleted successfully"
}
```

---

## 12. Admin - Quizzes

### 12.1 Get All Quizzes

**Endpoint:**
```http
GET /admin/quizzes
```

**Headers:**
```http
Authorization: Bearer {admin_token}
```

**Success Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "HTML Basics Quiz",
            "type": "topic",
            "topic_id": 1,
            "course_id": null,
            "total_points": 10,
            "pass_percentage": 50,
            "created_by": 1,
            "created_at": "2024-01-10T10:00:00.000000Z"
        }
    ]
}
```

---

### 12.2 Create Quiz

**Endpoint:**
```http
POST /admin/quizzes
```

**Headers:**
```http
Authorization: Bearer {admin_token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "title": "JavaScript Basics Quiz",
    "type": "topic",
    "topic_id": 5,
    "total_points": 20,
    "pass_percentage": 60,
    "questions": [
        {
            "question_text": "What is JavaScript?",
            "points": 10,
            "answers": [
                {
                    "answer_text": "A programming language",
                    "is_correct": true
                },
                {
                    "answer_text": "A coffee brand",
                    "is_correct": false
                },
                {
                    "answer_text": "A framework",
                    "is_correct": false
                }
            ]
        },
        {
            "question_text": "What does 'var' do?",
            "points": 10,
            "answers": [
                {
                    "answer_text": "Declares a variable",
                    "is_correct": true
                },
                {
                    "answer_text": "Creates a function",
                    "is_correct": false
                }
            ]
        }
    ]
}
```

**Validation Rules:**
- `title`: required, string
- `type`: required, in:assessment,topic,course
- `topic_id`: required_if:type,topic, exists:topics,id
- `course_id`: required_if:type,course, exists:courses,id
- `total_points`: required, integer
- `pass_percentage`: required, integer, min:1, max:100
- `questions`: required, array, min:1
- `questions.*.question_text`: required, string
- `questions.*.points`: required, integer
- `questions.*.answers`: required, array, min:2
- `questions.*.answers.*.answer_text`: required, string
- `questions.*.answers.*.is_correct`: required, boolean

**Success Response (201):**
```json
{
    "success": true,
    "message": "Quiz created successfully",
    "data": {
        "id": 10,
        "title": "JavaScript Basics Quiz",
        "type": "topic",
        "topic_id": 5,
        "total_points": 20,
        "pass_percentage": 60,
        "questions": [
            {
                "id": 25,
                "question_text": "What is JavaScript?",
                "points": 10,
                "answers": [
                    {
                        "id": 75,
                        "answer_text": "A programming language",
                        "is_correct": true
                    },
                    {
                        "id": 76,
                        "answer_text": "A coffee brand",
                        "is_correct": false
                    },
                    {
                        "id": 77,
                        "answer_text": "A framework",
                        "is_correct": false
                    }
                ]
            }
        ]
    }
}
```

**💡 Important Notes:**
- الـ `questions` و `answers` بيتعملوا في نفس الوقت (Transaction)
- كل سؤال لازم يكون فيه على الأقل إجابة واحدة صحيحة
- مجموع `points` في كل الأسئلة لازم يساوي `total_points`

---

### 12.3 Update Quiz

**Endpoint:**
```http
PUT /admin/quizzes/{id}
```

**Headers:**
```http
Authorization: Bearer {admin_token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "title": "Updated Quiz Title",
    "type": "topic",
    "topic_id": 5,
    "total_points": 25,
    "pass_percentage": 70,
    "questions": [ ... ]
}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Quiz updated successfully",
    "data": { ... }
}
```

---

### 12.4 Delete Quiz

**Endpoint:**
```http
DELETE /admin/quizzes/{id}
```

**Headers:**
```http
Authorization: Bearer {admin_token}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Quiz deleted successfully"
}
```

---

## 13. Admin - Videos

### 13.1 Get All Videos

**Endpoint:**
```http
GET /admin/videos
```

**Headers:**
```http
Authorization: Bearer {admin_token}
```

**Success Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "React Tutorial Part 1",
            "filename": "react-tutorial-abc123.mp4",
            "path": "/storage/videos/react-tutorial-abc123.mp4",
            "url": "http://127.0.0.1:8000/storage/videos/react-tutorial-abc123.mp4",
            "size": 15728640,
            "uploaded_by": 1,
            "created_at": "2024-02-15T10:00:00.000000Z"
        }
    ]
}
```

---

### 13.2 Upload Video

**Endpoint:**
```http
POST /admin/videos/upload
```

**Headers:**
```http
Authorization: Bearer {admin_token}
Content-Type: multipart/form-data
```

**Request Body (Form Data):**
```
title: "React Hooks Tutorial"
video: [file]
```

**Validation Rules:**
- `title`: required, string, max:255
- `video`: required, file, mimes:mp4,avi,mov,wmv, max:512000 (500MB)

**Success Response (201):**
```json
{
    "success": true,
    "message": "Video uploaded successfully",
    "data": {
        "id": 5,
        "title": "React Hooks Tutorial",
        "filename": "react-hooks-xyz789.mp4",
        "path": "/storage/videos/react-hooks-xyz789.mp4",
        "url": "http://127.0.0.1:8000/storage/videos/react-hooks-xyz789.mp4",
        "size": 25165824,
        "uploaded_by": 1,
        "created_at": "2024-02-20T10:30:00.000000Z"
    }
}
```

**💡 Frontend Implementation:**
```javascript
// File upload
const handleUpload = async (file, title) => {
    const formData = new FormData();
    formData.append('title', title);
    formData.append('video', file);
    
    const response = await axios.post('/admin/videos/upload', formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        },
        onUploadProgress: (progressEvent) => {
            const percent = Math.round(
                (progressEvent.loaded * 100) / progressEvent.total
            );
            setUploadProgress(percent);
        }
    });
    
    return response.data;
};

// UI
<input
    type="file"
    accept="video/mp4,video/avi,video/mov,video/wmv"
    onChange={(e) => setFile(e.target.files[0])}
/>
{uploadProgress > 0 && (
    <ProgressBar value={uploadProgress} />
)}
```

---

### 13.3 Get Video Details

**Endpoint:**
```http
GET /admin/videos/{id}
```

**Headers:**
```http
Authorization: Bearer {admin_token}
```

**Success Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "title": "React Tutorial Part 1",
        "filename": "react-tutorial-abc123.mp4",
        "url": "http://127.0.0.1:8000/storage/videos/react-tutorial-abc123.mp4",
        "size": 15728640,
        "uploaded_by": 1,
        "uploader": {
            "id": 1,
            "name": "Admin User"
        },
        "created_at": "2024-02-15T10:00:00.000000Z"
    }
}
```

---

### 13.4 Delete Video

**Endpoint:**
```http
DELETE /admin/videos/{id}
```

**Headers:**
```http
Authorization: Bearer {admin_token}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Video deleted successfully"
}
```

**⚠️ Note:**
- الملف بيتمسح من الـ Storage
- الـ Record بيتمسح من الـ Database

---

## 14. Admin - Teams

### 14.1 Get All Teams

**Endpoint:**
```http
GET /admin/teams
```

**Headers:**
```http
Authorization: Bearer {admin_token}
```

**Success Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Awesome Developers",
            "code": "ABC12345",
            "project_type": "web",
            "max_members": 5,
            "members": [
                {
                    "user": {
                        "id": 1,
                        "name": "Ahmed Hassan"
                    },
                    "track": {
                        "id": 1,
                        "title": "Web Development Track"
                    }
                }
            ],
            "created_at": "2024-02-15T10:00:00.000000Z"
        }
    ]
}
```

---

### 14.2 Get Team Details

**Endpoint:**
```http
GET /admin/teams/{id}
```

**Headers:**
```http
Authorization: Bearer {admin_token}
```

**Success Response (200):**
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
                    "email": "ahmed@test.com"
                },
                "track": {
                    "id": 1,
                    "title": "Web Development Track"
                },
                "progress": {
                    "courses_completed": 2,
                    "topics_viewed": 15,
                    "quizzes_passed": 10,
                    "total_score": 85
                }
            }
        ],
        "created_at": "2024-02-15T10:00:00.000000Z"
    }
}
```

---

## Error Handling

### HTTP Status Codes

| Code | Meaning | When it happens |
|------|---------|----------------|
| 200 | OK | Request successful |
| 201 | Created | Resource created successfully |
| 400 | Bad Request | Invalid request data |
| 401 | Unauthorized | Token missing or invalid |
| 403 | Forbidden | No permission for this resource |
| 404 | Not Found | Resource doesn't exist |
| 422 | Validation Error | Input validation failed |
| 500 | Server Error | Internal server error |

---

### Common Error Responses

#### 401 - Unauthenticated
```json
{
    "success": false,
    "message": "Unauthenticated."
}
```

**Cause:** No token or invalid token

**Frontend Action:**
```javascript
if (error.response?.status === 401) {
    // Clear storage
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    
    // Redirect to login
    navigate('/login');
}
```

---

#### 403 - Forbidden
```json
{
    "success": false,
    "message": "Unauthorized. Admin access required."
}
```

**Cause:** User doesn't have permission

**Frontend Action:**
```javascript
if (error.response?.status === 403) {
    alert('You do not have permission to access this resource.');
    navigate('/dashboard');
}
```

---

#### 422 - Validation Error
```json
{
    "success": false,
    "errors": {
        "email": [
            "The email has already been taken."
        ],
        "password": [
            "The password must be at least 8 characters."
        ]
    }
}
```

**Frontend Action:**
```javascript
if (error.response?.status === 422) {
    const errors = error.response.data.errors;
    
    // Display errors under each field
    Object.keys(errors).forEach(field => {
        showFieldError(field, errors[field][0]);
    });
}
```

---

#### 500 - Server Error
```json
{
    "success": false,
    "message": "Internal server error"
}
```

**Frontend Action:**
```javascript
if (error.response?.status === 500) {
    alert('Something went wrong. Please try again later.');
    // Log error for debugging
    console.error(error);
}
```

---

## 💡 Frontend Best Practices

### 1. Token Management
```javascript
// axios interceptor
axios.interceptors.request.use(config => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Handle token expiration
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 401) {
            localStorage.clear();
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);
```

---

### 2. Loading States
```javascript
const [loading, setLoading] = useState(false);

const fetchData = async () => {
    setLoading(true);
    try {
        const response = await api.get('/tracks');
        setData(response.data.data);
    } catch (error) {
        console.error(error);
    } finally {
        setLoading(false);
    }
};
```

---

### 3. Error Handling
```javascript
const handleError = (error) => {
    if (error.response) {
        // Server responded with error
        const message = error.response.data.message;
        toast.error(message);
    } else if (error.request) {
        // No response from server
        toast.error('Network error. Please check your connection.');
    } else {
        // Request setup error
        toast.error('An error occurred. Please try again.');
    }
};
```

---

### 4. Quiz Countdown Timer
```javascript
const [timeLeft, setTimeLeft] = useState('');
const [canRetry, setCanRetry] = useState(false);

useEffect(() => {
    if (!canRetryAt) {
        setCanRetry(true);
        return;
    }
    
    const interval = setInterval(() => {
        const now = new Date();
        const retryTime = new Date(canRetryAt);
        const diff = retryTime - now;
        
        if (diff <= 0) {
            setCanRetry(true);
            setTimeLeft('');
            clearInterval(interval);
        } else {
            const hours = Math.floor(diff / 3600000);
            const minutes = Math.floor((diff % 3600000) / 60000);
            const seconds = Math.floor((diff % 60000) / 1000);
            setTimeLeft(`${hours}h ${minutes}m ${seconds}s`);
        }
    }, 1000);
    
    return () => clearInterval(interval);
}, [canRetryAt]);
```

---

## 📋 Quick Reference Table

| Action | Method | Endpoint | Auth Required | Admin Only |
|--------|--------|----------|---------------|------------|
| Register | POST | /auth/register | ❌ | ❌ |
| Login | POST | /auth/login | ❌ | ❌ |
| Logout | POST | /auth/logout | ✅ | ❌ |
| Get Tracks | GET | /tracks | ✅ | ❌ |
| Enroll | POST | /tracks/{id}/enroll | ✅ | ❌ |
| Get Course | GET | /courses/{id} | ✅ | ❌ |
| Get Topic | GET | /topics/{id} | ✅ | ❌ |
| Mark Viewed | POST | /topics/{id}/mark-viewed | ✅ | ❌ |
| Get Quiz | GET | /quizzes/{id} | ✅ | ❌ |
| Submit Quiz | POST | /quizzes/{id}/submit | ✅ | ❌ |
| Create Team | POST | /teams/create | ✅ | ❌ |
| Join Team | POST | /teams/join | ✅ | ❌ |
| Get Progress | GET | /progress | ✅ | ❌ |
| Admin: Get Users | GET | /admin/users | ✅ | ✅ |
| Admin: Create Track | POST | /admin/tracks | ✅ | ✅ |
| Admin: Upload Video | POST | /admin/videos/upload | ✅ | ✅ |

---

## 🎯 Testing with Postman

### 1. Create Environment

**Variables:**
```
base_url: http://127.0.0.1:8000/api
token: (will be set after login)
```

### 2. Test Flow
```
1. Register → Save token
2. Login → Update token
3. Get Tracks → View available tracks
4. Enroll in Track → Start learning
5. Get Course → View topics
6. Get Topic → View content
7. Mark as Viewed → Complete topic
8. Get Quiz → View questions
9. Submit Quiz → Get result
```

**تم بحمد الله!**