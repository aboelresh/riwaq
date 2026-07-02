# Project Structure

## Backend Structure (Laravel)
```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── Auth/
│   │   │   │   │   ├── LoginController.php
│   │   │   │   │   ├── RegisterController.php
│   │   │   │   │   └── PasswordResetController.php
│   │   │   │   ├── TrackController.php
│   │   │   │   ├── CourseController.php
│   │   │   │   ├── TopicController.php
│   │   │   │   ├── QuizController.php
│   │   │   │   ├── TeamController.php
│   │   │   │   ├── ProgressController.php
│   │   │   │   └── Admin/
│   │   │   │       ├── UserController.php
│   │   │   │       ├── TrackController.php
│   │   │   │       ├── CourseController.php
│   │   │   │       ├── TopicController.php
│   │   │   │       ├── QuizController.php
│   │   │   │       ├── VideoController.php
│   │   │   │       └── TeamController.php
│   │   ├── Middleware/
│   │   │   ├── IsAdmin.php
│   │   │   └── TrackProgress.php
│   │   └── Requests/
│   │       ├── Auth/
│   │       ├── Track/
│   │       ├── Course/
│   │       ├── Topic/
│   │       ├── Quiz/
│   │       └── Team/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Track.php
│   │   ├── Course.php
│   │   ├── Topic.php
│   │   ├── Quiz.php
│   │   ├── Question.php
│   │   ├── Answer.php
│   │   ├── Team.php
│   │   ├── Video.php
│   │   ├── UserTrack.php
│   │   ├── UserCourseProgress.php
│   │   ├── UserTopicProgress.php
│   │   ├── UserQuizAttempt.php
│   │   └── TeamMember.php
│   └── Services/
│       ├── ProgressService.php
│       ├── QuizService.php
│       ├── UnlockService.php
│       └── VideoService.php
├── database/
│   ├── migrations/
│   │   ├── 2024_01_01_000001_create_users_table.php
│   │   ├── 2024_01_01_000002_create_tracks_table.php
│   │   ├── 2024_01_01_000003_create_courses_table.php
│   │   ├── ... (all migrations)
│   └── seeders/
│       └── DatabaseSeeder.php
├── routes/
│   └── api.php
├── storage/
│   └── app/
│       └── videos/
└── public/
    └── storage/
```

## Files We Will Create

### Migrations (17 files)
1. create_users_table
2. create_tracks_table
3. create_courses_table
4. create_track_courses_table
5. create_topics_table
6. create_course_topics_table
7. create_quizzes_table
8. create_questions_table
9. create_answers_table
10. create_user_tracks_table
11. create_user_course_progress_table
12. create_user_topic_progress_table
13. create_user_quiz_attempts_table
14. create_user_quiz_answers_table
15. create_teams_table
16. create_team_members_table
17. create_videos_table

### Models (15 files)
User, Track, Course, Topic, Quiz, Question, Answer, Team, Video, UserTrack, UserCourseProgress, UserTopicProgress, UserQuizAttempt, UserQuizAnswer, TeamMember

### Controllers (19 files)
Auth (3), Public API (6), Admin API (7), Middleware (2), Services (4)

### Routes (1 file)
api.php

---

## Total Files to Create: ~52 files