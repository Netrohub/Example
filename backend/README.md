# Laravel Backend (Dashboard + API)

This directory contains app-specific code (models, controllers, routes, middleware, and views) to be used within a Laravel 11 application. It expects the shared MySQL schema in `../db/schema.sql` and interoperates with the Python bot.

## Quick steps

1. Create a Laravel 11 app in this directory:

```bash
composer create-project laravel/laravel backend
```

2. Install packages:
```bash
cd backend
composer require laravel/socialite socialiteproviders/discord guzzlehttp/guzzle
```

3. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Copy/merge the files in this folder into your Laravel app structure (they’re already placed assuming project root is `backend/`).

5. Run migrations? Not required; DB schema is already provided at `../db/schema.sql` and loaded via Docker.

6. Serve app:
```bash
php artisan serve
```

## Notes
- OAuth uses Discord via Socialite. Configure `DISCORD_CLIENT_ID`, `DISCORD_CLIENT_SECRET`, and `DISCORD_REDIRECT_URI`.
- API endpoints for the bot are protected with a simple bearer token `API_TOKEN`.
- The dashboard writes `rallies` with computed `send_at` and `prealert_at` matching the bot logic.