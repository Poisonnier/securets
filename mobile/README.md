# Mobile

Expo (React Native) app.

> Run all commands from this `mobile/` folder.

## Recommended

```powershell
npm run start:tailscale
```

Starts the Expo dev server (Metro) with **cross-network (Tailscale)** access. Use this for testing on a physical device via **Expo Go** — it works even when the phone is on a different network (e.g. mobile data), not just the same Wi-Fi.

It also runs `detect-lan-ip.js --tailscale` first, then starts Expo with a cleared cache (`expo start -c`).

LAN-only alternative: `npm start`

## First-time setup

```powershell
npm install
```

## Other ways to run

```powershell
npm start            # LAN only — phone must be on the same Wi-Fi as the PC
npm run start:tailscale  # cross-network via Tailscale (recommended)
npm run android      # open on a connected Android device/emulator
npm run ios          # open on an iOS simulator/device
npm run web          # run in the browser (react-native-web)
```

## Helper scripts

```powershell
npm run ip             # detect and write the LAN IP into .env
npm run ip:tailscale   # detect and write the Tailscale IP into .env
```

## Requirements

- Node.js + npm
- The [Expo Go](https://expo.dev/go) app on your physical device
- The backend running — in `../web`, run `composer run dev`

## Notes

- `EXPO_PUBLIC_API_URL` and `EXPO_PUBLIC_REVERB_HOST` in `.env` must point to the host PC's LAN IP or Tailscale IP (not `localhost`, which means the phone itself).
- After changing any `EXPO_PUBLIC_*` value, restart Metro with a cleared cache: `npx expo start -c`.
- The PC and the phone must be on the same network, or both connected to the same Tailscale network.
- Expo Go does not support remote push notifications.
