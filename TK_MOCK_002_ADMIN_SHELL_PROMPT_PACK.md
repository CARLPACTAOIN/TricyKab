# TK-MOCK-002 Admin Shell Prompt Pack

## Purpose

This file is the reusable Antigravity prompt pack for `TK-MOCK-002`.

It establishes the shared admin shell for these Sprint 2 screens:

- `Admin Login`
- `Dashboard Overview`
- `Bookings and Trips Monitor`
- `Booking or Trip Detail View`

This prompt pack must be used by:

- Perez for `TK-MOCK-007`
- Kenth for any downstream admin screens that must visually match the admin shell

## Read First

Source files and sections to review before prompting:

- `TK_MOCK_001_DESIGN_PACKET.md`
- `ANTIGRAVITY_PROMPT_PROTOCOL.md`
- `SCRUM_SPRINTS_MOCKUP_ANTIGRAVITY.md`
- `MOCKUP_PRD_ANTIGRAVITY.md` sections 5, 7.1 Admin Web, 8.1, 8.2, 8.5, 11, and 13
- `PRD_MD_V2.md` sections 17 and 20
- `resources/views/dashboard.blade.php`
- `resources/views/layouts/stitch.blade.php`
- `resources/views/layouts/partials/stitch_sidebar.blade.php`
- `resources/views/layouts/partials/stitch_header.blade.php`
- `resources/views/layouts/components/filter-bar.blade.php`
- `resources/views/layouts/components/kpi-card.blade.php`
- `resources/views/layouts/components/status-badge.blade.php`
- `resources/views/layouts/components/table-shell.blade.php`

## Locked Admin Shell Rules

These rules define the admin shell and must not be reinterpreted in downstream tickets:

- Use Inter typography and the current TricyKab purple-forward palette.
- Keep the shell desktop-first and operational, not consumer-oriented.
- Use the fixed left sidebar + sticky top header structure already present in the repo.
- Use information-dense cards with clear hierarchy and restrained spacing.
- Keep KPI cards compact, readable, and consistent in height.
- Use one filter-bar language across dashboard and monitor screens.
- Use white cards on light background with subtle borders and soft shadows.
- Use status badges as compact chips, not large banners.
- Keep tables readable with strong headers, nowrap cells where useful, and hover feedback.
- Keep detail views audit-oriented: clear booking reference, passenger, driver, fare, timestamps, state, and manual action framing.
- Keep Admin Login visually compatible with the shell even though it is a standalone auth screen.

## Admin Shell Component Checklist

The admin shell must consistently include these reusable pieces:

- sidebar brand block
- sidebar nav with active state treatment
- sticky header with breadcrumb/title context
- global search placement in the header
- compact action buttons for help, notifications, theme, and logout
- filter bar with rounded select controls and two-button action cluster
- KPI cards with title, value, optional icon, and compact subtitle
- table shell with clear header row and status badges
- detail card pattern with section labels and action buttons

## Master Admin Shell Prompt

Paste this after the `TK_MOCK_001` master prompt when working on admin shell outputs.

```md
Build the TricyKab admin shell as a reusable desktop-first operational interface.

Use the existing TricyKab admin layout as the visual parent:
- fixed left sidebar
- sticky top header
- white cards on light gray background
- purple-forward action hierarchy
- compact KPI cards
- compact filter bar
- operational data tables
- small rounded status badges

The shell must support these screens without changing the design language:
- Admin Login
- Dashboard Overview
- Bookings and Trips Monitor
- Booking or Trip Detail View

Keep the UI modern, local-government-friendly, and audit-oriented.
Do not make it look like a fintech app, a SaaS marketing page, or a mobile app.
Do not introduce new brand colors or alternate typography.

Use these admin shell building blocks consistently:
- sidebar with icon + label nav items
- sticky header with search and quick actions
- filter bar with date/TODA/status/type controls
- KPI cards with consistent spacing and icon placement
- data tables with clear header row and hover state
- detail card with metadata sections and quick actions
- compact badge system for booking lifecycle states
```

## Screen-Specific Prompt Blocks

### 1. Admin Login

```md
Generate an Admin Login screen that visually belongs to the same TricyKab admin system.

Must show:
- TricyKab branding
- admin-only sign-in framing
- email or username field
- password field
- helper text that passenger and driver sign-in uses OTP-based mobile flows

Keep it clean, centered, and compatible with the rest of the admin color system.
```

### 2. Dashboard Overview

```md
Generate a Dashboard Overview screen using the locked admin shell.

Must show:
- summary KPI cards
- date filter
- TODA filter
- barangay filter
- booking and trip table preview
- pickup heatmap placeholder
- destination heatmap placeholder
- CSV export control
- PDF export control

The dashboard should feel dense, operational, and presentation-ready.
```

### 3. Bookings and Trips Monitor

```md
Generate a Bookings and Trips Monitor screen using the locked admin shell.

Must show:
- status-driven rows
- ride type
- passenger
- driver
- fare
- booking reference
- time columns
- filters
- clear status badges

Use only these status labels:
- CREATED
- SEARCHING_DRIVER
- DRIVER_ASSIGNED
- DRIVER_ON_THE_WAY
- DRIVER_ARRIVED
- TRIP_IN_PROGRESS
- COMPLETED
- CANCELLED_BY_PASSENGER
- CANCELLED_BY_DRIVER
- NO_SHOW_PASSENGER
- NO_SHOW_DRIVER
- CANCELLED_NO_DRIVER
```

### 4. Booking or Trip Detail View

```md
Generate a Booking or Trip Detail View using the locked admin shell.

The screen should feel like an investigation-ready operations detail panel.

Must emphasize:
- booking reference
- passenger info
- driver info
- ride type
- current status
- fare summary
- booking and trip timestamps
- route or area summary
- manual action framing for view/edit/override context

Keep the page structured as a desktop admin detail workspace, not a profile page.
```

## Downstream Reuse Rule

Any downstream admin ticket must explicitly say:

`Match the approved visual parent from TK-MOCK-002. Do not redesign the admin shell.`

## Expected Output Package

When this prompt pack is used, the output package should include:

- finalized prompt text
- named screen exports
- short PRD-alignment self-check
