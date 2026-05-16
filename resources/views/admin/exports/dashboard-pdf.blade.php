<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>TricyKab Dashboard KPI Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        .meta { color: #64748b; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        th { background: #f1f5f9; font-size: 10px; text-transform: uppercase; }
        .kpi-grid { width: 100%; margin-bottom: 16px; }
        .kpi-grid td { border: none; padding: 8px; vertical-align: top; width: 33%; }
        .kpi-value { font-size: 16px; font-weight: bold; }
        .kpi-label { font-size: 9px; color: #64748b; text-transform: uppercase; }
        .section-title { font-size: 13px; font-weight: bold; margin: 12px 0 6px; }
    </style>
</head>
<body>
    <h1>TricyKab Dashboard KPI Report</h1>
    <p class="meta">
        {{ $adminRoleLabel }} · {{ $rangeLabel }}
        @if($todaName) · TODA: {{ $todaName }} @endif
        @if($barangayName) · Barangay: {{ $barangayName }} @endif
        <br>Generated {{ $generatedAt->format('Y-m-d H:i') }}
    </p>

    <table class="kpi-grid">
        <tr>
            <td>
                <div class="kpi-label">Avg Wait Time</div>
                <div class="kpi-value">{{ number_format($metrics['avg_wait_minutes'], 1) }} min</div>
            </td>
            <td>
                <div class="kpi-label">Booking-to-Accept</div>
                <div class="kpi-value">{{ $metrics['booking_to_accept_rate'] }}%</div>
            </td>
            <td>
                <div class="kpi-label">Completion Rate</div>
                <div class="kpi-value">{{ $metrics['completion_rate'] }}%</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="kpi-label">Active Drivers</div>
                <div class="kpi-value">{{ $metrics['active_drivers'] }}</div>
            </td>
            <td>
                <div class="kpi-label">Online Drivers</div>
                <div class="kpi-value">{{ $metrics['online_drivers'] }}</div>
            </td>
            <td>
                <div class="kpi-label">Driver Availability</div>
                <div class="kpi-value">{{ $metrics['driver_availability_rate'] }}%</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="kpi-label">Trips Today</div>
                <div class="kpi-value">{{ $metrics['trips_today'] }}</div>
            </td>
            <td>
                <div class="kpi-label">Total Bookings (window)</div>
                <div class="kpi-value">{{ $metrics['total_bookings'] }}</div>
            </td>
            <td>
                <div class="kpi-label">Drivers On Trip</div>
                <div class="kpi-value">{{ $metrics['drivers_on_trip'] }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Trips per Barangay (top 10)</div>
    <table>
        <thead>
            <tr>
                <th>Barangay</th>
                <th>Origin</th>
                <th>Destination</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($metrics['trips_per_barangay'] as $row)
                <tr>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['origin_count'] }}</td>
                    <td>{{ $row['destination_count'] }}</td>
                    <td>{{ $row['total'] }}</td>
                </tr>
            @empty
                <tr><td colspan="4">No barangay data for selected filters.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Latest Bookings</div>
    <table>
        <thead>
            <tr>
                <th>Reference</th>
                <th>Status</th>
                <th>Passenger</th>
                <th>Driver</th>
                <th>Fare</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach($metrics['latest_bookings'] as $booking)
                <tr>
                    <td>{{ $booking->booking_reference }}</td>
                    <td>{{ $booking->status }}</td>
                    <td>{{ $booking->passenger?->name ?? '—' }}</td>
                    <td>{{ $booking->driver?->full_name ?? '—' }}</td>
                    <td>₱{{ number_format((float) $booking->fare_amount, 2) }}</td>
                    <td>{{ $booking->created_at?->format('Y-m-d H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
