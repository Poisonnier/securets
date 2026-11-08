# Contributing to SecureTS

Thank you for contributing to SecureThreatShield (SecureTS). This guide keeps the codebase consistent, secure, and lean.

By participating, you agree to follow our [Code of Conduct](CODE_OF_CONDUCT.md). Report security vulnerabilities privately, as described in [SECURITY.md](SECURITY.md). Never open a public issue for a vulnerability.

---

## 1. Branching Strategy

| Branch | Purpose | Merges Into |
| ------ | ------- | ----------- |
| `main` | Stable production releases only. Protected and tagged. | — |
| `develop` **(Recommended)** | The primary target for all active development, staging, and community pull requests. | `main` |
| `feature/*` | Isolated UI updates or API endpoints (e.g., `feature/totp-countdown`, `feature/access-request-endpoint`). | `develop` |

> **Note:** While core development is currently managed on a separate personal branch for organizational reasons, all external contributions must target the `develop` branch.

### Workflow

1. Fork the repository, or create a branch from the latest `develop`.
2. Name the branch `feature/<short-description>`, using lowercase kebab-case.
3. Limit each branch to a single concern, such as one UI component or one endpoint.
4. Rebase onto the latest `develop` before opening a pull request.
5. Open the pull request against `develop`. Pull requests that target `main` will be closed.

### Commit Messages

Follow [Conventional Commits](https://www.conventionalcommits.org/):

```
feat(mobile): add TOTP countdown ring
fix(api): reject expired setup tokens
docs: clarify local setup steps
```

---

## 2. Coding Standards

### Backend (Laravel)

- **PSR-12** is required and enforced by Laravel Pint. Run `composer lint` before committing.
- **Thin controllers.** Controllers receive the request, delegate, and return a response. Put domain logic in Actions or Services.
- **Form Requests for validation.** Never validate inline in controllers.
- **Policies for authorization.** Every protected resource needs a Policy or Gate check.
- **Strict types.** Declare explicit parameter and return types on every method. Code must pass Larastan (`composer types:check`).
- **Configuration.** Read values with `config()` only. Never call `env()` outside `config/`.
- **Queries.** Use Eloquent or the query builder with bound parameters. Eager-load relationships to prevent N+1 queries.
- **Tests.** Every change needs a Pest test. Security-critical paths (tokens, approvals, TOTP) need both success and failure cases.

### Frontend (React Native & Livewire)

**Component hierarchy**
- Keep each component single-purpose. Screens compose components, and components compose primitives.
- Keep the hierarchy shallow and readable. A reviewer should understand a screen's structure at a glance.
- Livewire components own one concern each. Extract shared markup into Blade components.
- Mobile: put reusable UI in `mobile/components/`, API modules in `mobile/src/api/`, and shared logic in `mobile/src/lib/`.

**Lightweight state management**
- Use built-ins first: `useState`, `useReducer`, `useContext`, and custom hooks.
- Keep state local. Lift it only when two or more components actually need it.
- Reserve Context for truly global concerns, such as the auth session and theme.
- Derive values from existing state instead of storing duplicates.
- Don't add a global state library (Redux, MobX, Zustand, Recoil, Jotai, etc.) unless a maintainer has approved it in an issue first.

**No bloated or "vibe-coded" UI**

Pull requests will be rejected if they include:
- Generic wrapper components that only pass props through
- Speculative abstractions or "future-proof" layers with no current use
- Dependencies added for something a few lines of code can do
- Unused components, styles, exports, or placeholder screens
- Commented-out code or leftover `console.log` / `dd()` calls

Every line should earn its place. If you're unsure, leave it out.

### Styling (WorkOS-Inspired Minimal Dark Mode)

Use **only** the approved palette:

| Token | Hex | Usage |
| ----- | --- | ----- |
| `background` | `#0E1326` | Page and app background |
| `surface` | `#1C2236` | Cards, modals, inputs, list rows |
| `primary` | `#4063D7` | Primary actions, active states, focus rings |
| `accent` | `#00BBFF` | Sparing highlights only (TOTP timer, live status) |

Text is `#FFFFFF`, with 60% opacity for secondary text. Borders are `#FFFFFF` at 8% opacity.

**Rules**
- No new colors, gradients, or light-mode variants.
- No hex literals in markup. On web, use Tailwind theme tokens (e.g., `bg-surface`). On mobile, use `ThemeContext` tokens.
- No shadows or glows. Separate layers with a 1px border.
- Use a 12px radius for cards and 8px for buttons and inputs.
- Spacing follows a 4px grid. Card padding is 24px on web and 16px on mobile.
- Use one sans-serif family with weights 400, 500, and 600 only. TOTP codes are monospaced.
- Use the `accent` color at most once per screen region, and never as a large fill.
- Each screen has one primary action.

---

## 3. Pull Request Process

1. **Run tests locally.** All checks must pass before you open a pull request:

   ```bash
   composer test   # Pint check + PHPStan + Pest
   ```

2. **Write a concise description.** State what changed, why it changed, and link the related issue. Skip long narratives.
3. **Attach UI screenshots (mandatory for frontend changes).** Every change to Livewire views, Blade templates, or React Native screens needs before-and-after screenshots. Mobile changes should show both iOS and Android when the platforms render differently.
4. **Pass review.** Merging requires one approving review and passing CI.
5. **Squash merge.** Pull requests are squash-merged into `develop`, and the branch is deleted afterward.

### Pull Request Checklist

- [ ] Targets the `develop` branch
- [ ] Branch name and commits follow the conventions above
- [ ] `composer test` passes locally
- [ ] New or changed behavior has Pest tests
- [ ] Description is concise and links the related issue
- [ ] Screenshots are attached for any frontend change
- [ ] Uses only approved palette tokens, with no shadows or hex literals
- [ ] No new dependencies, or each one is justified in the description
- [ ] No secrets, `.env` files, or debug code committed
