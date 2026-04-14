# TK-MOCK-002 Admin Mock Data Sheet

## Purpose

This file is the mock-data sheet for the `TK-MOCK-002` admin shell deliverable.

Use this data for:

- `Admin Login`
- `Dashboard Overview`
- `Bookings and Trips Monitor`
- `Booking or Trip Detail View`

Use these values consistently in Antigravity prompts and exported screens so the admin set reads like one system.

## Filters

### Date Filter Options

- `Today`
- `Last 7 Days`
- `Last 30 Days`
- `April 2026`

### TODA Filter Options

- `All TODAs`
- `Poblacion TODA`
- `Osias TODA`
- `Nongnongan TODA`

### Barangay Filter Options

- `All Barangays`
- `Poblacion`
- `Osias`
- `Nongnongan`

### Monitor Status Filter Options

- `All Statuses`
- `CREATED`
- `SEARCHING_DRIVER`
- `DRIVER_ASSIGNED`
- `DRIVER_ON_THE_WAY`
- `DRIVER_ARRIVED`
- `TRIP_IN_PROGRESS`
- `COMPLETED`
- `CANCELLED_BY_PASSENGER`
- `CANCELLED_BY_DRIVER`
- `NO_SHOW_PASSENGER`
- `NO_SHOW_DRIVER`
- `CANCELLED_NO_DRIVER`

### Ride Type Filter Options

- `All Types`
- `SHARED`
- `SPECIAL`

## Dashboard KPI Values

Use these values as the default proof-set dataset:

| KPI | Value | Subtitle |
|---|---|---|
| Average Passenger Wait Time | `6.8 min` | `Driver assigned at - booking created at` |
| Booking-to-Accept Rate | `78%` | `Accepted bookings / searchable bookings` |
| Booking Completion Rate | `91%` | `Completed bookings / assigned bookings` |
| Active Drivers | `142` | `Online and eligible` |
| Trips Today | `326` | `Completed trips (today)` |
| Driver Availability Rate | `64%` | `Online eligible time / total service time` |

## Admin Login Copy

### Branding

- Product name: `TricyKab`
- Framing line: `Admin access only. Passenger and driver sign-in uses OTP in the mobile apps.`
- Footer note: `Need access? Contact the LGU/TMU system administrator.`

### Fields

- Primary identifier label: `Email`
- Password label: `Password`
- Primary CTA: `Sign In`
- Secondary utility: `Forgot password?`

## Booking Monitor Table Rows

Use these rows for the monitor proof and downstream admin screens.

| Reference | Ride Type | Passenger | Driver | Status | Fare | Created |
|---|---|---|---|---|---|---|
| `BK-2026-0018` | `SHARED` | `Maria Clara` | `Mariano Ramos` | `COMPLETED` | `PHP 45.00` | `Today 09:12` |
| `BK-2026-0019` | `SPECIAL` | `Juan Dela Cruz` | `Carlos Miguel` | `DRIVER_ASSIGNED` | `PHP 80.00` | `Today 08:51` |
| `BK-2026-0020` | `SHARED` | `Amy Lee` | `Roberto Cruz` | `SEARCHING_DRIVER` | `PHP 35.00` | `Today 08:45` |
| `BK-2026-0021` | `SPECIAL` | `Liza Mae Torres` | `Rogelio Santos` | `DRIVER_ON_THE_WAY` | `PHP 95.00` | `Today 08:37` |
| `BK-2026-0022` | `SHARED` | `Paolo Reyes` | `N/A` | `CANCELLED_NO_DRIVER` | `PHP 40.00` | `Today 08:15` |
| `BK-2026-0023` | `SHARED` | `Karen Dizon` | `Joel Navarro` | `NO_SHOW_PASSENGER` | `PHP 35.00` | `Yesterday 17:42` |

## Booking Detail Record

Use this detail record for the `Booking or Trip Detail View`.

### Core Identity

- Booking reference: `BK-2026-0019`
- Receipt reference: `RCT-2026-000083`
- Ride type: `SPECIAL`
- Current status: `DRIVER_ASSIGNED`

### Passenger

- Name: `Juan Dela Cruz`
- Contact: `0917 123 4589`
- Pickup: `Poblacion Public Market`
- Destination: `USM Main Gate`

### Driver

- Name: `Carlos Miguel`
- TODA: `Poblacion TODA`
- Plate number: `KBC-214`
- Contact: `0918 441 0092`

### Fare

- Suggested fare: `PHP 90.00`
- Passenger proposed fare: `PHP 80.00`
- Agreed fare display: `PHP 80.00`
- Payment method: `Cash`

### Timeline

- Booking created: `April 15, 2026 08:51 AM`
- Driver assigned: `April 15, 2026 08:53 AM`
- ETA display: `4 min`

### Action Labels

- Primary action: `View Full Timeline`
- Secondary action: `Edit Booking`
- Tertiary action: `Flag for Review`

## Quick Action Labels

Use these actions where an admin action bar is needed:

- `Export CSV`
- `Export PDF`
- `View All`
- `Apply Filters`
- `Clear`
- `View Details`
- `Edit`
- `Flag for Review`
- `Suspend`

## Status Badge Labels

Use the exact label forms below:

- `CREATED`
- `SEARCHING_DRIVER`
- `DRIVER_ASSIGNED`
- `DRIVER_ON_THE_WAY`
- `DRIVER_ARRIVED`
- `TRIP_IN_PROGRESS`
- `COMPLETED`
- `CANCELLED_BY_PASSENGER`
- `CANCELLED_BY_DRIVER`
- `NO_SHOW_PASSENGER`
- `NO_SHOW_DRIVER`
- `CANCELLED_NO_DRIVER`

## Desktop Proof Defaults

Use these defaults for the shell proof export:

- viewport width: desktop, 1440px target
- theme: light mode
- active nav item: `Dashboard`
- visible KPI count: 4 to 6 cards
- visible monitor rows: 4 to 6 rows
- detail panel state: one active `SPECIAL` ride in `DRIVER_ASSIGNED`
