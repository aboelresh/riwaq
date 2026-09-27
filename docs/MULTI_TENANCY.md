\# Multi-Tenancy — CodeMaster



\## Architecture Decision



\*\*Pattern:\*\* Shared Schema (single database, `organization\_id` column on tenant-owned tables)



\*\*Reason:\*\* Simpler operations, lower cost, easier migrations. Tenant isolation is

enforced at the application layer via Eloquent Global Scopes rather than

database-level separation.



\*\*Deployment modes:\*\*

\- `SINGLE\_LICENSE\_MODE=false` — full multi-tenant SaaS

\- `SINGLE\_LICENSE\_MODE=true` — single organization, same codebase, zero code changes



\---



\## Tenant Resolution Flow



HTTP Request

│

▼

ResolveTenant Middleware

│

├── Read JWT claims → org\_id

│

└── (Browser) Read subdomain → lookup Organization

│

▼

Verify org is active

│

▼

Verify user is active member of org

│

▼

TenantContext::set($organization)

│

▼

Request proceeds





\*\*Security note:\*\* The `org\_id` in the JWT is a \*selector\*, not an authorization grant.

Membership is verified on every request. A forged or modified `org\_id` claim will

fail the membership check.



\---



\## Tenant Ownership Matrix



| Entity | Owned By | organization\_id |

|---|---|---|

| Organization | Root | — (IS the tenant) |

| Plan | Global | ❌ |

| Subscription | Tenant | ✅ |

| User | Shared (many-to-many) | ❌ (via pivot) |

| Track | Tenant | ✅ |

| Course | Tenant | ✅ |

| Topic | Tenant | ✅ |

| Quiz | Tenant | ✅ |

| Video | Tenant | ✅ |

| Team + sub-entities | Tenant | ✅ (on Team) |

| UserTrack | Tenant | ✅ |

| UserCourseProgress | Tenant | ✅ |

| UserTopicProgress | Tenant | ✅ |

| UserQuizAttempt | Tenant | ✅ |

| AssessmentResult | Tenant | ✅ |

| Notification | Tenant | ✅ |

| AI Usage Log | Tenant | ✅ |

| Role Definition | Global | ❌ |



\---



\## BelongsToTenant Trait



Applied to all tenant-owned Models. Provides two behaviors:



\*\*1. Auto-filter on read:\*\*

```php

static::addGlobalScope('tenant', function (Builder $query) {

&#x20;   $query->where('organization\_id', TenantContext::currentId());

});

```



\*\*2. Auto-set on create:\*\*

```php

static::creating(function ($model) {

&#x20;   $model->organization\_id = TenantContext::currentId();

});

```



\*\*Bypass (system/admin only):\*\*

```php

Course::withoutTenantScope()->where(...)->get();

```



\---



\## Organization Membership



Users are linked to organizations via a many-to-many pivot:



users

↕ (many-to-many)

organization\_users

├── organization\_id

├── user\_id

├── role: owner | admin | instructor | student

├── status: active | invited | suspended

└── joined\_at



organizations





A user can belong to multiple organizations with different roles.

The active organization is determined by the JWT `org\_id` claim.

Switching organizations: `POST /api/v1/auth/switch-organization`



\---



\## Plans \& Entitlements



Each organization subscribes to a plan that defines limits:



| Limit | Starter | Professional | Enterprise |

|---|---|---|---|

| Students | 50 | 500 | Unlimited |

| Courses | 5 | 50 | Unlimited |

| Tracks | 2 | 20 | Unlimited |

| Storage | 5 GB | 50 GB | 500 GB |

| AI calls/month | 200 | 2,000 | Unlimited |

| Custom domain | ❌ | ✅ | ✅ |

| White label | ❌ | ❌ | ✅ |



\*\*Entitlements are checked before resource creation:\*\*

```php

$result = EntitlementService::canCreateCourse($org);

if (!$result->allowed) {

&#x20;   return response()->json(\['message' => $result->reason], 403);

}

```



This keeps plan logic in one place (`EntitlementService`) rather than

scattered `if ($plan === 'pro')` checks throughout controllers.



\---



\## Single License Deployment



For clients who purchase a single deployment:



```env

SINGLE\_LICENSE\_MODE=true

SINGLE\_LICENSE\_ORGANIZATION\_ID=1

```



`ResolveTenant` middleware always returns organization #1.

No other code changes required. The multi-tenant architecture is fully

preserved — the client simply has one organization.

