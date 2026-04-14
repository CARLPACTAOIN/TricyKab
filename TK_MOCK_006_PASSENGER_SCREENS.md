# TK-MOCK-006 Passenger Screen Production
**Assignee:** Carl
**Objective:** Produce the complex passenger booking and active-trip screens.

## Screen 1: Booking Composition (Antigravity Prompt)

```md
Create the Passenger 'Book Ride' mobile screen, centered on a web artboard. 
Follow the TK-MOCK-004 Passenger Shell baseline.

Visual Assets & Brand:
- Font: Inter. Primary Brand: #6258ca. Smooth, modern layout.
- Map Background: Full-bleed map showing the route from "Poblacion Municipal Hall" to "Osias National High School". 

UI Layout:
- Top sticky header with the TricyKab logo and back chevron.
- Bottom slide-up panel (surface-container-lowest, highly rounded top corners), exhibiting an elevated ambient shadow.
- Inside panel: 
  - Pickup ("Poblacion Municipal Hall") and Destination ("Osias National High School") input fields.
  - Ride Type Selector: Toggle between "SHARED" (active, colored #6258ca) and "SPECIAL". 
  - Fare Panel: Emphasize the Shared Fare value of "₱35.00".
- Bottom CTA: Full-width button "Book Ride" with a subtle ambient glow (backdrop-blur glassmorphism approach for a premium automotive feel).
```

## Screen 2: Assigned-Driver State (Antigravity Prompt)

```md
Create the Passenger 'Driver Assigned' mobile screen, centered on a web artboard.

Visual Assets & Brand:
- Focus on the transition state. Map area shows a dynamic route line.
- Use glassmorphism for floating overlays to keep the map visible underneath (backdrop-blur: 24px).

UI Layout:
- Top Header: Floating pill displaying the status badge `DRIVER_ON_THE_WAY` (dark text on a very light secondary blue #23b7e5 background).
- Center: Pulse rings (2-3 semi-transparent rings of #23b7e5) centered on the driver's map pin showing dynamic movement.
- Bottom Panel: 
  - Driver Profile Card: "Juan Dela Cruz" (Avatar), "XYZ-5678", "Osias TODA". 
  - Prominent ETA reading "4 min".
  - Actions: "Contact Driver" (Ghost button) and a "Cancel Request" (muted red text).
```

## Screen 3: In-Progress Trip State (Antigravity Prompt)

```md
Create the Passenger 'Trip In Progress' mobile screen, centered on a web artboard.

Visual Assets & Brand:
- Deep focus on safety and map tracking.

UI Layout:
- Top Floating Badge: `TRIP_IN_PROGRESS` (Teal #09ad95 text on highly transparent success-green background).
- Map Area: Focused driver pin progressing toward destination.
- Bottom Panel: 
  - Driver summary (Juan Dela Cruz, Plate XYZ-5678).
  - Safety Center: A highly visible "SOS" secondary action button (Red, outline-variant, 15% opacity ghost border with error red text) clearly distinct from standard actions.
  - Final ETA to destination: "6 min".
```
