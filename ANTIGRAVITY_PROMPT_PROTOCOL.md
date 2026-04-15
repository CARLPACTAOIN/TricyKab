# TricyKab Antigravity Prompt Protocol

## Purpose

This file is the shared Antigravity prompting contract for the TricyKab mockup sprint work.

Use this together with:

- `SCRUM_SPRINTS_MOCKUP_ANTIGRAVITY.md`
- `MOCKUP_PRD_ANTIGRAVITY.md`
- `PRD_MD_V2.md`

This file exists to keep all teammate outputs visually connected, PRD-aligned, and consistent with the approved TricyKab mockup direction.

## Start Order

1. Carl completes `TK-MOCK-001` first and shares the locked design packet, master prompt scaffold, status-chip matrix, export naming rules, and presentation order.
2. Perez completes `TK-MOCK-002`, Vasquez completes `TK-MOCK-004`, and Aleighx completes `TK-MOCK-005` before the production tickets that depend on them.
3. No one starts a production ticket until its dependencies are already available from the assigned owner in `SCRUM_SPRINTS_MOCKUP_ANTIGRAVITY.md`.
4. Every teammate works only inside the screens listed in their ticket. No extra screens, no extra modules, no helpful redesigns.

## Required Inputs Before Prompting

Before opening Antigravity, each teammate must have:

- this file
- `SCRUM_SPRINTS_MOCKUP_ANTIGRAVITY.md`
- `MOCKUP_PRD_ANTIGRAVITY.md`
- `PRD_MD_V2.md`
- their exact ticket section
- the approved visual parent output from the dependency ticket
- the shared status-chip and naming rules from `TK-MOCK-001`

## Master Prompt

Paste this first in every Antigravity session, then append the ticket-specific block below.

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
- Use Kabacan names and realistic Filipino names
- Use only SHARED and SPECIAL ride types
- Use only these states: CREATED, SEARCHING_DRIVER, DRIVER_ASSIGNED, DRIVER_ON_THE_WAY, DRIVER_ARRIVED, TRIP_IN_PROGRESS, COMPLETED, CANCELLED_BY_PASSENGER, CANCELLED_BY_DRIVER, NO_SHOW_PASSENGER, NO_SHOW_DRIVER, CANCELLED_NO_DRIVER
- Do not add cargo, wallet payments, chat, scheduled bookings, surge pricing, or backend logic
- Do not redesign the whole product; continue the existing TricyKab system

Important behavior:
- Reuse one consistent top bar, sidebar, card style, status badge style, button system, and map placeholder treatment
- Match the approved visual parent for this flow
- Generate only the screens listed in my ticket
```

## Ticket Prompt Template

After the Master Prompt, append this template and replace the placeholders with the exact ticket details.

```md
My sprint ticket:
- Ticket ID: TK-MOCK-XXX
- Assignee: NAME
- Flow: Admin or Passenger or Driver
- Approved visual parent: TK-MOCK-XXX by NAME
- Exact screens to generate:
  - Screen 1
  - Screen 2
  - Screen 3

Read-first constraints:
- Cite the exact sections from MOCKUP_PRD_ANTIGRAVITY.md required by this ticket
- Cite the exact sections from PRD_MD_V2.md required by this ticket
- Keep labels, badges, spacing, and card styles aligned with the approved visual parent

Output rules:
- Generate only the listed screens
- Keep filenames in this format: TK-MOCK-XXX-role-01-screen-slug.png
- After generation, give a short self-check:
  - screens completed
  - PRD sections followed
  - off-MVP features excluded
```

## Visual Parent Map

Use this mapping so every downstream ticket extends an approved screen family instead of inventing a new one.

| Ticket | Owner | Approved visual parent |
|---|---|---|
| `TK-MOCK-007` | Perez | `TK-MOCK-002` by Perez |
| `TK-MOCK-008` | Kenth | `TK-MOCK-002` by Perez |
| `TK-MOCK-006` | Carl | `TK-MOCK-004` by Vasquez |
| `TK-MOCK-009` | Vasquez | `TK-MOCK-004` by Vasquez |
| `TK-MOCK-010` | Aleighx | `TK-MOCK-005` by Aleighx |
| `TK-MOCK-013` | Kenth | `TK-MOCK-010` by Aleighx |
| `TK-MOCK-014` | Vasquez | `TK-MOCK-006` by Carl and `TK-MOCK-009` by Vasquez |
| `TK-MOCK-012` | Perez | `TK-MOCK-007` by Perez |
| `TK-MOCK-015` | Aleighx | `TK-MOCK-010` by Aleighx |

## Prompting Rules for Connected Output

- Always say `match the approved visual parent` somewhere in the ticket prompt.
- Reuse the same badge colors, button hierarchy, card radii, spacing rhythm, and map placeholder treatment from the parent ticket.
- Do not rename screens, statuses, ride types, or major CTA labels unless the PRD or mockup PRD requires it.
- If Antigravity supports reference-image inputs, include the approved parent exports as references for the session.
- If the result drifts visually, correct it by re-prompting toward the parent screens instead of accepting a near match.

## Handoff Message

When a teammate finishes a dependency ticket, they should send this message with the prompt text and exported screens:

```md
Use these attached screens as the locked visual parent for this flow. Match layout language, badge system, spacing, map placeholder treatment, and button hierarchy. Do not redesign the system. Only expand it for your assigned screens.
```

## Minimum Handoff Package

Each teammate must hand off these items to the next dependent ticket owner:

- exact Antigravity prompt used
- exported screen filenames
- one sentence stating what is locked visually
- one sentence stating what must not change in the next ticket
