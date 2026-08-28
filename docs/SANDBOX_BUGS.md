# Sandbox Bugs Log 
 
## Bug 001 - Unauthenticated requests without Accept header crash with 500 
- Endpoint: any auth:api protected route (e.g. /api/search) 
- Trigger: request without Accept: application/json header 
- Before fix: 500 RouteNotFoundException - Route [login] not defined 
- After fix: 401 Unauthenticated (correct) 
- Fix: added middleware->redirectGuestsTo(fn () => null) in bootstrap/app.php 
- Status: FIXED in sandbox, needs porting to real project 
## Bug 002 - Email login is case-sensitive on SQLite but not on MySQL 
- Trigger: login with lowercase email when seeder stored mixed case 
- SQLite result: 401 Invalid credentials 
- MySQL result: 200 OK (case-insensitive by default collation) 
- Impact: inconsistent behavior between Sandbox and Production 
- Fix needed: force lowercase on email before storing + before querying 
- Status: OPEN - needs fix in Phase 2 (Auth refactor) 
## Observation 001 - List Courses has no pagination 
- Endpoint: GET /api/courses 
- Issue: returns all courses in one response, no pagination 
- Impact: performance risk at scale 
- Fix: add paginate(15) in Phase 5 (API Polish) 
## Observation 002 - Course show returns all topic content inline (no pagination) 
- Endpoint: GET /api/courses/{id} 
- Issue: returns full topic content for all topics at once 
- Impact: heavy payload at scale 
- Fix: return topic list with summary only, full content on GET /topics/{id} 
## Bug 003 - Video progress accepts current_time greater than duration 
- Endpoint: POST /api/topics/{id}/video-progress 
- Input: current_time=9999, duration=600 
- Issue: no cross-field validation, logically impossible state 
- Fix: add rule current_time <= duration in Phase 2 (UpdateVideoProgressRequest) 
- Status: OPEN 
## Bug 004 - Quiz submit accepts question/answer ids not belonging to the quiz 
- Endpoint: POST /api/quizzes/{id}/submit 
- Input: answers with random question_id/answer_id 
- Issue: no validation that ids belong to this quiz, silently scores 0 
- Impact: could allow score manipulation in edge cases 
- Fix: validate each question_id belongs to quiz in Phase 2 
- Status: OPEN 
## Observation 003 - Search returns empty silently for queries under 2 chars 
- Endpoint: GET /api/search?q= 
- Issue: no feedback to user that minimum 2 characters required 
- Fix: return 400 with clear message or add message in response 
## Bug 005 - AI Chat exposes internal error message in fallback response 
- Endpoint: POST /api/ai/chat 
- Issue: when AI provider fails, response includes 'error' field with internal message 
- Example: "error": "Gemini API key not configured" 
- Fix: remove 'error' key from response in production (check APP_DEBUG) 
- Status: OPEN 
## Bug 006 - Analytics endpoint crashes with 500 (wrong column name) 
- Endpoint: GET /api/analytics 
- Error: "table user_topic_progress has no column named status" 
- Root cause: AnalyticsController uses .where('status','viewed') 
-   but migration defines boolean columns: is_viewed, is_completed 
- Fix: replace 'status','viewed' with 'is_viewed',true (3 places in controller) 
-   and 'status','completed' with 'is_completed',true 
- Status: OPEN - needs fix before any production launch 
## Bug 007 - Profile endpoint exposes sensitive fields (verification_code) 
- Endpoint: GET /api/profile 
- Issue: response includes verification_code, verification_code_expires_at 
- Fix: create UserResource that hides sensitive fields in Phase 2 
- Status: OPEN 
## Bug 008 - Update Profile marks profile_completed=true even with empty body 
- Endpoint: POST /api/profile/update 
- Issue: profile_completed forced to true regardless of actual content 
- Fix: only set profile_completed=true if name+bio+goals are all present 
- Status: OPEN 
## Observation 004 - My Teams has no pagination 
- Endpoint: GET /api/teams/my-teams 
- Issue: returns all user teams at once 
- Fix: add paginate(10) in Phase 5 
## Bug 009 - Team creation has no DB transaction (partial write risk) 
- Endpoint: POST /api/teams/create 
- Issue: 4 DB operations (Team+Member+Section+attach) with no transaction 
- Risk: if step 2/3/4 fails, orphaned team record stays in DB 
- Fix: wrap all 4 in DB::transaction() in Phase 2 
- Status: OPEN 
## Observation 005 - Team names are not unique 
- Endpoint: POST /api/teams/create 
- Issue: no unique constraint on team name, duplicates allowed 
- Decision needed: intentional or gap? 
## Bug 010 - Show Team has no membership/authorization check (potential IDOR) 
- Endpoint: GET /api/teams/{id} 
- Issue: any authenticated user can view any team's details 
- Decision needed: are teams public or private by design? 
- Fix: add membership check or make it explicit in Policy 
- Status: OPEN - needs product decision 
## Observation 006 - Update General Section silently ignores name change 
- Endpoint: PUT /api/teams/{id}/sections/{generalSectionId} 
- Issue: returns 200 success but name field is silently ignored 
- Fix: return message clarifying general section name is immutable 
## Bug 011 - Bulk checklist creation has no DB transaction 
- Endpoint: POST /api/teams/{id}/tasks/{taskId}/checklist (bulk) 
- Issue: creates items in loop without transaction 
- Risk: partial creation if any item fails midway 
- Fix: wrap foreach in DB::transaction() in Phase 2 
- Status: OPEN 
## Observation 007 - No uniqueness constraint on challenge type per week 
- Endpoint: POST /api/teams/{id}/challenges 
- Issue: can create multiple challenges with same target_type in same week 
- Decision needed: intentional or should be limited to one per type per week? 
## Bug 012 - Chat Send Message throws 500 when Reverb is offline 
- Endpoint: POST /api/teams/{id}/chat/messages 
- Issue: broadcast failure crashes the HTTP request 
- Fix: wrap broadcast in try-catch or use ShouldBroadcastNow with queue 
- Status: OPEN 
## Bug 013 - Admin List Users exposes password hash 
- Endpoint: GET /api/admin/users 
- Issue: response includes hashed password column 
- Fix: use UserResource with $hidden respected, or select specific columns 
- Status: OPEN 
## Observation 008 - Admin List Tracks returns all tracks with nested courses (no pagination) 
- Endpoint: GET /api/admin/tracks 
- Fix: paginate + lazy load courses only when needed 
## Bug 014 - Delete Track has no safety check for enrolled users 
- Endpoint: DELETE /api/admin/tracks/{id} 
- Issue: deletes track without warning about enrolled users 
- Fix: check enrollment count before delete, require confirmation or soft-delete 
- Status: OPEN - needs product decision (soft delete vs hard delete) 
## Bug 015 - Admin can create quiz with no correct answers 
- Endpoint: POST /api/admin/quizzes 
- Issue: validator checks answer count and types but not that at least one is_correct=true 
- Impact: unpassable quiz can be published 
- Fix: add custom rule - each question needs exactly one is_correct=true answer 
- Status: OPEN 
## Bug 016 - Video upload uses ini_set for upload_max_filesize (has no effect) 
- Endpoint: POST /api/admin/videos/upload 
- Issue: ini_set cannot change upload_max_filesize/post_max_size at runtime 
-   PHP reads these from php.ini before any code executes 
- Fix: set upload_max_filesize=512M in php.ini or .htaccess, remove ini_set lines 
- Status: OPEN - critical for any video larger than default php.ini limit 
## Bug 016b - Video upload catch block exposes e->getMessage() 
- Same pattern as Bug 005, 006 - needs same fix: throw $e or use generic message 
## Observation 009 - All Admin list endpoints lack pagination 
- Endpoints: admin/users, admin/tracks, admin/courses, admin/topics, admin/quizzes, admin/videos, admin/teams 
- Fix: add paginate(20) to all admin list endpoints in Phase 5 
## Bug 017 - Update Profile accepts invalid name format (numbers, special chars) 
- Endpoint: POST /api/profile/update 
- Issue: name field has no format validation, accepts "Ahmed123!@#" 
- Fix: add 'name' => 'string|max:255|regex:/^[\p{L}\s]+$/u' in UpdateProfileRequest
- Found by: teammate (independent discovery)
- Status: OPEN