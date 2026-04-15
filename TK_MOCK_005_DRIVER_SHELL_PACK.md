# TK-MOCK-005 Driver Mobile Shell Pack
**Assignee:** Aleighx
**Objective:** Prepare the driver mobile shell and simple-state prompt pack.

## 1. Driver Mobile-Shell Prompt Pack

*Append this to the Master Prompt for all Driver screens to ensure structural consistency.*

```md
My sprint ticket:
- Ticket ID: TK-MOCK-005 (or specific production ticket)
- Assignee: Aleighx
- Flow: Driver
- Approved visual parent: TK-MOCK-005 (Base Driver Shell)

Driver Shell UI Structure & Elements:
- Render this as a mobile-sized container centered on a web artboard.
- Home Header: Sticky top bar containing the driver's avatar, name, current TODA affiliation, and a prominent "Online/Offline" toggle switch.
- Availability Card: A prominent top banner or floating card on the map indicating current status ("You are ONLINE - Finding passengers...").
- Map Background: A full-bleed map behind the floating UI cards, centered on the driver's current location (mocked, static).
- Offer Card (Incoming Booking): A large card sliding up from the bottom with a bold countdown ring treatment. Display pickup, destination, ride type (Shared/Special), and estimated fare. Include a massive Primary color (#6258ca) "ACCEPT" button and a subtle ghost "Decline" option.
- End-Trip Confirmation State: A modal or bottom sheet specifically asking "Did you receive cash payment?".
- Trip Summary Card: Shown after trip completion. A clean success-colored (#09ad95) header card displaying the final fare, receipt number, and a "Return to Map" action.
```

## 2. Driver Mock-Data Sheet

Use these exact values to maintain continuity in the driver's perspective.

**Identity & Verification:**
- Driver Name: `Juan Dela Cruz`
- Tricycle Plate Number: `XYZ-5678`
- TODA Affiliation: `Poblacion TODA`
- Verification Hint: `Verified Driver (Badge #405)`
- OTP Code: `1 0 2 0 3 0`

**Performance (Earnings Snapshot):**
- Trips Completed Today: `8 trips`
- Today's Earnings Snapshot: `₱480.00`
- Minimum Fare Context: `₱35.00 Base`

**Scenario Values (Offer & End Trip):**
- Incoming Offer Countdown: `14s` (in a circular progress indicator)
- Offer Pickup: `Poblacion Municipal Hall`
- Offer Destination: `Osias National High School`
- Offer Ride Type: `SHARED` or `SPECIAL`
- End Trip Collection: `₱80.00` (for a Special trip)
- Cash Payment Label: `Confirm Cash Received`
- Status confirmation: `Payment Recorded Successfully`

## 3. Visual Rule Note (Action vs. Admin)

- **Action-Oriented Feel:** The driver app must feel fast and reaction-driven. Do NOT use dense tables or complex dashboards like the Admin web views.
- **High Contrast:** The incoming offer card needs extremely high contrast. When an offer appears, dim the background map by 60% and make the offer card pure white (or bright dark-slate in dark mode) so it's the only thing the driver looks at.
- **Countdown Timer:** The countdown timer must look urgent but clean—use a circular ring that "empties" out as time ticks down, styled in an alert color (e.g., orange or red) if it goes below 5 seconds.
- **Fat Finger Padding:** CTA buttons MUST be large and spanning the full width of the card. A moving tricycle driver needs large touch targets. Do not use small text links for primary actions.
