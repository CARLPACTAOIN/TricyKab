# Kabacan Smart Tricycle Dispatch System — User Manual

This manual describes the **Kabacan Smart Tricycle Dispatch System** (implementation codebase: **TricyKab**): what it does, who uses it, and how to complete everyday tasks safely and consistently. Product intent and behavior are defined in `PRD_MD_V2.md`; this document translates that specification into practical guidance for passengers, drivers, and administrators.

---

## Table of contents

1. [What this system is](#1-what-this-system-is)
2. [Who uses the system](#2-who-uses-the-system)
3. [Major components](#3-major-components)
4. [Core concepts](#4-core-concepts)
5. [Passenger guide](#5-passenger-guide)
6. [Driver guide](#6-driver-guide)
7. [Administrator guide](#7-administrator-guide)
8. [Booking lifecycle (what each status means)](#8-booking-lifecycle-what-each-status-means)
9. [How dispatch matching works](#9-how-dispatch-matching-works)
10. [Fares: shared vs special](#10-fares-shared-vs-special)
11. [Payments, receipts, and disputes](#11-payments-receipts-and-disputes)
12. [Safety: SOS alerts](#12-safety-sos-alerts)
13. [Compliance (drivers)](#13-compliance-drivers)
14. [Connectivity, offline behavior, and retries](#14-connectivity-offline-behavior-and-retries)
15. [Security and account access](#15-security-and-account-access)
16. [Pilot or lab deployment notes](#16-pilot-or-lab-deployment-notes)
17. [Privacy and data handling (summary)](#17-privacy-and-data-handling-summary)
18. [Appendix: reference API routes (v1)](#appendix-reference-api-routes-v1)

---

## 1. What this system is

The Kabacan Smart Tricycle Dispatch System is a **geolocation-based dispatch and trip management platform** for tricycle transportation in Kabacan. It supports:

- Digital ride requests (bookings) from passengers  
- Dispatch of accredited drivers using proximity, fairness, and policy rules  
- Trip execution tracking (pickup, start, end)  
- **Cash-first** payment recording and digital receipts (informational; not a tax invoice)  
- Administrative oversight for LGU, TMU, and TODA operators  

It is designed to reduce unpredictable roadside hailing, shorten passenger wait times, reduce unnecessary driver roaming, and give operators visibility into trips and compliance.

---

## 2. Who uses the system

| Role | Typical user | Primary goals |
|------|----------------|---------------|
| **Passenger** | Resident or visitor | Request rides, track assigned driver and trip, pay cash, view receipts and history, use SOS when needed |
| **Driver** | Accredited TODA driver | Go online, receive offers, execute trips, record passengers (shared rides), record cash payment |
| **TODA Admin** | Association operator | View and support **own TODA** drivers and trips; limited operational actions per policy |
| **LGU / TMU Admin** | Municipal traffic / LGU staff | Full operational and policy management: drivers, tricycles, fares, standby points, disputes, SOS, audits, exports |

Detailed permissions follow **scope-based access** (see `PRD_MD_V2.md`, Security section). If your deployment uses role labels differently in the UI, the underlying rule is the same: **organization boundaries must not leak across TODAs** unless you are in an LGU-wide role.

---

## 3. Major components

| Component | Technology (target) | Purpose |
|-----------|---------------------|---------|
| **Passenger mobile app** | Flutter (Android-first) | OTP login, book rides, live tracking, receipts, SOS |
| **Driver mobile app** | Flutter (Android-first) | OTP login, online/offline, offers, trip execution, payment recording |
| **Admin web dashboard** | Laravel (Blade / web UI) | CRUD and oversight for operators |
| **Backend API** | Laravel (`/api/v1/...`) | Transactions, auth, booking state, trips, payments |
| **Realtime layer (when enabled)** | Firebase (mirror) | Low-latency UI updates; **SQL remains source of truth** |

In some pilot configurations, realtime Firebase mirroring may be **disabled** and apps may rely on **REST polling** for live updates. Behavior should still reconcile to the server after reconnect (see [Section 14](#14-connectivity-offline-behavior-and-retries)).

---

## 4. Core concepts

### 4.1 Bookings and trips

- A **booking** is the passenger’s request and its full lifecycle until completion or cancellation.  
- A **trip** is the execution record for moving passengers once the booking progresses into active travel. The PRD assumes **one trip per booking** for MVP.

### 4.2 Ride types

- **Shared ride** — Additional passengers may join (system-booked or walk-in). Fare for the **original app passenger** is fixed per LGU rules and **does not change** when others are added. Driver must not exceed legal vehicle capacity. ETA is **estimated only** (non-guaranteed).  
- **Special ride (pakyaw)** — Exclusive ride for the booking party. Fare is negotiated within **admin-configured min/max** bounds; a **locked agreed fare** is required before the trip can start. No extra passengers are added after acceptance.

### 4.3 Standby points

**Standby points** are LGU/TODA-approved waiting areas. Drivers positioned inside an approved standby zone receive favorable consideration in dispatch ranking (without overriding proximity entirely).

### 4.4 TODA and barangay context

Drivers belong to a **TODA**. Addresses may be associated with **barangays** for zoning and analytics. Dispatch may apply a **soft** local preference (not hard exclusion) depending on configuration.

---

## 5. Passenger guide

### 5.1 First-time setup

1. Install the **Passenger** app from the distribution channel your LGU or pilot provides (side-loaded APK in pilots).  
2. Grant **location** permission when prompted so pickup and routing estimates work. If denied, use **manual pin placement** on the map where supported.  
3. Sign in with your **mobile number**.  
4. Request the **OTP** sent by SMS, enter the code, and complete verification.  
   - New users typically receive a **minimal profile** automatically.

### 5.2 Booking a ride

Typical flow:

1. Open **Book ride** (or equivalent home action).  
2. Confirm **pickup** (GPS or adjusted pin) and **destination**.  
3. Choose **Shared** or **Special**.  
4. Review **distance/time estimates** and **fare**:  
   - **Shared**: fixed fare per active LGU fare rule.  
   - **Special**: review **suggested** fare; enter a **proposed fare** within allowed bounds when the product flow asks for it.  
5. Submit the booking. The system enters **searching for a driver**.

**Tips**

- Add **pickup notes** (landmark, gate, color of building) when pins are ambiguous.  
- If the app reports **no fare rule**, operators must configure fares in admin; you cannot complete a priced booking without a valid rule.

### 5.3 While the system searches for a driver

You should see:

- Booking **reference**  
- **Ride type** and endpoints  
- Fare estimate (shared) or negotiation status (special)  
- Status such as **searching** / **matching**

You may **cancel** according to cancellation rules ([Section 5.6](#56-cancellations)).

### 5.4 After a driver is assigned

You should see **driver and vehicle details** (name, plate, TODA context as applicable), **ETA**, and—once active—**live tracking** when enabled.

**Cancellation after assignment** is allowed only within policy (typically a **short grace window** after assignment). Watch any displayed **grace countdown**.

### 5.5 Pickup and trip start

States transition roughly as:

**Driver on the way → Driver arrived → Trip in progress**

When the driver marks **arrived**, meet at the agreed pickup promptly. If you cannot locate each other, use **contact options** provided by the app when available.

### 5.6 Cancellations

**Passenger**

- Generally **free cancellation before a driver is assigned**.  
- After assignment, cancellation may be allowed only during a **grace period** (no monetary cancellation fee in MVP per PRD).  

**Outcomes if no driver accepts**

- Booking may end as **cancelled: no driver available** after retries. Use **book again** or adjust pickup/time.

### 5.7 Paying for the ride

MVP assumes **cash payment**. The driver (or process you are instructed to follow) records payment in the app. You should see **payment status** and can **view a digital receipt** for completed trips.

### 5.8 Trip history and receipts

Open **My trips** / **History** to view past bookings. Completed items should link to **receipt details** (receipt number, amounts, timestamps). Receipts are **operational records**, not official tax invoices.

### 5.9 Ratings and feedback

Where enabled, you may **rate the driver** after a trip. Passenger ratings **do not affect dispatch ranking in MVP** per PRD.

### 5.10 SOS

If you feel unsafe:

1. Use the **SOS** control (floating action or equivalent).  
2. The system raises an alert with **your location** and booking/trip context where applicable.  
3. **Stay safe**—SOS supplements, not replaces, emergency services.

Administrators monitor SOS lists and update alert status as incidents are handled.

### 5.11 Disputes and issues

If something goes wrong (fare disagreement, no-show disagreement, conduct concern):

- Use **in-app dispute or report** paths when present, or contact support channels your LGU publishes.  
- **LGU admins** review disputes; resolutions are logged.

---

## 6. Driver guide

### 6.1 Account and eligibility

- Drivers authenticate with **mobile OTP** like passengers, but **operational access requires** a **pre-created, approved** driver account.  
- You must be linked to a **TODA**, have **approved verification**, and an **active tricycle** registration to go online.

### 6.2 Going online

1. Open the **Driver** app and sign in.  
2. Ensure **GPS** is on and permissions granted.  
3. Toggle **Online** when ready to receive offers.  
4. The app should publish **periodic location** while online (fresh location is required to be eligible for dispatch).

If your location is **stale** or inaccurate, fix GPS before accepting jobs—stale drivers are excluded from matching.

### 6.3 Receiving and responding to offers

When a booking targets you among ranked candidates:

- You see an **offer card** with pickup, destination, ride type, estimated fare, and a **countdown** (offer TTL).  
- Choose **Accept** or **Decline** before expiry.

**If you tap Accept**

- The server assigns the booking to **one** driver. Others lose the race with a clear outcome (**offer closed** / lost race).  
- If you were late, you may see that another driver won—this is normal under concurrency.

### 6.4 Navigating to pickup

After acceptance:

- Move toward the pickup pin along navigable roads.  
- Keep location updates flowing—passengers see progress when tracking is enabled.

### 6.5 Arrival

Tap **Arrived** when you are truly at pickup. The server validates **proximity** and **GPS quality**. If rejected:

- Move closer to the pin or wait for a better GPS fix.  
- Follow on-screen guidance (“accuracy insufficient”, etc.).

### 6.6 Special ride fare agreement

For **special** bookings:

- Ensure **proposed** and **agreed** fares are accepted within bounds **before starting** the trip.  
- The trip **cannot start** until fare rules for special rides are satisfied.

### 6.7 Starting the trip

Tap **Start trip** only after **arrival** validates and preconditions are met. **Shared** rides begin with **passenger count** initialized for the original passenger.

### 6.8 During the trip (shared rides)

- **Add passenger** actions record **walk-ins** or extra riders picked up along the route.  
- Each add must respect **capacity**.  
- Adding passengers **does not change** the original app passenger’s fare.

**Detours** on shared rides should stay within policy (PRD: tolerate only limited extra time vs initial ETA—operators enforce this in practice).

### 6.9 Ending the trip and payment

1. Tap **End trip** at drop-off.  
2. Collect **cash** if that is the agreed method.  
3. **Record payment** in the app as instructed by workflow.  
4. Confirm totals with the passenger; they can view **receipt** information.

If connectivity fails mid-action, see [Section 14](#14-connectivity-offline-behavior-and-retries). Use **idempotent retries** for critical actions where the app supports them.

### 6.10 Passenger no-show

If the passenger does not appear after you arrived and the **waiting threshold** elapses, you may mark **passenger no-show** per policy. This closes the assignment and logs the event.

### 6.11 Driver cancellation

You may cancel after assignment in restricted circumstances; cancellations are **logged**. Repeated cancellations trigger **compliance warnings or suspension** thresholds ([Section 13](#13-compliance-drivers)).

### 6.12 History and earnings

Use **Trip history** and **Earnings** (or equivalent) to review past work. Figures are subject to admin corrections when disputes are resolved.

---

## 7. Administrator guide

Sign in to the **Admin** web dashboard using **username/email and password** (organization policy may add MFA later).

### 7.1 Shared pages (authenticated admins)

- **Profile** — Account settings for the signed-in admin user.

### 7.2 Dashboard and analytics

- **Dashboard** — KPI summary and charts (filters may include date ranges, TODA, ride type).  
- **Dashboard export** — Download CSV for dashboard metrics where enabled.  
- **Analytics** — Deeper analytics view.  
- **Search** — Global search helpers for operational objects.

### 7.3 Operational directories

- **Drivers** — Create/read/update driver records, verification workflow, compliance flags as implemented.  
- **Tricycles** — Register units, capacities, plate numbers, linkage to TODA and drivers.  
- **Bookings** — Browse and open booking detail by **reference**.  
- **Bookings export** — CSV export with filters.

### 7.4 LGU-only configuration and oversight

The following are typically restricted to **LGU-class** administrators (`lgu.only` middleware in routing):

- **TODAs** — Manage associations.  
- **Fare rules** — Configure **shared** and **special** fare matrices, bounds, and effective dating.  
- **Standby points** — Create, edit, delete approved waiting zones (map coordinates, radius, weights, status).  
- **Disputes** — List, update status, bulk actions, export.  
- **SOS alerts** — Monitor passenger SOS events, update status (including bulk), export.  
- **Audit logs** — Review immutable records of sensitive actions; export.

### 7.5 Development / pilot aids

When running a **debug/pilot** configuration, an admin path such as **Dev → Recent OTPs** may list OTP challenges **for testing without SMS**. **Disable or hide this in production**—it is not a normal operator tool.

### 7.6 Overrides and audits

Any manual correction—status changes, fare adjustments, forced completion—must include a **non-empty reason** and writes an **audit log** entry with actor and state snapshots. Follow least-privilege: TODA admins only act within their scope.

---

## 8. Booking lifecycle (what each status means)

These names follow the PRD state model:

| Status | Plain-language meaning |
|--------|-------------------------|
| **CREATED** | Saved internally; dispatch may still be wiring up. |
| **SEARCHING_DRIVER** | Dispatch is actively offering the job to ranked drivers. |
| **DRIVER_ASSIGNED** | A driver won acceptance; grace cancellation may apply. |
| **DRIVER_ON_THE_WAY** | Driver is headed to pickup; passenger tracking becomes meaningful. |
| **DRIVER_ARRIVED** | Driver confirmed arrival within acceptable GPS thresholds. |
| **TRIP_IN_PROGRESS** | Passenger onboard (or ride underway per policy). |
| **COMPLETED** | Trip ended; fare/receipt flow concluded operationally. |
| **CANCELLED_BY_PASSENGER** | Passenger cancelled under allowed rules. |
| **CANCELLED_BY_DRIVER** | Driver cancelled after assignment. |
| **CANCELLED_NO_DRIVER** | No acceptance after retries/expansion. |
| **NO_SHOW_DRIVER** | Driver failed pickup obligations per evidence/rules. |
| **NO_SHOW_PASSENGER** | Passenger failed to appear after arrival/timer rules. |

---

## 9. How dispatch matching works

Dispatch is automatic—passengers do not pick a driver manually.

**Eligibility (typical checks)**

- Driver account **approved** and user **active**  
- Driver **online** with **fresh** GPS  
- **Active** tricycle registration  
- Not already busy with another conflicting assignment  
- Within the current **search radius**

**Ranking (conceptual)**

The engine scores candidates using weighted factors such as:

- **Distance** to pickup (dominant weight)  
- **Standby point** presence  
- **Fairness** (avoid sending every job to the same few drivers)  
- **Soft local** barangay/TODA preference

**Offer rounds**

- A small batch of top drivers (for example **3–5**) receives an offer with a **short TTL** (for example **15 seconds**).  
- If nobody accepts, the system **expands the search radius** and retries until a maximum number of attempts.  
- Only **one** driver can win; simultaneous accepts are resolved safely on the server (**first valid commit wins**).

---

## 10. Fares: shared vs special

### 10.1 Shared

- Fare is **fixed** by LGU **fare rules** (zone flat rate and/or base + per-km).  
- **Walk-ins** do not change the app passenger’s quoted fare.

### 10.2 Special

- System shows a **suggested** fare from distance and configured multipliers.  
- Passenger proposes within **[min, max]** bounds.  
- Driver accepts/rejects.  
- **Agreed fare locks** before **start trip**.

### 10.3 End of trip

- Shared: final fare follows the rule snapshot from booking creation (distance-based adjustments only where policy allows).  
- Special: final fare follows the **locked** agreed value unless a formal admin adjustment resolves a dispute.

---

## 11. Payments, receipts, and disputes

- **Method**: **Cash** for MVP.  
- **Recording**: Driver or process records cash received; system stores **payment status** linked to the booking.  
- **Receipt**: Generated payload with receipt number; **not** a tax invoice.  
- **Disputes**: Passengers/drivers escalate fare or operational disagreements; admins resolve with notes; audits retain history.

---

## 12. Safety: SOS alerts

**Passengers** trigger SOS with location. **Admins** triage open alerts, update statuses, and export logs for incidents. SOS does not replace **911** or local emergency numbers—use those when life safety is at risk.

---

## 13. Compliance (drivers)

The system tracks behavioral signals such as cancellations and no-shows. Example PRD thresholds:

- **3** driver cancellations in a day → **warning**  
- **5** in a day → **temporary suspension** event  
- High **cancellation rate** over a window → **flagged for review**

These protect passengers and fair operators.

---

## 14. Connectivity, offline behavior, and retries

Mobile networks drop. The PRD requires:

- **Server-authoritative** state for booking/trip transitions  
- **Reconciliation** after reconnect (`GET` booking/trip state, active assignment endpoints)  
- **Idempotency** on critical writes (`Idempotency-Key` header on selected API calls) so retries do not double-charge transitions  

**Practical guidance**

- If an action **hangs**, wait briefly, then retry **once** with the same idempotency key if your app stores it.  
- If unsure whether **end trip** succeeded, reopen the assignment and verify whether the server shows **completed**.

---

## 15. Security and account access

### 15.1 OTP basics (mobile roles)

- **6-digit** codes, short expiry, limited attempts, resend cooldowns, hourly request caps.  
- Treat OTPs like passwords—do not share them.

### 15.2 Tokens

- Mobile sessions use **access** and **refresh** tokens with bounded lifetimes.  
- **Suspended** users cannot operate regardless of possession of a token.

### 15.3 Admin

- Password-based login for dashboard users; protect workstations, enforce lock screens, and rotate passwords per organizational policy.

---

## 16. Pilot or lab deployment notes

Pilot setups (see `DEPLOY.md`) commonly:

- Run Laravel **queue workers** for background jobs.  
- Use **database** queues/cache/session for simplicity.  
- May set **`FIREBASE_PROJECTION_ENABLED=false`** and rely on **polling** instead of Firebase mirrors.  
- May expose **Recent OTPs** only while **`APP_DEBUG=true`** for SMS-less testing.

Production deployments should enable real SMS, hardened secrets, disable debug OTP pages, and configure Firebase only with proper credentials and security rules.

---

## 17. Privacy and data handling (summary)

- Follow applicable privacy law (for example **Philippines Data Privacy Act** principles).  
- Only authorized roles access personal data.  
- Driver documents reside in **secured storage**; audit sensitive views.  
- Purpose limitation: use data for transport operations, safety, compliance, and mandated reporting—not unrelated marketing.

---

## Appendix: reference API routes (v1)

Base URL pattern: `{APP_URL}/api/v1`

**Health**

| Method | Path | Notes |
|--------|------|------|
| GET | `/ping` | Service check |

**Authentication**

| Method | Path | Notes |
|--------|------|------|
| POST | `/auth/otp/request` | Request OTP |
| POST | `/auth/otp/verify` | Exchange OTP for tokens |
| POST | `/passenger/register` | Passenger registration flow |
| POST | `/passenger/verify-phone` | Phone verification |
| POST | `/passenger/login` | Passenger login |

**Passenger (authenticated)**

| Method | Path | Notes |
|--------|------|------|
| GET/POST | `/passenger/me/profile` | View/update profile |
| POST | `/bookings` | Create booking |
| POST | `/bookings/{booking}/cancel` | Cancel booking |
| GET | `/bookings`, `/bookings/{booking}` | List/detail |
| GET | `/bookings/{booking}/trip-tracking` | Tracking payload |
| POST | `/bookings/{booking}/passenger-ack` | Passenger acknowledgment step |
| GET | `/bookings/{booking}/receipt` | Receipt |
| POST | `/passenger/sos` | Raise SOS |

**Driver (authenticated)**

| Method | Path | Notes |
|--------|------|------|
| POST | `/drivers/me/availability` | Online/offline + location |
| GET | `/drivers/me/profile` | Driver profile |
| GET | `/drivers/me/bookings`, `/drivers/me/bookings/{booking}` | Assignment lists/detail |
| GET | `/drivers/me/dispatch-offers` | Incoming offers |
| POST | `/drivers/bookings/{booking}/accept` | Accept |
| POST | `/drivers/bookings/{booking}/decline` | Decline |
| POST | `/drivers/bookings/{booking}/cancel` | Cancel assignment |
| POST | `/drivers/trips/{trip}/arrive` | Mark arrived |
| POST | `/drivers/trips/{trip}/location` | Telemetry ping |
| POST | `/drivers/trips/{trip}/start` | Start trip |
| POST | `/drivers/trips/{trip}/add-passengers` | Shared ride additions |
| POST | `/drivers/trips/{trip}/end` | End trip |
| POST | `/payments/{booking}/record` | Record cash payment |

---

### Document maintenance

- **Product rules**: `PRD_MD_V2.md`  
- **Implementation status vs PRD**: `PRD_PROGRESS_AUDIT.md`  
- **Deployment**: `DEPLOY.md`

When operational policy changes (fare bounds, grace periods, dispatch radii), update **admin configuration** first, then amend training materials (this manual) as needed.
