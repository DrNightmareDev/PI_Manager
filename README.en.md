# EVE PI Manager

> **⚠️ ARCHIVED — This project has been superseded by [PlanetFlow](https://github.com/DrNightmareDev/PlanetFlow.APP).**
> EVE PI Manager is no longer actively developed. All features have been ported and extended in PlanetFlow, which is the current actively maintained version. Please use PlanetFlow for all new installations.

[Deutsch](README.de.md) | [English](README.en.md) | [ZH-CN](README.zh-Hans.md)

`EVE PI Manager` is a self-hosted Planetary Industry dashboard for EVE Online. It combines PI monitoring, planning, and operational tooling in one FastAPI application with PostgreSQL, Celery, and RabbitMQ.

If the project is useful to you, in-game ISK donations to `DrNightmare` are welcome.

## Features

- Dashboard with colony status, ISK/day, live expiry countdown, pagination, and CSV export
- Character management with main/alt grouping, token health, scope refresh, and PI skill overview
- Corporation and shared-data views for PI operations across linked characters
- Inventory, hauling route planning, intel, killboard, skyhooks, and PI templates
- PI Chain Planner, Colony Assignment Planner, System Analyzer, System Mix, Compare, and Fittings
- Background refresh via Celery + RabbitMQ, with APScheduler fallback for simpler setups
- ETag-based ESI caching, market cache, webhook alerts, and manager access controls
- UI translations for German, English, and Simplified Chinese

## Main Pages

- `Dashboard`
- `Characters`
- `Corporation`
- `Inventory`
- `Hauling`
- `Intel`
- `Killboard`
- `Skyhooks`
- `PI Templates`
- `Jita Market`
- `PI Chain Planner`
- `Colony Assignment Planner`
- `System Analyzer`
- `System Mix`
- `Compare`
- `Fittings`
- `Manager`

## Required ESI Scopes

```text
esi-planets.manage_planets.v1
esi-planets.read_customs_offices.v1
esi-location.read_location.v1
esi-characters.read_corporation_roles.v1
esi-skills.read_skills.v1
esi-fittings.read_fittings.v1
```

Some features, such as structure lookup and richer corporation tooling, also benefit from `esi-search.search_structures.v1`.

## Quick Start

```bash
cp .env.example .env
docker compose up -d
```

Fill in at least:

```env
DB_PASSWORD=change_me
EVE_CLIENT_ID=your_client_id
EVE_CLIENT_SECRET=your_client_secret
EVE_CALLBACK_URL=http://your-domain-or-ip/auth/callback
SECRET_KEY=replace_me_with_a_long_random_secret_key
```

Important notes:

- `COOKIE_SECURE=false` for plain HTTP
- `COOKIE_SECURE=true` when the app is served behind HTTPS
- Leave `CELERY_BROKER_URL` empty only if you intentionally want APScheduler fallback mode

## Docker Compose

Default services:

- `db`
- `rabbitmq`
- `app`
- `celery_worker`
- `celery_beat`
- `celery_ws`

Optional profiles:

- `nginx`: built-in reverse proxy
- `pgbouncer`: connection pooling for larger deployments
- `monitoring`: Flower task monitor

Useful commands:

```bash
docker compose up -d
docker compose logs -f app
docker compose logs -f celery_worker
docker compose logs -f celery_beat
docker compose logs -f celery_ws
```

Update an existing Compose install:

```bash
bash scripts/update_compose.sh
```

## Native Linux

Fresh install:

```bash
sudo bash scripts/setup_linux.sh
```

Upgrade an older installation:

```bash
sudo bash scripts/upgrade_to_latest.sh
```

Regular update:

```bash
sudo bash scripts/update_linux.sh
```

Service names:

- `eve-pi-manager`
- `eve-pi-manager-worker`
- `eve-pi-manager-beat`
- `eve-pi-manager-ws`

## Native Windows

Windows support is available for local or smaller installs.

Setup:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\setup_windows.ps1
```

Update:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\update_windows.ps1
```

RabbitMQ and Celery are not provisioned automatically on Windows, so simpler deployments may run in APScheduler fallback mode.

## Administrator Tools

- `scripts/add_administrator.py`
- `scripts/remove_administrator.py`
- `/manager` for access policies, account management, translation editing, and cache/error recovery

## Health Check

```text
GET /health
```

Example response:

```json
{
  "status": "ok",
  "database": "ok",
  "rabbitmq": "ok"
}
```

If Celery is not configured, RabbitMQ is reported as `not_configured`.

## Tech Stack

- FastAPI + Jinja2
- PostgreSQL + SQLAlchemy + Alembic
- Celery + RabbitMQ
- Gunicorn / Uvicorn
- Bootstrap 5

## License

MIT. See [LICENSE](LICENSE).
