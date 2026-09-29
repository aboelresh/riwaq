\# Architecture Decision Records — Riwaq



Key engineering decisions made during development, with rationale.



\---



\## 1. Shared Schema Multi-Tenancy



\*\*Decision:\*\* All tenants share the same database tables with an `organization\_id` column.



\*\*Alternatives considered:\*\*

\- Separate database per tenant (more isolation, much higher operational complexity)

\- Separate schema per tenant (PostgreSQL-specific, not supported in MySQL well)



\*\*Rationale:\*\* Shared schema is simpler to operate, migrate, and back up.

Tenant isolation is enforced at the application layer (Eloquent Global Scopes),

which provides strong guarantees without the overhead of managing N databases.



\---



\## 2. JWT over Session-based Auth



\*\*Decision:\*\* Stateless JWT authentication (`tymon/jwt-auth`).



\*\*Rationale:\*\*

\- Mobile app and SPA compatibility — no cookie-based session issues

\- Tenant context (`org\_id`) can be embedded in the token

\- Horizontal scaling: no shared session store required (Redis handles cache)

\- JWT blacklisting on logout handles revocation



\*\*Trade-off:\*\* Token revocation requires a blacklist (Redis). Accepted — Redis

is already in the stack for cache and queue.



\---



\## 3. Tenant Context via JWT Claim



\*\*Decision:\*\* The active organization is determined from the `org\_id` JWT claim,

not from a request header or URL parameter.



\*\*Rationale:\*\*

\- JWT is signed — cannot be forged by the client

\- Headers (`X-Tenant-ID`) are client-controlled and unsafe as a primary source

\- Subdomains are used for browser UX but not as the sole source of truth

\- Membership is still verified on every request (JWT claim ≠ authorization)



\---



\## 4. Eloquent Global Scope for Tenant Isolation



\*\*Decision:\*\* `BelongsToTenant` trait adds a Global Scope to tenant-owned Models.



\*\*Alternatives considered:\*\*

\- Manual `where('organization\_id', ...)` in every query

\- Repository pattern with tenant injection



\*\*Rationale:\*\* Global Scopes are enforced at the Model level.

A developer cannot accidentally return cross-tenant data — even `Course::all()`

is automatically filtered. Manual filtering is error-prone at scale.



\*\*Escape hatch:\*\* `Model::withoutTenantScope()` for system-level admin queries.



\---



\## 5. Entitlements Service over Inline Plan Checks



\*\*Decision:\*\* Plan limits checked via `EntitlementService::canCreateCourse($org)`

rather than `if ($org->plan === 'pro')` inline.



\*\*Rationale:\*\*

\- Single place to change limit logic

\- Returns `EntitlementResult` value object with `allowed` and `reason`

\- Easy to test in isolation

\- Decouples business rules from HTTP layer



\---



\## 6. Background Queue for Emails



\*\*Decision:\*\* All emails dispatched as queued jobs, not sent synchronously.



\*\*Rationale:\*\*

\- HTTP response time not affected by mail provider latency or failure

\- Automatic retry on failure (3 attempts, 60s backoff)

\- Transient mail failures don't cause 500 errors for users

\- Queue workers can be scaled independently



\---



\## 7. SQLite for Testing, MySQL for Production



\*\*Decision:\*\* Tests use SQLite in-memory; production uses MySQL 8.4.



\*\*Rationale:\*\*

\- SQLite in-memory tests run \~4x faster (no disk I/O)

\- MySQL-specific logic (collation fix, foreign key ordering) tested separately

\- Migration compatibility with both drivers is verified before release



\*\*Migration ordering fix applied:\*\* `plans` table must be created before

`organizations` (MySQL enforces FK constraints at creation time; SQLite defers them).



\---



\## 8. API Versioning from Day One



\*\*Decision:\*\* All routes under `/api/v1/` prefix.



\*\*Rationale:\*\*

\- Breaking changes can be introduced in `/api/v2/` without affecting existing clients

\- Mobile apps can pin to a version while the API evolves

\- Prevents tight coupling between API and client release cycles



\---



\## 9. Domain Events for Milestone Notifications



\*\*Decision:\*\* `TopicCompleted`, `CourseCompleted`, `TrackCompleted` events

dispatched from `ProgressService`, not from Controllers.



\*\*Rationale:\*\*

\- Controllers stay thin — notification logic not duplicated

\- New listeners can be added without modifying existing code

\- Testable in isolation



\---



\## 10. Security Headers as Middleware



\*\*Decision:\*\* `SecurityHeaders` middleware adds security headers to every response.



\*\*Rationale:\*\*

\- Centralized — not repeated per controller or route

\- Applied automatically to all future endpoints

\- Can be disabled per-route if needed (e.g., Swagger UI assets)

