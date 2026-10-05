<?php

namespace App\Http\Controllers\Api\Organization;

use App\Http\Controllers\Controller;
use App\Models\AiUsageLog;
use App\Models\Course;
use App\Models\Track;
use App\SaaS\EntitlementService;
use App\SaaS\TenantContext;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    /**
     * GET /organization
     * Current organization details + plan info.
     */
    public function show(): JsonResponse
    {
        $org = TenantContext::current();
        $org->load('plan', 'activeSubscription');

        return response()->json([
            'success' => true,
            'data'    => [
                'id'            => $org->id,
                'name'          => $org->name,
                'slug'          => $org->slug,
                'subdomain'     => $org->subdomain,
                'custom_domain' => $org->custom_domain,
                'logo'          => $org->logo,
                'primary_color' => $org->primary_color,
                'timezone'      => $org->timezone,
                'locale'        => $org->locale,
                'plan'          => $org->plan ? [
                    'name'                   => $org->plan->name,
                    'max_students'           => $org->plan->max_students,
                    'max_courses'            => $org->plan->max_courses,
                    'max_tracks'             => $org->plan->max_tracks,
                    'max_storage_gb'         => $org->plan->max_storage_gb,
                    'max_ai_calls_per_month' => $org->plan->max_ai_calls_per_month,
                    'allow_custom_domain'    => $org->plan->allow_custom_domain,
                    'allow_white_label'      => $org->plan->allow_white_label,
                ] : null,
                'subscription'  => $org->activeSubscription ? [
                    'status'             => $org->activeSubscription->status,
                    'billing_interval'   => $org->activeSubscription->billing_interval,
                    'current_period_end' => $org->activeSubscription->current_period_end,
                    'trial_ends_at'      => $org->activeSubscription->trial_ends_at,
                ] : null,
            ],
        ]);
    }

    /**
     * PUT /organization
     * Update organization settings (owner/admin only).
     */
    public function update(Request $request): JsonResponse
    {
        $org  = TenantContext::current();
        $user = auth()->user();

        $membership = $org->users()
            ->where('user_id', $user->id)
            ->first();

        if ((!$membership || !in_array($membership->pivot->role, ['owner', 'admin']))
            && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only organization owners and admins can update settings.',
            ], 403);
        }

        $validated = $request->validate([
            'name'          => 'sometimes|string|max:255',
            'logo'          => 'nullable|string',
            'primary_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'timezone'      => 'nullable|string|timezone',
            'locale'        => 'nullable|string|in:en,ar',
        ]);

        $org->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Organization updated.',
            'data'    => ['id' => $org->id, 'name' => $org->name],
        ]);
    }

    /**
     * GET /organization/members
     * List all members of current organization.
     */
    public function members(): JsonResponse
    {
        $org = TenantContext::current();

        $members = $org->users()
            ->withPivot(['role', 'status', 'joined_at'])
            ->get()
            ->map(fn($user) => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'role'      => $user->pivot->role,
                'status'    => $user->pivot->status,
                'joined_at' => $user->pivot->joined_at,
            ]);

        return response()->json([
            'success' => true,
            'data'    => $members,
        ]);
    }

    /**
     * POST /organization/members/invite
     * Invite a user by email to the organization.
     */
    public function invite(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'role'  => 'required|in:admin,instructor,student',
        ]);

        $org  = TenantContext::current();
        $user = auth()->user();

        // Only owner/admin can invite
        $membership = $org->users()
            ->where('user_id', $user->id)
            ->first();

        if (!$membership || !in_array($membership->pivot->role, ['owner', 'admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only owners and admins can invite members.',
            ], 403);
        }

        // Entitlement check for students
        if ($request->role === 'student') {
            $result = EntitlementService::canAddStudent($org);
            if (!$result->allowed) {
                return response()->json([
                    'success' => false,
                    'message' => $result->reason,
                ], 403);
            }
        }

        // Find user by email (case-insensitive)
        $invitee = \App\Models\User::whereRaw('LOWER(email) = ?', [
            strtolower($request->email),
        ])->first();

        if (!$invitee) {
            return response()->json([
                'success' => false,
                'message' => 'No user found with this email. Ask them to register first.',
            ], 404);
        }

        // Check if already a member
        if ($org->users()->where('user_id', $invitee->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'User is already a member of this organization.',
            ], 400);
        }

        // Add as invited member
        $org->users()->attach($invitee->id, [
            'role'      => $request->role,
            'status'    => 'invited',
            'joined_at' => now(),
        ]);

        // Audit log
        AuditLogService::log('MEMBER_INVITED', 'Organization', $org->id, [
            'invited_user' => $invitee->id,
            'role'         => $request->role,
        ]);

        // Notify invitee
        NotificationService::send(
            userId: $invitee->id,
            type:   'org_invite',
            title:  "Invited to {$org->name}",
            body:   "{$user->name} invited you to join {$org->name} as {$request->role}.",
            data:   ['organization_id' => $org->id]
        );

        return response()->json([
            'success' => true,
            'message' => "Invitation sent to {$invitee->name}.",
        ]);
    }

    /**
     * PUT /organization/members/{userId}/role
     * Update a member's role.
     */
    public function updateRole(Request $request, int $userId): JsonResponse
    {
        $request->validate([
            'role' => 'required|in:admin,instructor,student',
        ]);

        $org  = TenantContext::current();
        $user = auth()->user();

        // Only owner can change roles
        $myMembership = $org->users()
            ->where('user_id', $user->id)
            ->first();

        if (!$myMembership || $myMembership->pivot->role !== 'owner') {
            return response()->json([
                'success' => false,
                'message' => 'Only the organization owner can change member roles.',
            ], 403);
        }

        // Cannot change owner's own role
        if ($userId === $org->owner_id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot change the role of the organization owner.',
            ], 400);
        }

        $member = $org->users()
            ->where('user_id', $userId)
            ->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'User is not a member of this organization.',
            ], 404);
        }

        $org->users()->updateExistingPivot($userId, [
            'role' => $request->role,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully.',
        ]);
    }

    /**
     * DELETE /organization/members/{userId}
     * Remove a member from the organization.
     */
    public function removeMember(int $userId): JsonResponse
    {
        $org  = TenantContext::current();
        $user = auth()->user();

        // Only owner/admin can remove
        $myMembership = $org->users()
            ->where('user_id', $user->id)
            ->first();

        if (!$myMembership || !in_array($myMembership->pivot->role, ['owner', 'admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only owners and admins can remove members.',
            ], 403);
        }

        // Cannot remove the owner
        if ($userId === $org->owner_id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot remove the organization owner.',
            ], 400);
        }

        // Audit log before detach
        AuditLogService::log('MEMBER_REMOVED', 'Organization', $org->id, [
            'removed_user' => $userId,
        ]);

        $org->users()->detach($userId);

        return response()->json([
            'success' => true,
            'message' => 'Member removed successfully.',
        ]);
    }

    /**
     * GET /organization/usage
     * Current usage vs plan limits.
     */
    public function usage(): JsonResponse
    {
        $org  = TenantContext::current();
        $plan = $org->plan;

        $usage = [
            'students'            => $org->users()->wherePivot('role', 'student')->count(),
            'instructors'         => $org->users()->wherePivot('role', 'instructor')->count(),
            'courses'             => Course::withoutTenantScope()->where('organization_id', $org->id)->count(),
            'tracks'              => Track::withoutTenantScope()->where('organization_id', $org->id)->count(),
            'ai_calls_this_month' => AiUsageLog::where('organization_id', $org->id)
                                        ->where('used_at', '>=', now()->startOfMonth())
                                        ->count(),
        ];

        $limits = $plan ? [
            'max_students'           => $plan->max_students,
            'max_instructors'        => $plan->max_instructors,
            'max_courses'            => $plan->max_courses,
            'max_tracks'             => $plan->max_tracks,
            'max_ai_calls_per_month' => $plan->max_ai_calls_per_month,
        ] : null;

        return response()->json([
            'success' => true,
            'data'    => compact('usage', 'limits'),
        ]);
    }
}