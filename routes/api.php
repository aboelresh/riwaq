<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\TrackController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\TopicController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\TeamTaskController;
use App\Http\Controllers\Api\TeamSectionController;
use App\Http\Controllers\Api\TaskChecklistController;
use App\Http\Controllers\Api\TeamNoteController;
use App\Http\Controllers\Api\ProgressController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\AiChatController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\TeamChallengeController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\TrackController as AdminTrackController;
use App\Http\Controllers\Api\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Api\Admin\TopicController as AdminTopicController;
use App\Http\Controllers\Api\Admin\QuizController as AdminQuizController;
use App\Http\Controllers\Api\Admin\VideoController as AdminVideoController;
use App\Http\Controllers\Api\Admin\TeamController as AdminTeamController;

Route::get('/', function () {
    return response()->json([
        'success' => true,
        'message' => 'Code Master API',
        'version' => '1.0.0'
    ]);
});

Route::prefix('auth')->group(function () {
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('login', [LoginController::class, 'login']);
    
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [LogoutController::class, 'logout']);
    });
   /* Route::prefix('auth')->group(function () {
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('login', [LoginController::class, 'login']);
    
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [LogoutController::class, 'logout']);
        Route::post('verify', [App\Http\Controllers\Api\Auth\VerificationController::class, 'verify']);
        Route::post('verify/resend', [App\Http\Controllers\Api\Auth\VerificationController::class, 'resend']);
    });
});*/
});

// Public browsing
Route::get('/tracks', [TrackController::class, 'index']);
Route::get('/tracks/{id}', [TrackController::class, 'show'])->where('id', '[0-9]+');
Route::get('/courses', [CourseController::class, 'index']);

Route::middleware('auth:api')->group(function () {
    // Global search
    Route::get('/search', [SearchController::class, 'search']);

    // AI Study Assistant
    Route::post('/ai/chat', [AiChatController::class, 'chat']);

    // Protected video streaming
    Route::get('/videos/stream/{topicId}', [App\Http\Controllers\Api\VideoStreamController::class, 'stream']);

    // Tracks (authenticated) — my-tracks MUST come before {id}
    Route::get('/tracks/my-tracks', [TrackController::class, 'myTracks']);
    Route::post('/tracks/{id}/enroll', [TrackController::class, 'enroll']);
    Route::post('/tracks/{id}/switch', [TrackController::class, 'switchTrack']);

    // Courses
    Route::prefix('courses')->group(function () {
        Route::get('/{id}', [CourseController::class, 'show']);
    });

    // Topics
    Route::prefix('topics')->group(function () {
        Route::get('/{id}', [TopicController::class, 'show']);
        Route::post('/{id}/mark-viewed', [TopicController::class, 'markAsViewed']);
        Route::get('/{id}/video-progress', [TopicController::class, 'getVideoProgress']);
        Route::post('/{id}/video-progress', [TopicController::class, 'updateVideoProgress']);
    });

    // Quizzes
    Route::prefix('quizzes')->group(function () {
        Route::get('/{id}', [QuizController::class, 'show']);
        Route::post('/{id}/submit', [QuizController::class, 'submit']);
        Route::get('/attempts/{attemptId}/result', [QuizController::class, 'result']);
    });

    // Teams
    Route::prefix('teams')->group(function () {
        Route::get('/my-teams', [TeamController::class, 'myTeams']);
        Route::get('/search-user', [TeamController::class, 'searchUser']);
        Route::get('/my-tasks', [TeamTaskController::class, 'myTasks']);
        Route::post('/create', [TeamController::class, 'create']);
        Route::post('/join', [TeamController::class, 'join']);
        Route::get('/{id}', [TeamController::class, 'show']);
        Route::put('/{id}', [TeamController::class, 'update']);
        Route::delete('/{id}', [TeamController::class, 'destroy']);
        Route::post('/{id}/leave', [TeamController::class, 'leave']);
        Route::delete('/{id}/members/{userId}', [TeamController::class, 'kickMember']);
        Route::post('/{id}/invite', [TeamController::class, 'inviteByUsername']);
        Route::post('/{id}/invite-email', [TeamController::class, 'inviteByEmail']);
        Route::get('/{id}/progress', [TeamController::class, 'progress']);
        Route::get('/{id}/activity', [TeamController::class, 'activity']);
        Route::get('/{id}/achievements/{userId}', [TeamController::class, 'achievements']);

        // Team Sections
        Route::get('/{teamId}/sections', [TeamSectionController::class, 'index']);
        Route::post('/{teamId}/sections', [TeamSectionController::class, 'store']);
        Route::put('/{teamId}/sections/{sectionId}', [TeamSectionController::class, 'update']);
        Route::delete('/{teamId}/sections/{sectionId}', [TeamSectionController::class, 'destroy']);
        Route::post('/{teamId}/sections/{sectionId}/members', [TeamSectionController::class, 'addMembers']);
        Route::delete('/{teamId}/sections/{sectionId}/members/{userId}', [TeamSectionController::class, 'removeMember']);

        // Team Notes
        Route::get('/{teamId}/notes', [TeamNoteController::class, 'all']);
        Route::get('/{teamId}/notes/{userId}', [TeamNoteController::class, 'index']);
        Route::post('/{teamId}/notes', [TeamNoteController::class, 'store']);
        Route::put('/{teamId}/notes/{noteId}', [TeamNoteController::class, 'update']);
        Route::delete('/{teamId}/notes/{noteId}', [TeamNoteController::class, 'destroy']);

        // Team Tasks
        Route::get('/{teamId}/tasks', [TeamTaskController::class, 'index']);
        Route::post('/{teamId}/tasks', [TeamTaskController::class, 'store']);
        Route::put('/{teamId}/tasks/{taskId}', [TeamTaskController::class, 'update']);
        Route::delete('/{teamId}/tasks/{taskId}', [TeamTaskController::class, 'destroy']);
        Route::get('/{teamId}/tasks/{taskId}/comments', [TeamTaskController::class, 'comments']);
        Route::post('/{teamId}/tasks/{taskId}/comments', [TeamTaskController::class, 'addComment']);

        // Task Checklist
        Route::get('/{teamId}/tasks/{taskId}/checklist', [TaskChecklistController::class, 'index']);
        Route::post('/{teamId}/tasks/{taskId}/checklist', [TaskChecklistController::class, 'store']);
        Route::put('/{teamId}/tasks/{taskId}/checklist/{itemId}/toggle', [TaskChecklistController::class, 'toggle']);
        Route::put('/{teamId}/tasks/{taskId}/checklist/{itemId}', [TaskChecklistController::class, 'update']);
        Route::delete('/{teamId}/tasks/{taskId}/checklist/{itemId}', [TaskChecklistController::class, 'destroy']);

        // Team Challenges
        Route::get('/{teamId}/challenges', [TeamChallengeController::class, 'index']);
        Route::post('/{teamId}/challenges', [TeamChallengeController::class, 'store']);
        Route::get('/{teamId}/challenges/progress', [TeamChallengeController::class, 'progress']);

        // Chat (WebSocket)
        Route::get('/{teamId}/chat/channels', [ChatController::class, 'getChannels']);
        Route::get('/{teamId}/chat/messages', [ChatController::class, 'getMessages']);
        Route::post('/{teamId}/chat/messages', [ChatController::class, 'sendMessage']);
        Route::post('/{teamId}/chat/dm', [ChatController::class, 'getOrCreateDm']);
    });

    // Progress
    Route::prefix('progress')->group(function () {
        Route::get('/', [ProgressController::class, 'index']);
        Route::get('/level-analysis', [App\Http\Controllers\Api\LevelAnalysisController::class, 'show']);
    });

    // Profile
    Route::prefix('profile')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\ProfileController::class, 'show']);
        Route::post('/update', [App\Http\Controllers\Api\ProfileController::class, 'update']);
    });

    // Analytics
    Route::get('/analytics', [App\Http\Controllers\Api\AnalyticsController::class, 'index']);

    // ✅ Assessment Routes (for all authenticated users)
    Route::prefix('assessment')->group(function () {
        Route::get('/questions', [App\Http\Controllers\Api\AssessmentController::class, 'getQuestions']);
        Route::post('/submit', [App\Http\Controllers\Api\AssessmentController::class, 'submit']);
        Route::get('/my-result', [App\Http\Controllers\Api\AssessmentController::class, 'getMyResult']);
    });

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/read-all', [NotificationController::class, 'markAllRead']);
        Route::post('/{id}/read', [NotificationController::class, 'markRead']);
    });

    // ========== Admin Routes ==========
    Route::prefix('admin')->middleware('admin')->group(function () {
        
        // Users
        Route::prefix('users')->group(function () {
            Route::get('/', [AdminUserController::class, 'index']);
            Route::get('/{id}', [AdminUserController::class, 'show']);
        });

        // Tracks
        Route::prefix('tracks')->group(function () {
            Route::get('/', [AdminTrackController::class, 'index']);
            Route::post('/', [AdminTrackController::class, 'store']);
            Route::put('/{id}', [AdminTrackController::class, 'update']);
            Route::delete('/{id}', [AdminTrackController::class, 'destroy']);
        });

        // Courses
        Route::prefix('courses')->group(function () {
            Route::get('/', [AdminCourseController::class, 'index']);
            Route::post('/', [AdminCourseController::class, 'store']);
            Route::put('/{id}', [AdminCourseController::class, 'update']);
            Route::delete('/{id}', [AdminCourseController::class, 'destroy']);
        });

        // Topics
        Route::prefix('topics')->group(function () {
            Route::get('/', [AdminTopicController::class, 'index']);
            Route::post('/', [AdminTopicController::class, 'store']);
            Route::put('/{id}', [AdminTopicController::class, 'update']);
            Route::delete('/{id}', [AdminTopicController::class, 'destroy']);
        });

        // Quizzes
        Route::prefix('quizzes')->group(function () {
            Route::get('/', [AdminQuizController::class, 'index']);
            Route::post('/', [AdminQuizController::class, 'store']);
            Route::put('/{id}', [AdminQuizController::class, 'update']);
            Route::delete('/{id}', [AdminQuizController::class, 'destroy']);
        });

        // Videos
        Route::prefix('videos')->group(function () {
            Route::get('/', [AdminVideoController::class, 'index']);
            Route::post('/upload', [AdminVideoController::class, 'upload']);
            Route::get('/{id}', [AdminVideoController::class, 'show']);
            Route::delete('/{id}', [AdminVideoController::class, 'destroy']);
        });

        // Teams
        Route::prefix('teams')->group(function () {
            Route::get('/', [AdminTeamController::class, 'index']);
            Route::get('/{id}', [AdminTeamController::class, 'show']);
        });
    });
});