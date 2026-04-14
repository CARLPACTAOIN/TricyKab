# PRD Progress Audit

- Audit date: `2026-04-13`
- Baseline: `current branch, including local uncommitted changes`
- PRD source: `PRD_MD_V2.md`
- Repo evidence source: current working tree inspection across `routes/`, `app/`, `resources/views/`, `database/migrations/`, `database/seeders/`, and `tests/`
- Counting rule: placeholders, static demo data, and navigation stubs do not count as completed implementation
- Status legend: `Done`, `Partial`, `Not Started`, `Off-PRD / Rework`

## Executive Snapshot

- Overall status: no major PRD area is fully implemented end-to-end yet. Evidence: audit matrix below
- Strongest area: admin web CRUD for TODAs, drivers, tricycles, and basic fare-rule management. Evidence: `app/Http/Controllers/Admin/TodaController.php`, `app/Http/Controllers/Admin/DriverController.php`, `app/Http/Controllers/Admin/TricycleController.php`, `app/Http/Controllers/Admin/FareController.php`
- Weakest areas: passenger app, driver app, API layer, dispatch engine, realtime sync, notifications, and compliance jobs. Evidence: `routes/`, `app/`, `database/migrations/`, `resources/views/dashboard.blade.php`
- Current repo shape: Laravel web app with Blade admin pages, sample data seeders, and an in-progress PRD alignment pass for admin auth, dashboard layout, fare rules, and booking statuses. Evidence: `routes/web.php`, `resources/views/dashboard.blade.php`, `database/seeders/DatabaseSeeder.php`, `app/Models/Booking.php`, `app/Models/FareMatrix.php`
- Status count across the audit matrix: `Done: 0`, `Partial: 8`, `Not Started: 4`, `Off-PRD / Rework: 1`
- Rework items that should not be treated as completed PRD progress:
- Laravel Breeze-style registration and email-verification remnants still exist in views and tests even though the current auth flow removed those routes. Evidence: `routes/auth.php`, `resources/views/auth/register.blade.php`, `resources/views/auth/verify-email.blade.php`, `tests/Feature/Auth/RegistrationTest.php`, `tests/Feature/Auth/EmailVerificationTest.php`
- PRD mobile and backend APIs are not implemented; the repo currently exposes only web/auth/console routes and does not contain `routes/api.php`. Evidence: `routes/`
- Several PRD admin modules are reachable from the sidebar but still render a generic placeholder screen. Evidence: `routes/web.php`, `resources/views/admin/coming-soon.blade.php`
- Core models and enums are still simplified relative to the PRD schema. Evidence: `database/migrations/0001_01_01_000000_create_users_table.php`, `database/migrations/2026_02_14_164256_create_drivers_table.php`, `database/migrations/2026_02_15_010000_create_bookings_table.php`, `app/Models/Driver.php`, `app/Models/Booking.php`

## PRD Alignment Matrix

| PRD Area | Current State | Status | Key Gaps | Evidence |
|---|---|---|---|---|
| Auth and Security | Admin web login exists and is restricted to `role=admin`; password reset remains available | `Partial` | No passenger/driver OTP auth, no token/session-device model, no scopes, no organization-boundary enforcement; registration and verify-email remnants still exist | `routes/auth.php`, `routes/web.php`, `app/Http/Middleware/EnsureAdmin.php`, `app/Http/Controllers/Auth/AuthenticatedSessionController.php`, `resources/views/auth/login.blade.php`, `resources/views/auth/register.blade.php`, `resources/views/auth/verify-email.blade.php` |
| Admin Dashboard | Dashboard UI now mirrors PRD categories with KPI cards, filters, charts, heatmap placeholders, and booking/trip table | `Partial` | Data is static, exports are UI-only, filters are not wired, role-specific dashboard behavior is absent, and multiple admin modules are placeholders | `resources/views/dashboard.blade.php`, `routes/web.php`, `resources/views/admin/coming-soon.blade.php` |
| Drivers / TODAs / Tricycles management | Admin CRUD, search, filters, and forms exist for these three entities | `Partial` | Models and schema do not match PRD fields for verification, compliance, registration status, assigned barangays, and scoped admin access | `app/Http/Controllers/Admin/DriverController.php`, `app/Http/Controllers/Admin/TodaController.php`, `app/Http/Controllers/Admin/TricycleController.php`, `app/Models/Driver.php`, `database/migrations/2026_02_14_164221_create_todas_table.php`, `database/migrations/2026_02_14_164221_create_tricycles_table.php`, `database/migrations/2026_02_14_164256_create_drivers_table.php` |
| Fare rules | Shared and special fare management UI, persistence, and calculator service exist | `Partial` | No barangay origin/destination zoning, no effective windows, no fare-rule status, no admin ownership, no surcharge JSON, and no booking fare snapshot linkage | `app/Http/Controllers/Admin/FareController.php`, `app/Models/FareMatrix.php`, `app/Services/FareCalculatorService.php`, `database/migrations/2026_02_14_165516_create_fare_matrices_table.php`, `database/migrations/2026_04_13_120000_add_prd_columns_to_fare_matrices_table.php`, `resources/views/admin/fares/index.blade.php` |
| Booking and trip lifecycle | Booking model and migration now include PRD-like status constants and seeded sample bookings | `Partial` | No booking controllers, no trip model/table, no transaction-backed state machine, no special fare proposal flow, and no cancellation/no-show workflows | `app/Models/Booking.php`, `database/migrations/2026_02_15_010000_create_bookings_table.php`, `database/seeders/DatabaseSeeder.php` |
| Payments and receipts | Cash payment model, migration, and seeded payment rows exist | `Partial` | No payment-recording API, no receipt table or receipt generation, no receipt UI, and no dispute-ready payment state flow | `app/Models/Payment.php`, `database/migrations/2026_02_15_010001_create_payments_table.php`, `database/seeders/DatabaseSeeder.php` |
| Dispatch engine | No dispatch orchestration layer is implemented | `Not Started` | Ranking, candidate selection, offer windows, retries, race handling, and driver acceptance logic are absent | `routes/`, `app/Http/Controllers/`, `database/migrations/` |
| Realtime and notifications | No realtime mirror or notification subsystem is present | `Not Started` | No Firebase paths, no notification log, no push delivery, and no live booking/trip sync | `routes/`, `app/`, `database/migrations/` |
| Passenger app | No passenger mobile application code is present in this repo | `Not Started` | Passenger OTP login, booking flow, trip tracking, receipt history, SOS, and dispute submission are missing | `app/`, `resources/`, `routes/` |
| Driver app | No driver mobile application code is present in this repo | `Not Started` | Availability, offer handling, arrival/start/end trip, add-passenger flow, and telemetry are missing | `app/`, `resources/`, `routes/` |
| Compliance and analytics | Analytics is represented only as dashboard UI shell; no compliance engine exists | `Partial` | No threshold jobs, no warning/suspension automation, no analytics queries backing the UI, and no export implementation | `resources/views/dashboard.blade.php`, `routes/web.php`, `database/migrations/0001_01_01_000002_create_jobs_table.php` |
| Data model and backend APIs | Basic models and migrations exist for users, TODAs, tricycles, drivers, bookings, fare rules, and payments; only web routes are defined | `Partial` | Most PRD tables are missing, `routes/api.php` is missing, and the current schema does not match PRD entities such as passengers, trips, receipts, disputes, SOS, audit logs, auth sessions, and OTP challenges | `routes/`, `database/migrations/0001_01_01_000000_create_users_table.php`, `database/migrations/2026_02_15_010000_create_bookings_table.php`, `app/Models/` |
| Tests and quality | Automated tests still focus on default Laravel auth/profile behavior | `Off-PRD / Rework` | Registration and email-verification tests target flows no longer present in the current branch and not aligned with the PRD; domain coverage is missing for admin CRUD, fares, bookings, and state transitions | `tests/Feature/Auth/RegistrationTest.php`, `tests/Feature/Auth/EmailVerificationTest.php`, `tests/Feature/ProfileTest.php`, `tests/Feature/Auth/AuthenticationTest.php` |

## Implemented Surfaces

- Admin-only web access exists, with an `admin` middleware guard and login enforcement for non-admin users. Evidence: `app/Http/Middleware/EnsureAdmin.php`, `app/Http/Controllers/Auth/AuthenticatedSessionController.php`, `routes/web.php`
- Admin login UI exists and now explicitly states that passenger and driver sign-in belongs to OTP-based mobile apps. Evidence: `resources/views/auth/login.blade.php`
- Admin CRUD exists for TODAs, drivers, and tricycles, including filters and edit/create flows. Evidence: `app/Http/Controllers/Admin/TodaController.php`, `app/Http/Controllers/Admin/DriverController.php`, `app/Http/Controllers/Admin/TricycleController.php`
- Global admin search exists across TODAs, drivers, and tricycles. Evidence: `app/Http/Controllers/Admin/SearchController.php`, `routes/web.php`
- Dashboard shell exists with PRD-inspired KPI cards, filters, chart areas, heatmap placeholders, and a bookings/trips table. Evidence: `resources/views/dashboard.blade.php`
- Fare-rule administration exists for `shared` and `special` ride types, along with a calculator service and migration extension for `multiplier`, `min_fare`, and `max_fare`. Evidence: `app/Http/Controllers/Admin/FareController.php`, `app/Services/FareCalculatorService.php`, `database/migrations/2026_04_13_120000_add_prd_columns_to_fare_matrices_table.php`
- Booking and payment persistence exists at a basic level, including PRD-like booking statuses in the model and seeded booking/payment records. Evidence: `app/Models/Booking.php`, `app/Models/Payment.php`, `database/seeders/DatabaseSeeder.php`
- Sample Kabacan data exists for TODAs, tricycles, drivers, fares, a passenger user, bookings, and cash payments. Evidence: `database/seeders/DatabaseSeeder.php`

## Sprint Planning Notes

- Sprint bucket 1: stabilize PRD-aligned auth and admin foundations by removing stale Breeze registration and email-verification remnants, tightening admin-role boundaries, and deciding how web admin auth coexists with future OTP flows.
- Sprint bucket 2: align schema and domain models with the PRD by adding the missing entities and fields for passengers, standby points, trips, receipts, disputes, SOS alerts, audit logs, auth sessions, OTP challenges, and driver/tricycle compliance attributes.
- Sprint bucket 3: implement backend APIs and booking lifecycle rules by adding `routes/api.php`, booking endpoints, special-fare proposal handling, trip start/end flows, cancellation logic, and no-show handling.
- Sprint bucket 4: implement dispatch, realtime, and telemetry by adding dispatch services/jobs, candidate ranking, offer-expiry handling, race-safe assignment, live status sync, and notification logging/delivery.
- Sprint bucket 5: implement passenger and driver app surfaces, because both PRD mobile apps are effectively greenfield relative to the current repo state.
- Sprint bucket 6: repair the automated test suite by removing or rewriting stale auth tests and replacing them with PRD-aligned coverage for admin auth, fare rules, CRUD permissions, bookings, and state transitions.
