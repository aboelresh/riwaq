<?php

namespace App\SaaS;

use App\Models\Organization;
use RuntimeException;

/**
 * Request-scoped tenant context.
 *
 * Resolved once per request by ResolveTenant middleware.
 * After resolution, the rest of the application uses this
 * as the single source of truth for the current organization.
 *
 * Never resolved from user input directly — always from:
 *   1. JWT org_id claim (API requests)
 *   2. Subdomain (browser requests)
 * Then validated against organization_users membership.
 */
class TenantContext
{
    private static ?Organization $current = null;
    private static bool $resolved = false;

    public static function set(Organization $organization): void
    {
        static::$current  = $organization;
        static::$resolved = true;
    }

    public static function current(): Organization
    {
        if (!static::$resolved || static::$current === null) {
            throw new RuntimeException(
                'TenantContext not resolved. ' .
                'Ensure ResolveTenant middleware ran before accessing tenant context.'
            );
        }

        return static::$current;
    }

    public static function currentId(): int
    {
        return static::current()->id;
    }

    public static function isResolved(): bool
    {
        return static::$resolved;
    }

    /**
     * Used in Single License mode or system-level operations.
     * Returns the default organization (id = 1).
     */
    public static function setDefault(): void
    {
        $org = Organization::findOrFail(
            config('app.single_license_organization_id', 1)
        );
        static::set($org);
    }

    /**
     * Used in tests and system jobs that run outside request context.
     */
    public static function actingAs(Organization $organization): void
    {
        static::set($organization);
    }

    /**
     * Reset between requests (or in tests between test cases).
     */
    public static function flush(): void
    {
        static::$current  = null;
        static::$resolved = false;
    }
}