#  Riwaq - Workflow & System Flow

## 1. User Registration & Track Selection Flow
```
START
  ↓
User Registers → JWT Token Generated
  ↓
User Logs In → Token Stored in Frontend
  ↓
[OPTIONAL] User Takes Assessment Quiz → System Recommends Track
  ↓
OR
  ↓
User Manually Selects Track
  ↓
User Enrolls in Track → System Creates UserTrack Record
  ↓ 
System Unlocks First Course in Track
  ↓
System Unlocks First Topic in First Course
  ↓
User Can Start Learning
```

---

## 2. Learning & Progress Flow
```
User Opens Unlocked Topic
  ↓
IF Topic Type = Video:
  → User Watches Video
  → User Clicks "Mark as Viewed" Button
  → System Records View in UserTopicProgress
  
IF Topic Type = Article:
  → User Reads Article
  → User Clicks "Mark as Viewed" Button
  → System Records View in UserTopicProgress
  ↓
User Takes Topic Quiz
  ↓
System Calculates Score
  ↓
IF Score >= 50%:
  → Quiz Passed
  → System Records in UserQuizAttempt (passed = true)
  → System Unlocks Next Topic in Course
  → User Can Continue
  
IF Score < 50%:
  → Quiz Failed
  → System Records in UserQuizAttempt (passed = false)
  → System Sets can_retry_at = now() + 1 hour
  → User Must Wait 1 Hour to Retry
```

---

## 3. Course Completion Flow
```
User Completes All Topics in Course
  ↓
User Takes Course Final Quiz
  ↓
System Calculates Total Score:
  - All Topic Quiz Scores (if passed)
  + Course Final Quiz Score
  ↓
System Calculates Course Max Score:
  - Sum of All Topic Quiz Points
  + Course Final Quiz Points
  ↓
System Calculates Percentage:
  Percentage = (Total Score / Max Score) × 100
  ↓
IF Percentage >= 50%:
  → Course Passed
  → System Marks Course as Completed
  → System Updates UserCourseProgress (is_completed = true)
  → System Unlocks Next Course in Track
  
IF Percentage < 50%:
  → Course Failed
  → System Sets can_retry_at = now() + 1 hour
  → User Must Retake Course Final Quiz After 1 Hour
```

---

## 4. Track Completion Flow
```
User Completes All Courses in Track
  ↓
System Marks Track as Completed
  ↓
System Updates UserTrack (completed_at = now())
  ↓
User Can:
  - Enroll in Another Track
  - Join Teams
  - View Certificates (if implemented)
```

---

## 5. Team System Flow
```
User A Creates Team
  ↓
System Generates Unique Team Code (8 characters)
  ↓
User A Shares Code with Team Members
  ↓
User B Joins Team Using Code
  ↓
User B Selects Track They Will Work On
  ↓
System Creates TeamMember Record
  ↓
All Team Members Can:
  - View Each Other's Progress
  - See Courses Completed
  - See Topics Viewed
  - See Quiz Scores
  ↓
Team Progress is Calculated in Real-Time
```

---

## 6. Admin Content Management Flow
```
Admin Logs In (role = 'admin')
  ↓
Admin Can:
  
CREATE TRACK:
  → Define Track Title & Description
  → Select Existing Courses
  → Set Course Order
  → Save Track
  
CREATE COURSE:
  → Define Course Title & Description
  → Select Existing Topics
  → Set Topic Order
  → Assign to Multiple Tracks
  → Save Course
  
CREATE TOPIC:
  → Choose Type (Article or Video)
  → IF Article: Write Content
  → IF Video: Upload or Link Video
  → Save Topic
  → Assign to Multiple Courses
  
CREATE QUIZ:
  → Choose Type (Assessment, Topic, Course)
  → Link to Topic or Course
  → Add Questions
  → Add Multiple Choice Answers
  → Mark Correct Answers
  → Set Total Points
  → Set Pass Percentage (default 50%)
  → Save Quiz
  
UPLOAD VIDEO:
  → Select Video File (max 500MB)
  → Enter Video Title
  → System Stores in storage/app/public/videos
  → System Creates Video Record
  → Video URL Generated: /storage/videos/{filename}
  
VIEW USERS:
  → See All Learners
  → View Individual Progress
  → See Enrolled Tracks
  → View Course Completion
  → See Quiz Scores
  
VIEW TEAMS:
  → See All Teams
  → View Team Members
  → Monitor Team Progress
```

---

## 7. Quiz Submission & Grading Flow
```
User Opens Quiz
  ↓
System Checks:
  - Has User Passed This Quiz Before? → Block Access
  - Can User Retry Now? → Check can_retry_at
  ↓
IF Access Granted:
  → Display Questions (without showing correct answers)
  → User Selects Answers
  → User Submits Quiz
  ↓
System Processing:
  1. Loop Through Each Question
  2. Check if Selected Answer is Correct
  3. Add Points if Correct
  4. Calculate Total Score
  5. Calculate Percentage = (Score / Max Score) × 100
  ↓
  6. Check Pass Status:
     IF Percentage >= pass_percentage:
       → passed = true
       → can_retry_at = null
     ELSE:
       → passed = false
       → can_retry_at = now() + 1 hour
  ↓
  7. Create UserQuizAttempt Record
  8. Create UserQuizAnswer Records (for each question)
  ↓
  9. Trigger Unlock Logic:
     IF Quiz Type = Topic Quiz AND Passed:
       → Unlock Next Topic in Course
       → Update Course Score
     
     IF Quiz Type = Course Quiz AND Passed:
       → Check Course Completion
       → Unlock Next Course in Track
  ↓
Return Result to User
```

---

## 8. Unlock Logic Details

### Topic Unlock:
```
Current Topic Quiz Passed
  ↓
Get All Topics in Course (ordered)
  ↓
Find Current Topic Position
  ↓
Get Next Topic
  ↓
Create/Update UserTopicProgress
  → is_unlocked = true
```

### Course Unlock:
```
Current Course Completed (50%+ score)
  ↓
Get All Courses in Track (ordered)
  ↓
Find Current Course Position
  ↓
Get Next Course
  ↓
Create/Update UserCourseProgress
  → is_unlocked = true
  → started_at = now()
  ↓
Unlock First Topic in New Course
```

---

## 9. Score Calculation Details

### Topic Quiz Score:
```
User Answers = [Q1: A2, Q2: A5, Q3: A9]
  ↓
For Each Answer:
  IF answer.is_correct = true:
    score += question.points
  ↓
Final Score = Sum of Correct Answer Points
```

### Course Total Score:
```
Course Total Score = 
  Sum of All Topic Quiz Scores (only passed attempts)
  +
  Course Final Quiz Score (if passed)
```

### Course Pass Calculation:
```
Max Possible Score = 
  Sum of All Topic Quiz total_points
  +
  Course Final Quiz total_points

User Total Score = [calculated above]

Percentage = (User Total Score / Max Possible Score) × 100

IF Percentage >= 50%:
  → Course Passed
ELSE:
  → Course Failed
```

---

## 10. Database Operations Flow

### User Progress Initialization:
```
When User Enrolls in Track:
  1. Create UserTrack record
  2. Get First Course
  3. Create UserCourseProgress (is_unlocked = true)
  4. Get First Topic
  5. Create UserTopicProgress (is_unlocked = true)
```

### Quiz Attempt Creation:
```
When User Submits Quiz:
  1. Begin Database Transaction
  2. Create UserQuizAttempt
  3. For Each Answer:
     → Create UserQuizAnswer
  4. Commit Transaction
  5. Trigger Unlock Logic
  6. Update Progress Scores
```

### Progress Update:
```
After Any Quiz Submission:
  1. Calculate New Course Score
  2. Update UserCourseProgress.total_score
  3. Check if Course Should be Completed
  4. IF Yes:
     → Set is_completed = true
     → Set completed_at = now()
     → Trigger Next Course Unlock
```

---

## 11. Security & Authorization Flow

### JWT Authentication:
```
User Logs In
  ↓
Server Validates Credentials
  ↓
Server Generates JWT Token (contains user_id, role)
  ↓
Token Sent to Frontend
  ↓
Frontend Stores Token (localStorage/cookie)
  ↓
On Each Request:
  → Frontend Sends Token in Authorization Header
  → Server Validates Token
  → Server Extracts User Info
  → Server Checks Permissions
  → IF Valid: Process Request
  → IF Invalid: Return 401 Unauthorized
```

### Admin Authorization:
```
Request to Admin Endpoint
  ↓
IsAdmin Middleware Checks:
  1. Is User Authenticated?
  2. Is User Role = 'admin'?
  ↓
IF Both True:
  → Allow Access
ELSE:
  → Return 403 Forbidden
```

---

## 12. Error Handling Flow
```
User Makes Request
  ↓
System Validates Input
  ↓
IF Validation Fails:
  → Return 422 with Error Details
  
IF User Not Authenticated:
  → Return 401 Unauthorized
  
IF User Not Authorized (wrong role):
  → Return 403 Forbidden
  
IF Resource Not Found:
  → Return 404 Not Found
  
IF Server Error:
  → Log Error
  → Return 500 with Generic Message
  
IF Success:
  → Return 200/201 with Data
```

---

## Summary

The system follows a strict progression model:
1. Users must complete quizzes to unlock next content
2. Minimum 50% score required to pass
3. Failed attempts have 1-hour cooldown
4. Progress is tracked at every level (topic, course, track)
5. Teams enable collaborative learning and progress monitoring
6. Admins have full control over content creation and user monitoring