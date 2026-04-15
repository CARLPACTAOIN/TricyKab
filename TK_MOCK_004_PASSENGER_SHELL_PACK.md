# TK-MOCK-004 Passenger Mobile Shell Pack
**Assignee:** Vasquez (Darvie)
**Objective:** Passenger mobile shell and simple-state prompt pack.

## 1. Passenger Mobile-Shell Prompt Pack

*Append this to the Master Prompt for all Passenger screens to ensure structural consistency before injecting screen-specific requirements.*

```md
My sprint ticket:
- Ticket ID: TK-MOCK-004 (or specific production ticket)
- Assignee: Vasquez
- Flow: Passenger 
- Approved visual parent: TK-MOCK-004 (Base Passenger Shell)

Passenger Shell UI Structure:
- Render this as a mobile-sized container centered on a web artboard (e.g., modern iPhone proportions).
- Top Bar: Sticky header with a clean, centered title or "TricyKab" logo. Include an avatar or menu icon on the right, and a back chevron on the left if applicable.
- Main Body Area: Full-bleed map placeholder filling the upper screen space (following the Map Placeholder Rule Set).
- Overlays (Status): Place status headers (e.g., "Searching...", "In Progress") as floating cards overlapping the top of the map or bottom content. Use the Status-Chip Matrix colors.
- Bottom Summary Card: A white (or dark grey in dark mode), rounded-top panel sliding up from the bottom containing all critical data (fare, driver info, ETAs).
- Actions: Keep buttons at the very bottom of the Summary Card. One primary CTA (full width, #6258ca Primary color) and a max of one secondary CTA (ghost button or text link).
```

## 2. Passenger Mock-Data Sheet

Use these exact data points to maintain continuity across the passenger story.

**Authentication:**
- Passenger Name: `Maria Clara`
- Phone Number: `+63 912 345 6789`
- OTP Code: `4 5 9 8 2 1`

**Locations:**
- Pickup: `Poblacion Municipal Hall`
- Destination: `Osias National High School`

**Values & Labels:**
- Booking Reference: `BK-2026-0042`
- Receipt Number: `RCT-2026-000042`
- Shared Fare: `₱35.00`
- Special Fare: `₱80.00`
- ETA variations: `2 min`, `4 min`, `6 min`
- Payment Method: `Cash`

**Trip History Rows (For History Screen):**
- Row 1: `Today 09:15 AM`, `Osias → Poblacion`, `₱35.00`, `COMPLETED`
- Row 2: `Yesterday 04:30 PM`, `Nongnongan → Osias`, `₱80.00`, `COMPLETED`
- Row 3: `Monday 07:10 AM`, `Poblacion → Market`, `₱35.00`, `CANCELLED_BY_DRIVER`

## 3. Simple-State Motion Note (For "Searching" and "Waiting" States)

When creating static mockups of transitional states (e.g., `Searching Driver`):

- **Do NOT** design complex animations or over-engineered loading screens.
- **Use "Pulse" rings:** Represent loading by placing two to three semi-transparent rings (in the Secondary `#23b7e5` color) expanding outward from the user's map pin or the status badge.
- **Skeleton loading:** If data is fetching, show simple grey, rounded rectangles instead of text (`animate-pulse` look).
- **Driver Avatar slot:** In the waiting screen, show a dashed circle or a muted silhouette where the driver's face will eventually appear.
