# TK-MOCK-002 Admin Shell Proof Export

## Purpose

This file defines the proof-export artifact for `TK-MOCK-002`.

The proof export exists to verify that the admin shell is locked before `TK-MOCK-007` and `TK-MOCK-008` start final screen production.

## Repo Evidence

Proof source files:

- `resources/views/admin/shell-proof.blade.php`
- `resources/views/layouts/stitch.blade.php`
- `resources/views/layouts/partials/stitch_sidebar.blade.php`
- `resources/views/layouts/partials/stitch_header.blade.php`
- `resources/views/layouts/components/filter-bar.blade.php`
- `resources/views/layouts/components/kpi-card.blade.php`
- `resources/views/layouts/components/status-badge.blade.php`
- `resources/views/layouts/components/table-shell.blade.php`

Proof route:

- route name: `admin.shell-proof`
- path: `/admin/shell-proof`

## Expected Export

When the shell-proof screen is opened in the browser, export one desktop screenshot with this exact filename:

- `TK-MOCK-002-admin-00-shell-proof.png`

## What The Export Must Show

The proof export must visibly confirm:

- sidebar brand block and nav treatment
- sticky top header and quick actions
- one filter bar with admin controls
- KPI cards with consistent card structure
- table shell with status badges
- detail panel shell with action buttons
- badge pattern preview across multiple statuses
- desktop grid behavior and spacing rhythm

## Export Capture Defaults

- viewport target: 1440px wide desktop
- mode: light theme
- nav state: `Dashboard` active
- use the mock data from `TK_MOCK_002_ADMIN_MOCK_DATA_SHEET.md`
- keep export framed around the full shell, not just one card

## Review Checklist

- layout looks like a continuation of the existing TricyKab admin UI
- cards use one spacing system
- badges use one treatment system
- filters, table shell, and detail shell belong to the same visual family
- nothing in the proof export introduces off-PRD modules

## Downstream Use

This proof export becomes the visual parent reference for:

- `TK-MOCK-007`
- `TK-MOCK-008`
