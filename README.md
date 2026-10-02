# Riwaq API

> Multi-tenant SaaS adaptive learning platform built with Laravel 12.

[![Tests](https://img.shields.io/badge/tests-127%20passed-brightgreen)]()
[![PHP](https://img.shields.io/badge/PHP-8.2-blue)]()
[![Laravel](https://img.shields.io/badge/Laravel-12-red)]()
[![Version](https://img.shields.io/badge/version-1.0.0-orange)]()

---

## Overview

Riwaq is a REST API backend for an adaptive learning platform. It supports
multiple academies (tenants) on a single deployment, each with isolated content,
users, and subscriptions.

A student in Academy A cannot access content from Academy B — this is enforced
at the database query level via Eloquent Global Scopes, not just middleware.

---

## Features

| Feature |
|---|
| JWT Authentication |
| Multi-tenancy (Shared Schema) |
| Role-based Authorization (Admin / Learner) |
| Tenant Isolation (BelongsToTenant Global Scope) |
| Learning Tracks → Courses → Topics |
| Quiz Engine with retry cooldowns |
| Video progress tracking with resume |
| Assessment + track recommendation |
| AI Chat (multi-provider + fallback) |
| Team collaboration (sections, tasks, chat) |
| In-app Notifications |
| Background email jobs (queued) |
| SaaS Plans + Entitlements |
| Organization management |
| Rate limiting (auth, AI, API) |
| Search |
| API versioning (`/api/v1/`) |
| OpenAPI / Swagger documentation |
| Health check endpoint |
| Docker + docker-compose |
| GitHub Actions CI/CD |

---

## Tech Stack

| Layer | Technology |
|---|---|
| Language | PHP 8.2 |
| Framework | Laravel 12 |
| Database | MySQL 8.4 (production) / SQLite (testing) |
| Cache | Redis |
| Queue | Redis + Supervisor |
| Auth | tymon/jwt-auth |
| API Docs | L5-Swagger / OpenAPI 3.0 |
| Containerization | Docker + docker-compose |
| CI/CD | GitHub Actions |
| Testing | PHPUnit |

---

## Architecture

HTTP Request
│
▼
Routing (/api/v1/*)
│
▼
Middleware Stack
├── SecurityHeaders
├── Authentication (JWT)
├── ResolveTenant (JWT org_id → TenantContext)
└── IsAdmin (where applicable)
│
▼
Controller
│
▼
FormRequest (validation)
│
▼
Service / Domain Logic
├── EntitlementService (plan limits)
├── QuizService
├── ProgressService
└── NotificationService
│
▼
Eloquent Model
└── BelongsToTenant (Global Scope → WHERE organization_id = ?)
│
▼
MySQL / Redis


**Multi-tenancy** is enforced at the Model level. Every tenant-owned Model applies
a Global Scope that filters all queries by `organization_id`. A developer cannot
accidentally return cross-tenant data from a Controller — the Model prevents it.

---

## Requirements

- PHP 8.2+
- MySQL 8.4 or SQLite 3
- Redis 7
- Composer
- PHP extensions: pdo_mysql, redis, mbstring, xml, curl, zip, gd, bcmath

---

## Installation

```bash
# 1. Clone
git clone https://github.com/aboelresh/riwaq.git
cd riwaq

# 2. Install dependencies
composer install

# 3. Environment setup
cp .env.example .env
php artisan key:generate
php artisan jwt:secret

# 4. Database
php artisan migrate
php artisan db:seed --class=ProductionSeeder

# 5. Start server
php artisan serve
```

---

## Environment Variables

Copy `.env.example` and fill in your values. Key variables:

```env
DB_CONNECTION=mysql
DB_DATABASE=Riwaq

REDIS_HOST=127.0.0.1

QUEUE_CONNECTION=redis
CACHE_STORE=redis

JWT_SECRET=        # php artisan jwt:secret
JWT_TTL=60

AI_PROVIDER=gemini
GEMINI_API_KEY=

CORS_ALLOWED_ORIGINS=https://yourdomain.com

SINGLE_LICENSE_MODE=false   # true for single-tenant deployment
```

See `.env.production.example` for the full production configuration.

---

## API Documentation

Swagger UI is available at:

http://localhost:8000/api/documentation


All endpoints are under `/api/v1/`. Authentication uses Bearer JWT tokens.

```bash
# Login
POST /api/v1/auth/login
{ "email": "admin@example.com", "password": "..." }

# Use the returned token
Authorization: Bearer <token>
```

---

## Multi-Tenancy

Riwaq supports multiple academies (organizations) on a single server.

**How it works:**

1. User logs in → JWT token contains `org_id` claim
2. Every request: `ResolveTenant` middleware reads `org_id` from JWT
3. Middleware verifies the user is an active member of that organization
4. `TenantContext::current()` is set for the request lifetime
5. All tenant-owned Models (`Track`, `Course`, `Quiz`, `Team`, etc.) apply
   a Global Scope: `WHERE organization_id = {current_org_id}`

**Single License mode:** Set `SINGLE_LICENSE_MODE=true` in `.env`.
The system always resolves to organization #1. Same codebase, zero code changes.

---

## Authentication Flow

POST /api/v1/auth/login
↓
Verify credentials
↓
Resolve organizations
↓
1 org → issue scoped JWT (org_id in claims)
2+ org → return org list → POST /auth/switch-organization
↓
JWT contains: { sub: user_id, org_id: org_id }


---

## Queue & Scheduler

**Queue workers** handle email delivery:

```bash
# Development
php artisan queue:work

# Production (managed by Supervisor)
# See docker/supervisor/Riwaq.conf
```

**Scheduler** runs via cron:
cd /var/www/Riwaq && php artisan schedule:run

Scheduled tasks:
- `app:cleanup-expired-codes` — daily
- `app:cleanup-ai-logs` — monthly
- `app:health-check-log` — every 5 minutes

---

## Testing

```bash
# Run all tests
php artisan test

# Run specific suite
php artisan test tests/Feature/Security/
```

**Results:**

| Suite | Tests |
|---|---|
| Unit (QuizService) | 8 |
| Feature (Auth) | 11 |
| Feature (Admin) | 12 |
| Feature (Teams) | 16 |
| Feature (Profile, Search, etc.) | 21 |
| Security (Auth, Authorization, Input, Tenant, API) | 35 |
| SaaS (Tenant Isolation) | 10 |
| **Total** | **127** | ** 398 assertions** |

Tested on both **SQLite** (fast, for CI) and **MySQL 8** (production compatibility).

---

## Security

Key security measures implemented:

- **Authentication:** bcrypt password hashing, JWT invalidation on logout, no user enumeration on password reset
- **Authorization:** Role-based (Admin/Learner) + object-level (users can only access their own resources)
- **Tenant Isolation:** Global Scope on all tenant-owned Models — cross-tenant data leak is impossible at the ORM level
- **Input Validation:** Mass assignment protection, SQL injection prevention via Eloquent, XSS prevention via name regex validation
- **API Security:** Rate limiting on auth (5/min), AI (30/hour), and general API (60/min). Security headers on all responses. CORS restricted in production.
- **Error Handling:** No stack traces or internal details exposed in API responses

See `docs/SECURITY.md` for full details.

---

## Production Deployment

```bash
# One-command setup
php artisan app:setup-production
```

This runs: migrations → seeding → config cache → route cache → storage link.

See `docs/PRE_LAUNCH_CHECKLIST.md` and `docs/DEPLOYMENT_RUNBOOK.md` for the
full production deployment guide.

**Docker:**

```bash
docker compose up -d
```

---

## Project Structure

app/
├── Console/Commands/ # Artisan commands (cleanup, audit, setup)
├── Events/ # Domain events (TopicCompleted, CourseCompleted)
├── Http/
│ ├── Controllers/Api/ # Organized by domain (Auth, Admin, Teams, etc.)
│ ├── Middleware/ # SecurityHeaders, ResolveTenant, IsAdmin
│ └── Requests/ # FormRequests for all validated inputs
├── Jobs/ # Queued jobs (email sending)
├── Listeners/ # Event listeners (milestone notifications)
├── Models/ # Eloquent models
├── SaaS/ # Multi-tenancy core
│ ├── TenantContext.php
│ ├── BelongsToTenant.php
│ └── EntitlementService.php
└── Services/ # Domain services (QuizService, ProgressService, etc.)

docs/ # Architecture, security, deployment documentation
tests/
├── Feature/ # API endpoint tests
│ └── Security/ # Security-specific test suite
└── Unit/ # Service unit tests


---

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history.

---

## Version

**v1.0.0** — Production Ready

127 tests · 398 assertions · MySQL + SQLite · Laravel 12 · PHP 8.2