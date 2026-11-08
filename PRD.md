# SecureThreatShield (SecureTS) — Product Requirements Document

| Field     | Value                       |
| --------- | --------------------------- |
| Product   | SecureThreatShield (SecureTS) |
| Platforms | Web (Laravel + Livewire), Mobile (React Native + Expo) |
| Owner     | Nathan G. Abrenica          |
| Status    | Active Development          |
| Updated   | November 2026               |

---

## 1. Product Overview

SecureTS is an authentication layer that external applications (**Target Apps**) integrate with to protect user account access. When someone tries to sign in to a protected account, the Target App sends an access request to SecureTS. SecureTS sends a real-time prompt to the account owner's registered mobile device, and the owner approves or denies it. When push isn't available, TOTP codes are the fallback second factor.

SecureTS handles **authentication only**. It never touches a Target App's business logic, data, or unrelated features.

## 2. Problem Statement

Stolen or leaked credentials are the most common way accounts get compromised. Passwords alone can't tell the real owner apart from an attacker who holds valid credentials. SecureTS requires a second, device-bound confirmation from the real owner before access is granted.

## 3. Goals

- Block unauthorized access to Target App accounts, even when the password has been compromised.
- Deliver login approval prompts in near real time (target: under 2 seconds).
- Offer TOTP as a dependable offline fallback.
- Give Target App developers a small, well-documented integration surface.
- Ship a calm, minimalist interface that users trust at a glance.

## 4. Non-Goals

- Managing a Target App's business functions, content, or database.
- Replacing a Target App's primary identity provider or user directory.
- Supporting SMS or email OTP (both are excluded for security reasons).

---

## 5. Target Audience

### 5.1 End Users
People who hold accounts in Target Apps and want stronger protection without friction. They expect a one-tap approval experience and clear visibility into sign-in attempts.

### 5.2 Target App Developers
Engineering teams that add SecureTS to their authentication flow. They need simple credentials, predictable API responses, and clear setup documentation.

### 5.3 Security-Conscious Organizations
Small to mid-size teams that want enterprise-grade MFA without the cost and complexity of an enterprise platform.

---

## 6. Core Features

### 6.1 Web Platform (Laravel + Livewire)

| ID     | Feature                 | Description |
| ------ | ----------------------- | ----------- |
| W-01   | Account & MFA Enrollment | Registration, login, and TOTP enrollment with a QR code and recovery codes. |
| W-02   | Integration Hub         | Register Target Apps; issue an Integration ID and API Secret for each; rotate or revoke secrets. |
| W-03   | Setup Token Issuance    | Generate short-lived, single-use setup tokens, delivered as a QR code or deep link. |
| W-04   | Device Management       | List registered devices; revoke lost or replaced devices. |
| W-05   | Login History           | A filterable audit log of access requests with timestamp, Target App, device, IP, and result. |
| W-06   | Access Request API      | A Sanctum-protected endpoint that Target Apps call to start an approval. |
| W-07   | Real-Time Broadcasting  | Reverb broadcasts approval requests and results. |
| W-08   | Subscriptions           | Stripe-backed plan tiers that control integration and device limits. |

### 6.2 Mobile Platform (React Native + Expo)

| ID     | Feature                 | Description |
| ------ | ----------------------- | ----------- |
| M-01   | Secure Sign-In          | Authenticates against the SecureTS API; tokens are stored in `expo-secure-store` only. |
| M-02   | Device Pairing          | Scan or enter a setup token; the device receives a unique Instance Token. |
| M-03   | Login Approvals         | A push/WebSocket prompt showing the Target App, location/IP, and time, with **Approve** and **Deny** actions. |
| M-04   | TOTP Codes              | 6-digit, 30-second rotating codes for each linked account, with a countdown indicator. |
| M-05   | Request Expiry          | Pending approvals expire automatically after 60 seconds and are treated as denied. |
| M-06   | Activity Feed           | Recent approval history for the device. |

---

## 7. Key User Flows

### 7.1 Integration
`Developer → Registers Target App → Receives Integration ID + API Secret → Integration ready`

### 7.2 Device Pairing
`User → Selects Target App → SecureTS issues setup token → User scans token in mobile app → SecureTS validates → Instance Token issued → Target App notified`

### 7.3 Login Approval
`User signs in to Target App → Target App sends Access Request → SecureTS resolves device → Push + Reverb prompt → User approves/denies → Result returned → Target App grants/blocks access`

### 7.4 TOTP Fallback
`Push unavailable → User opens mobile app → Enters current TOTP code in Target App → Target App verifies via SecureTS → Access granted/denied`

---

## 8. Functional Requirements

| ID    | Requirement |
| ----- | ----------- |
| FR-01 | Target Apps must be registered before they can submit access requests. |
| FR-02 | Each integration gets a unique Integration ID and a hashed API Secret. |
| FR-03 | Setup tokens are single-use and expire after 10 minutes. |
| FR-04 | Each paired device receives a unique, revocable Instance Token. |
| FR-05 | Every access request must reference a valid Instance Token. |
| FR-06 | Approval prompts are delivered over both Reverb and Expo push. |
| FR-07 | A user can approve or deny each request exactly once. |
| FR-08 | Unanswered requests expire and resolve as **denied**. |
| FR-09 | The result is returned to the Target App synchronously (polling) or by webhook. |
| FR-10 | TOTP verification follows RFC 6238 with a ±1 step tolerance window. |
| FR-11 | Every request and result is written to the login history. |

## 9. Non-Functional Requirements

| ID     | Requirement |
| ------ | ----------- |
| NFR-01 | **Security:** TLS for all traffic; secrets hashed at rest; no credentials in logs. |
| NFR-02 | **Latency:** Approval prompt delivered in under 2 seconds at p95. |
| NFR-03 | **Reliability:** Clear, deterministic results (`approved`, `denied`, `expired`). |
| NFR-04 | **Isolation:** No access to Target App data outside authentication. |
| NFR-05 | **Accessibility:** WCAG 2.1 AA contrast; touch targets at least 44×44 pt. |
| NFR-06 | **Performance:** Mobile cold start under 2 seconds; minimal dependency footprint. |

---

## 10. Design System

SecureTS follows a **strict, WorkOS-inspired minimalist dark mode**. The interface should feel precise, quiet, and trustworthy. There is no light mode.

### 10.1 Color Tokens

| Token          | Hex       | Usage |
| -------------- | --------- | ----- |
| `background`   | `#0E1326` | App and page background. The only background color. |
| `surface`      | `#1C2236` | Cards, modals, inputs, list rows. |
| `primary`      | `#4063D7` | Primary buttons, active states, links, focus rings. |
| `accent`       | `#00BBFF` | Sparing highlights only: TOTP countdown, live status, key data points. |
| `text`         | `#FFFFFF` | Headings and primary text. |
| `text-muted`   | `#FFFFFF` at 60% opacity | Secondary text, labels, metadata. |
| `border`       | `#FFFFFF` at 8% opacity  | Hairline dividers and card borders. |

### 10.2 Rules

1. **No other hues.** Do not introduce gradients, extra brand colors, or decorative colors. Destructive actions (e.g., **Deny**, **Revoke**) use outlined neutral buttons with clear labels, not red fills.
2. **Accent restraint.** `#00BBFF` appears no more than once per screen region. It never fills large areas.
3. **Flat surfaces.** No drop shadows or glows. Use a 1px `border` token to separate layers.
4. **Radius.** Cards use 12px; buttons and inputs use 8px. Apply the same values everywhere.
5. **Spacing.** Use a 4px base grid. Card padding is 24px on web and 16px on mobile.
6. **Typography.** Use a single sans-serif family (Inter or the system UI font). TOTP codes use a monospaced, tabular-figure font. Use only weights 400, 500, and 600.
7. **Motion.** Keep transitions between 150 and 200ms with ease-out. No bouncing, parallax, or decorative animation.
8. **Iconography.** Use one outline icon set at a 1.5px stroke weight.
9. **Density.** Every screen has exactly one primary action. Leave generous negative space.

### 10.3 Implementation

- **Web:** Define the tokens as Tailwind theme variables and use only tokenized utilities (e.g., `bg-background`, `bg-surface`). Arbitrary hex values in templates are prohibited.
- **Mobile:** Expose the tokens through `src/theme/ThemeContext.jsx`. Inline hex literals in components are prohibited.

---

## 11. Success Metrics

- Push approval delivery success rate of at least 99%
- Median approval response time under 5 seconds
- Zero confirmed account takeovers on protected accounts
- Target App integration completed in under 30 minutes, measured from documentation start to first approval

## 12. Release Priorities

| Priority | Scope |
| -------- | ----- |
| P1 | Integration, device pairing, access requests, push approvals, results |
| P2 | TOTP enrollment and fallback, setup tokens, integration confirmation |
| P3 | Token expiry and revocation, device management, audit log, subscriptions |

## 13. Open Questions

1. Should results be delivered only by webhook, only by polling, or by both?
2. Should the number of devices per user be limited by plan tier?
3. Should approval prompts require biometric confirmation on the device?
