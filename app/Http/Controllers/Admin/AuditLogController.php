<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $objectType = $request->string('object_type')->toString();
        $action = $request->string('action')->toString();
        $range = $request->string('range')->toString() ?: '7d';
        $range = in_array($range, ['today', '7d', '30d', 'all'], true) ? $range : '7d';

        $query = AuditLog::query()->with('actor');

        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('actor_name', 'like', '%' . $search . '%')
                    ->orWhere('action', 'like', '%' . $search . '%')
                    ->orWhere('reason', 'like', '%' . $search . '%')
                    ->orWhere('object_type', 'like', '%' . $search . '%');
            });
        }

        if ($objectType !== '') {
            $query->where('object_type', $objectType);
        }

        if ($action !== '') {
            $query->where('action', $action);
        }

        if ($range === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($range === '7d') {
            $query->where('created_at', '>=', now()->startOfDay()->subDays(6));
        } elseif ($range === '30d') {
            $query->where('created_at', '>=', now()->startOfDay()->subDays(29));
        }

        $logs = $query->latest()->paginate(15)->withQueryString();
        $objectTypes = AuditLog::query()->select('object_type')->distinct()->orderBy('object_type')->pluck('object_type');
        $actions = AuditLog::query()->select('action')->distinct()->orderBy('action')->pluck('action');

        return view('admin.audit-logs.index', compact('logs', 'search', 'objectType', 'action', 'range', 'objectTypes', 'actions'));
    }

    public function export(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $objectType = $request->string('object_type')->toString();
        $action = $request->string('action')->toString();
        $range = $request->string('range')->toString() ?: '7d';
        $range = in_array($range, ['today', '7d', '30d', 'all'], true) ? $range : '7d';

        $query = AuditLog::query()->with('actor');

        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('actor_name', 'like', '%' . $search . '%')
                    ->orWhere('action', 'like', '%' . $search . '%')
                    ->orWhere('reason', 'like', '%' . $search . '%')
                    ->orWhere('object_type', 'like', '%' . $search . '%');
            });
        }
        if ($objectType !== '') {
            $query->where('object_type', $objectType);
        }
        if ($action !== '') {
            $query->where('action', $action);
        }
        if ($range === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($range === '7d') {
            $query->where('created_at', '>=', now()->startOfDay()->subDays(6));
        } elseif ($range === '30d') {
            $query->where('created_at', '>=', now()->startOfDay()->subDays(29));
        }

        $rows = $query->latest()->get();

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Timestamp', 'Actor', 'ActorType', 'ObjectType', 'ObjectId', 'Action', 'Reason', 'IP']);
            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->id,
                    $row->created_at?->toDateTimeString(),
                    $row->actor_name ?: $row->actor?->name,
                    $row->actor_type,
                    $row->object_type,
                    $row->object_id,
                    $row->action,
                    $row->reason,
                    $row->ip_address,
                ]);
            }
            fclose($handle);
        }, 'audit-logs-export-' . now()->format('Ymd-His') . '.csv', ['Content-Type' => 'text/csv']);
    }
}
