<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>TricyKab Bookings Export</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1e293b; }
        h1 { font-size: 16px; margin: 0 0 4px; }
        .meta { color: #64748b; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 5px 6px; text-align: left; }
        th { background: #f1f5f9; font-size: 9px; text-transform: uppercase; }
    </style>
</head>
<body>
    <h1>Bookings &amp; Trips Export</h1>
    <p class="meta">{{ $adminRoleLabel }} · {{ $total }} record(s) · Generated {{ $generatedAt->format('Y-m-d H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Reference</th>
                <th>Status</th>
                <th>Ride</th>
                <th>Passenger</th>
                <th>Driver</th>
                <th>TODA</th>
                <th>Fare</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bookings as $booking)
                <tr>
                    <td>{{ $booking->booking_reference }}</td>
                    <td>{{ $booking->status }}</td>
                    <td>{{ $booking->ride_type }}</td>
                    <td>{{ $booking->passenger?->name ?? '—' }}</td>
                    <td>{{ $booking->driver?->full_name ?? '—' }}</td>
                    <td>{{ $booking->driver?->toda?->name ?? '—' }}</td>
                    <td>₱{{ number_format((float) $booking->fare_amount, 2) }}</td>
                    <td>{{ $booking->created_at?->format('Y-m-d H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
