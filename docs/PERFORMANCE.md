\# Performance — CodeMaster



\## Caching Strategy



\*\*Driver:\*\* Redis (production) / File (local dev)



\### Public Endpoints (cached)



| Endpoint | Cache Key | TTL | Invalidated On |

|---|---|---|---|

| `GET /api/v1/tracks` | `tracks:list:page:{n}` | 1 hour | Track created/updated/deleted |

| `GET /api/v1/tracks/{id}` | `tracks:show:{id}` | 1 hour | Track updated/deleted |

| `GET /api/v1/courses` | `courses:list:page:{n}` | 1 hour | Course created/updated/deleted |



These endpoints are read-heavy with infrequent writes — ideal cache candidates.

Cache stores plain arrays (not Eloquent Resources) to avoid serialization issues.



\*\*Cache invalidation\*\* is triggered in Admin controllers immediately after

any create/update/delete operation.



\### Level Analysis (User-level cache)



`GET /api/v1/progress/level-analysis` uses `UserLevelAnalysis` table as

a persistent cache layer. Recalculation triggered manually via `?refresh=true`

or on significant progress events.



\---



\## N+1 Query Prevention



\### Identified and Fixed



\*\*TeamController::progress()\*\* — was querying member progress individually:

```php

// Before (N+1):

foreach ($members as $member) {

&#x20;   $member->progress; // N queries

}



// After (eager loading):

$team->load(\['members.courseProgress', 'members.topicProgress']);

```



\*\*TeamController::activity()\*\* — same pattern, fixed with `loadMissing()`.



\*\*LevelAnalysisService::gatherStats()\*\* — was making 8+ separate queries.

Refactored to use `loadMissing()` for bulk loading, then computing stats

from in-memory collections.



\---



\## Database Indexes



Indexes added for frequently-queried columns:



| Table | Column(s) | Purpose |

|---|---|---|

| `users` | `username` | Username lookup |

| `teams` | `created\_by` | User's teams listing |

| `team\_tasks` | `status`, `priority` | Task filtering |

| All tenant tables | `organization\_id` | Tenant scope filtering |

| `organization\_users` | `(organization\_id, role)` | Member role queries |

| `ai\_usage\_logs` | `(organization\_id, used\_at)` | Monthly usage counting |



\---



\## Pagination



All list endpoints are paginated to prevent large response payloads:



| Endpoint | Per Page |

|---|---|

| Public tracks | 12 |

| Public courses | 15 |

| Admin lists | 20 |



\---



\## Background Queue



Email operations moved off the request lifecycle:



| Operation | Before | After |

|---|---|---|

| Send verification email | Sync (blocks response) | Queued job |

| Send password reset email | Sync | Queued job |

| Send team invite email | Sync | Queued job |



\*\*Impact:\*\* Registration response time reduced from \~2s (SMTP latency) to \~50ms.



\---



\## Rate Limiting



Protects server from abuse and ensures fair resource allocation:



| Limiter | Limit (prod) | Scope |

|---|---|---|

| `auth` | 5/min | Per IP |

| `sensitive` | 10/min | Per user/IP |

| `api` | 60/min | Per user/IP |

| AI Chat | 30/hour | Per user |



Rate limits are environment-aware: relaxed in local/testing to allow

automated test suites to run without hitting limits.



\---



\## What Was Not Done (and why)



\- \*\*Redis tagging for cache invalidation:\*\* Not used because the SQLite sandbox

&#x20; doesn't support Redis tags. In production with Redis, `Cache::tags(\['tracks'])->flush()`

&#x20; would be more precise than `Cache::flush()`.



\- \*\*Full-text search:\*\* Current search uses `LIKE '%query%'` — suitable for

&#x20; the current scale. At larger scale, Meilisearch or Elasticsearch would be used.



\- \*\*Query time measurements:\*\* No profiling tool (Debugbar, Telescope) was

&#x20; installed in the sandbox. Performance improvements are based on query count

&#x20; reduction, not measured response times.

