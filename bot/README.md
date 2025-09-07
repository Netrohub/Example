# Discord Rally Bot

## Setup

1. Create and fill `.env` from `.env.example`.
2. Create venv and install deps:

```bash
python3 -m venv .venv
. .venv/bin/activate
pip install -r requirements.txt
```

3. Run:

```bash
python main.py
```

## Notes
- Bot expects a MySQL schema with a `rallies` table containing `send_at` and `prealert_at` in UTC and `status` field (`pending` -> `scheduled`).
- Alternatively, you can point the bot at a Laravel API and adapt polling logic.