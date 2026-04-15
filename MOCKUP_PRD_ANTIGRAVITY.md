# TricyKab Mockup PRD for Antigravity

## 1. Purpose

This document defines the scope and instructions for producing the next presentation deliverable for TricyKab: a non-functional web-app mockup set created in Antigravity.

This is not a build specification for working software.

The goal is to create polished, PRD-aligned screens that visually communicate how the system should look and flow during the Wednesday review.

## 2. Source of Truth and Precedence

- Product behavior, roles, flows, and scope must follow `PRD_MD_V2.md`.
- This mockup PRD narrows that full PRD into a design-only deliverable for Antigravity.
- Existing repo UI is the visual baseline and must be preserved unless a change is required to improve consistency.
- If this document and the main PRD differ on product behavior, `PRD_MD_V2.md` wins.

## 3. Current Goal

Produce a coherent set of static web mockups for the TricyKab system that:

- align with the system flows in `PRD_MD_V2.md`
- reuse the existing visual language already present in this repo
- cover the main Admin, Passenger, and Driver experiences
- do not require working logic, live data, or connected APIs

## 4. Scope for This Deliverable

### In Scope

- Static web mockups only
- Admin dashboard and admin management screens
- Passenger journey mockups presented as web-based mobile-style screens
- Driver journey mockups presented as web-based mobile-style screens
- State screens that explain the booking lifecycle
- Consistent layout, branding, typography, cards, status badges, and map panels

### Out of Scope

- Actual Laravel feature implementation
- Realtime behavior
- Backend/API integration
- Database correctness
- Working forms
- Working charts
- Native mobile app build
- Redesigning the existing landing page from scratch

## 5. Visual Baseline to Preserve

The mockups must look like a continuation of the current project, not a new product.

Primary visual references in the repo:

- `resources/views/welcome.blade.php`
- `resources/views/dashboard.blade.php`
- `resources/views/layouts/stitch.blade.php`
- `resources/views/layouts/partials/stitch_sidebar.blade.php`
- `tailwind.config.js`
- `resources/css/app.css`

### Brand and UI Direction

- Use the current `Inter`-based visual system
- Keep the current purple-forward brand instead of inventing a new palette
- Preserve the current rounded cards, soft shadows, clean borders, and dashboard-heavy admin style
- Keep the tone modern, operational, local-government-friendly, and commuter-friendly
- Continue using icon-led UI similar to the current Material Icons usage

### Current Color Foundation

- Primary: `#6258ca`
- Secondary: `#23b7e5`
- Success: `#09ad95`
- Background Light: `#f6f6f8`
- Background Dark: `#15141e`
- Footer Dark: `#283250`

### Layout Character

- Admin views should feel data-dense and operational
- Passenger and Driver flows should feel simpler, map-first, and action-oriented
- Cards should use consistent padding and rounded corners
- Tables must remain readable on smaller screens by using clear hierarchy and overflow handling
- Hover and focus states should be visually obvious even if non-functional

## 6. Product Areas That Must Drive the Mockups

The mockups should represent the MVP described in the main PRD, especially:

- Section 2: Product Overview
- Section 3: Personas and User Roles
- Section 15: Passenger App Functional Specification
- Section 16: Driver App Functional Specification
- Section 17: Admin Dashboard Functional Specification
- Section 20: Analytics and KPI Definitions

Important product reminders from the PRD:

- Passenger authentication is OTP-based
- Driver authentication is OTP-based and only for approved drivers
- Admin uses web authentication
- Shared and Special rides are both required in the mockups
- Dispatch, ETA, trip tracking, receipts, SOS, dashboard KPIs, and audit-oriented admin surfaces are part of the story
- Cargo, digital payments, chat, scheduled bookings, and surge pricing are out of MVP and should not appear as core mockup features

## 7. Mockup Deliverable Set

The professor only needs mockups, so the recommended deliverable is a screen set that clearly tells the system story.

### 7.1 Required Screens

#### Admin Web

1. Admin Login
2. Dashboard Overview
3. Drivers Management
4. TODA Management
5. Tricycle Fleet
6. Fare Rules
7. Bookings and Trips Monitor
8. Booking or Trip Detail View

#### Passenger Flow

1. OTP Login
2. Book Ride
3. Searching Driver
4. Driver Assigned and ETA
5. Trip In Progress
6. Trip Complete and Receipt
7. Trip History

#### Driver Flow

1. OTP Login
2. Driver Home and Availability
3. Incoming Booking Offer
4. Assigned Booking and Navigate to Pickup
5. Trip In Progress
6. Add Passenger for Shared Ride
7. End Trip and Payment Record

### 7.2 Stretch Screens if Time Allows

- Standby Points
- Disputes
- SOS Alerts
- Analytics detail page
- Audit Logs
- Passenger no-driver-available state
- Passenger cancellation state
- Driver arrival state

## 8. Screen Requirements

### 8.1 Admin Login

Must show:

- TricyKab branding
- Admin-only sign-in framing
- Email or username field
- Password field
- Optional helper text explaining that Passenger and Driver sign-in uses OTP-based app flows

### 8.2 Dashboard Overview

Must reflect PRD minimum views:

- Summary KPI cards
- Date filter
- TODA filter
- Barangay filter
- Booking and trip table
- Pickup heatmap area
- Destination heatmap area
- CSV export control
- PDF export control

Suggested KPIs:

- Average Passenger Wait Time
- Booking-to-Accept Rate
- Booking Completion Rate
- Trips per Barangay
- Active Drivers
- Driver Availability Rate

### 8.3 Drivers, TODAs, and Tricycle Fleet

Each management screen should show:

- Search
- Filters
- Summary counts
- Primary table
- Row status chips
- Primary CTA for add or create
- Secondary actions for edit, view, suspend, or assign

The UI should communicate operational administration, not consumer UX.

### 8.4 Fare Rules

Must show:

- Shared vs Special fare rule distinction
- Base fare
- Per km rate if shown
- Min fare
- Max fare
- Multiplier for Special ride logic
- Rule scope or route/barangay context
- Rule status chip
- Edit or create CTA

Do not present cargo pricing or digital payment pricing.

### 8.5 Bookings and Trips Monitor

Must show:

- Status-driven rows
- Ride type
- Passenger
- Driver
- Fare
- Booking reference
- Time columns
- Filters
- Clear state badges

Recommended status labels:

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

### 8.6 Passenger Book Ride

Must show:

- Pickup input
- Destination input
- Map or route panel
- Ride type selector: `SHARED` and `SPECIAL`
- Fare estimate panel
- For Special ride, show suggested fare and passenger proposal field
- Main CTA to submit booking

### 8.7 Passenger Searching Driver

Must show:

- Booking reference
- Search state headline
- Loading or pulse state
- Pickup and destination summary
- Estimated fare
- Ride type
- Cancel CTA only if still allowed

### 8.8 Passenger Assigned Driver and ETA

Must show:

- Driver profile card
- Plate number
- TODA
- ETA
- Map area
- Contact action
- Status progression

### 8.9 Passenger Trip In Progress

Must show:

- Live trip context
- Driver card
- Route or map block
- Status badge
- Safety-oriented secondary action such as `SOS`

### 8.10 Passenger Trip Complete and Receipt

Must show:

- Final fare
- Payment method
- Receipt number
- Timestamp summary
- Route summary
- CTA to view receipt details or history

### 8.11 Driver Home and Availability

Must show:

- Driver profile summary
- Availability toggle
- Today trip count
- Earnings snapshot
- Current status
- Shortcuts to trip history or active assignment

### 8.12 Driver Incoming Offer

Must show:

- Pickup
- Destination
- Ride type
- Estimated fare
- Countdown timer
- Accept CTA
- Decline CTA

### 8.13 Driver Assigned Pickup Screen

Must show:

- Passenger info
- Pickup map
- Arrival CTA
- Trip notes or pickup guidance

### 8.14 Driver Trip In Progress

Must show:

- Active trip banner
- Passenger count
- Trip progress
- Add Passenger action for Shared ride
- End Trip CTA

### 8.15 Driver End Trip and Payment Record

Must show:

- Final fare
- Cash payment recorded state
- Receipt handoff confirmation
- Trip completion summary

## 9. UX Rules for Antigravity

- Keep admin surfaces information-dense but readable
- Keep mobile-style Passenger and Driver screens simple and focused on one primary action
- Use clear status chips throughout the booking lifecycle
- Use map panels or map placeholders in booking, assignment, and trip screens
- Use horizontal scrolling or card conversion for wide data tables if a smaller viewport is shown
- Use loading placeholders or subtle pulse states for searching and waiting screens
- Use clear action hierarchy: one primary action, one or two secondary actions
- Make every screen presentation-ready even if no interaction is wired

## 10. Content and Mock Data Guidance

Use believable local content tied to Kabacan operations.

Recommended examples:

- TODAs: `Poblacion TODA`, `Osias TODA`, `Nongnongan TODA`
- Barangays: `Poblacion`, `Osias`, `Nongnongan`
- Currency: `PHP` and `₱`
- Booking reference format: `BK-2026-00XX`
- Receipt format: `RCT-2026-0000XX`

Example screen data:

- Shared fare: `₱35.00` to `₱45.00`
- Special fare: `₱80.00` to `₱120.00`
- ETA: `2 min`, `4 min`, `6 min`
- Driver names: realistic Filipino names

## 11. Antigravity Execution Instructions

When generating in Antigravity:

1. Build one shared design system first
2. Reuse one consistent top bar, sidebar, card style, status badge style, and button system
3. Preserve the current TricyKab palette and overall brand identity
4. Create Admin screens as desktop-first web views
5. Create Passenger and Driver flows as mobile-style screens presented inside a web project
6. Prioritize visual storytelling of state changes over screen quantity
7. Use static mock data only
8. Do not spend time wiring actions or creating real data logic
9. Keep the landing page visually compatible with the new mockups
10. Avoid introducing off-PRD modules such as cargo, chat, wallet payments, or scheduled bookings

## 12. Suggested Antigravity Prompt

Use this as the starting prompt in Antigravity, then refine screen by screen:

```md
Create a static web mockup set for TricyKab, a smart tricycle dispatch system for Kabacan. Follow the product behavior and scope from `PRD_MD_V2.md`, but do not implement functionality. This is for presentation mockups only.

Use the existing project design language as the visual baseline:
- Inter typography
- primary color `#6258ca`
- secondary color `#23b7e5`
- success color `#09ad95`
- light background `#f6f6f8`
- dark background `#15141e`
- rounded cards, soft shadows, clean borders, icon-led UI
- modern Laravel admin dashboard feel

Create a coherent screen set covering:
- Admin Login
- Dashboard Overview
- Drivers Management
- TODA Management
- Tricycle Fleet
- Fare Rules
- Bookings and Trips Monitor
- Booking or Trip Detail
- Passenger OTP Login
- Passenger Book Ride
- Passenger Searching Driver
- Passenger Driver Assigned and ETA
- Passenger Trip In Progress
- Passenger Trip Complete and Receipt
- Passenger Trip History
- Driver OTP Login
- Driver Home and Availability
- Driver Incoming Booking Offer
- Driver Assigned Pickup
- Driver Trip In Progress
- Driver Add Passenger
- Driver End Trip and Payment Record

Use PRD-aligned ride types and states only:
- SHARED
- SPECIAL
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

Do not include cargo, wallet payments, chat, scheduled bookings, or other off-MVP modules.
```

## 13. Acceptance Criteria for This Mockup Pass

This mockup deliverable is successful if:

- it clearly looks like TricyKab rather than a new product
- it follows the main PRD's MVP scope
- it covers the Admin, Passenger, and Driver stories
- it communicates the booking lifecycle visually
- it is polished enough for review even without functionality
- it avoids non-PRD features

## 14. Final Note

This file is a mockup brief for Antigravity, not a replacement for `PRD_MD_V2.md`.

For product correctness, always cross-check the main PRD.
