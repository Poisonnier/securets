# AGENTS.md — AI Agent Operating Guide for SecureTS

This file is the contract for any LLM or coding agent working in this repository. Read it fully before taking any action. When instructions conflict, the user's explicit prompt wins, then this file, then your defaults.

---

## 1. Project Context

- **Product:** SecureThreatShield (SecureTS). Push login approvals and TOTP MFA for third-party Target Apps.
- **Backend/Web (`/`):** Laravel 13, PHP 8.3+, Livewire 4, Blaze, Tailwind CSS, Sanctum, Reverb, Pest 5, Larastan, Pint.
- **Mobile (`/mobile`):** React Native + Expo SDK 58, React 19, `expo-secure-store`, `expo-notifications`, `pusher-js`, `axios`.
- **Data:** Supabase (PostgreSQL) for shared environments; SQLite locally.
- **Specs:** `PRD.md` is the source of truth for features and design. Laravel-specific skills live in `.agents/skills/`. Load the matching skill before you work in its domain.

---

## 2. Global Token Constraints (Every Task)

- **No filler.** No greetings, apologies, affirmations, or closing offers.
- **Minimal output.** Output only the solution. Don't explain code or summarize actions unless asked.
- **Diffs, not files.** Never reprint an entire existing file. Mark skipped regions with `// ... existing code ...`. Output full files only when creating new ones.
- **Targeted discovery.** Never list or read whole directories recursively. Use exact paths or targeted searches. Never read `vendor/`, `node_modules/`, `storage/`, lock files, or build output.
- **No boilerplate.** Assume expert knowledge of Laravel and React Native. Don't scaffold unrequested files, wrappers, or abstractions.
- **Native state only (mobile).** Use `useState`, `useReducer`, Context, and custom hooks. Never add Redux, Zustand, MobX, or similar libraries without approval.

---

## 3. Batch-Based Prompting Methodology

The user prefixes each prompt with a tier tag. Each tier sets the scope, the read budget, and the output shape. Work is delivered in **batches**: one coherent, self-contained unit of change per response.

### Batch Principles
1. **One batch = one concern.** Group every edit for a single goal into one response. Never mix unrelated changes.
2. **Read once, write once.** Gather all the context a batch needs up front, then emit all of its edits together. Don't interleave reading and writing.
3. **Stop at the boundary.** When a batch is done, stop. Don't start the next batch without the user's signal.
4. **Declare the batch.** For multi-batch work, prefix the output with a single line, `Batch N/M: <scope>`, and nothing else.

### [LOW] — Documentation & Small Fixes
- Read **only** the file or lines named in the prompt. Don't trace imports.
- Output only the exact replacement lines, typo fix, or docblock.
- Don't run formatters, builds, or tests unless instructed.

### [MEDIUM] — Coding, Debugging & Features
- Read at most **3 files**: the target class/component and its direct model or dependency. Ask before reading more.
- **Debugging:** Give at most a one-sentence root cause, then the fix. Don't explain standard framework errors.
- **Features:** Deliver core logic first. Ask before spending tokens on extended styling, edge cases, or error handling.

### [LARGE] — Multi-File Generation
- For generating several new files in one pass (docs, scaffolds, test suites).
- Read only what is needed to ground the output in the real codebase.
- Output each file as a separate code block labeled with its filename. Add no commentary unless requested.

### [HIGH] — Architecture, Refactoring & Security Sweeps
Plan first, code second:
1. **Discovery:** Read only what's needed to trace the data flow (e.g., an API controller → Livewire component → mobile API module). No code generation.
2. **Plan:** Output a bulleted plan that lists the files to change, the security rules being applied, and any breaking changes.
3. **Halt:** End the response and wait for approval.
4. **Incremental delivery:** After approval, deliver one batch per step. Wait for "Next" or an error report between steps.
5. **Scope discipline:** Don't clean up adjacent code. Fix only what the plan names.

---

## 4. Terminal & Build Guardrails (Anti-Loop)

- **One-strike rule:** If a command fails, you get one autonomous fix-and-retry. If it fails again, halt and ask.
- **Log truncation:** Read only the first 20 lines and the final error of any long stack trace.
- **Cascade halt:** If one fix produces 3 or more new errors in different files, stop. Give a one-sentence summary and wait.
- **Banned without explicit permission:**
  - `composer update`, `npm update`, or any global dependency install
  - `php artisan migrate:fresh`, `migrate:reset`, `db:wipe`, or any destructive migration against Supabase
  - Deleting `vendor/`, `node_modules/`, or `.expo/`
  - `npx expo prebuild --clean`, `./gradlew clean`, `pod install`
  - Committing, pushing, or rewriting git history
- **Allowed verification:** `composer lint`, `composer types:check`, `php artisan test --filter=<Name>`. Run targeted tests, not the full suite, unless asked.

---

## 5. Code Standards

### Backend
- PSR-12, enforced by Pint. Use strict parameter and return types. Code must pass Larastan.
- Validate in Form Requests and authorize with Policies. Keep controllers thin.
- Use `config()` only. Never call `env()` outside `config/`.
- Every change needs a Pest test. Security paths need both success and failure cases.

### Mobile
- Store credentials only in `expo-secure-store`. Never use AsyncStorage for secrets.
- Make API calls only through `src/api/client.js`.
- Never log tokens, TOTP seeds, or credential-bearing responses.
- No unused code, speculative abstractions, or dependencies a few lines could replace.

### Security (Both Platforms)
- Escape all output (Blade `{{ }}`; no `dangerouslySetInnerHTML`).
- Keep secrets in environment variables only. Never hard-code or commit them.
- Tokens must be single-use, expiring, and scoped to their device or request.

---

## 6. Strict UI Design Rules

SecureTS uses a **WorkOS-inspired minimalist dark mode**. These rules are non-negotiable.

### Palette (the only permitted colors)

| Token        | Value                    | Use |
| ------------ | ------------------------ | --- |
| `background` | `#0E1326`                | Page/app background |
| `surface`    | `#1C2236`                | Cards, modals, inputs, rows |
| `primary`    | `#4063D7`                | Primary actions, active states, focus |
| `accent`     | `#00BBFF`                | Sparing highlights (TOTP timer, live status) |
| `text`       | `#FFFFFF`                | Primary text |
| `text-muted` | `#FFFFFF` @ 60% opacity  | Secondary text |
| `border`     | `#FFFFFF` @ 8% opacity   | 1px dividers/borders |

### Hard Rules
1. **No new colors,** gradients, or light-mode variants. Destructive actions use outlined neutral buttons.
2. **No hex literals in markup.** On web, use Tailwind theme tokens (`bg-surface`, `text-primary`). On mobile, use `ThemeContext` tokens.
3. **No shadows or glows.** Separate layers with the 1px `border` token.
4. **Radius:** 12px for cards and 8px for buttons and inputs.
5. **Spacing:** 4px grid. Card padding is 24px on web and 16px on mobile.
6. **Typography:** One sans-serif family; weights 400, 500, and 600 only. TOTP codes are monospaced with tabular figures.
7. **Accent restraint:** `#00BBFF` appears at most once per screen region and never as a large fill.
8. **One primary action per screen.** Favor negative space over density.
9. **Motion:** 150–200ms ease-out. No decorative animation.
10. **Contrast:** WCAG 2.1 AA minimum; touch targets at least 44×44 pt.

If a request conflicts with these rules, follow the rules and flag the conflict in one sentence.

---

## 7. Pre-Response Checklist

- [ ] Tier tag identified and its read budget respected
- [ ] The output is a single coherent batch
- [ ] Diffs only, with no reprinted existing files
- [ ] No new dependencies, colors, or unrequested abstractions
- [ ] No banned commands run
- [ ] No filler text
