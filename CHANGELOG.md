\# Changelog — Riwaq



\## \[1.0.0] — 2026-09-27



\### Initial Production Release



\#### Architecture

\- Laravel 12 / PHP 8.2 backend API

\- Multi-tenant SaaS with Shared Schema + `organization\_id` isolation

\- JWT authentication with tenant context in token claims

\- Single License deployment mode (same codebase, `SINGLE\_LICENSE\_MODE=true`)



\#### Core Features

\- Adaptive learning with Tracks → Courses → Topics → Quizzes

\- AI Chat assistant (Rafiq) with multi-provider support + fallback

\- Assessment system with track recommendations

\- Team collaboration (sections, tasks, checklist, chat, challenges)

\- Video progress tracking with resume support

\- Notification system (in-app + queued email)



\#### Security

\- 17 bugs fixed from initial audit

\- Rate limiting (auth: 5/min, AI: 30/hour, API: 60/min)

\- Tenant isolation enforced at Model level via Global Scope

\- Security headers middleware (X-Frame-Options, CSP, HSTS in prod)

\- No sensitive fields exposed in any API response

\- Error responses never expose stack traces



\#### SaaS Layer

\- Organizations, Plans, Subscriptions

\- Entitlement enforcement (courses, tracks, students, AI calls)

\- AI usage logging per organization per month

\- Organization management endpoints (invite, roles, usage)



\#### Infrastructure

\- Docker + docker-compose (app, nginx, db, redis, queue)

\- GitHub Actions CI/CD (test on PR, deploy on merge to main)

\- Supervisor config for queue workers

\- Production Nginx config with SSL

\- Automated security audit command



\#### Testing

\- 127 PHPUnit tests / 398 assertions — all passing

\- Unit: QuizService (8 tests)

\- Feature: Auth, Admin, Teams, Profile, Search, Pagination, Notifications

\- Security: Auth, Authorization, Input Validation, Tenant Isolation, API

\- SaaS: TenantIsolation (10 tests)



\#### API Documentation

\- OpenAPI/Swagger via L5-Swagger

\- PHP Attributes annotations on Auth, Tracks, Health, Admin controllers

\- Available at `/api/documentation`

