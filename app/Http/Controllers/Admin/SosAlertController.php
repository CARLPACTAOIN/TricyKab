<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SosAlert;
use Illuminate\Http\Request;

class SosAlertController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->string('status')->toString();
        $search = trim((string) $request->input('search', ''));

        $query = SosAlert::query()->with(['booking', 'passenger']);

        if (in_array($status, ['OPEN', 'ACKNOWLEDGED', 'CLOSED'], true)) {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('passenger_name', 'like', '%' . $search . '%')
                    ->orWhere('location_note', 'like', '%' . $search . '%')
                    ->orWhereHas('booking', fn ($bookingQuery) => $bookingQuery->where('booking_reference', 'like', '%' . $search . '%'));
            });
        }

        $alerts = $query->latest()->paginate(10)->withQueryString();
        $summary = [
            'OPEN' => SosAlert::query()->where('status', 'OPEN')->count(),
            'ACKNOWLEDGED' => SosAlert::query()->where('status', 'ACKNOWLEDGED')->count(),
            'CLOSED' => SosAlert::query()->where('status', 'CLOSED')->count(),
        ];

        return view('admin.sos.index', compact('alerts', 'summary', 'status', 'search'));
    }

    public function updateStatus(Request $request, SosAlert $sosAlert)
    {
        $validated = $request->validate([
            'status' => 'required|in:ACKNOWLEDGED,CLOSED',
        ]);

        $previous = $sosAlert->only(['status', 'acknowledged_by_admin_id', 'acknowledged_at', 'closed_by_admin_id', 'closed_at']);

        $sosAlert->status = $validated['status'];
        if ($validated['status'] === 'ACKNOWLEDGED') {
            $sosAlert->acknowledged_by_admin_id = auth()->id();
            $sosAlert->acknowledged_at = now();
        }
        if ($validated['status'] === 'CLOSED') {
            if (! $sosAlert->acknowledged_at) {
                $sosAlert->acknowledged_by_admin_id = auth()->id();
                $sosAlert->acknowledged_at = now();
            }
            $sosAlert->closed_by_admin_id = auth()->id();
            $sosAlert->closed_at = now();
        }
        $sosAlert->save();

        AuditLog::create([
            'actor_user_id' => auth()->id(),
            'actor_type' => 'USER',
            'actor_name' => auth()->user()?->name,
            'object_type' => 'SOS_ALERT',
            'object_id' => $sosAlert->id,
            'action' => 'SOS_STATUS_UPDATED',
            'previous_state_json' => $previous,
            'new_state_json' => $sosAlert->only(['status', 'acknowledged_by_admin_id', 'acknowledged_at', 'closed_by_admin_id', 'closed_at']),
            'reason' => 'SOS status updated by admin',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.sos')->with('success', 'SOS alert updated successfully.');
    }

    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'alert_ids' => 'required|array|min:1',
            'alert_ids.*' => 'integer|exists:sos_alerts,id',
            'status' => 'required|in:ACKNOWLEDGED,CLOSED',
        ]);

        $alerts = SosAlert::query()->whereIn('id', $validated['alert_ids'])->get();
        foreach ($alerts as $sosAlert) {
            $previous = $sosAlert->only(['status', 'acknowledged_by_admin_id', 'acknowledged_at', 'closed_by_admin_id', 'closed_at']);
            $sosAlert->status = $validated['status'];

            if ($validated['status'] === 'ACKNOWLEDGED') {
                $sosAlert->acknowledged_by_admin_id = auth()->id();
                $sosAlert->acknowledged_at = now();
            }
            if ($validated['status'] === 'CLOSED') {
                if (! $sosAlert->acknowledged_at) {
                    $sosAlert->acknowledged_by_admin_id = auth()->id();
                    $sosAlert->acknowledged_at = now();
                }
                $sosAlert->closed_by_admin_id = auth()->id();
                $sosAlert->closed_at = now();
            }
            $sosAlert->save();

            AuditLog::create([
                'actor_user_id' => auth()->id(),
                'actor_type' => 'USER',
                'actor_name' => auth()->user()?->name,
                'object_type' => 'SOS_ALERT',
                'object_id' => $sosAlert->id,
                'action' => 'SOS_BULK_STATUS_UPDATED',
                'previous_state_json' => $previous,
                'new_state_json' => $sosAlert->only(['status', 'acknowledged_by_admin_id', 'acknowledged_at', 'closed_by_admin_id', 'closed_at']),
                'reason' => 'Bulk SOS status update',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return redirect()->route('admin.sos')->with('success', 'Selected SOS alerts updated.');
    }

    public function export(Request $request)
    {
        $status = $request->string('status')->toString();
        $search = trim((string) $request->input('search', ''));
        $query = SosAlert::query()->with(['booking', 'passenger']);

        if (in_array($status, ['OPEN', 'ACKNOWLEDGED', 'CLOSED'], true)) {
            $query->where('status', $status);
        }
        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('passenger_name', 'like', '%' . $search . '%')
                    ->orWhere('location_note', 'like', '%' . $search . '%');
            });
        }

        $rows = $query->latest()->get();
        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Passenger', 'BookingReference', 'Latitude', 'Longitude', 'LocationNote', 'Status', 'CreatedAt']);
            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->id,
                    $row->passenger_name ?: $row->passenger?->name,
                    $row->booking?->booking_reference,
                    $row->latitude,
                    $row->longitude,
                    $row->location_note,
                    $row->status,
                    $row->created_at?->toDateTimeString(),
                ]);
            }
            fclose($handle);
        }, 'sos-alerts-export-' . now()->format('Ymd-His') . '.csv', ['Content-Type' => 'text/csv']);
    }
}
