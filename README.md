# SecureTS

SecureTS is a Laravel + Livewire + React Native application for per-user Integration Hub with MFA, push approval notifications, device registration, and login history.

---

## Team Local Setup

Each developer runs the backend on their **own machine**. The mobile app connects to whoever's machine is running the server on the local Wi-Fi network.

### Prerequisites
- PHP 8.3+
- Composer
- Node.js 20+
- Expo Go app on your phone (same Wi-Fi as your PC)

---

### 1. Clone & Install Dependencies

```bash
git clone https://github.com/kentjohnllanita8978-oss/secure-hub.git
cd secure-hub
composer install
npm install
```

### 2. Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Then open `.env` and fill in the Supabase database credentials (ask your team lead for these).

### 3. Run Migrations

```bash
php artisan migrate
```

### 4. Start the Backend

Run all services with one command:

```bash
composer run dev
```

This starts the Laravel server, Reverb WebSocket, queue worker, and Vite asset server together.

The web app will be at `http://localhost:8000`.

---

### 5. Configure the Mobile App

```bash
cd mobile
cp .env.example .env
```

Open `mobile/.env` and set `EXPO_PUBLIC_API_URL` to **your machine's local IP address**:

**Find your LAN IP:**
- **Windows:** Run `ipconfig` in a terminal → look for **IPv4 Address** (e.g. `192.168.1.42`)
- **macOS/Linux:** Run `ifconfig` → look for `inet` under your Wi-Fi adapter

Then set:
```env
EXPO_PUBLIC_API_URL=http://192.168.x.x:8000/api
EXPO_PUBLIC_REVERB_HOST=192.168.x.x
EXPO_PUBLIC_REVERB_PORT=8080
EXPO_PUBLIC_REVERB_SCHEME=http
```

> ⚠️ Your phone must be on the **same Wi-Fi network** as your PC.

### 6. Start the Mobile App

```bash
cd mobile
npx expo start --clear
```

Scan the QR code with Expo Go on your phone.

---

## Useful Commands

```bash
# Run all services (server + reverb + queue + vite)
composer run dev

# Run tests
php artisan test

# Format code
php artisan pint

# Build frontend assets
npm run build
```
