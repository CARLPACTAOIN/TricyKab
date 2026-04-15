# TK-MOCK-003 Design Packet

## 1. Admin-Management Prompt Pack

*Copy and paste this prompt when generating the operational admin management screens in `TK-MOCK-008`.*

```md
Create four static, data-dense Admin Management screens for the TricyKab web dashboard: Drivers Management, TODA Management, Tricycle Fleet, and Fare Rules.

Inherit the Master System Baseline:
- Inter font, rounded cards, clean borders, minimal shadows.
- Sidebar and top header layout using the `admin` role structure.

Standardize the Management Pattern:
For each of the 4 screens, use the exact same structural template:
1. Top Section: Page Title alongside a Primary CTA (e.g. "+ Add Driver" with #6258ca background).
2. Summary Bar: 2-4 small, data-dense KPI summary counts related to the specific table (e.g. "Total Active: 142").
3. Filter Bar: A search input, 2 dropdown filters (e.g. "Status", "TODA"), and a reset button.
4. Primary Table: A wide, readable table with column headers, 5-8 sample rows, a column for Status Chips, and a "Secondary Actions" column (three-dot menu or Edit/View text links).

Screen Specific Constraints:
- Drivers Management: Table must include Driver Name, Phone, TODA, Tricycle Plate, and Driver Status (ONLINE, OFFLINE, SUSPENDED).
- TODA Management: Table must include TODA Name, Code, Assigned Barangay, and Active Drivers count.
- Tricycle Fleet: Table must include Plate Number, TODA Name, Make/Model, Capacity (default 4), and Registration Status (ACTIVE, PENDING).
- Fare Rules: Must visually distinguish SHARED vs SPECIAL rules. Table columns: Ride Type, Route/Scope (e.g. Global vs specific Barangay), Base Fare, Min Fare, Max Fare, Multiplier, Status. Do not include cargo pricing.
```

## 2. Role-Safe Mock-Data Sheet

Use these exact values when populating tables to ensure realism and Kabacan context.

### Driver Statuses & Records
- Statuses: `ONLINE` (Success/Green), `OFFLINE` (Gray), `SUSPENDED` (Danger/Red), `PENDING_APPROVAL` (Warning/Orange)
- Example Drivers: 
  - Juan Dela Cruz (0917-123-4567)
  - Pedro Penduko (0919-987-6543)
  - Maria Clara (0920-111-2222)

### TODA Records
- Statuses: `ACTIVE` (Success/Green)
- Examples:
  - Poblacion TODA (Code: POB-001) - 45 Drivers
  - Osias TODA (Code: OSI-002) - 30 Drivers
  - Nongnongan TODA (Code: NON-003) - 25 Drivers

### Fleet Records (Tricycles)
- Statuses: `ACTIVE`, `PENDING`, `EXPIRED`, `SUSPENDED`
- Examples:
  - Plate: RZ-1234, Kawasaki Barako II, Capacity: 4
  - Plate: YZ-9876, Honda TMX 125, Capacity: 4
  - Plate: QW-5555, Yamaha RS110F, Capacity: 4

### Fare Rules
- **SHARED Ride (Global):**
  - Base Fare: ₱15.00
  - Per Km: ₱0.00
  - Min/Max: ₱15.00 / ₱30.00
- **SPECIAL Ride (Global):**
  - Base Fare: ₱50.00
  - Multiplier: 1.25
  - Min/Max: ₱50.00 / ₱150.00

## 3. Visual Rule Note (Operational vs Consumer UI)

- **Prioritize Data Density over Whitespace:** Row heights in tables should be compact (e.g., 40-48px) rather than excessively padded.
- **Avoid Banners & Illustrations:** Do not use marketing illustrations, empty states with cute graphics, or large greeting banners. This is a back-office tool.
- **Grayscale for Data, Color for Status:** Text and gridlines should be predominantly gray/black. Reserve brand colors exclusively for actions (links/buttons) and status chips.
- **Horizontal Scrolling / Overflows:** Assume wide tables. Ensure columns don't compress to unreadable widths; use standard column formatting.

## 4. Required Export Set (Targeted for TK-MOCK-008)

The ultimate output for these screens will use the following standard naming:
1. `TK-MOCK-008-admin-03-drivers-management.png`
2. `TK-MOCK-008-admin-04-toda-management.png`
3. `TK-MOCK-008-admin-05-tricycle-fleet.png`
4. `TK-MOCK-008-admin-06-fare-rules.png`

## 5. PRD-Alignment Self-Check

- [x] **Cited PRD Sections Used:** `PRD_MD_V2.md` Section 17 (Admin Dashboard).
- [x] **Cited Mockup PRD Sections Used:** `MOCKUP_PRD_ANTIGRAVITY.md` Sections 7.1 (Admin Web), 8.3 (Drivers, TODAs, Fleet), 8.4 (Fare Rules), 11 (Execution).
- [x] **Exact Screens Completed:** Deliverables target prompt logic and data sheets for the 4 core admin management screens (Drivers, TODAs, Tricycles, Fares).
- [x] **Off-MVP Features Explicitly Excluded:** Standby points, disputes, audit logs, analytics, cargo fares, dynamic pricing.
