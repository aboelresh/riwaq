\# Security — Riwaq API



\## Overview



Security is implemented in layers, following OWASP API Security Top 10 guidelines.

Every layer is covered by automated tests in `tests/Feature/Security/`.



\---



\## 1. Authentication



\### Password Handling

\- Passwords hashed with \*\*bcrypt\*\* (cost factor 12) via Laravel's `Hash::make()`

\- Plain-text passwords never stored, logged, or returned in any response

\- Verified with `Hash::check()` — timing-safe comparison



\### JWT Tokens

\- Issued via `tymon/jwt-auth`

\- Contain: `sub` (user ID) + `org\_id` (active organization)

\- TTL: 60 minutes (configurable via `JWT\_TTL`)

\- \*\*Invalidated on logout\*\* — token added to blacklist, rejected on subsequent requests

\- Token blacklisting prevents replay attacks after logout



\### Email Verification

\- 6-digit code, expires in 15 minutes

\- Sent via queued job (not synchronously)

\- Expired codes cleaned up daily via scheduler



\### Password Reset

\- Same 6-digit code mechanism

\- \*\*User enumeration protection\*\*: `POST /auth/forgot-password` always returns 200

&#x20; regardless of whether the email exists in the database

\- Code cleared after successful reset



\### Case-Insensitive Email

\- Login uses `LOWER(email) = LOWER(input)` — prevents duplicate accounts

&#x20; via email case variation (e.g. `Admin@test.com` vs `admin@test.com`)



\---



\## 2. Authorization



\### Role-Based (Function-Level)

\- Two roles: `admin` and `learner`

\- Admin routes protected by `IsAdmin` middleware — returns 403 for learners

\- Role set to `learner` on registration — cannot be overridden via mass assignment



\### Object-Level (BOLA/IDOR Prevention)

\- Notifications: users can only mark their own notifications as read

&#x20; (returns 404, not 403, to avoid confirming resource existence)

\- Tasks: only team leader can create; only leader or assignee can update

\- Teams: leader cannot be kicked; leader cannot leave (must delete team)



\### Tenant-Level Authorization

\- Every tenant-owned resource filtered by `organization\_id` at query level

\- User must be an active member of the organization claimed in the JWT

\- Membership verified on every request in `ResolveTenant` middleware



\---



\## 3. Tenant Isolation



This is the most critical security boundary in a multi-tenant system.



\*\*Implementation:\*\* `BelongsToTenant` trait adds a Global Scope to every

tenant-owned Model:



```php

static::addGlobalScope('tenant', function (Builder $query) {

&#x20;   $query->where('organization\_id', TenantContext::currentId());

});

```



\*\*Result:\*\* Even if a developer writes `Course::all()` without any filter,

they will only receive courses belonging to the current organization.

Cross-tenant data leaks are impossible at the ORM level.



\*\*Bypass (admin/system only):\*\* `Model::withoutTenantScope()` — used only in

`EntitlementService` and reporting, never in public Controllers.



\*\*Verified by:\*\* `tests/Feature/Security/TenantSecurityTest.php` (8 tests)



\---



\## 4. Input Validation \& Injection Prevention



\### SQL Injection

\- All database queries use \*\*Eloquent ORM\*\* with PDO prepared statements

\- No raw SQL with user input (except `LOWER(email)` which is value-safe)

\- Search uses `LIKE '%' . $q . '%'` via Eloquent — PDO escapes the value



\### Mass Assignment

\- All Models define `$fillable` — only whitelisted fields accepted

\- `role` is not in `RegisterRequest` — always set to `learner` in controller

\- `email\_verified\_at` not settable via registration request



\### XSS Prevention

\- Name fields validated with regex: `/^\[\\p{L}\\s\\'-]+$/u`

\- Rejects: `<script>`, numbers, special symbols

\- API returns JSON — no HTML rendering of user input



\### Cross-Field Validation

\- Video progress: `current\_time` cannot exceed `duration` (Bug003 fix)

\- Quiz submission: `question\_id` and `answer\_id` must belong to the quiz (Bug004 fix)



\---



\## 5. API Security



\### Rate Limiting

| Limiter | Limit | Scope |

|---|---|---|

| `auth` | 5/min (prod) / 60/min (local) | Per IP |

| `sensitive` | 10/min (prod) / 120/min (local) | Per user/IP |

| `api` | 60/min (prod) / 300/min (local) | Per user/IP |

| AI Chat | 30/hour | Per user (cache-based) |



\### Security Headers (on every response)



X-Frame-Options: DENY

X-Content-Type-Options: nosniff

X-XSS-Protection: 1; mode=block

Referrer-Policy: strict-origin-when-cross-origin

Permissions-Policy: camera=(), microphone=(), geolocation=()

Strict-Transport-Security: max-age=31536000 (production only)





\### CORS

\- Configured in `config/cors.php`

\- Production: restricted to `CORS\_ALLOWED\_ORIGINS` env variable

\- Never `\*` in production



\### Error Responses

\- \*\*No stack traces\*\* in any API response

\- \*\*No exception class names\*\* or file paths exposed

\- \*\*No internal error messages\*\* — AI errors use fallback response (Bug005 fix)

\- All errors return structured JSON: `{ success: false, message: "..." }`



\---



\## 6. Security Test Suite



All security behaviors are covered by automated tests:



| Test File | Coverage |

|---|---|

| `AuthSecurityTest` | Password hashing, token invalidation, user enumeration, sensitive fields |

| `AuthorizationSecurityTest` | Admin/learner boundaries, IDOR protection, error format |

| `InputValidationSecurityTest` | Mass assignment, SQL injection, XSS, cross-field validation |

| `TenantSecurityTest` | Cross-tenant isolation, Global Scope, context switching |

| `ApiSecurityTest` | HTTP codes, sensitive fields, security headers, rate limiting, CORS |



Run: `php artisan test tests/Feature/Security/`

