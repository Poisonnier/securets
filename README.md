# SecureThreatShield (SecureTS)

SecureTS is a multi-factor authentication platform that protects user accounts in third-party **Target Apps**. A Target App sends an access request to SecureTS. SecureTS pushes a real-time approval prompt to the user's registered mobile device, the user approves or denies it, and SecureTS returns the decision to the Target App. For offline or fallback verification, SecureTS also issues time-based one-time passwords (TOTP).

The repository contains two applications:

| Path      | Application                                         |
| --------- | --------------------------------------------------- |
| `/`       | Web dashboard and API (Laravel + Livewire)          |
| `/mobile` | Authenticator app (React Native + Expo)             |

---

## Core Features

- **Push Login Approvals:** Real-time approve/deny prompts sent to the registered device over Laravel Reverb WebSockets and Expo push notifications.
- **TOTP Authenticator:** RFC 6238 time-based one-time passwords (via `pragmarx/google2fa`) as a second factor and offline fallback.
- **Integration Hub:** Each user can register Target Apps and get an Integration ID and API Secret for each one.
- **Device Registration:** Device binding with setup tokens and per-device Instance Tokens.
- **Login History:** An audit trail of every access request and its result.
- **Subscriptions:** Stripe-backed plan tiers (Pro, Ultra, Ultra Plus).

---

## Tech Stack

### Backend & Web
- **Laravel 13** (PHP 8.3+), with Sanctum for API token authentication
- **Livewire 4** + **Blaze** for server-driven UI
- **Tailwind CSS** for styling, built with Vite
- **Laravel Reverb** for real-time WebSocket broadcasting
- **Pest 5**, **Larastan**, and **Laravel Pint** for testing, static analysis, and formatting

### Mobile
- **React Native** + **Expo** (SDK 58)
- `expo-secure-store` for credential storage
- `expo-notifications` for push approvals
- `pusher-js` for the Reverb WebSocket client
- `axios` as the HTTP client

### Data
- **Supabase (PostgreSQL)** for shared team and staging environments
- **SQLite** for zero-config local development

---

## Prerequisites

- PHP 8.3+ and Composer
- Node.js 20+ and npm
- The Expo Go app on a physical device, or an Android/iOS simulator
- The phone and the development machine on the same network (or connected through Tailscale)

---

## Web Setup

### 1. Clone and install

```bash
git clone https://github.com/kentjohnllanita8978-oss/secure-hub.git securets
cd securets
composer install
npm install
```

### 2. Configure the environment

```bash
cp .env.example .env
php artisan key:generate
```

**Option A — SQLite (recommended for solo local development):**

```env
DB_CONNECTION=sqlite
```

```bash
touch database/database.sqlite
```

**Option B — Supabase (shared team database):** Fill in the `DB_*` credentials in `.env`. Get them from your team lead. Never commit them.

### 3. Migrate

```bash
php artisan migrate
```

### 4. Configure Reverb

Use port **8085** to avoid conflicts with a local Apache on 8080:

```env
REVERB_PORT=8085
REVERB_SERVER_PORT=8085
```

### 5. Configure Stripe (optional; needed only for subscriptions)

```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=
STRIPE_PRICE_PRO=price_...
STRIPE_PRICE_ULTRA=price_...
STRIPE_PRICE_ULTRA_PLUS=price_...
```

Leave `STRIPE_WEBHOOK_SECRET` empty during local development. Set it only when you forward events with `stripe listen`.

### 6. Run

```bash
composer run dev
```

This starts the Laravel server (`:8000`), Reverb (`:8085`), the queue worker, and Vite together. Open `http://localhost:8000`.

---

## Mobile Setup

### 1. Install

```bash
cd mobile
npm install
cp .env.example .env
```

### 2. Configure

```env
EXPO_PUBLIC_API_URL=http://192.168.x.x:8000/api
EXPO_PUBLIC_REVERB_HOST=192.168.x.x
EXPO_PUBLIC_REVERB_PORT=8085
EXPO_PUBLIC_REVERB_SCHEME=http
```

You usually don't need to edit these by hand, because `npm start` detects your LAN IP and writes it in automatically.

### 3. Run

```bash
npm start              # Same Wi-Fi network as the backend
npm run start:tailscale  # Different network (uses the Tailscale IP)
npm run android        # Android emulator
npm run ios            # iOS simulator
```

Scan the QR code with Expo Go.

---

## Useful Commands

```bash
composer run dev        # All backend services
composer test           # Pint check + PHPStan + Pest
composer lint           # Auto-format with Pint
composer types:check    # PHPStan / Larastan
php artisan test        # Pest only
npm run build           # Production web assets
```

---

## Documentation

- [Product Requirements](PRD.md)
- [Contributing Guide](CONTRIBUTING.md)
- [Security Policy](SECURITY.md)
- [Code of Conduct](CODE_OF_CONDUCT.md)
- [AI Agent Guide](AGENTS.md)

## License

Released under the [MIT License](LICENSE). © 2026 Nathan G. Abrenica.
