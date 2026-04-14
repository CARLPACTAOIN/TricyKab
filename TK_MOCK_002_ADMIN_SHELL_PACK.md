# TK-MOCK-002 Admin Desktop Shell Pack
**Assignee:** Perez
**Objective:** Establish the reusable desktop admin shell to match the existing dashboard baseline.

## 1. Admin Desktop-Shell Prompt Pack

*Append this to the Master Prompt for all Admin screens to ensure structural consistency.*

```md
My sprint ticket:
- Ticket ID: TK-MOCK-002 (or specific production ticket)
- Assignee: Perez
- Flow: Admin Web
- Approved visual parent: TK-MOCK-002 (Base Admin Shell)

Admin Shell UI Structure & Elements:
- Render this as a wide Desktop Web view (e.g., 1440px wide).
- Sidebar (Left): Fixed navigation menu with a dark background (#15141e or #283250). Top item shows the TricyKab logo. Highlight the active page in the primary color (#6258ca) with a left-border accent.
- Top Header: A clean white top bar containing page title on the left, and admin profile avatar (with a dropdown arrow) on the far right.
- Filter Bar: Below the header. Keep inputs visually distinct (white pills with soft borders). Include dropdowns for Date Range, TODA, and Barangay. Include ghost/outline buttons on the right for "Export CSV" and "Export PDF".
- Background: The main content area must use the light background `#f6f6f8`.
- Component Styling (Cards): All KPI cards, tables, and charts must rest inside white containers with `rounded-xl` corners and a very subtle soft shadow. No harsh/thick borders.
- Component Styling (Tables): Provide generous horizontal padding for rows. Header rows should be uppercase, small-text, and slightly gray.
- Detail Panel Shell: When showing a detail view, push the table/list to the left and slide a wide white "Detail Panel" from the right side, consuming about 1/3 of the screen width.
```

## 2. Admin Mock-Data Sheet

Use these exact data points to maintain continuity across all admin reports, monitors, and dashboards.

**KPI Values (Dashboard):**
- Avg Wait Time: `6.8 min`
- Booking-to-Accept: `78%`
- Completion Rate: `91%`
- Active Drivers (Online): `142`
- Trips Today: `326`
- Driver Availability: `64%`

**Global Filters:**
- Date Range: `Last 7 Days` (active), `Today`, `This Month`
- TODA Options: `All TODAs` (active), `Poblacion TODA`, `Osias TODA`, `Nongnongan TODA`
- Barangay Options: `All Barangays` (active), `Poblacion`, `Osias`, `Nongnongan`

**List/Table Values:**
- Booking References: `BK-2026-0018`, `BK-2026-0019`, `BK-2026-0020`
- Ride Types: `SHARED`, `SPECIAL`
- Fares: `₱35.00` (Shared), `₱80.00` (Special)
- Admin Actions: `View Detail`, `Edit`, `Suspend`, `Assign Driver`
- Passenger Names: `Maria Clara`, `Juan D.`, `Amy Lee`
- Driver Names: `Mariano Ramos`, `Carlos Miguel`, `Roberto Cruz`

## 3. Quick Shell Layout Proof Notes

*Instead of a full screen generation, ensure the following constraints are proven true when generating TK-MOCK-007 and TK-MOCK-008:*

- **Spacing:** The gutter between the sidebar and the main content is consistent (e.g., `p-6` or `p-8`).
- **Badge patterns:** Status badges are rendered as small, highly-rounded pills with bold text. They MUST use the colors defined in `TK-MOCK-001` (e.g., `COMPLETED` is Success/Teal `#09ad95` text on a highly transparent Teal background).
- **Desktop Grid:** KPI cards run horizontally across the top (3 or 4 columns). The table or heatmap occupies the main 2/3 column layout below it.
