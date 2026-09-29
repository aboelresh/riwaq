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
use App\Http\Controllers\Api\Organization\OrganizationController;

// ── API v1 wrapper ──────────────────────────────────────────────────────────
// All routes are under /api/v1/ prefix for future versioning support.
// When v2 is needed, create routes/api_v2.php without breaking v1 clients.
Route::prefix('v1')->group(function () {

// Health check
Route::get('/', fn() => response()->json([
    'success' => true,
    'message' => 'Riwaq API',
    'version' => '1.0.0',
]));
Route::get('/health', [\App\Http\Controllers\Api\HealthController::class, 'check']);

// ─── Auth (rate limited: 5/min per IP) ────────────────────────────────────
Route::middleware('throttle:auth')->prefix('auth')->group(function () {
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('login',    [LoginController::class, 'login']);

    // Forgot + Reset Password (uses same 6-digit code mechanism as verification)
    Route::post('forgot-password', [ForgotPasswordController::class, 'send']);
    Route::post('reset-password',  [ResetPasswordController::class, 'reset']);

    Route::middleware('auth:api')->group(function () {
        Route::post('logout',        [LogoutController::class, 'logout']);
        Route::post('verify',        [VerificationController::class, 'verify']);
        Route::post('verify/resend', [VerificationController::class, 'resend']);
        Route::post('switch-organization', [\App\Http\Controllers\Api\Auth\SwitchOrganizationController::class, 'switch']);
    });
});

// ─── Public browsing (no auth needed) ─────────────────────────────────────
Route::get('/tracks',          [TrackController::class, 'index']);
Route::get('/tracks/{id}',     [TrackController::class, 'show'])->where('id', '[0-9]+');
Route::get('/courses',         [CourseController::class, 'index']);
Route::get('/courses/{id}',    [CourseController::class, 'show'])->where('id', '[0-9]+');

// ─── Authenticated routes (rate limited: 60/min per user) ─────────────────
Route::middleware(['auth:api', 'throttle:api'])->group(function () {

    // Search
    Route::get('/search', [SearchController::class, 'search']);

    // AI Chat (sensitive: 10/min per user)
    Route::middleware('throttle:sensitive')
        ->post('/ai/chat', [AiChatController::class, 'chat']);

    // Tracks
    Route::prefix('tracks')->group(function () {
        Route::get('/my-tracks',      [TrackController::class, 'myTracks']);
        Route::post('/{id}/enroll',   [TrackController::class, 'enroll']);
        Route::post('/{id}/switch',   [TrackController::class, 'switchTrack']);
    });

    // Topics
    Route::prefix('topics/{id}')->group(function () {
        Route::get('/',               [TopicController::class, 'show']);
        Route::post('/mark-viewed',   [TopicController::class, 'markAsViewed']);
        Route::get('/video-progress', [TopicController::class, 'getVideoProgress']);
        Route::post('/video-progress',[TopicController::class, 'updateVideoProgress']);
    });

    // Videos
    Route::get('/videos/stream/{id}', [VideoStreamController::class, 'stream']);

    // Quizzes
    Route::prefix('quizzes')->group(function () {
        Route::get('/{id}',              [QuizController::class, 'show']);
        Route::post('/{id}/submit',      [QuizController::class, 'submit']);
        Route::get('/attempts/{id}/result', [QuizController::class, 'result']);
    });

    // Assessment
    Route::prefix('assessment')->group(function () {
        Route::get('/questions',  [AssessmentController::class, 'getQuestions']);
        Route::post('/submit',    [AssessmentController::class, 'submit']);
        Route::get('/my-result',  [AssessmentController::class, 'getMyResult']);
    });

    // Progress
    Route::prefix('progress')->group(function () {
        Route::get('/',              [ProgressController::class, 'index']);
        Route::get('/level-analysis',[LevelAnalysisController::class, 'index']);
    });

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index']);

    // Profile
    Route::prefix('profile')->group(function () {
        Route::get('/',       [ProfileController::class, 'show']);
        Route::post('/update',[ProfileController::class, 'update']);
    });

    // Notifications
    Route::prefix('notifications')->group(function () {
    Route::get('/',              [NotificationController::class, 'index']);
    Route::get('/unread-count',  [NotificationController::class, 'unreadCount']);
    Route::post('/read-all',     [NotificationController::class, 'markAllRead']);
    Route::post('/{id}/read',    [NotificationController::class, 'markRead']);
});
    // ── Organization Management ────────────────────────────────────
    Route::prefix('organization')->group(function () {
    Route::get('/',                         [OrganizationController::class, 'show']);
    Route::put('/',                         [OrganizationController::class, 'update']);
    Route::get('/members',                  [OrganizationController::class, 'members']);
    Route::post('/members/invite',          [OrganizationController::class, 'invite']);
    Route::put('/members/{userId}/role',    [OrganizationController::class, 'updateRole']);
    Route::delete('/members/{userId}',      [OrganizationController::class, 'removeMember']);
    Route::get('/usage',                    [OrganizationController::class, 'usage']);
});

    // Teams
    Route::prefix('teams')->group(function () {
          Route::get('/my-teams',   [TeamController::class, 'myTeams']);
        Route::get('/my-tasks',   [TeamTaskController::class, 'myTasks']);
        Route::get('/search-user',[TeamController::class, 'searchUser']);
        Route::post('/create',    [TeamController::class, 'create']);
        Route::post('/join',      [TeamController::class, 'join']);

        Route::prefix('/{teamId}')->group(function () {
            Route::get('/',         [TeamController::class, 'show']);
            Route::put('/',         [TeamController::class, 'update']);
            Route::delete('/',      [TeamController::class, 'destroy']);
            Route::get('/progress', [TeamController::class, 'progress']);
            Route::get('/activity', [TeamController::class, 'activity']);
            Route::post('/leave',   [TeamController::class, 'leave']);
            Route::get('/achievements/{userId}', [TeamController::class, 'achievements']);
            Route::delete('/members/{userId}',   [TeamController::class, 'kickMember']);
            Route::post('/invite',       [TeamController::class, 'inviteByUsername']);
            Route::post('/invite-email', [TeamController::class, 'inviteByEmail']);

            // Sections
            Route::prefix('/sections')->group(function () {
                Route::get('/',                           [TeamSectionController::class, 'index']);
                Route::post('/',                          [TeamSectionController::class, 'store']);
                Route::put('/{sectionId}',                [TeamSectionController::class, 'update']);
                Route::delete('/{sectionId}',             [TeamSectionController::class, 'destroy']);
                Route::post('/{sectionId}/members',       [TeamSectionController::class, 'addMembers']);
                Route::delete('/{sectionId}/members/{userId}', [TeamSectionController::class, 'removeMember']);
            });

            // Notes
            Route::prefix('/notes')->group(function () {
                Route::get('/',           [TeamNoteController::class, 'all']);
                Route::get('/{userId}',   [TeamNoteController::class, 'index']);
                Route::post('/',          [TeamNoteController::class, 'store']);
                Route::put('/{noteId}',   [TeamNoteController::class, 'update']);
                Route::delete('/{noteId}',[TeamNoteController::class, 'destroy']);
            });

            // Tasks
            Route::prefix('/tasks')->group(function () {
                Route::get('/',          [TeamTaskController::class, 'index']);
                Route::post('/',         [TeamTaskController::class, 'store']);
                Route::put('/{taskId}',  [TeamTaskController::class, 'update']);
                Route::delete('/{taskId}',[TeamTaskController::class, 'destroy']);
                Route::get('/{taskId}/comments',  [TeamTaskController::class, 'comments']);
                Route::post('/{taskId}/comments', [TeamTaskController::class, 'addComment']);

                // Checklist
                Route::prefix('/{taskId}/checklist')->group(function () {
                    Route::get('/',             [TaskChecklistController::class, 'index']);
                    Route::post('/',            [TaskChecklistController::class, 'store']);
                    Route::put('/{itemId}',     [TaskChecklistController::class, 'update']);
                    Route::post('/{itemId}/toggle', [TaskChecklistController::class, 'toggle']);
                    Route::delete('/{itemId}',  [TaskChecklistController::class, 'destroy']);
                });
            });

            // Challenges
            Route::prefix('/challenges')->group(function () {
                Route::get('/',          [TeamChallengeController::class, 'index']);
                Route::post('/',         [TeamChallengeController::class, 'store']);
                Route::get('/progress',  [TeamChallengeController::class, 'progress']);
            });

            // Chat
            Route::prefix('/chat')->group(function () {
                Route::get('/channels',  [ChatController::class, 'getChannels']);
                Route::get('/messages',  [ChatController::class, 'getMessages']);
                Route::post('/messages', [ChatController::class, 'sendMessage']);
                Route::post('/dm',       [ChatController::class, 'getOrCreateDm']);
            });
        });
    });
});

// ─── Admin routes (rate limited: 60/min per user) ─────────────────────────
Route::middleware(['auth:api', 'admin', 'throttle:api'])->prefix('admin')->group(function () {
    Route::apiResource('users',  AdminUserController::class)->only(['index', 'show']);
    Route::apiResource('tracks', AdminTrackController::class)->except(['show']);
    Route::apiResource('courses',AdminCourseController::class)->except(['show']);
    Route::apiResource('topics', AdminTopicController::class)->except(['show']);
    Route::apiResource('quizzes',AdminQuizController::class)->except(['show']);
    Route::get('teams',          [AdminTeamController::class, 'index']);
    Route::get('teams/{id}',     [AdminTeamController::class, 'show']);

    // Video upload (sensitive: 10/min per user)
    Route::middleware('throttle:sensitive')->group(function () {
        Route::get('videos',            [AdminVideoController::class, 'index']);
        Route::post('videos/upload',    [AdminVideoController::class, 'upload']);
        Route::get('videos/{id}',       [AdminVideoController::class, 'show']);
        Route::delete('videos/{id}',    [AdminVideoController::class, 'destroy']);
    });
});
});