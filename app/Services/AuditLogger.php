<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * PRD §2.4 / §7.21 — central audit-log helper.
 *
 * Writes to the `audit_logs` table for every important state-changing action so
 * the admin panel and disputes flow have an immutable trail. Failure to write
 * an audit row never blocks the calling business transaction.
 *
 * Phase 2 upgrade: snapshots now capture the full model array, actor role,
 * target fields changed, and exact UTC timestamp context.
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
        ?array $targetFields = null,
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
                'target_fields' => $targetFields,
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

    /**
     * PRD §7.21 — enriched audit snapshot for model-level overrides.
     *
     * Captures the full model attribute array before and after the change,
     * the actor role, specific fields that changed, and exact timestamp context.
     * Designed for admin status overrides and dispute resolutions per §6.5.
     *
     * @param  array<string, mixed>  $previousAttributes  $model->toArray() before mutation
     * @param  array<string, mixed>  $newAttributes       $model->toArray() after mutation
     */
    public function logModelChange(
        Model $model,
        string $action,
        array $previousAttributes,
        array $newAttributes,
        ?User $actor,
        ?string $reason,
        ?Request $request = null,
        ?array $targetFields = null,
    ): ?AuditLog {
        // Compute changed fields automatically if not explicitly provided
        if ($targetFields === null) {
            $targetFields = array_keys(array_diff_assoc($newAttributes, $previousAttributes));
        }

        $enrichPrevious = [
            'model_class' => get_class($model),
            'model_id' => $model->getKey(),
            'attributes' => $previousAttributes,
            'actor_role' => $actor?->role ?? 'SYSTEM',
            'actor_id' => $actor?->id,
            'snapshot_timestamp_utc' => now()->toIso8601String(),
        ];

        $enrichNext = [
            'model_class' => get_class($model),
            'model_id' => $model->getKey(),
            'attributes' => $newAttributes,
            'actor_role' => $actor?->role ?? 'SYSTEM',
            'actor_id' => $actor?->id,
            'snapshot_timestamp_utc' => now()->toIso8601String(),
        ];

        return $this->log(
            actor: $actor,
            objectType: class_basename($model),
            objectId: $model->getKey(),
            action: $action,
            previous: $enrichPrevious,
            next: $enrichNext,
            reason: $reason,
            ipAddress: $request?->ip(),
            userAgent: $request?->userAgent(),
            targetFields: $targetFields,
        );
    }
}
