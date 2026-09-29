# Riwaq - Feature List

## 1. User Management (إدارة المستخدمين)
### 1.1 Authentication (المصادقة)
-  Registration (التسجيل)
-  Login (تسجيل الدخول)
-  Logout (تسجيل الخروج)
-  Password Reset (استعادة كلمة المرور)
-  Email Verification (تأكيد البريد الإلكتروني)

### 1.2 User Roles (أدوار المستخدمين)
-  Learner (متعلم)
-  Admin (مدير)

---

## 2. Track System (نظام المسارات)
### 2.1 Track Selection (اختيار المسار)
-  Optional Assessment Quiz (امتحان تحديد الميول - اختياري)
-  Manual Track Selection (اختيار المسار يدوياً)

### 2.2 Track Structure (بنية المسار)
-  Track contains Multiple Courses (المسار يحتوي على كورسات)
-  Course contains Multiple Topics (الكورس يحتوي على مواضيع)
-  Topic can be Article or Video (الموضوع: مقال أو فيديو)

---

## 3. Progress System (نظام التقدم)
### 3.1 Unlocking Mechanism (آلية فتح المحتوى)
-  First Topic in First Course Unlocked by Default
-  Next Topic Unlocks when User achieves 50% Quiz Score
-  Retry After 1 Hour if Failed

### 3.2 Scoring System (نظام النقاط)
-  Video View Completion (مشاهدة الفيديو)
-  Article Read Completion (قراءة المقال)
-  Topic Quiz Score (نقاط كويز الموضوع)
-  Course Final Quiz Score (نقاط الامتحان النهائي)

### 3.3 Course Completion (إتمام الكورس)
-  Course Final Quiz = Sum of All Topic Quiz Scores
-  Pass Course: 50% of Total Course Points
-  Unlock Next Course After Pass

---

## 4. Quiz System (نظام الاختبارات)
### 4.1 Quiz Types (أنواع الاختبارات)
-  Assessment Quiz (امتحان تحديد الميول)
-  Topic Quiz (كويز بعد كل موضوع)
-  Course Final Quiz (امتحان نهائي للكورس)

### 4.2 Quiz Features (مميزات الاختبار)
-  Multiple Choice Questions
-  Score Calculation
-  Pass/Fail Logic (50% threshold)
-  Retry After 1 Hour

---

## 5. Team System (نظام الفرق)
### 5.1 Team Creation (إنشاء فريق)
-  Create Team with Unique Code
-  Define Project Type (Mobile/Web)
-  Define Number of Members
-  Select Required Tracks

### 5.2 Team Management (إدارة الفريق)
-  Assign Members to Tracks
-  Member Info (Name, Email, Photo)
-  Join Team Using Code
-  View Team Members Progress

---

## 6. Admin Dashboard (لوحة التحكم)
### 6.1 User Management (إدارة المستخدمين)
-  View All Users
-  View User Progress
-  View User Tracks

### 6.2 Content Management (إدارة المحتوى)
-  Create/Edit/Delete Tracks
-  Create/Edit/Delete Courses
-  Create/Edit/Delete Topics
-  Create/Edit/Delete Quizzes
-  Assign Courses to Multiple Tracks
-  Assign Topics to Multiple Courses

### 6.3 Team Monitoring (مراقبة الفرق)
-  View All Teams
-  View Team Members
-  View Team Progress

### 6.4 Media Management (إدارة الوسائط)
-  Upload Videos
-  View/Delete Videos
-  Video Storage & Streaming

---

## 7. API Endpoints (نقاط النهاية)
### 7.1 Authentication APIs
- POST /api/register
- POST /api/login
- POST /api/logout
- POST /api/password/reset

### 7.2 Track APIs
- GET /api/tracks
- GET /api/tracks/{id}/courses
- POST /api/tracks (Admin)

### 7.3 Course APIs
- GET /api/courses/{id}/topics
- POST /api/courses (Admin)

### 7.4 Topic APIs
- GET /api/topics/{id}
- POST /api/topics/{id}/complete
- POST /api/topics (Admin)

### 7.5 Quiz APIs
- GET /api/quizzes/{id}
- POST /api/quizzes/{id}/submit
- GET /api/quizzes/{id}/result

### 7.6 Team APIs
- POST /api/teams
- POST /api/teams/join
- GET /api/teams/{id}/members
- GET /api/teams/{id}/progress

### 7.7 Progress APIs
- GET /api/user/progress
- GET /api/user/scores

### 7.8 Admin APIs
- GET /api/admin/users
- GET /api/admin/teams
- POST /api/admin/videos/upload