<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Dispute;
use Illuminate\Http\Request;

class DisputeController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->string('status')->toString();
        $search = trim((string) $request->input('search', ''));

        $query = Dispute::query()->with(['booking', 'driver', 'resolver']);

        if (in_array($status, ['OPEN', 'UNDER_REVIEW', 'RESOLVED', 'REJECTED'], true)) {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('reported_by_name', 'like', '%' . $search . '%')
                    ->orWhere('dispute_type', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhereHas('booking', fn ($bookingQuery) => $bookingQuery->where('booking_reference', 'like', '%' . $search . '%'))
                    ->orWhereHas('driver', fn ($driverQuery) => $driverQuery
                        ->where('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%'));
            });
        }

        $disputes = $query->latest()->paginate(10)->withQueryString();

        $summary = [
            'OPEN' => Dispute::query()->where('status', 'OPEN')->count(),
            'UNDER_REVIEW' => Dispute::query()->where('status', 'UNDER_REVIEW')->count(),
            'RESOLVED' => Dispute::query()->where('status', 'RESOLVED')->count(),
            'REJECTED' => Dispute::query()->where('status', 'REJECTED')->count(),
        ];

        return view('admin.disputes.index', compact('disputes', 'summary', 'status', 'search'));
    }

    public function update(Request $request, Dispute $dispute)
    {
        $validated = $request->validate([
            'status' => 'required|in:OPEN,UNDER_REVIEW,RESOLVED,REJECTED',
            'resolution_notes' => 'nullable|string|max:1000',
        ]);

        $previous = $dispute->only(['status', 'resolution_notes', 'resolved_by_admin_id', 'resolved_at']);

        $dispute->status = $validated['status'];
        $dispute->resolution_notes = $validated['resolution_notes'] ?? null;
        if (in_array($dispute->status, ['RESOLVED', 'REJECTED'], true)) {
            $dispute->resolved_by_admin_id = auth()->id();
            $dispute->resolved_at = now();
        } else {
            $dispute->resolved_by_admin_id = null;
            $dispute->resolved_at = null;
        }
        $dispute->save();

        AuditLog::create([
            'actor_user_id' => auth()->id(),
            'actor_type' => 'USER',
            'actor_name' => auth()->user()?->name,
            'object_type' => 'DISPUTE',
            'object_id' => $dispute->id,
            'action' => 'DISPUTE_STATUS_UPDATED',
            'previous_state_json' => $previous,
            'new_state_json' => $dispute->only(['status', 'resolution_notes', 'resolved_by_admin_id', 'resolved_at']),
            'reason' => $validated['resolution_notes'] ?? 'Status updated by admin',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.disputes')->with('success', 'Dispute updated successfully.');
    }

    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'dispute_ids' => 'required|array|min:1',
            'dispute_ids.*' => 'integer|exists:disputes,id',
            'status' => 'required|in:OPEN,UNDER_REVIEW,RESOLVED,REJECTED',
            'resolution_notes' => 'nullable|string|max:1000',
        ]);

        $disputes = Dispute::query()->whereIn('id', $validated['dispute_ids'])->get();
        foreach ($disputes as $dispute) {
            $previous = $dispute->only(['status', 'resolution_notes', 'resolved_by_admin_id', 'resolved_at']);

            $dispute->status = $validated['status'];
            $dispute->resolution_notes = $validated['resolution_notes'] ?? null;
            if (in_array($dispute->status, ['RESOLVED', 'REJECTED'], true)) {
                $dispute->resolved_by_admin_id = auth()->id();
                $dispute->resolved_at = now();
            } else {
                $dispute->resolved_by_admin_id = null;
                $dispute->resolved_at = null;
            }
            $dispute->save();

            AuditLog::create([
                'actor_user_id' => auth()->id(),
                'actor_type' => 'USER',
                'actor_name' => auth()->user()?->name,
                'object_type' => 'DISPUTE',
                'object_id' => $dispute->id,
                'action' => 'DISPUTE_BULK_STATUS_UPDATED',
                'previous_state_json' => $previous,
                'new_state_json' => $dispute->only(['status', 'resolution_notes', 'resolved_by_admin_id', 'resolved_at']),
                'reason' => $validated['resolution_notes'] ?? 'Bulk dispute status update',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return redirect()->route('admin.disputes')->with('success', 'Selected disputes updated.');
    }

    public function export(Request $request)
    {
        $status = $request->string('status')->toString();
        $search = trim((string) $request->input('search', ''));

        $query = Dispute::query()->with(['booking', 'driver']);
        if (in_array($status, ['OPEN', 'UNDER_REVIEW', 'RESOLVED', 'REJECTED'], true)) {
            $query->where('status', $status);
        }
        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('reported_by_name', 'like', '%' . $search . '%')
                    ->orWhere('dispute_type', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $rows = $query->latest()->get();

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'BookingReference', 'Driver', 'ReportedBy', 'Type', 'Status', 'Description', 'CreatedAt']);
            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->id,
                    $row->booking?->booking_reference,
                    $row->driver?->full_name,
                    $row->reported_by_name ?: $row->reported_by_role,
                    $row->dispute_type,
                    $row->status,
                    $row->description,
                    $row->created_at?->toDateTimeString(),
                ]);
            }
            fclose($handle);
        }, 'disputes-export-' . now()->format('Ymd-His') . '.csv', ['Content-Type' => 'text/csv']);
    }
}
