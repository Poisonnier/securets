# Web

Laravel backend API + web UI.

> Run all commands from this `web/` folder (this is where `composer.json` lives).

## Recommended

```powershell
composer run dev
```

Starts **everything at once** via `concurrently`, which is what you want for normal development:

| Service | Description |
| --- | --- |
| `server` | `php artisan serve --host=0.0.0.0 --port=8000` — the API / web app |
| `reverb` | `php artisan reverb:start --host=0.0.0.0 --port=8085` — real-time WebSocket server |
| `queue` | `php artisan queue:listen --tries=1 --timeout=0` — background job worker |
| `vite` | `npm run dev` — frontend assets with HMR |

Use this whenever you develop locally, and when a physical mobile device needs to reach the API (`--host=0.0.0.0` binds to your LAN/Tailscale IP).

## First-time setup

```powershell
composer run setup
```

Installs PHP + JS dependencies, creates `.env`, generates the app key, runs migrations, and builds assets.

## Run services individually

Useful when you only need one piece, or want separate terminals/logs.

```powershell
php artisan serve --host=0.0.0.0 --port=8000   # API / web server
php artisan reverb:start --host=0.0.0.0 --port=8085  # WebSocket (Reverb)
php artisan queue:listen --tries=1 --timeout=0       # queue worker
npm run dev                                          # Vite (assets + HMR)
```

## Other commands

```powershell
composer run test          # config clear + lint + static analysis + test suite
composer run lint          # Pint (auto-fix code style)
composer run lint:check    # Pint check only
composer run types:check   # PHPStan static analysis
npm run build              # production build of frontend assets
```

## Requirements

- PHP 8.3+ and Composer
- Node.js + npm

## Notes

- Configuration is read from `.env`. Copy `.env.example` to `.env` if it is missing, then run `php artisan key:generate`.
- Physical devices can only reach the API if the server binds to `0.0.0.0` (as in `composer run dev`).
