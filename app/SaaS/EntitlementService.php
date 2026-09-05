<?php

namespace App\SaaS;

use App\Models\Organization;
use Carbon\Carbon;

/**
 * Answers the question: "Can this organization do X right now?"
 *
 * Separates Plan Limits (what's allowed) from Business Rules (how to enforce).
 * Controllers/Services call this instead of checking plan strings directly.
 *
 * Example:
 *   EntitlementService::canCreateCourse($org)  → true/false + reason
 *   EntitlementService::canUseAI($org)         → true/false + reason
 */
class EntitlementService
{
    public static function canCreateCourse(Organization $org): EntitlementResult
    {
        $plan    = $org->plan;
        $current = $org->courses()->withoutTenantScope()
                       ->where('organization_id', $org->id)->count();

        if ($plan && $current >= $plan->max_courses) {
            return EntitlementResult::denied(
                "Course limit reached ({$current}/{$plan->max_courses}). Upgrade your plan."
            );
        }

        return EntitlementResult::allowed();
    }

    public static function canCreateTrack(Organization $org): EntitlementResult
    {
        $plan    = $org->plan;
        $current = $org->tracks()->withoutTenantScope()
                       ->where('organization_id', $org->id)->count();

        if ($plan && $current >= $plan->max_tracks) {
            return EntitlementResult::denied(
                "Track limit reached ({$current}/{$plan->max_tracks}). Upgrade your plan."
            );
        }

        return EntitlementResult::allowed();
    }

    public static function canUseAI(Organization $org): EntitlementResult
    {
        $plan = $org->plan;
        if (!$plan) return EntitlementResult::allowed();

        $used = \App\Models\AiUsageLog::where('organization_id', $org->id)
            ->where('used_at', '>=', Carbon::now()->startOfMonth())
            ->count();

        if ($used >= $plan->max_ai_calls_per_month) {
            return EntitlementResult::denied(
                "AI usage limit reached ({$used}/{$plan->max_ai_calls_per_month} this month). Upgrade your plan."
            );
        }

        return EntitlementResult::allowed();
    }

    public static function canAddStudent(Organization $org): EntitlementResult
    {
        $plan    = $org->plan;
        $current = $org->users()
            ->wherePivot('role', 'student')
            ->wherePivot('status', 'active')
            ->count();

        if ($plan && $current >= $plan->max_students) {
            return EntitlementResult::denied(
                "Student limit reached ({$current}/{$plan->max_students}). Upgrade your plan."
            );
        }

        return EntitlementResult::allowed();
    }
}

/**
 * Value object returned by EntitlementService.
 * Clean to use in Controllers:
 *
 *   $result = EntitlementService::canCreateCourse($org);
 *   if (!$result->allowed) {
 *       return $this->error($result->reason, 403);
 *   }
 */
class EntitlementResult
{
    public function __construct(
        public readonly bool   $allowed,
        public readonly string $reason = ''
    ) {}

    public static function allowed(): self
    {
        return new self(true);
    }

    public static function denied(string $reason): self
    {
        return new self(false, $reason);
    }
}