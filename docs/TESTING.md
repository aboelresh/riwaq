\# Testing — CodeMaster



\## Overview



127 automated tests covering business logic, API behavior, security, and

tenant isolation. Tests run on both SQLite (fast, for CI) and MySQL (production

compatibility verified).



\---



\## Run Tests



```bash

\# All tests

php artisan test



\# Specific suite

php artisan test tests/Feature/Security/

php artisan test tests/Feature/SaaS/

php artisan test tests/Unit/



\# With coverage (requires Xdebug)

php artisan test --coverage

```



\---



\## Test Suites



\### Unit Tests (`tests/Unit/`)



| File | Tests | What it verifies |

|---|---|---|

| `Services/QuizServiceTest` | 8 | Score calculation, pass/fail threshold, retry cooldown, foreign ID rejection |



\### Feature Tests (`tests/Feature/`)



| File | Tests | What it verifies |

|---|---|---|

| `Auth/LoginTest` | 5 | Login flow, case-insensitive email, wrong password, validation, no sensitive fields |

| `Auth/RegisterTest` | 6 | Registration, email lowercase, duplicate detection, welcome notification, queue dispatch |

| `Admin/TrackTest` | 7 | Admin CRUD, learner restriction, enrolled user protection (Bug014) |

| `Admin/QuizTest` | 5 | No correct answer rejection (Bug015), single answer rejection, admin create, learner block |

| `Teams/CreateTeamTest` | 6 | Team creation, auto-sections, auto-membership, enrollment check |

| `Teams/TeamOperationsTest` | 10 | Transactions (Bug009, Bug011), soft delete, leader rules, task permissions |

| `NotificationsTest` | 5 | Welcome notification, idempotent mark-read, IDOR protection, unread count |

| `PaginationTest` | 5 | Per-page limits (12/15/20), page 2, search filter |

| `ProfileTest` | 6 | Sensitive field hiding (Bug007), name validation (Bug017), empty body (Bug008), completion logic |

| `SearchTest` | 6 | Results structure, min-length, SQL injection safety, authentication required |



\### Security Tests (`tests/Feature/Security/`)



| File | Tests | What it verifies |

|---|---|---|

| `AuthSecurityTest` | 9 | Password not in response, verification code hidden, token invalidation, user enumeration, bcrypt |

| `AuthorizationSecurityTest` | 6 | All admin endpoints blocked for learner, IDOR on notifications, error format |

| `InputValidationSecurityTest` | 9 | Mass assignment (role, verified\_at), SQL injection, XSS in name, cross-field validation, AI limits |

| `TenantSecurityTest` | 8 | Org A/B isolation, Global Scope, auto-tag on create, context switching, withoutTenantScope |

| `ApiSecurityTest` | 9 | 404/405 JSON format, sensitive fields, security headers, rate limiting, CORS |



\### SaaS Tests (`tests/Feature/SaaS/`)



| File | Tests | What it verifies |

|---|---|---|

| `TenantIsolationTest` | 10 | Course/track isolation, API-level isolation, entitlement enforcement, membership boundary, single license mode |



\---



\## Results



Tests: 127 passed (398 assertions)

Duration: \~7s (SQLite) / \~28s (MySQL)





\---



\## Database Compatibility



| Database | Migration | Seeding | Tests |

|---|---|---|---|

| SQLite (in-memory) | ✅ | ✅ | ✅ (127 pass) |

| MySQL 8.4 | ✅ | ✅ | ✅ (127 pass) |



\*\*Note:\*\* Migration ordering matters on MySQL (FK constraints enforced at creation).

`plans` table is created before `organizations` to satisfy the FK on `plan\_id`.



\---



\## Test Configuration



`phpunit.xml` configures test environment:



```xml

DB\_CONNECTION=sqlite

DB\_DATABASE=:memory:

CACHE\_STORE=array

QUEUE\_CONNECTION=sync

MAIL\_MAILER=array

```



\- In-memory SQLite: no disk I/O, \~4x faster than MySQL

\- `QUEUE\_CONNECTION=sync`: jobs run synchronously in tests (no worker needed)

\- `MAIL\_MAILER=array`: emails captured, not sent

\- `Queue::fake()` used in tests that need to assert job dispatch



\---



\## Bugs Fixed (verified by tests)



| Bug | Test |

|---|---|

| Bug001: Route \[login] not defined | `AuthSecurityTest::unauthenticated\_request\_returns\_json\_not\_redirect` |

| Bug003: current\_time > duration | `InputValidationSecurityTest::video\_progress\_rejects\_current\_time` |

| Bug004: Quiz foreign IDs | `QuizServiceTest::it\_ignores\_answers\_with\_no\_matching\_question` |

| Bug005: AI error exposed | `ApiSecurityTest::error\_responses\_never\_expose\_internal\_info` |

| Bug007: verification\_code in profile | `AuthSecurityTest::login\_response\_never\_exposes\_verification\_code` |

| Bug008: empty body = profile complete | `ProfileTest::update\_profile\_empty\_body\_does\_not\_set\_profile\_completed` |

| Bug009: Team no transaction | `TeamOperationsTest::create\_team\_wraps\_in\_transaction` |

| Bug011: Bulk checklist no transaction | `TeamOperationsTest::bulk\_checklist\_uses\_transaction` |

| Bug014: Delete enrolled track | `TrackTest::admin\_cannot\_delete\_track\_with\_enrolled\_users` |

| Bug015: Quiz no correct answer | `QuizTest::quiz\_with\_no\_correct\_answer\_is\_rejected` |

| Bug017: Name accepts symbols | `ProfileTest::update\_profile\_with\_invalid\_name\_format` |

