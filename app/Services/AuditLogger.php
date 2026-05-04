<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * PRD §2.4 / §7.21 — central audit-log helper.
 *
 * Writes to the `audit_logs` table for every important state-changing action so
 * the admin panel and disputes flow have an immutable trail.  Failure to write
 * an audit row never blocks the calling business transaction.
 */
class AuditLogger
{
    public function log(
        ?User $actor,
        string $objectType,
        int|string|null $objectId,
        string $action,
        ?array $previous = null,
        ?array $next = null,
        ?string $reason = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        ?string $actorTypeOverride = null,
        ?string $actorNameOverride = null,
    ): ?AuditLog {
        try {
            $actorType = $actorTypeOverride ?? ($actor !== null ? strtoupper((string) ($actor->role ?? 'USER')) : 'SYSTEM');
            $actorName = $actorNameOverride ?? ($actor->name ?? null);

            return AuditLog::query()->create([
                'actor_user_id' => $actor?->id,
                'actor_type' => $actorType,
                'actor_name' => $actorName,
                'object_type' => $objectType,
                'object_id' => $objectId,
                'action' => $action,
                'previous_state_json' => $previous,
                'new_state_json' => $next,
                'reason' => $reason,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent !== null ? substr($userAgent, 0, 250) : null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('audit.write_failed', [
                'object_type' => $objectType,
                'object_id' => $objectId,
                'action' => $action,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
