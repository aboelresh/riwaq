<?php

use App\Http\Controllers\Api\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Api\Admin\QuizController as AdminQuizController;
use App\Http\Controllers\Api\Admin\TeamController as AdminTeamController;
use App\Http\Controllers\Api\Admin\TopicController as AdminTopicController;
use App\Http\Controllers\Api\Admin\TrackController as AdminTrackController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\VideoController as AdminVideoController;
use App\Http\Controllers\Api\AiChatController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\AssessmentController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Auth\VerificationController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\LevelAnalysisController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ProgressController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\TaskChecklistController;
use App\Http\Controllers\Api\TeamChallengeController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\TeamNoteController;
use App\Http\Controllers\Api\TeamSectionController;
use App\Http\Controllers\Api\TeamTaskController;
use App\Http\Controllers\Api\TopicController;
use App\Http\Controllers\Api\TrackController;
use App\Http\Controllers\Api\VideoStreamController;
use Illuminate\Support\Facades\Route;

// ── Health Check ───────────────────────────────────────────────────────────
Route::get('/', fn() => response()->json([
    'success' => true,
    'message' => 'Code Master API',
    'version' => '1.0.0',
]));

// ── Auth — rate limited 5/min per IP ───────────────────────────────────────
Route::middleware('throttle:auth')->prefix('auth')->group(function () {
    Route::post('register',        [RegisterController::class, 'register']);
    Route::post('login',           [LoginController::class, 'login']);
    Route::post('forgot-password', [ForgotPasswordController::class, 'send']);
    Route::post('reset-password',  [ResetPasswordController::class, 'reset']);

    Route::middleware('auth:api')->group(function () {
        Route::post('logout',        [LogoutController::class, 'logout']);
        Route::post('verify',        [VerificationController::class, 'verify']);
        Route::post('verify/resend', [VerificationController::class, 'resend']);
    });
});

// ── Public browsing ────────────────────────────────────────────────────────
Route::get('/tracks',       [TrackController::class, 'index']);
Route::get('/tracks/{id}',  [TrackController::class, 'show'])->where('id', '[0-9]+');
Route::get('/courses',      [CourseController::class, 'index']);
Route::get('/courses/{id}', [CourseController::class, 'show'])->where('id', '[0-9]+');

// ── Authenticated — rate limited 60/min per user ───────────────────────────
Route::middleware(['auth:api', 'throttle:api'])->group(function () {

    // Search
    Route::get('/search', [SearchController::class, 'search']);

    // AI Chat — sensitive: 10/min per user
    Route::middleware('throttle:sensitive')
         ->post('/ai/chat', [AiChatController::class, 'chat']);

    // Video streaming
    Route::get('/videos/stream/{topicId}', [VideoStreamController::class, 'stream']);

    // Tracks (authenticated)
    Route::get('/tracks/my-tracks',         [TrackController::class, 'myTracks']);
    Route::post('/tracks/{id}/enroll',      [TrackController::class, 'enroll']);
    Route::post('/tracks/{id}/switch',      [TrackController::class, 'switchTrack']);

    // Topics
    Route::get('/topics/{id}',                  [TopicController::class, 'show']);
    Route::post('/topics/{id}/mark-viewed',     [TopicController::class, 'markAsViewed']);
    Route::get('/topics/{id}/video-progress',   [TopicController::class, 'getVideoProgress']);
    Route::post('/topics/{id}/video-progress',  [TopicController::class, 'updateVideoProgress']);

    // Quizzes
    Route::get('/quizzes/{id}',                       [QuizController::class, 'show']);
    Route::post('/quizzes/{id}/submit',               [QuizController::class, 'submit']);
    Route::get('/quizzes/attempts/{attemptId}/result',[QuizController::class, 'result']);

    // Assessment
    Route::get('/assessment/questions',  [AssessmentController::class, 'getQuestions']);
    Route::post('/assessment/submit',    [AssessmentController::class, 'submit']);
    Route::get('/assessment/my-result',  [AssessmentController::class, 'getMyResult']);

    // Progress
    Route::get('/progress',               [ProgressController::class, 'index']);
    Route::get('/progress/level-analysis',[LevelAnalysisController::class, 'show']);

    // Profile
    Route::get('/profile',        [ProfileController::class, 'show']);
    Route::post('/profile/update',[ProfileController::class, 'update']);

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index']);

    // Notifications
    Route::get('/notifications',              [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('/notifications/read-all',    [NotificationController::class, 'markAllRead']);
    Route::post('/notifications/{id}/read',   [NotificationController::class, 'markRead']);

    // ── Teams ──────────────────────────────────────────────────────────────
    Route::get('/teams/my-teams',    [TeamController::class, 'myTeams']);
    Route::get('/teams/search-user', [TeamController::class, 'searchUser']);
    Route::get('/teams/my-tasks',    [TeamTaskController::class, 'myTasks']);
    Route::post('/teams/create',     [TeamController::class, 'create']);
    Route::post('/teams/join',       [TeamController::class, 'join']);

    // Team base
    Route::get('/teams/{id}',                    [TeamController::class, 'show']);
    Route::put('/teams/{id}',                    [TeamController::class, 'update']);
    Route::delete('/teams/{id}',                 [TeamController::class, 'destroy']);
    Route::post('/teams/{id}/leave',             [TeamController::class, 'leave']);
    Route::get('/teams/{id}/progress',           [TeamController::class, 'progress']);
    Route::get('/teams/{id}/activity',           [TeamController::class, 'activity']);
    Route::get('/teams/{id}/achievements/{uid}', [TeamController::class, 'achievements']);
    Route::delete('/teams/{id}/members/{uid}',   [TeamController::class, 'kickMember']);
    Route::post('/teams/{id}/invite',            [TeamController::class, 'inviteByUsername']);
    Route::post('/teams/{id}/invite-email',      [TeamController::class, 'inviteByEmail']);

    // Sections
    Route::get('/teams/{teamId}/sections',                           [TeamSectionController::class, 'index']);
    Route::post('/teams/{teamId}/sections',                          [TeamSectionController::class, 'store']);
    Route::put('/teams/{teamId}/sections/{sectionId}',               [TeamSectionController::class, 'update']);
    Route::delete('/teams/{teamId}/sections/{sectionId}',            [TeamSectionController::class, 'destroy']);
    Route::post('/teams/{teamId}/sections/{sectionId}/members',      [TeamSectionController::class, 'addMembers']);
    Route::delete('/teams/{teamId}/sections/{sectionId}/members/{userId}', [TeamSectionController::class, 'removeMember']);

    // Notes
    Route::get('/teams/{teamId}/notes',              [TeamNoteController::class, 'all']);
    Route::get('/teams/{teamId}/notes/{userId}',     [TeamNoteController::class, 'index']);
    Route::post('/teams/{teamId}/notes',             [TeamNoteController::class, 'store']);
    Route::put('/teams/{teamId}/notes/{noteId}',     [TeamNoteController::class, 'update']);
    Route::delete('/teams/{teamId}/notes/{noteId}',  [TeamNoteController::class, 'destroy']);

    // Tasks
    Route::get('/teams/{teamId}/tasks',              [TeamTaskController::class, 'index']);
    Route::post('/teams/{teamId}/tasks',             [TeamTaskController::class, 'store']);
    Route::put('/teams/{teamId}/tasks/{taskId}',     [TeamTaskController::class, 'update']);
    Route::delete('/teams/{teamId}/tasks/{taskId}',  [TeamTaskController::class, 'destroy']);
    Route::get('/teams/{teamId}/tasks/{taskId}/comments',  [TeamTaskController::class, 'comments']);
    Route::post('/teams/{teamId}/tasks/{taskId}/comments', [TeamTaskController::class, 'addComment']);

    // Checklist
    Route::get('/teams/{teamId}/tasks/{taskId}/checklist',                  [TaskChecklistController::class, 'index']);
    Route::post('/teams/{teamId}/tasks/{taskId}/checklist',                 [TaskChecklistController::class, 'store']);
    Route::put('/teams/{teamId}/tasks/{taskId}/checklist/{itemId}',         [TaskChecklistController::class, 'update']);
    Route::post('/teams/{teamId}/tasks/{taskId}/checklist/{itemId}/toggle', [TaskChecklistController::class, 'toggle']);
    Route::delete('/teams/{teamId}/tasks/{taskId}/checklist/{itemId}',      [TaskChecklistController::class, 'destroy']);

    // Challenges
    Route::get('/teams/{teamId}/challenges',          [TeamChallengeController::class, 'index']);
    Route::post('/teams/{teamId}/challenges',         [TeamChallengeController::class, 'store']);
    Route::get('/teams/{teamId}/challenges/progress', [TeamChallengeController::class, 'progress']);

    // Chat
    Route::get('/teams/{teamId}/chat/channels',   [ChatController::class, 'getChannels']);
    Route::get('/teams/{teamId}/chat/messages',   [ChatController::class, 'getMessages']);
    Route::post('/teams/{teamId}/chat/messages',  [ChatController::class, 'sendMessage']);
    Route::post('/teams/{teamId}/chat/dm',        [ChatController::class, 'getOrCreateDm']);
});

// ── Admin — rate limited 60/min per user ───────────────────────────────────
Route::middleware(['auth:api', 'admin', 'throttle:api'])->prefix('admin')->group(function () {

    // Users
    Route::get('users',     [AdminUserController::class, 'index']);
    Route::get('users/{id}',[AdminUserController::class, 'show']);

    // Tracks
    Route::get('tracks',        [AdminTrackController::class, 'index']);
    Route::post('tracks',       [AdminTrackController::class, 'store']);
    Route::put('tracks/{id}',   [AdminTrackController::class, 'update']);
    Route::delete('tracks/{id}',[AdminTrackController::class, 'destroy']);

    // Courses
    Route::get('courses',        [AdminCourseController::class, 'index']);
    Route::post('courses',       [AdminCourseController::class, 'store']);
    Route::put('courses/{id}',   [AdminCourseController::class, 'update']);
    Route::delete('courses/{id}',[AdminCourseController::class, 'destroy']);

    // Topics
    Route::get('topics',        [AdminTopicController::class, 'index']);
    Route::post('topics',       [AdminTopicController::class, 'store']);
    Route::put('topics/{id}',   [AdminTopicController::class, 'update']);
    Route::delete('topics/{id}',[AdminTopicController::class, 'destroy']);

    // Quizzes
    Route::get('quizzes',        [AdminQuizController::class, 'index']);
    Route::post('quizzes',       [AdminQuizController::class, 'store']);
    Route::put('quizzes/{id}',   [AdminQuizController::class, 'update']);
    Route::delete('quizzes/{id}',[AdminQuizController::class, 'destroy']);

    // Videos — sensitive: 10/min per user
    Route::middleware('throttle:sensitive')->group(function () {
        Route::get('videos',          [AdminVideoController::class, 'index']);
        Route::post('videos/upload',  [AdminVideoController::class, 'upload']);
        Route::get('videos/{id}',     [AdminVideoController::class, 'show']);
        Route::delete('videos/{id}',  [AdminVideoController::class, 'destroy']);
    });

    // Teams
    Route::get('teams',      [AdminTeamController::class, 'index']);
    Route::get('teams/{id}', [AdminTeamController::class, 'show']);
});