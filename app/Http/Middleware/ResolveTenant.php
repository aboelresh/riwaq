<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use App\SaaS\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        // Single License Mode: always organization #1, skip resolution
        if (config('app.single_license_mode', false)) {
            TenantContext::setDefault();
            return $next($request);
        }

        $organizationId = $this->resolveOrganizationId($request);

        if (!$organizationId) {
            return response()->json([
                'success' => false,
                'message' => 'Organization context required.',
            ], 400);
        }

        $organization = Organization::where('id', $organizationId)
            ->where('is_active', true)
            ->first();

        if (!$organization) {
            return response()->json([
                'success' => false,
                'message' => 'Organization not found or inactive.',
            ], 404);
        }

        // JWT org_id ≠ Authorization — must verify membership separately
        if (auth()->check()) {
            $isMember = $organization->users()
                ->where('user_id', auth()->id())
                ->where('status', 'active')
                ->exists();

            if (!$isMember) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not a member of this organization.',
                ], 403);
            }
        }

        TenantContext::set($organization);

        return $next($request);
    }

    private function resolveOrganizationId(Request $request): ?int
    {
        // Priority 1: JWT claim (API requests)
        try {
            $token = JWTAuth::parseToken()->getPayload();
            if ($token->get('org_id')) {
                return (int) $token->get('org_id');
            }
        } catch (\Throwable) {
            // No token or invalid — try other sources
        }

        // Priority 2: Subdomain (browser requests)
        $host      = $request->getHost();
        $subdomain = $this->extractSubdomain($host);

        if ($subdomain) {
            $org = Organization::where('subdomain', $subdomain)->first()
                ?? Organization::where('custom_domain', $host)->first();

            if ($org) return $org->id;
        }

        return null;
    }

    private function extractSubdomain(string $host): ?string
    {
        $baseDomain = config('app.base_domain', 'codemaster.com');

        if (str_ends_with($host, '.' . $baseDomain)) {
            return str_replace('.' . $baseDomain, '', $host);
        }

        return null;
    }
}