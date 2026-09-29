# Business Rules — Riwaq

## 1. Track Enrollment
- User can enroll in ONE active track at a time
- To enroll in a second track: must reach 25% progress in current track
- Switching tracks: old track moves to 'waitlist' status
- Teams enforce track consistency: all members must be on same track (learning teams only)

## 2. Content Unlock System
- Topics unlock sequentially within a course
- Next topic unlocks AFTER: current topic viewed + quiz passed (if quiz exists)
- If no quiz exists on a topic: topic is always accessible (no unlock gate)
- First topic in a course: unlocked automatically on course unlock
- First course in a track: unlocked automatically on track enrollment

## 3. Quiz Rules
- User can only attempt a quiz ONCE per hour (retry cooldown after fail)
- Passed quiz: cannot retake (locked)
- Score calculation: (correct answers points / total points) × 100
- Pass threshold: defined per quiz (pass_percentage field)
- Every question MUST have at least one is_correct=true answer (enforced on creation)
- Submitted question/answer IDs must belong to the quiz (enforced in SubmitQuizRequest)

## 4. Video Progress
- Topic marked as viewed: when watched_percent >= 80%
- Progress saves: any time POST /topics/{id}/video-progress is called
- current_time cannot exceed duration (cross-field validation)
- Resume: last_position is saved and returned on GET /topics/{id}/video-progress

## 5. Teams
- Max members: set by creator (min:2, max:10)
- Team code: auto-generated, unique, 8 characters uppercase
- Leader: cannot leave team (must delete instead)
- Leader cannot be kicked
- Tasks: only leader/admin can CREATE, leader or assignee can UPDATE status
- Sections: cannot delete the General section
- Soft delete: teams are soft-deleted (data preserved 30 days)

## 6. Profile Completion
- profile_completed=true only when: name + bio + goals are ALL present
- Sending empty body to update profile does NOT mark it as completed

## 7. Notifications
- All notifications stored in notifications table (in-app)
- Events that trigger notifications:
  - TopicCompleted → 'topic_completed' notification
  - CourseCompleted → 'course_completed' notification
  - TrackCompleted → 'track_completed' notification
  - TeamInvite → 'team_invite' notification
  - MemberJoined → 'member_joined' notification to team
  - Register → 'welcome' notification

## 8. AI Chat Rate Limiting
- 30 messages per hour per user (enforced in AiChatController)
- Separate from API rate limiting (60/min)
- Fallback mode: if AI provider fails, returns keyword-based response
- Internal errors are never exposed to client (fallback: true, no error field)

## 9. Assessment
- One-time assessment per user (can view result but not retake)
- Answers: A, B, C, D only (case-sensitive, uppercase)
- Recommends a track based on score distribution across domains

## 10. Password Reset
- Same 6-digit code mechanism as email verification
- Code expires in 15 minutes
- User enumeration protection: always returns 200 regardless of email existence
- Code cleared after successful reset