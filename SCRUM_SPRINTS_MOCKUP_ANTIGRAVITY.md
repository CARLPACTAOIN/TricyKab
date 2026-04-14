# TricyKab Antigravity Mockup Scrum Sprints

## Purpose

This file converts `MOCKUP_PRD_ANTIGRAVITY.md` into a timeboxed Scrum micro-sprint plan for the Wednesday, April 15, 2026 review.

This is an Antigravity-first mockup plan, not a Laravel implementation plan.

## Timeline

- Planning target: Tuesday, April 14, 2026
- Review target: Wednesday, April 15, 2026
- Sprint window 1: Tuesday, April 14, 2026, 9:00 AM to 12:00 PM
- Sprint window 2: Tuesday, April 14, 2026, 1:00 PM to 8:00 PM
- Sprint window 3: Wednesday, April 15, 2026, 8:00 AM to 11:00 AM

## Team Roster

| Member | Primary lane | Complexity target |
|---|---|---|
| You | Shared design system, complex passenger flow, final integration | Highest |
| Perez | Core admin shell and admin operational screens | High |
| Kenth | Admin management screens and complex driver execution flow | High |
| Vasquez | Simpler passenger screens and passenger stretch states | Lighter |
| Aleighx | Simpler driver screens, driver stretch state, export cleanup | Lighter |

## Shared Context

All tickets must start from the same source-of-truth set before any prompting or generation work:

- `MOCKUP_PRD_ANTIGRAVITY.md`
- `PRD_MD_V2.md`
- `PRD_PROGRESS_AUDIT.md`
- `resources/views/welcome.blade.php`
- `resources/views/dashboard.blade.php`
- `resources/views/layouts/stitch.blade.php`
- `resources/views/layouts/partials/stitch_sidebar.blade.php`
- `resources/css/app.css`
- `tailwind.config.js`

All tickets must use these PRD sections:

- `PRD_MD_V2.md` section 15: Passenger App Functional Specification
- `PRD_MD_V2.md` section 16: Driver App Functional Specification
- `PRD_MD_V2.md` section 17: Admin Dashboard Functional Specification
- `PRD_MD_V2.md` section 20: Analytics and KPI Definitions

All tickets must use these mockup PRD sections:

- Section 5: Visual Baseline to Preserve
- Section 6: Product Areas That Must Drive the Mockups
- Section 7: Mockup Deliverable Set
- Section 9: UX Rules for Antigravity
- Section 10: Content and Mock Data Guidance
- Section 11: Antigravity Execution Instructions
- Section 13: Acceptance Criteria for This Mockup Pass

Current repo reality from `PRD_PROGRESS_AUDIT.md` that must shape the work:

- Admin web already has a visual baseline that should be reused, not replaced.
- Passenger app is `Not Started`; do not assume existing passenger screens or flows exist in the repo.
- Driver app is `Not Started`; do not assume existing driver screens or flows exist in the repo.
- Analytics, audit-oriented screens, and several admin modules are still placeholders; mockups must still follow the PRD and mockup brief.

## Global Guardrails

- Build static mockups only.
- Do not build working Laravel features, routes, controllers, APIs, or data logic.
- Preserve the current TricyKab visual language: Inter typography, purple-forward brand, rounded cards, soft shadows, clean borders, dashboard-heavy admin feel.
- Keep the landing page visually compatible with the new mockups. Do not redesign it from scratch.
- Admin screens must be desktop-first web screens.
- Passenger and Driver screens must be mobile-style screens presented inside a web project.
- Use Kabacan mock data only: `Poblacion TODA`, `Osias TODA`, `Nongnongan TODA`, `Poblacion`, `Osias`, `Nongnongan`, `PHP`, realistic Filipino names, `BK-2026-00XX`, and `RCT-2026-0000XX`.
- Reuse one shared status-chip system across admin, passenger, and driver screens.
- Use map panels or map placeholders where the mockup PRD calls for them. Do not spend time on map integration.
- If payment appears in the mockups, show cash-oriented MVP behavior. Do not introduce wallet or digital-payment UX as a core feature.
- Exclude cargo, wallet payments, chat, scheduled bookings, surge pricing, and backend logic.
- If the mockup PRD and the main PRD disagree on behavior, `PRD_MD_V2.md` wins.

## Standard Output Package for Every Ticket

Every ticket must end with these three outputs:

1. Finalized Antigravity prompt text for the ticket scope.
2. Named screen export set using this format: `TK-MOCK-<ticket-id>-<role>-<two-digit-order>-<screen-slug>.png`
3. Short PRD-alignment self-check with:
   - cited PRD sections used
   - cited mockup PRD sections used
   - exact screens completed
   - off-MVP features explicitly excluded

## Presentation Order

Use this fixed review order in the final exported set:

1. Admin Web: `Admin Login`
2. Admin Web: `Dashboard Overview`
3. Admin Web: `Drivers Management`
4. Admin Web: `TODA Management`
5. Admin Web: `Tricycle Fleet`
6. Admin Web: `Fare Rules`
7. Admin Web: `Bookings and Trips Monitor`
8. Admin Web: `Booking or Trip Detail View`
9. Passenger Flow: `OTP Login`
10. Passenger Flow: `Book Ride`
11. Passenger Flow: `Searching Driver`
12. Passenger Flow: `Driver Assigned and ETA`
13. Passenger Flow: `Trip In Progress`
14. Passenger Flow: `Trip Complete and Receipt`
15. Passenger Flow: `Trip History`
16. Driver Flow: `OTP Login`
17. Driver Flow: `Driver Home and Availability`
18. Driver Flow: `Incoming Booking Offer`
19. Driver Flow: `Assigned Booking and Navigate to Pickup`
20. Driver Flow: `Trip In Progress`
21. Driver Flow: `Add Passenger for Shared Ride`
22. Driver Flow: `End Trip and Payment Record`
23. Stretch: `Analytics detail page`
24. Stretch: `Audit Logs`
25. Stretch: `Passenger no-driver-available state`
26. Stretch: `Passenger cancellation state`
27. Stretch: `Driver arrival state`

## Required Screen Ownership Matrix

This matrix is the source of truth for exact required-screen ownership. Each required screen is owned once.

| Flow | Exact screen name | Owner | Production ticket |
|---|---|---|---|
| Admin Web | `Admin Login` | Perez | `TK-MOCK-007` |
| Admin Web | `Dashboard Overview` | Perez | `TK-MOCK-007` |
| Admin Web | `Drivers Management` | Kenth | `TK-MOCK-008` |
| Admin Web | `TODA Management` | Kenth | `TK-MOCK-008` |
| Admin Web | `Tricycle Fleet` | Kenth | `TK-MOCK-008` |
| Admin Web | `Fare Rules` | Kenth | `TK-MOCK-008` |
| Admin Web | `Bookings and Trips Monitor` | Perez | `TK-MOCK-007` |
| Admin Web | `Booking or Trip Detail View` | Perez | `TK-MOCK-007` |
| Passenger Flow | `OTP Login` | Vasquez | `TK-MOCK-009` |
| Passenger Flow | `Book Ride` | You | `TK-MOCK-006` |
| Passenger Flow | `Searching Driver` | Vasquez | `TK-MOCK-009` |
| Passenger Flow | `Driver Assigned and ETA` | You | `TK-MOCK-006` |
| Passenger Flow | `Trip In Progress` | You | `TK-MOCK-006` |
| Passenger Flow | `Trip Complete and Receipt` | Vasquez | `TK-MOCK-009` |
| Passenger Flow | `Trip History` | Vasquez | `TK-MOCK-009` |
| Driver Flow | `OTP Login` | Aleighx | `TK-MOCK-010` |
| Driver Flow | `Driver Home and Availability` | Aleighx | `TK-MOCK-010` |
| Driver Flow | `Incoming Booking Offer` | Aleighx | `TK-MOCK-010` |
| Driver Flow | `Assigned Booking and Navigate to Pickup` | Kenth | `TK-MOCK-013` |
| Driver Flow | `Trip In Progress` | Kenth | `TK-MOCK-013` |
| Driver Flow | `Add Passenger for Shared Ride` | Kenth | `TK-MOCK-013` |
| Driver Flow | `End Trip and Payment Record` | Aleighx | `TK-MOCK-010` |

## Ticket Template Used Throughout

Every ticket in this file follows the same structure:

- `Ticket ID`
- `Assignee`
- `Objective`
- `Read First`
- `Exact Screens`
- `Deliverables`
- `Dependencies`
- `Out of Scope`
- `Definition of Done`

## Sprint 1: Foundation and Prompt Pack

**Sprint window:** Tuesday, April 14, 2026, 9:00 AM to 12:00 PM

### TK-MOCK-001

- **Assignee:** You
- **Objective:** Lock the shared TricyKab mockup system so every later ticket inherits one visual language, one prompt structure, one naming system, and one presentation order.
- **Read First:** Shared Context files above, `MOCKUP_PRD_ANTIGRAVITY.md` sections 5, 6, 7, 9, 10, 11, and 13, `PRD_MD_V2.md` sections 15, 16, 17, and 20.
- **Exact Screens:**
  - Admin Web: `Admin Login`
  - Admin Web: `Dashboard Overview`
  - Admin Web: `Drivers Management`
  - Admin Web: `TODA Management`
  - Admin Web: `Tricycle Fleet`
  - Admin Web: `Fare Rules`
  - Admin Web: `Bookings and Trips Monitor`
  - Admin Web: `Booking or Trip Detail View`
  - Passenger Flow: `OTP Login`
  - Passenger Flow: `Book Ride`
  - Passenger Flow: `Searching Driver`
  - Passenger Flow: `Driver Assigned and ETA`
  - Passenger Flow: `Trip In Progress`
  - Passenger Flow: `Trip Complete and Receipt`
  - Passenger Flow: `Trip History`
  - Driver Flow: `OTP Login`
  - Driver Flow: `Driver Home and Availability`
  - Driver Flow: `Incoming Booking Offer`
  - Driver Flow: `Assigned Booking and Navigate to Pickup`
  - Driver Flow: `Trip In Progress`
  - Driver Flow: `Add Passenger for Shared Ride`
  - Driver Flow: `End Trip and Payment Record`
- **Deliverables:**
  - One master Antigravity prompt scaffold covering product context, visual baseline, allowed ride types, allowed statuses, excluded modules, and Kabacan mock data.
  - One status-chip matrix for `CREATED`, `SEARCHING_DRIVER`, `DRIVER_ASSIGNED`, `DRIVER_ON_THE_WAY`, `DRIVER_ARRIVED`, `TRIP_IN_PROGRESS`, `COMPLETED`, `CANCELLED_BY_PASSENGER`, `CANCELLED_BY_DRIVER`, `NO_SHOW_PASSENGER`, `NO_SHOW_DRIVER`, and `CANCELLED_NO_DRIVER`.
  - One map placeholder rule set for admin heatmaps, passenger route blocks, and driver pickup or trip screens.
  - One export naming guide using the file-format rule defined above.
  - One final presentation-order checklist using the order in this file.
- **Dependencies:** None.
- **Out of Scope:** Creating any final screen exports, changing repo code, inventing a new brand system, or adding features beyond the mockup PRD and PRD.
- **Definition of Done:**
  - The master prompt scaffold can be copied into any later ticket without rewriting the product context.
  - The shared design packet preserves Inter and the current purple-forward TricyKab language.
  - The status-chip matrix and naming guide are complete enough that later tickets do not invent their own conventions.
  - The output package is complete.

### TK-MOCK-002

- **Assignee:** Perez
- **Objective:** Establish the reusable desktop admin shell based on the existing dashboard and sidebar language so all admin screens look like one system.
- **Read First:** Shared Context files above, `resources/views/dashboard.blade.php`, `resources/views/layouts/stitch.blade.php`, `resources/views/layouts/partials/stitch_sidebar.blade.php`, `MOCKUP_PRD_ANTIGRAVITY.md` sections 5, 7.1 Admin Web, 8.1, 8.2, 8.5, and 11, `PRD_MD_V2.md` sections 17 and 20.
- **Exact Screens:**
  - Admin Web: `Admin Login`
  - Admin Web: `Dashboard Overview`
  - Admin Web: `Bookings and Trips Monitor`
  - Admin Web: `Booking or Trip Detail View`
- **Deliverables:**
  - One reusable admin-shell prompt pack for sidebar, header, filter bar, KPI cards, table shell, detail panel shell, action bar, and status badges.
  - One admin mock-data sheet for KPI values, filters, booking references, ride types, and admin action labels.
  - One quick shell proof export showing spacing, card styling, badge patterns, and desktop grid behavior.
- **Dependencies:** `TK-MOCK-001`
- **Out of Scope:** Working filters, real exports, backend-linked tables, or stretch admin modules.
- **Definition of Done:**
  - The shell clearly matches the existing dashboard baseline instead of looking like a different product.
  - KPI cards, filters, tables, and status badges are standardized for reuse in `TK-MOCK-007`.
  - The output package is complete.

### TK-MOCK-003

- **Assignee:** Kenth
- **Objective:** Prepare the admin management prompt pack so all operational management screens share one consistent table-heavy pattern.
- **Read First:** Shared Context files above, `resources/views/admin/drivers/index.blade.php`, `resources/views/admin/todas/index.blade.php`, `resources/views/admin/tricycles/index.blade.php`, `resources/views/admin/fares/index.blade.php`, `MOCKUP_PRD_ANTIGRAVITY.md` sections 7.1 Admin Web, 8.3, 8.4, and 11, `PRD_MD_V2.md` section 17.
- **Exact Screens:**
  - Admin Web: `Drivers Management`
  - Admin Web: `TODA Management`
  - Admin Web: `Tricycle Fleet`
  - Admin Web: `Fare Rules`
- **Deliverables:**
  - One admin-management prompt pack for search, filters, summary counts, primary tables, row status chips, and CTA hierarchy.
  - One role-safe mock-data sheet for driver status, TODA records, fleet records, and shared versus special fare rules.
  - One visual rule note for how to keep these screens operational and data-dense without drifting into consumer UI.
- **Dependencies:** `TK-MOCK-001`
- **Out of Scope:** Standby points, disputes, audit logs, analytics, or any live CRUD behavior.
- **Definition of Done:**
  - The four management screens can be produced from one consistent prompt pack.
  - Fare Rules clearly distinguishes `SHARED` and `SPECIAL`.
  - The output package is complete.

### TK-MOCK-004

- **Assignee:** Vasquez
- **Objective:** Prepare the passenger mobile shell and simple-state prompt pack so the simpler passenger screens can be built quickly and consistently.
- **Read First:** Shared Context files above, `resources/views/welcome.blade.php`, `resources/views/dashboard.blade.php`, `MOCKUP_PRD_ANTIGRAVITY.md` sections 7.1 Passenger Flow, 8.7, 8.10, 9, 10, and 11, `PRD_MD_V2.md` section 15.
- **Exact Screens:**
  - Passenger Flow: `OTP Login`
  - Passenger Flow: `Searching Driver`
  - Passenger Flow: `Trip History`
  - Passenger Flow: `Trip Complete and Receipt`
- **Deliverables:**
  - One passenger mobile-shell prompt pack for top bar, map card, status header, primary CTA, secondary CTA, and bottom summary card.
  - One passenger mock-data sheet for OTP, references, ETA labels, fare values, receipt values, and trip-history rows.
  - One simple-state motion note for loading or pulse behavior on waiting screens without overdesign.
- **Dependencies:** `TK-MOCK-001`
- **Out of Scope:** Complex booking-form composition, assigned live map states, or backend-auth behavior.
- **Definition of Done:**
  - The passenger shell is simple, map-first, and focused on one primary action.
  - The simpler passenger screens can be produced without inventing new layout rules.
  - The output package is complete.

### TK-MOCK-005

- **Assignee:** Aleighx
- **Objective:** Prepare the driver mobile shell and simple-state prompt pack so the lighter driver screens and later driver handoff remain consistent.
- **Read First:** Shared Context files above, `resources/views/welcome.blade.php`, `resources/views/dashboard.blade.php`, `MOCKUP_PRD_ANTIGRAVITY.md` sections 7.1 Driver Flow, 8.11, 8.12, 8.15, 9, 10, and 11, `PRD_MD_V2.md` section 16.
- **Exact Screens:**
  - Driver Flow: `OTP Login`
  - Driver Flow: `Driver Home and Availability`
  - Driver Flow: `Incoming Booking Offer`
  - Driver Flow: `End Trip and Payment Record`
- **Deliverables:**
  - One driver mobile-shell prompt pack for home header, availability card, offer card, countdown treatment, trip summary card, and end-trip confirmation state.
  - One driver mock-data sheet for driver identity, verification hints, trip counts, earnings snapshot, countdown values, and cash-payment labels.
  - One visual rule note for how driver states should feel action-oriented rather than admin-heavy.
- **Dependencies:** `TK-MOCK-001`
- **Out of Scope:** Assigned pickup navigation, arrival validation, add-passenger flow, or any real driver availability logic.
- **Definition of Done:**
  - The driver shell can be reused later by `TK-MOCK-010` and referenced by `TK-MOCK-013`.
  - Countdown, availability, and end-trip states are visually coherent.
  - The output package is complete.

**Sprint 1 gate:** `TK-MOCK-001` through `TK-MOCK-005` must all end with reusable prompts, mock-data lists, and a locked visual system before any production ticket starts.

## Sprint 2: Required Screen Production

**Sprint window:** Tuesday, April 14, 2026, 1:00 PM to 8:00 PM

### TK-MOCK-006

- **Assignee:** You
- **Objective:** Produce the complex passenger booking and active-trip screens that define the main passenger story.
- **Read First:** Shared Context files above, `MOCKUP_PRD_ANTIGRAVITY.md` sections 7.1 Passenger Flow, 8.6, 8.8, 8.9, 11, and 13, `PRD_MD_V2.md` sections 15.2, 15.3, and 21.1 through 21.3.
- **Exact Screens:**
  - Passenger Flow: `Book Ride`
  - Passenger Flow: `Driver Assigned and ETA`
  - Passenger Flow: `Trip In Progress`
- **Deliverables:**
  - Three final passenger screen exports following the presentation order.
  - One Antigravity prompt for booking composition, including pickup, destination, map block, `SHARED`, `SPECIAL`, fare estimate, suggested fare, and passenger proposal field.
  - One Antigravity prompt for assigned-driver state, including driver card, plate number, TODA, ETA, contact action, map area, and status progression.
  - One Antigravity prompt for in-progress trip state, including route block, driver card, active-trip context, and visible `SOS` secondary action.
- **Dependencies:** `TK-MOCK-001`
- **Out of Scope:** Working OTP, real map routing, live driver tracking, or backend fare calculation.
- **Definition of Done:**
  - `Book Ride` visibly differentiates `SHARED` and `SPECIAL` behavior without introducing off-MVP features.
  - `Driver Assigned and ETA` visually communicates driver identity, ETA, and route context.
  - `Trip In Progress` shows a safety-oriented `SOS` action and live-trip framing.
  - The output package is complete.

### TK-MOCK-007

- **Assignee:** Perez
- **Objective:** Produce the core admin operational story for sign-in, overview, monitoring, and detailed incident-ready inspection.
- **Read First:** Shared Context files above, `MOCKUP_PRD_ANTIGRAVITY.md` sections 7.1 Admin Web, 8.1, 8.2, 8.5, 11, and 13, `PRD_MD_V2.md` sections 17, 20, and 24.
- **Exact Screens:**
  - Admin Web: `Admin Login`
  - Admin Web: `Dashboard Overview`
  - Admin Web: `Bookings and Trips Monitor`
  - Admin Web: `Booking or Trip Detail View`
- **Deliverables:**
  - Four final admin screen exports following the presentation order.
  - One Antigravity prompt for admin authentication framing, including admin-only language and OTP helper note for passenger and driver roles.
  - One Antigravity prompt for dashboard KPIs, filters, heatmap placeholders, and export controls.
  - One Antigravity prompt for booking monitor and detail view, including booking reference, ride type, passenger, driver, fare, time columns, status-driven badges, and override-ready detail framing.
- **Dependencies:** `TK-MOCK-001`, `TK-MOCK-002`
- **Out of Scope:** Real admin auth, real CSV or PDF export logic, live filters, or working override actions.
- **Definition of Done:**
  - `Dashboard Overview` includes summary cards, date filter, TODA filter, barangay filter, booking and trip table, pickup heatmap area, destination heatmap area, CSV export control, and PDF export control.
  - `Bookings and Trips Monitor` uses only the approved status labels from the mockup PRD.
  - `Booking or Trip Detail View` reads like an audit-ready operational screen rather than a generic profile page.
  - The output package is complete.

### TK-MOCK-008

- **Assignee:** Kenth
- **Objective:** Produce the four core admin management screens using the operational pattern prepared in Sprint 1.
- **Read First:** Shared Context files above, `MOCKUP_PRD_ANTIGRAVITY.md` sections 7.1 Admin Web, 8.3, 8.4, 11, and 13, `PRD_MD_V2.md` section 17, `PRD_PROGRESS_AUDIT.md` rows for Drivers / TODAs / Tricycles management and Fare rules.
- **Exact Screens:**
  - Admin Web: `Drivers Management`
  - Admin Web: `TODA Management`
  - Admin Web: `Tricycle Fleet`
  - Admin Web: `Fare Rules`
- **Deliverables:**
  - Four final admin screen exports following the presentation order.
  - One Antigravity prompt for the three table-driven management screens.
  - One Antigravity prompt for the `Fare Rules` screen with `SHARED` and `SPECIAL`, base fare, per-km context, min fare, max fare, multiplier, route or barangay scope, status chip, and edit or create CTA.
- **Dependencies:** `TK-MOCK-001`, `TK-MOCK-003`
- **Out of Scope:** Standby-point management, disputes, compliance rules, or any real CRUD or validation behavior.
- **Definition of Done:**
  - Each management screen includes search, filters, summary counts, primary table, row status chips, primary CTA, and secondary actions.
  - `Fare Rules` does not introduce cargo pricing, digital payment pricing, or other off-MVP fare concepts.
  - The output package is complete.

### TK-MOCK-009

- **Assignee:** Vasquez
- **Objective:** Produce the simpler passenger screens that complete the passenger story and keep the mobile UI consistent.
- **Read First:** Shared Context files above, `MOCKUP_PRD_ANTIGRAVITY.md` section 7.1 Passenger Flow, sections 8.7 and 8.10, sections 11 and 13, `PRD_MD_V2.md` sections 15.1, 15.3, 15.4, and 15.5.
- **Exact Screens:**
  - Passenger Flow: `OTP Login`
  - Passenger Flow: `Searching Driver`
  - Passenger Flow: `Trip Complete and Receipt`
  - Passenger Flow: `Trip History`
- **Deliverables:**
  - Four final passenger screen exports following the presentation order.
  - One Antigravity prompt for OTP sign-in framing and passenger identity confirmation.
  - One Antigravity prompt for the waiting-state screen, including booking reference, loading or pulse state, pickup and destination summary, estimated fare, ride type, and cancel CTA only if still allowed.
  - One Antigravity prompt for receipt and history, including final fare, payment method, receipt number, timestamp summary, route summary, and newest-first trip history.
- **Dependencies:** `TK-MOCK-001`, `TK-MOCK-004`
- **Out of Scope:** Real OTP verification, retry logic, live booking reconciliation, or dispute submission.
- **Definition of Done:**
  - `Searching Driver` feels like a waiting state, not an empty dashboard.
  - `Trip Complete and Receipt` clearly reads as cash-based MVP completion with a receipt record.
  - `Trip History` is visually ordered newest first and clearly linked to receipt details.
  - The output package is complete.

### TK-MOCK-010

- **Assignee:** Aleighx
- **Objective:** Produce the simpler driver screens that establish driver identity, availability, offer response, and trip closure.
- **Read First:** Shared Context files above, `MOCKUP_PRD_ANTIGRAVITY.md` section 7.1 Driver Flow, sections 8.11, 8.12, 8.15, sections 11 and 13, `PRD_MD_V2.md` sections 16.1, 16.2, and 16.5.
- **Exact Screens:**
  - Driver Flow: `OTP Login`
  - Driver Flow: `Driver Home and Availability`
  - Driver Flow: `Incoming Booking Offer`
  - Driver Flow: `End Trip and Payment Record`
- **Deliverables:**
  - Four final driver screen exports following the presentation order.
  - One Antigravity prompt for driver OTP sign-in and approved-driver framing.
  - One Antigravity prompt for driver home and offer handling, including availability toggle, status, trip count, earnings snapshot, pickup, destination, ride type, estimated fare, countdown, accept CTA, and decline CTA.
  - One Antigravity prompt for end-trip completion, including final fare, cash payment recorded state, receipt handoff confirmation, and trip completion summary.
- **Dependencies:** `TK-MOCK-001`, `TK-MOCK-005`
- **Out of Scope:** Real eligibility checks, push notifications, offer race handling, or backend payment recording.
- **Definition of Done:**
  - `Driver Home and Availability` clearly communicates current status and online or offline readiness.
  - `Incoming Booking Offer` has a visible countdown and strong accept versus decline hierarchy.
  - `End Trip and Payment Record` closes the driver story without introducing wallet or non-cash flow.
  - The output package is complete.

**Sprint 2 gate:** Every required admin screen and every simpler passenger and driver screen must exist by the end of Sprint 2. Only the more complex driver execution screens and explicitly scheduled stretch screens should remain for Sprint 3.

## Sprint 3: Complex Flow Completion and Review Pack

**Sprint window:** Wednesday, April 15, 2026, 8:00 AM to 11:00 AM

### TK-MOCK-011

- **Assignee:** You
- **Objective:** Run the final integration pass, enforce cross-screen consistency, keep the landing page visually compatible, and assemble the final presentation-ready export order.
- **Read First:** Shared Context files above, this entire file, `MOCKUP_PRD_ANTIGRAVITY.md` sections 5, 11, and 13.
- **Exact Screens:**
  - Admin Web: `Admin Login`
  - Admin Web: `Dashboard Overview`
  - Admin Web: `Drivers Management`
  - Admin Web: `TODA Management`
  - Admin Web: `Tricycle Fleet`
  - Admin Web: `Fare Rules`
  - Admin Web: `Bookings and Trips Monitor`
  - Admin Web: `Booking or Trip Detail View`
  - Passenger Flow: `OTP Login`
  - Passenger Flow: `Book Ride`
  - Passenger Flow: `Searching Driver`
  - Passenger Flow: `Driver Assigned and ETA`
  - Passenger Flow: `Trip In Progress`
  - Passenger Flow: `Trip Complete and Receipt`
  - Passenger Flow: `Trip History`
  - Driver Flow: `OTP Login`
  - Driver Flow: `Driver Home and Availability`
  - Driver Flow: `Incoming Booking Offer`
  - Driver Flow: `Assigned Booking and Navigate to Pickup`
  - Driver Flow: `Trip In Progress`
  - Driver Flow: `Add Passenger for Shared Ride`
  - Driver Flow: `End Trip and Payment Record`
  - Stretch: `Analytics detail page`
  - Stretch: `Audit Logs`
  - Stretch: `Passenger no-driver-available state`
  - Stretch: `Passenger cancellation state`
  - Stretch: `Driver arrival state`
- **Deliverables:**
  - One final export manifest in the exact Presentation Order defined in this file.
  - One consistency pass note covering typography, color, badge, spacing, map placeholders, and CTA hierarchy across the full set.
  - One final review checklist confirming no off-MVP features appear in the exported sequence.
- **Dependencies:** `TK-MOCK-001`, `TK-MOCK-006`, `TK-MOCK-007`, `TK-MOCK-008`, `TK-MOCK-009`, `TK-MOCK-010`
- **Out of Scope:** Rebuilding completed screens from scratch, changing screen ownership, or starting unscheduled stretch backlog.
- **Definition of Done:**
  - The full set reads like one TricyKab story from admin to passenger to driver.
  - The landing page remains visually compatible with the mockup system.
  - Stretch screens are included only if completed and polished before the review cutoff.
  - The output package is complete.

### TK-MOCK-012

- **Assignee:** Perez
- **Objective:** Add polish to the admin set and create the two scheduled admin stretch screens only after the required admin screens are locked.
- **Read First:** Shared Context files above, `MOCKUP_PRD_ANTIGRAVITY.md` section 7.2 Stretch Screens if Time Allows, sections 11 and 13, `PRD_MD_V2.md` sections 17 and 20.
- **Exact Screens:**
  - Stretch: `Analytics detail page`
  - Stretch: `Audit Logs`
- **Deliverables:**
  - Two final stretch admin screen exports.
  - One Antigravity prompt for analytics detail that extends the dashboard KPI story without inventing new KPI definitions.
  - One Antigravity prompt for audit logs with immutable-history framing, filters, actor, timestamp, action type, and export-aware layout.
- **Dependencies:** `TK-MOCK-001`, `TK-MOCK-002`, `TK-MOCK-007`
- **Out of Scope:** `Standby Points`, `Disputes`, `SOS Alerts`, or any new admin module beyond the two scheduled stretch screens.
- **Definition of Done:**
  - Required admin screens are already locked before stretch generation begins.
  - `Analytics detail page` feels like a deeper view of the existing dashboard rather than a new product surface.
  - `Audit Logs` reads as operational and compliance-aware.
  - The output package is complete.

### TK-MOCK-013

- **Assignee:** Kenth
- **Objective:** Produce the complex driver execution screens that finish the actual driver trip story.
- **Read First:** Shared Context files above, `MOCKUP_PRD_ANTIGRAVITY.md` section 7.1 Driver Flow, sections 8.13, 8.14, 11, and 13, `PRD_MD_V2.md` sections 16.3, 16.4, 14.2, 14.3, and 14.4.
- **Exact Screens:**
  - Driver Flow: `Assigned Booking and Navigate to Pickup`
  - Driver Flow: `Trip In Progress`
  - Driver Flow: `Add Passenger for Shared Ride`
- **Deliverables:**
  - Three final driver screen exports following the presentation order.
  - One Antigravity prompt for assigned pickup and navigation state, including passenger info, pickup map, arrival CTA, and pickup guidance.
  - One Antigravity prompt for in-progress trip state and shared-ride passenger-count context.
  - One Antigravity prompt for the add-passenger state, including quantity input, capacity framing, and shared-ride wording.
- **Dependencies:** `TK-MOCK-001`, `TK-MOCK-005`, `TK-MOCK-010`
- **Out of Scope:** Real navigation, arrival validation, GPS checks, or backend capacity enforcement.
- **Definition of Done:**
  - The three screens visually flow from offer acceptance into pickup, trip execution, and shared-ride add-passenger context.
  - `Add Passenger for Shared Ride` stays within MVP shared-ride behavior and does not mutate the original passenger fare story.
  - The output package is complete.

### TK-MOCK-014

- **Assignee:** Vasquez
- **Objective:** Produce the simpler passenger stretch states and normalize passenger naming, references, and mock data across the full passenger set.
- **Read First:** Shared Context files above, `MOCKUP_PRD_ANTIGRAVITY.md` section 7.2 Stretch Screens if Time Allows, sections 11 and 13, `PRD_MD_V2.md` sections 15.2, 15.3, and 21.1.
- **Exact Screens:**
  - Stretch: `Passenger no-driver-available state`
  - Stretch: `Passenger cancellation state`
- **Deliverables:**
  - Two final stretch passenger screen exports.
  - One Antigravity prompt for no-driver-available state with retry or new-booking CTA.
  - One Antigravity prompt for passenger cancellation state with clear closure messaging and non-conflicting status treatment.
  - One passenger normalization note aligning references, fare ranges, and labels across `TK-MOCK-006` and `TK-MOCK-009`.
- **Dependencies:** `TK-MOCK-001`, `TK-MOCK-006`, `TK-MOCK-009`
- **Out of Scope:** New passenger features, working retry logic, or dispute-handling flows.
- **Definition of Done:**
  - Both stretch states are clearly labeled as passenger outcomes in the booking lifecycle.
  - Labels, references, and mock data remain consistent across the full passenger set.
  - The output package is complete.

### TK-MOCK-015

- **Assignee:** Aleighx
- **Objective:** Produce the driver arrival stretch state, then finalize driver export naming and order-checking against the shared design packet.
- **Read First:** Shared Context files above, `MOCKUP_PRD_ANTIGRAVITY.md` section 7.2 Stretch Screens if Time Allows, sections 11 and 13, `PRD_MD_V2.md` sections 16.3 and 14.2.
- **Exact Screens:**
  - Stretch: `Driver arrival state`
- **Deliverables:**
  - One final stretch driver screen export.
  - One Antigravity prompt for driver arrival state with arrival confirmation framing and handoff into trip start.
  - One driver export cleanup note confirming filenames and order for all driver screens owned by Aleighx and Kenth.
- **Dependencies:** `TK-MOCK-001`, `TK-MOCK-010`
- **Out of Scope:** Live arrival validation, start-trip handshake logic, or any new driver features outside the planned flow.
- **Definition of Done:**
  - `Driver arrival state` clearly bridges assigned pickup and trip start.
  - Driver export names follow the shared naming convention with no gaps or duplicates.
  - The output package is complete.

**Sprint 3 gate:** The exported set must be presentation-ready, follow the fixed story order, and show no off-PRD features.

## Non-Blocking Stretch Backlog Not Scheduled in the Timebox

These screens are acknowledged from `MOCKUP_PRD_ANTIGRAVITY.md` section 7.2 but are not scheduled in the April 14 to April 15 micro-sprint window:

- `Standby Points`
- `Disputes`
- `SOS Alerts`

Do not start these unless every required screen and every scheduled stretch screen above is already locked and exported.

## Final Validation Checklist

- Every required screen from `MOCKUP_PRD_ANTIGRAVITY.md` section 7.1 is assigned exactly once in the Required Screen Ownership Matrix.
- All scheduled stretch screens are clearly marked non-blocking.
- Passenger tickets cite passenger screen requirements and `PRD_MD_V2.md` section 15.
- Driver tickets cite driver screen requirements and `PRD_MD_V2.md` section 16.
- Admin tickets cite admin requirements and `PRD_MD_V2.md` sections 17 and 20.
- `You`, Perez, and Kenth own the complex work.
- Vasquez and Aleighx primarily own the simpler screens and state variants.
- No ticket asks for Laravel features, API work, database work, realtime behavior, or off-MVP modules.
- All tickets require the same three-output package: prompt text, named exports, and PRD self-check.

## Assumptions and Defaults

- The review is on Wednesday, April 15, 2026, so this plan uses micro-sprints instead of week-long Scrum iterations.
- Delivery is Antigravity-first; this document plans mockup production, not working software.
- `PRD_PROGRESS_AUDIT.md` was reviewed for current-state context, but this sprint-plan document alone does not materially change implementation status.
