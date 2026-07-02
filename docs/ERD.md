# ERD - Entity Relationship Diagram

## Relationships Explanation

### 1. Users Relationships
- users (1) → (∞) tracks [created_by]
- users (1) → (∞) courses [created_by]
- users (1) → (∞) topics [created_by]
- users (1) → (∞) quizzes [created_by]
- users (1) → (∞) videos [uploaded_by]
- users (1) → (∞) teams [created_by]
- users (∞) → (∞) tracks [user_tracks] - User enrollment
- users (∞) → (∞) teams [team_members] - Team membership

### 2. Track Relationships
- tracks (∞) → (∞) courses [track_courses] - Many-to-Many
- tracks (1) → (∞) user_tracks
- tracks (1) → (∞) team_members

### 3. Course Relationships
- courses (∞) → (∞) topics [course_topics] - Many-to-Many
- courses (1) → (∞) quizzes [course final quiz]
- courses (1) → (∞) user_course_progress

### 4. Topic Relationships
- topics (1) → (1) quizzes [topic quiz]
- topics (1) → (∞) user_topic_progress

### 5. Quiz Relationships
- quizzes (1) → (∞) questions
- quizzes (1) → (∞) user_quiz_attempts

### 6. Question Relationships
- questions (1) → (∞) answers
- questions (1) → (∞) user_quiz_answers

### 7. Answer Relationships
- answers (1) → (∞) user_quiz_answers

### 8. Team Relationships
- teams (1) → (∞) team_members

### 9. Attempt Relationships
- user_quiz_attempts (1) → (∞) user_quiz_answers

---

## Key Notes

### Pivot Tables (Many-to-Many)
1. **track_courses**: Links tracks with courses
2. **course_topics**: Links courses with topics
3. **team_members**: Links teams with users and tracks

### Progress Tracking Tables
1. **user_tracks**: Which tracks user enrolled in
2. **user_course_progress**: User progress in each course
3. **user_topic_progress**: User progress in each topic
4. **user_quiz_attempts**: All quiz attempts by user
5. **user_quiz_answers**: User answers in each attempt

### Content Tables
1. **tracks**: Learning paths
2. **courses**: Course collections
3. **topics**: Individual lessons (article/video)
4. **quizzes**: Tests (assessment/topic/course)
5. **questions**: Quiz questions
6. **answers**: Question options
7. **videos**: Video files metadata