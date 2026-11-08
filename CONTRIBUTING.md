# Contributing to SecureTS

Thank you for contributing. This guide keeps the codebase consistent, secure, and lean.

By participating, you agree to follow our [Code of Conduct](CODE_OF_CONDUCT.md). Report security issues privately, as described in [SECURITY.md](SECURITY.md). Never open a public issue for a vulnerability.

---

## Branching Strategy

| Branch       | Purpose | Merges Into |
| ------------ | ------- | ----------- |
| `main`       | Production-ready, protected. Tagged releases only. | — |
| `develop`    | Integration branch for the next release. | `main` |
| `feature/*`  | New features (`feature/totp-countdown`). | `develop` |
| `fix/*`      | Non-urgent bug fixes (`fix/reverb-reconnect`). | `develop` |
| `hotfix/*`   | Urgent production fixes. | `main` **and** `develop` |
| `docs/*`     | Documentation only. | `develop` |
| `chore/*`    | Tooling, dependencies, CI. | `develop` |

### Workflow

1. Branch from `develop`, or from `main` for a hotfix.
2. Keep each branch focused on a single concern.
3. Rebase onto the latest `develop` before you open a PR.
4. Open a PR into `develop`. Fill in the template and link the related issue.
5. You need one approving review and passing CI to merge.
6. Use **squash merge**. Delete the branch after merging.

### Commit Messages

Follow [Conventional Commits](https://www.conventionalcommits.org/):

```
feat(mobile): add TOTP countdown ring
fix(api): reject expired setup tokens
docs: update local setup instructions
chore(deps): bump laravel/framework to 13.18
```

---

## Backend Standards (Laravel)

### Code Style
- Follow **PSR-12**, enforced by **Laravel Pint** (`pint.json`).
- Run `composer lint` before you commit. CI runs `composer lint:check`.
- Declare explicit parameter and return types on every method.
- All code must pass **Larastan** (`composer types:check`) at the configured level.

### Architecture
- Validate input in **Form Requests**, not in controllers.
- Enforce authorization with **Policies** and Gates.
- Keep controllers thin. Put domain logic in Actions or Services.
- Prefer Eloquent and the query builder over raw SQL. If raw SQL is unavoidable, always bind parameters.
- Eager-load relationships to prevent N+1 queries.
- Read secrets with `config()` only. Never call `env()` outside config files.

### Testing
- Write tests with **Pest**. Every feature and bug fix needs at least one test.
- Cover security-critical paths (tokens, approvals, TOTP) with both success and failure cases.
- Run the full suite before you open a PR:

```bash
composer test   # Pint check + PHPStan + Pest
```

### Livewire & Blade
- Keep components small and single-purpose.
- Use only design-system tokens for styling. See [PRD §10](PRD.md#10-design-system).
- Use `{{ }}` escaping by default. Use `{!! !!}` only for content you have explicitly sanitized.

---

## Mobile Standards (React Native + Expo)

### Lightweight State Management

We favor **small, explicit, token-efficient state** over heavy abstractions.

- **Use built-ins first:** `useState`, `useReducer`, `useContext`, and custom hooks.
- **Keep state local.** Lift it only when two or more components actually need it.
- **Use Context sparingly** for truly global concerns (auth session, theme). Split contexts by concern to avoid unnecessary re-renders.
- **No global state libraries** (Redux, MobX, Zustand, Recoil, Jotai, etc.) without prior maintainer approval in an issue.
- **Derive, don't duplicate.** Compute values from existing state instead of storing them twice.

### No Vibe-Coded Bloat

PRs are rejected if they contain:
- Dependencies added for something a few lines of code can do
- Speculative abstractions, wrappers, or "future-proof" layers with no current use
- Unused files, components, styles, or exports
- Generic boilerplate (empty providers, placeholder screens, copy-pasted helpers)
- Commented-out code or leftover debug `console.log` calls
- Hard-coded colors instead of theme tokens

Every line should earn its place. If you're unsure, leave it out.

### Security
- Store tokens and secrets **only** in `expo-secure-store`. Never put credentials in AsyncStorage.
- Never log tokens, TOTP seeds, or API responses that contain credentials.
- Read configuration from `EXPO_PUBLIC_*` variables. Never commit `.env` files.

### Structure

```
mobile/
├── App.jsx
├── components/      # Reusable UI components
└── src/
    ├── api/         # Axios client and endpoint modules
    ├── lib/         # Notifications, Reverb, utilities
    └── theme/       # Design tokens and ThemeContext
```

---

## Pull Request Checklist

- [ ] Branch name and commits follow the conventions above
- [ ] `composer test` passes
- [ ] New or changed behavior has tests
- [ ] UI follows the design system (no new colors, no shadows)
- [ ] No new dependencies, or each one is justified in the PR description
- [ ] No secrets, `.env` files, or debug code committed
- [ ] Documentation is updated where relevant
