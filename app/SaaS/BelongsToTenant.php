<?php

namespace App\SaaS;

use Illuminate\Database\Eloquent\Builder;

/**
 * Apply this trait to every tenant-owned Eloquent Model.
 *
 * It adds:
 * 1. A Global Scope that filters ALL queries by current organization_id.
 * 2. A creating hook that auto-sets organization_id on new records.
 *
 * This is the security invariant — even if a developer forgets to filter
 * in a Controller or Service, the Model itself enforces the boundary.
 *
 * To bypass (admin/system only):
 *   Model::withoutTenantScope()->...
 */
trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        // Auto-filter all queries by current tenant
        static::addGlobalScope('tenant', function (Builder $query) {
            if (TenantContext::isResolved()) {
                $query->where(
                    (new static)->getTable() . '.organization_id',
                    TenantContext::currentId()
                );
            }
        });

        // Auto-set organization_id on create
        static::creating(function ($model) {
            if (TenantContext::isResolved() && empty($model->organization_id)) {
                $model->organization_id = TenantContext::currentId();
            }
        });
    }

    /**
     * Escape hatch for system-level queries (admin, jobs, reports).
     * MUST be used explicitly and intentionally — never in regular Controllers.
     */
   /**
 * DANGER: Bypasses tenant isolation entirely.
 * ONLY use in:
 * 1. EntitlementService (counting resources across tenant)
 * 2. System-level admin commands
 * 3. Reporting/analytics that need cross-tenant data
 *
 * NEVER use in:
 * - Public controllers
 * - User-facing endpoints
 * - Any code that accepts user input
 *
 * Every use must be reviewed in PR checklist.
 */
public static function withoutTenantScope(): Builder
{
    return static::withoutGlobalScope('tenant');
}

    public function organization()
    {
        return $this->belongsTo(\App\Models\Organization::class);
    }
}