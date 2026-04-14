# TK-MOCK-001 Design Packet

## 1. Master Antigravity Prompt Scaffold

*Copy and paste this section first in every Antigravity session.*

```md
Create static presentation mockups for TricyKab only. This is not working software.

Source of truth:
- ANTIGRAVITY_PROMPT_PROTOCOL.md
- SCRUM_SPRINTS_MOCKUP_ANTIGRAVITY.md
- MOCKUP_PRD_ANTIGRAVITY.md
- PRD_MD_V2.md

Follow these fixed rules:
- Preserve the current TricyKab visual baseline
- Inter typography
- Primary color #6258ca
- Secondary color #23b7e5
- Success color #09ad95
- Light background #f6f6f8
- Dark background #15141e
- Rounded cards, soft shadows, clean borders, icon-led UI
- Modern Laravel admin dashboard feel
- Admin screens are desktop-first web views
- Passenger and Driver screens are mobile-style screens inside a web project
- Static mock data only
- Use Kabacan names (Poblacion, Osias, Nongnongan) and realistic Filipino names
- Use only SHARED and SPECIAL ride types
- Use only MVP statuses (see status-chip matrix)
- Do not add cargo, wallet payments, chat, scheduled bookings, surge pricing, or backend logic
- Do not redesign the whole product; continue the existing TricyKab system

Important behavior:
- Reuse one consistent top bar, sidebar, card style, status badge style, button system, and map placeholder treatment
- Match the approved visual parent for this flow
- Generate only the screens listed in my ticket
```

## 2. Status-Chip Matrix

Use these exact labels and color associations for consistency across all states.

| Status Enum | Suggested Color System | Scenario |
| :--- | :--- | :--- |
| `CREATED` | **Secondary (Blue)** `#23b7e5` | Initial booking created |
| `SEARCHING_DRIVER` | **Secondary (Blue) Pulse** | Waiting for a driver to accept |
| `DRIVER_ASSIGNED` | **Primary (Purple)** `#6258ca` | Driver accepted the offer |
| `DRIVER_ON_THE_WAY` | **Primary (Purple)** `#6258ca` | Driver navigating to pickup |
| `DRIVER_ARRIVED` | **Success (Teal)** `#09ad95` | Driver at the pickup location |
| `TRIP_IN_PROGRESS` | **Primary (Purple)** `#6258ca` | Ride is active |
| `COMPLETED` | **Success (Teal)** `#09ad95` | Trip successfully ended and paid |
| `CANCELLED_BY_PASSENGER` | **Warning/Danger (Red)** | Passenger cancelled before/during |
| `CANCELLED_BY_DRIVER` | **Warning/Danger (Red)** | Driver cancelled before pickup |
| `NO_SHOW_PASSENGER` | **Warning/Danger (Red)** | Passenger didn't appear |
| `NO_SHOW_DRIVER` | **Warning/Danger (Red)** | Driver didn't appear |
| `CANCELLED_NO_DRIVER` | **Warning/Gray** | System timeout |

## 3. Map Placeholder Rule Set

Map panels must act as standard placeholders across all mockups. **Do not** spend time implementing live routing or real APIs.

- **Admin Heatmaps (Dashboard):** Use a static, low-opacity greyscale map background with semi-transparent primary (`#6258ca`) and secondary (`#23b7e5`) colored blobs overlayed to represent pickup/destination hotspots.
- **Passenger Route Blocks:** Display a clean map card with a simple two-point path. Use a distinct pin for Pickup and Destination. Include an estimated route line (primary color) between them.
- **Driver Pickup/Trip Screens:** Display an active navigation perspective map dummy. Highlight the driver's current location (e.g., a tricycle icon) with a highlighted path to the pickup point or destination. Keep the UI layer stacked above the map.

## 4. Export Naming Guide

Every generated screen must use this strict naming format for easy compilation:
`TK-MOCK-<ticket-id>-<role>-<two-digit-order>-<screen-slug>.png`

**Role keys:** `admin`, `passenger`, `driver`, `stretch`
*Examples:*
- `TK-MOCK-007-admin-01-dashboard-overview.png`
- `TK-MOCK-006-passenger-10-book-ride.png`

## 5. Final Presentation-Order Checklist

Generate and verify these exports in the exact order below for final integration (`TK-MOCK-011`).

### Required System
- [ ] 01. `TK-MOCK-007-admin-01-login.png`
- [ ] 02. `TK-MOCK-007-admin-02-dashboard-overview.png`
- [ ] 03. `TK-MOCK-008-admin-03-drivers-management.png`
- [ ] 04. `TK-MOCK-008-admin-04-toda-management.png`
- [ ] 05. `TK-MOCK-008-admin-05-tricycle-fleet.png`
- [ ] 06. `TK-MOCK-008-admin-06-fare-rules.png`
- [ ] 07. `TK-MOCK-007-admin-07-bookings-and-trips.png`
- [ ] 08. `TK-MOCK-007-admin-08-booking-detail.png`
- [ ] 09. `TK-MOCK-009-passenger-09-otp-login.png`
- [ ] 10. `TK-MOCK-006-passenger-10-book-ride.png`
- [ ] 11. `TK-MOCK-009-passenger-11-searching-driver.png`
- [ ] 12. `TK-MOCK-006-passenger-12-driver-assigned.png`
- [ ] 13. `TK-MOCK-006-passenger-13-trip-in-progress.png`
- [ ] 14. `TK-MOCK-009-passenger-14-trip-complete.png`
- [ ] 15. `TK-MOCK-009-passenger-15-trip-history.png`
- [ ] 16. `TK-MOCK-010-driver-16-otp-login.png`
- [ ] 17. `TK-MOCK-010-driver-17-home-and-availability.png`
- [ ] 18. `TK-MOCK-010-driver-18-incoming-offer.png`
- [ ] 19. `TK-MOCK-013-driver-19-navigate-to-pickup.png`
- [ ] 20. `TK-MOCK-013-driver-20-trip-in-progress.png`
- [ ] 21. `TK-MOCK-013-driver-21-add-passenger.png`
- [ ] 22. `TK-MOCK-010-driver-22-end-trip.png`

### Stretch Targets
- [ ] 23. `TK-MOCK-012-stretch-23-analytics-detail.png`
- [ ] 24. `TK-MOCK-012-stretch-24-audit-logs.png`
- [ ] 25. `TK-MOCK-014-stretch-25-passenger-no-driver.png`
- [ ] 26. `TK-MOCK-014-stretch-26-passenger-cancellation.png`
- [ ] 27. `TK-MOCK-015-stretch-27-driver-arrival.png`
