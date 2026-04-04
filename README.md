# EVE PI Manager

> **⚠️ ARCHIVED — This project has been superseded by [PlanetFlow](https://github.com/DrNightmareDev/PlanetFlow.APP).**
> EVE PI Manager is no longer actively developed. All features have been ported and extended in PlanetFlow, which is the current actively maintained version. Please use PlanetFlow for all new installations.

[Deutsch](README.de.md) | [English](README.en.md) | [ZH-CN](README.zh-Hans.md)

Self-hosted Planetary Industry dashboard for EVE Online with ESI login, cached background refresh, planning tools, and corporation-aware PI workflows.

If the project is useful to you, in-game ISK donations to `DrNightmare` are welcome.

## Highlights

- Multi-account PI dashboard with live expiry countdown, CSV export, filters, and pagination
- Characters, corporation view, inventory, hauling, intel, killboard, skyhooks, and PI templates
- PI Chain Planner, Colony Assignment Planner, System Analyzer, System Mix, Compare, and Fittings
- Celery + RabbitMQ background refresh with APScheduler fallback when no broker is configured
- ETag-based ESI caching, DB-backed market cache, webhook alerts, and manager tools
- German, English, and Simplified Chinese UI translations

## Quick Start

```bash
cp .env.example .env
docker compose up -d
```

Required `.env` values:

- `DB_PASSWORD`
- `EVE_CLIENT_ID`
- `EVE_CLIENT_SECRET`
- `EVE_CALLBACK_URL`
- `SECRET_KEY`

The Docker stack starts:

- `db`
- `rabbitmq`
- `app`
- `celery_worker`
- `celery_beat`
- `celery_ws`

Optional profiles:

- `nginx`
- `pgbouncer`
- `monitoring`

## Key Scripts

| Script | Purpose |
|---|---|
| `scripts/setup_linux.sh` | Fresh native Linux install |
| `scripts/upgrade_to_latest.sh` | Upgrade older Linux installs |
| `scripts/update_linux.sh` | Update native Linux installs |
| `scripts/update_compose.sh` | Update Docker Compose installs |
| `scripts/setup_windows.ps1` | Native Windows setup |
| `scripts/update_windows.ps1` | Native Windows update |

## Health Check

```text
GET /health
```

Returns database and RabbitMQ state. If no broker is configured, RabbitMQ is reported as `not_configured`.

## Full Documentation

- [Deutsch](README.de.md)
- [English](README.en.md)
- [ZH-CN](README.zh-Hans.md)

## License

MIT. See [LICENSE](LICENSE).
