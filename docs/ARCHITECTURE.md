\# Architecture — Riwaq API



\## System Overview



Client (Mobile App / SPA / Admin Dashboard)

│

│ HTTPS

▼

Nginx

│

▼

PHP-FPM (Laravel 12)

│

┌──────┴──────┐

▼ ▼

MySQL 8.4 Redis 7

(primary DB) (cache + queue)





\---



\## Request Lifecycle



HTTP Request

│

▼

Route (/api/v1/\*)

│

▼

Middleware Stack (in order):



SecurityHeaders → adds security headers to response

ThrottleRequests → rate limiting (auth/api/sensitive)

auth:api (JWT) → validates JWT, sets auth()->user()

ResolveTenant → reads org\_id from JWT, verifies membership,

sets TenantContext::set($org)

IsAdmin → (admin routes only) checks user role

│

▼

FormRequest → validates input, returns 422 on failure

│

▼

Controller → thin, delegates to services

│

▼

Service Layer:

├── QuizService → scoring, retry logic, canTake checks

├── ProgressService → markViewed, checkCompletion, fire events

├── EntitlementService → plan limit checks (canCreateCourse, canUseAI)

├── NotificationService → create in-app notifications

├── CacheService → cache key constants, invalidation helpers

└── AuditLogService → admin action logging to audit channel

│

▼

Eloquent Models

└── BelongsToTenant → Global Scope: WHERE organization\_id = ?

│

▼

MySQL / Redis



\---



\## Layer Responsibilities



| Layer | Responsibility | Should NOT |

|---|---|---|

| Controller | Receive request, call service, return response | Contain business logic |

| FormRequest | Validate and authorize input | Touch database directly |

| Service | Implement business rules | Know about HTTP |

| Model | Define relationships, scopes, casts | Implement business rules |

| Event/Listener | Decouple side effects (notifications) | Block the request |

| Job | Background processing (emails) | Run in request lifecycle |

| Middleware | Cross-cutting concerns | Be domain-specific |



\---



\## Directory Structure



app/

├── Console/

│ ├── Kernel.php # Scheduler definitions

│ └── Commands/ # Artisan commands

│ ├── SetupProduction.php

│ ├── SecurityAudit.php

│ ├── CleanupExpiredCodes.php

│ └── CleanupAiLogs.php

│

├── Events/ # Domain events

│ ├── TopicCompleted.php

│ ├── CourseCompleted.php

│ ├── TrackCompleted.php

│ └── ChatMessageSent.php

│

├── Http/

│ ├── Controllers/Api/

│ │ ├── Auth/ # Login, Register, Verify, Reset

│ │ ├── Admin/ # Track, Course, Topic, Quiz, Video, User

│ │ ├── Organization/ # Organization management endpoints

│ │ ├── AiChatController.php

│ │ ├── TeamController.php

│ │ ├── TrackController.php

│ │ ├── CourseController.php

│ │ └── ...

│ ├── Middleware/

│ │ ├── SecurityHeaders.php

│ │ ├── ResolveTenant.php

│ │ └── IsAdmin.php

│ └── Requests/ # FormRequests organized by domain

│ ├── Auth/

│ ├── Admin/

│ ├── Quiz/

│ ├── Team/

│ └── ...

│

├── Jobs/ # Queued jobs

│ ├── SendVerificationEmailJob.php

│ ├── SendPasswordResetEmailJob.php

│ └── SendTeamInviteEmailJob.php

│

├── Listeners/

│ └── SendMilestoneNotification.php

│

├── Models/ # Eloquent models

│

├── SaaS/ # Multi-tenancy core

│ ├── TenantContext.php # Request-scoped singleton

│ ├── BelongsToTenant.php # Trait with Global Scope

│ └── EntitlementService.php # Plan limit enforcement

│

└── Services/

├── QuizService.php

├── ProgressService.php

├── NotificationService.php

├── CacheService.php

└── AuditLogService.php





\---



\## SaaS Layer



Organization

│

├── has many Users (via organization\_users pivot)

│ ├── role: owner | admin | instructor | student

│ └── status: active | invited | suspended

│

├── belongs to Plan

│ ├── max\_students, max\_courses, max\_tracks

│ ├── max\_storage\_gb, max\_ai\_calls\_per\_month

│ └── allow\_custom\_domain, allow\_white\_label

│

├── has one active Subscription

│

└── owns (via organization\_id):

Tracks, Courses, Topics, Quizzes,

Teams, Notifications, AI Usage Logs





\---



\## Background Processing



HTTP Request

│

▼

Controller dispatches Job

│

▼

Queue (Redis)

│

▼

Worker (Supervisor)

├── Riwaq-worker-default (x2) — general queue

└── Riwaq-worker-emails (x1) — email queue

│

▼

Job executes (email sent, notification created, etc.)

│

on failure:

└── Retry up to 3 times with 60s backoff

→ Failed jobs table for inspection

