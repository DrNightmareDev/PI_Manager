# EVE PI Manager

> **⚠️ ARCHIVIERT — Dieses Projekt wurde durch [PlanetFlow](https://planetflow.app) abgelöst.**
> EVE PI Manager wird nicht mehr aktiv weiterentwickelt. Alle Funktionen wurden portiert und in PlanetFlow erweitert, das die aktuell gewartete Version ist. Bitte PlanetFlow für alle neuen Installationen verwenden.

[Deutsch](README.de.md) | [English](README.en.md) | [ZH-CN](README.zh-Hans.md)

`EVE PI Manager` ist ein selbst gehostetes Planetary-Industry-Dashboard für EVE Online. Das Projekt bündelt PI-Überwachung, Planung und operative Werkzeuge in einer FastAPI-Anwendung mit PostgreSQL, Celery und RabbitMQ.

Wenn dir das Projekt hilft, freue ich mich über Ingame-ISK an `DrNightmare`.

## Funktionsumfang

- Dashboard mit Koloniestatus, ISK/Tag, Live-Ablaufcountdown, Paginierung und CSV-Export
- Charakterverwaltung mit Main/Alt-Zuordnung, Token-Status, Scope-Refresh und PI-Skill-Übersicht
- Corporation-Ansichten und gemeinsame PI-Daten über verknüpfte Charaktere hinweg
- Inventory, Hauling, Intel, Killboard, Skyhooks und PI Templates
- PI Chain Planner, Colony Assignment Planner, System Analyzer, System Mix, Compare und Fittings
- Hintergrundaktualisierung über Celery + RabbitMQ, mit APScheduler-Fallback für einfache Setups
- ETag-basiertes ESI-Caching, Marktcache, Webhook-Alerts und Manager-Zugriffssteuerung
- UI-Übersetzungen für Deutsch, Englisch und vereinfachtes Chinesisch

## Wichtige Seiten

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

## Benötigte ESI-Scopes

```text
esi-planets.manage_planets.v1
esi-planets.read_customs_offices.v1
esi-location.read_location.v1
esi-characters.read_corporation_roles.v1
esi-skills.read_skills.v1
esi-fittings.read_fittings.v1
```

Für Struktursuche und erweiterte Corp-Funktionen ist zusätzlich `esi-search.search_structures.v1` sinnvoll.

## Schnellstart

```bash
cp .env.example .env
docker compose up -d
```

Mindestens diese Werte in `.env` setzen:

```env
DB_PASSWORD=change_me
EVE_CLIENT_ID=your_client_id
EVE_CLIENT_SECRET=your_client_secret
EVE_CALLBACK_URL=http://your-domain-or-ip/auth/callback
SECRET_KEY=replace_me_with_a_long_random_secret_key
```

Wichtige Hinweise:

- `COOKIE_SECURE=false` bei reinem HTTP
- `COOKIE_SECURE=true`, wenn die App hinter HTTPS läuft
- `CELERY_BROKER_URL` nur leer lassen, wenn der APScheduler-Fallback ausdrücklich gewünscht ist

## Docker Compose

Standarddienste:

- `db`
- `rabbitmq`
- `app`
- `celery_worker`
- `celery_beat`
- `celery_ws`

Optionale Profile:

- `nginx`: eingebauter Reverse Proxy
- `pgbouncer`: Connection Pooling für größere Installationen
- `monitoring`: Flower Task Monitor

Nützliche Befehle:

```bash
docker compose up -d
docker compose logs -f app
docker compose logs -f celery_worker
docker compose logs -f celery_beat
docker compose logs -f celery_ws
```

Compose-Installation aktualisieren:

```bash
bash scripts/update_compose.sh
```

## Linux nativ

Neuinstallation:

```bash
sudo bash scripts/setup_linux.sh
```

Upgrade einer älteren Installation:

```bash
sudo bash scripts/upgrade_to_latest.sh
```

Reguläres Update:

```bash
sudo bash scripts/update_linux.sh
```

Service-Namen:

- `eve-pi-manager`
- `eve-pi-manager-worker`
- `eve-pi-manager-beat`
- `eve-pi-manager-ws`

## Windows nativ

Windows wird für lokale oder kleinere Installationen unterstützt.

Setup:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\setup_windows.ps1
```

Update:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\update_windows.ps1
```

RabbitMQ und Celery werden unter Windows nicht automatisch eingerichtet. Kleinere Setups laufen daher oft im APScheduler-Fallback.

## Administrator-Werkzeuge

- `scripts/add_administrator.py`
- `scripts/remove_administrator.py`
- `/manager` für Zugriffspolitik, Account-Verwaltung, Übersetzungen und Cache-/Fehlerkorrektur

## Health-Check

```text
GET /health
```

Beispiel:

```json
{
  "status": "ok",
  "database": "ok",
  "rabbitmq": "ok"
}
```

Wenn Celery nicht konfiguriert ist, wird RabbitMQ als `not_configured` gemeldet.

## Tech Stack

- FastAPI + Jinja2
- PostgreSQL + SQLAlchemy + Alembic
- Celery + RabbitMQ
- Gunicorn / Uvicorn
- Bootstrap 5

## Lizenz

MIT. Siehe [LICENSE](LICENSE).
