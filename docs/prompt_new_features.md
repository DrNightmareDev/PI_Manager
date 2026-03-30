# Implementation Prompt — EVE PI Manager: 5 New Features

You are implementing 5 new features for an existing self-hosted FastAPI/Python web application called **EVE PI Manager**. Read every section carefully before writing any code. Do not invent abstractions — extend what already exists.

---

## Codebase Context

**Tech stack:**
- FastAPI + Jinja2 templates (server-side rendering)
- SQLAlchemy 2.0 ORM, PostgreSQL, Alembic migrations
- Bootstrap 5.3 (dark theme via `data-bs-theme="dark"`, custom class `eve-body`)
- Bootstrap Icons 1.11 (`bi bi-*`)
- Celery 5 + RabbitMQ for background tasks
- All routers in `app/routers/`, templates in `app/templates/`, models in `app/models.py`
- i18n via `app/i18n.py`: `translate(key, lang, default=...)` / `translate_type_name(type_id, fallback, lang)`
- All templates extend `app/templates/base.html` with `{% extends "base.html" %}`
- Session auth via `app/dependencies.py`: `require_account` (any logged-in user), `require_admin`
- ESI calls in `app/esi.py` — use `ensure_valid_token(char, db)` before any auth'd ESI call
- SDE static data via `app/sde.py` — `get_system_local(system_id)` returns `{name, region_name, ...}`
- Colony data lives in `DashboardCache.colonies_json` (JSON list, loaded per account via `_load_colony_cache`)
- Each colony dict has: `planet_id`, `planet_name`, `planet_type`, `solar_system_id`, `solar_system_name`, `region_name`, `character_name`, `expiry_hours`, `expiry_iso`, `isk_day`, `productions`, `storage`, `is_active`, `is_stalled`, `highest_tier`

**PI production data (all in `app/pi_data.py`):**
- `PLANET_RESOURCES: dict[str, list[str]]` — planet type → list of P0 resources it yields
- `P0_TO_P1`, `P1_TO_P2`, `P2_TO_P3`, `P3_TO_P4` — full production chain dicts
- `ALL_P1`, `ALL_P2`, `ALL_P3`, `ALL_P4` — sorted lists of all products per tier
- `PLANET_TYPE_COLORS: dict[str, str]` — planet type → hex color

**Navigation structure (base.html):**
- Top-level nav items: Dashboard, Skyhooks, Planer (dropdown), Analyse (dropdown), Markt, Corporation, Manager
- "Planer" dropdown currently contains: PI Chain Planner (`/planner`), Colony Assignment Planner (`/colony-plan`), PI Templates (`/templates`)
- New pages go under whichever dropdown fits logically; new top-level pages get their own `<li class="nav-item">`

**Look & feel rules (apply to ALL new pages and components):**
1. Dark theme always — use Bootstrap dark variables, never hardcode light colors
2. Cards: `<div class="card eve-card">` with `<div class="card-header eve-card-header">` for titled sections
3. Tables: `<table class="table table-dark table-hover table-sm">` — always `table-sm` for density
4. Badges: `<span class="badge bg-secondary">` (neutral), `bg-success` (good), `bg-warning text-dark` (warn), `bg-danger` (crit)
5. Buttons: `btn-sm` everywhere except primary CTAs; use `btn-outline-*` for secondary actions
6. Icons: Bootstrap Icons only — `<i class="bi bi-NAME me-1"></i>` before text labels
7. Empty states: `<div class="text-muted text-center py-4"><i class="bi bi-inbox fs-2 d-block mb-2"></i>Kein Eintrag</div>`
8. Section headers within a page: `<h6 class="text-uppercase text-muted small mb-2">` with a relevant `bi` icon
9. Alerts/warnings inline: `<div class="alert alert-warning d-flex align-items-center gap-2 py-2">` — never full-page popups for non-critical info
10. JS: inline `<script>` at bottom of template block; no external JS files; use `fetch()` for AJAX calls; keep JS minimal and focused
11. Loading states: `<span class="spinner-border spinner-border-sm me-2"></span>` inside buttons while waiting
12. All user-visible strings must pass through `{{ t('key') }}` — add new keys to `app/locales/en.json`, `de.json`, `zh-Hans.json`
13. ESI portrait images: `<img src="{{ char.portrait_url }}" class="rounded-circle" width="32" height="32">`
14. Dotlan links: `https://evemaps.dotlan.net/system/SYSTEM_NAME_UNDERSCORED`

---

## Feature 1 — Vacation Mode per Character

### Goal
Let users mark individual characters as "on vacation" (inactive). Vacationed characters are excluded from token-error banners, webhook alerts, and colony expiry warnings. They remain visible in the character list with a clear visual indicator.

### Data model
Add a column to the existing `Character` model:
```python
# in app/models.py, class Character
vacation_mode = Column(Boolean, nullable=False, default=False, server_default="false")
```
Create Alembic migration: `alembic/versions/018_character_vacation_mode.py`

### Backend
In `app/routers/characters.py` (or wherever character management endpoints live), add:
```
POST /dashboard/characters/{character_id}/vacation   → toggle vacation_mode, return JSON {ok, vacation_mode}
```
Require `require_account`; verify the character belongs to `account.id` before toggling.

In `app/routers/dashboard.py`:
- `_build_dashboard_payload`: skip characters where `char.vacation_mode == True` for colony fetching (colonies from vacationed chars still show in the list but marked, not counted in totals)
- `token_error_chars` filter: exclude characters with `vacation_mode == True`

In `app/tasks.py` (`send_webhook_alerts_task`):
- Skip alert evaluation for characters where `vacation_mode == True`

### Frontend — Characters page (`characters.html`)
For each character card, add a vacation toggle button:
```html
<button class="btn btn-sm btn-outline-secondary vacation-toggle"
        data-char-id="{{ char.id }}"
        data-vacation="{{ char.vacation_mode | lower }}">
  <i class="bi bi-moon-stars{% if char.vacation_mode %}-fill{% endif %} me-1"></i>
  {% if char.vacation_mode %}Urlaub aktiv{% else %}Urlaub{% endif %}
</button>
```
On click: `fetch('/dashboard/characters/ID/vacation', {method:'POST'})` → update button state without page reload.

Characters in vacation mode show a muted overlay badge `<span class="badge bg-secondary">Urlaub</span>` on their portrait.

### Frontend — Dashboard banner
In the token-error banner logic, add a note: "X Charaktere im Urlaubs-Modus ausgeblendet" when `vacation_count > 0`.

---

## Feature 2 — zKillboard / Killboard Integration

### Goal
Show recent kill activity in the solar systems where a character has active PI colonies. Dangerous systems get a warning badge. This helps PI pilots avoid warping into a hot system to restart colonies.

### Data source
**zKillboard Redisq API** (no auth required, no API key):
- `GET https://redisq.zkillboard.com/listen.php?queueID=evepimgr_{account_id}&ttw=1` — long-polls for kills; not suitable for background use
- **Use instead:** `GET https://zkillboard.com/api/kills/solarSystemID/{system_id}/pastSeconds/3600/` — returns kills in last 1h for a given system

**Note:** This is a public, unauthenticated endpoint. Respect the `User-Agent` header requirement:
```python
headers = {"User-Agent": "EVE-PI-Manager/1.0 github.com/DrNightmareDev/PI_Manager"}
```

### Data model
```python
class KillActivityCache(Base):
    __tablename__ = "kill_activity_cache"
    system_id   = Column(BigInteger, primary_key=True)
    kill_count  = Column(Integer, nullable=False, default=0)
    fetched_at  = Column(DateTime(timezone=True), server_default=func.now(), onupdate=func.now())
    # Cache TTL: 15 minutes — do NOT re-fetch if fetched_at > 15 min ago
```
Migration: `019_kill_activity_cache.py`

### Backend
New router: `app/routers/killboard.py`, prefix `/killboard`

```python
@router.get("/system/{system_id}")
def get_system_kills(system_id: int, account=Depends(require_account), db=Depends(get_db)):
    """Returns kill count for a system in the last hour. Cached 15 min."""
    # 1. Check cache first
    # 2. If stale/missing: fetch zkillboard, store in DB, return result
    # 3. Return JSON: {system_id, kill_count, fetched_at_iso}
```

Danger levels (return as `danger_level` string):
- `"safe"`: 0 kills
- `"caution"`: 1–4 kills → badge `bg-warning text-dark`
- `"danger"`: 5+ kills → badge `bg-danger`

### Frontend — Dashboard integration
In `dashboard.html`, for each colony row that has a `solar_system_id`:
- Add a small kill-activity badge after the system name, lazy-loaded via JS
- On page load, collect all unique `solar_system_id` values from visible colonies
- Batch-fetch: `GET /killboard/system/SYSID` per unique system (max 10 concurrent via `Promise.all`)
- Badge HTML (injected by JS into `.kill-badge[data-sys-id="X"]` placeholder spans):
  ```html
  <!-- safe: nothing shown -->
  <!-- caution: --><span class="badge bg-warning text-dark ms-1" title="4 Kills letzte Stunde"><i class="bi bi-exclamation-triangle-fill me-1"></i>4</span>
  <!-- danger:  --><span class="badge bg-danger ms-1" title="12 Kills letzte Stunde"><i class="bi bi-skull-fill me-1"></i>12</span>
  ```

### Frontend — Hauling / Route Planner integration
See Feature 4 — kill data is reused there.

---

## Feature 3 — Optimal Product Mix Calculator (Extension to Colony Assignment Planner)

### Goal
Given the user's actual PI colonies (planet types already committed), calculate which P2/P3/P4 product(s) maximize ISK/day using current Jita market prices. Extends the existing Colony Assignment Planner at `/colony-plan`.

### Where to add it
**Tab inside `/colony-plan`** — the existing page already shows a planner. Add a second tab "Optimaler Mix" next to the existing content using Bootstrap tabs:
```html
<ul class="nav nav-tabs mb-3" id="colonyPlanTabs">
  <li class="nav-item"><button class="nav-link active" data-bs-target="#tab-planner">Planer</button></li>
  <li class="nav-item"><button class="nav-link" data-bs-target="#tab-optimizer">Optimaler Mix</button></li>
</ul>
```

### Algorithm (server-side, Python)
Input: list of planet types the user currently operates (from their `DashboardCache.colonies_json`).

```
For each P4 product:
  1. Resolve full input chain back to P0 resources
  2. Check which P0 resources are available from user's planet types
  3. Calculate "coverage ratio" = P0 inputs available / P0 inputs required
  4. Estimate theoretical ISK/day = market_sell_price * estimated_units_per_day * coverage_ratio
  5. Flag as "fully self-sufficient" if coverage_ratio == 1.0

For each P3 product (same logic)
For each P2 product (same logic)
```

**`estimated_units_per_day` reference values** (fixed constants, based on standard EVE PI cycle math):
- P2: ~690 units/day (standard 1-planet P2 factory setup)
- P3: ~155 units/day (standard 2-planet chain)
- P4: ~12 units/day (standard full chain)

These are intentionally conservative approximations — the goal is relative ranking, not exact profit.

### Backend
New endpoint in `app/routers/colony_plan.py`:
```
GET /colony-plan/optimizer?account_id=me
```
Returns JSON list sorted by `est_isk_day` descending:
```json
[
  {
    "product": "Wetware Mainframe",
    "tier": "P4",
    "est_isk_day": 184000000,
    "coverage": 1.0,
    "self_sufficient": true,
    "missing_p0": [],
    "market_price": 15500000,
    "planet_types_needed": ["Barren", "Gas", "Temperate", "Lava", "Oceanic"]
  },
  ...
]
```

Load market prices from `MarketCache` (already in DB). Use `app/market.PI_TYPE_IDS` to resolve product → type_id.

### Frontend — Optimizer tab
Display results as a sortable table (client-side sort via `data-sort` column headers):

| Produkt | Tier | Est. ISK/Tag | Marktpreis | Abdeckung | Fehlende P0 |
|---|---|---|---|---|---|
| Wetware Mainframe | P4 | 184M | 15.5M | 100% ✓ | — |
| Sterile Conduits | P4 | 91M | 12.1M | 80% | Noble Gas |

- "Abdeckung 100%" rows: green left border (`border-start border-success border-3`)
- Missing P0 resources shown as small `bg-danger` badges
- Tier filter buttons above table: `Alle / P2 / P3 / P4`
- Toggle: "Nur selbstversorgend" checkbox — hides rows with coverage < 1.0
- Each product name is a link to the PI Chain Planner (`/planner?product=NAME`) so user can drill down
- Price displayed as `{{ isk_val | human_isk }}` (use existing ISK formatting helper from dashboard)

---

## Feature 4 — Route Planner + Hauling List (combined page)

### Goal
A single page `/hauling` that answers: **"What do I need to do today, and in what order?"**

Shows:
1. **Hauling List** — all colonies with items in storage (> 0 volume), sorted by expiry urgency + value
2. **Route Optimizer** — given the character's current location (from ESI), suggest the optimal visit order to service the most urgent planets before they expire
3. **Kill Activity overlay** — each system in the list gets a kill badge from Feature 2

### ESI — Character Location
Scope `esi-location.read_location.v1` is already authorized (it's in the required scopes list).

```python
# New function in app/esi.py
def get_character_location(character_id: int, token: str) -> dict:
    """Returns {solar_system_id, station_id?, structure_id?}"""
    resp = _esi_get(f"/characters/{character_id}/location/", token=token)
    return resp  # {solar_system_id: int, ...}
```

### Backend
New router: `app/routers/hauling.py`, prefix `/hauling`

```python
@router.get("", response_class=HTMLResponse)
def hauling_page(request, account=Depends(require_account), db=Depends(get_db)):
    # 1. Load colony cache for account
    # 2. Filter colonies where storage has items (storage list not empty / qty > 0)
    # 3. For each: compute urgency score = (value_in_storage / max(expiry_hours, 0.1))
    # 4. Sort by urgency descending
    # 5. For main char: fetch current location via ESI (cache in session for 5 min)
    # 6. Pass to template

@router.get("/api/location")
def get_location(account=Depends(require_account), db=Depends(get_db)):
    """Returns current solar_system_id + name for main character. Cached 5 min in-process."""
```

**Urgency score formula:**
```
urgency = (storage_isk_value / max(expiry_hours, 0.1)) * (1 if is_active else 0.5)
```
Higher = needs attention sooner.

**Route optimization:**
Simple nearest-neighbor heuristic using EVE jump counts:
- Use `GET https://esi.evetech.net/latest/route/{origin}/{destination}/` (public ESI, no auth)
- Cache route lookups in-process dict (system_pair → jump_count, TTL 1h)
- Max 20 systems in route to avoid ESI abuse
- If location unavailable: skip route, show hauling list only

### Frontend (`hauling.html`)
Extends `base.html`. Add to navbar under a new top-level item (not in a dropdown — it's a primary daily workflow):
```html
<li class="nav-item">
  <a class="nav-link {% if '/hauling' in path %}active{% endif %}" href="/hauling">
    <i class="bi bi-truck me-1"></i>Hauling
  </a>
</li>
```

**Page layout (two-column on lg+, stacked on mobile):**

```
┌─────────────────────────────────────────────────────┐
│  [Character selector dropdown]  [📍 Aktueller Standort: Jita — Aktualisieren]  │
├──────────────────────┬──────────────────────────────┤
│  HAULING-LISTE       │  OPTIMALE ROUTE              │
│                      │                              │
│  [table of colonies  │  [ordered list 1→2→3…        │
│   with storage]      │   with jump counts]          │
│                      │                              │
│  Total: X Planeten   │  Gesamt: Y Sprünge           │
│  Wert: Z ISK         │  [Dotlan Multipath Link]     │
└──────────────────────┴──────────────────────────────┘
```

**Hauling list table columns:**
`Charakter | System [kill badge] | Planet | Typ | Ablauf | Lager-Inhalt | Wert | Dringlichkeit`

- "Lager-Inhalt": abbreviated product list with quantities, max 3 shown then `+N mehr`
- "Dringlichkeit": colored bar `<div class="progress" style="height:6px">` — 100% = expires in <2h, 0% = >48h
- Row click: opens Dotlan link for that system in new tab
- Expired rows: `table-danger` row class
- Stalled rows (no extractor running): `table-warning` row class

**Route panel:**
```html
<ol class="list-group list-group-numbered">
  <li class="list-group-item d-flex justify-content-between align-items-center eve-list-item">
    <div>
      <span class="fw-bold">Jita</span> <span class="text-muted small">Ausgangspunkt</span>
      <!-- kill badge injected by JS -->
    </div>
    <span class="badge bg-secondary">Start</span>
  </li>
  <li class="list-group-item ...">
    <div>
      <span class="fw-bold">Nonni</span>
      <span class="text-muted small ms-2">3 Sprünge</span>
    </div>
    <span class="badge bg-warning text-dark">2 Planeten</span>
  </li>
</ol>
```

Dotlan multipath link format:
`https://evemaps.dotlan.net/route/SYSTEM1:SYSTEM2:SYSTEM3`
(replace spaces with underscores)

Kill badges in route are fetched from Feature 2 endpoint (same JS batch fetch logic).

**"Aktualisieren" button** re-fetches character location via `/hauling/api/location` and rebuilds the route client-side by POSTing the system list to a new endpoint:
```
POST /hauling/api/route
Body: {origin_system_id: int, system_ids: [int, ...]}
Returns: {ordered: [{system_id, system_name, jumps_from_prev}], total_jumps: int}
```

---

## Feature 5 — Hauling List: Storage Value Calculation

This is tightly coupled to Feature 4. The "Wert" column in the hauling list requires knowing the ISK value of items currently in storage.

### Storage data available
Each colony dict already contains a `"storage"` field (list of `{product_name, quantity}` dicts, computed by `_compute_storage` in dashboard.py).

### Value calculation
Reuse the existing market price lookup already used in `_compute_isk_day`:
```python
from app.models import MarketCache
from app.market import PI_TYPE_IDS

def _compute_storage_value(storage: list[dict], price_mode: str, db: Session) -> float:
    total = 0.0
    for item in storage:
        type_id = PI_TYPE_IDS.get(item["product_name"])
        if not type_id:
            continue
        row = db.get(MarketCache, type_id)
        if not row:
            continue
        price = float(getattr(row, "best_sell" if price_mode == "sell" else "best_buy") or 0)
        total += price * item.get("quantity", 0)
    return total
```

Call this in `hauling_page` for each colony before passing to template. Use `account.price_mode` for consistency.

**No new DB table needed** — all data already exists.

---

## Alembic Migrations Required

Create these migration files in `alembic/versions/`:

| File | Change |
|---|---|
| `018_character_vacation_mode.py` | `ALTER TABLE characters ADD COLUMN vacation_mode BOOLEAN NOT NULL DEFAULT FALSE` |
| `019_kill_activity_cache.py` | Create `kill_activity_cache` table |

Each migration must have `upgrade()` and `downgrade()`. Follow the existing migration style in the repo.

---

## i18n Keys to Add

Add to all three locale files (`app/locales/en.json`, `de.json`, `zh-Hans.json`):

```json
"nav.hauling": "Hauling",
"hauling.title": "Hauling & Route",
"hauling.list": "Hauling List",
"hauling.route": "Optimal Route",
"hauling.urgency": "Urgency",
"hauling.storage_value": "Storage Value",
"hauling.jumps": "Jumps",
"hauling.location_unknown": "Location unknown",
"hauling.refresh_location": "Refresh Location",
"hauling.no_items": "No items in storage",
"vacation.active": "Vacation Active",
"vacation.toggle": "Vacation Mode",
"vacation.hint": "Hidden from alerts and token warnings",
"optimizer.title": "Optimal Product Mix",
"optimizer.self_sufficient": "Fully Self-Sufficient Only",
"optimizer.missing_p0": "Missing P0",
"optimizer.coverage": "Coverage",
"optimizer.est_isk_day": "Est. ISK/day",
"killboard.kills_last_hour": "kills last hour",
"killboard.safe": "Safe",
"killboard.caution": "Caution",
"killboard.danger": "Danger"
```

---

## Integration Checklist

- [ ] Register new routers in `app/main.py`: `app.include_router(hauling.router)`, `app.include_router(killboard.router)`
- [ ] Add `KillActivityCache` and vacation migration to `app/models.py`
- [ ] Add `_compute_storage_value` helper to `app/routers/dashboard.py` or a shared utility module
- [ ] Add `get_character_location` to `app/esi.py`
- [ ] Add "Hauling" nav item to `base.html` (between Skyhooks and Planer)
- [ ] Add optimizer tab to `colony_plan.html`
- [ ] Add kill badge placeholder `<span class="kill-badge" data-sys-id="{{ colony.solar_system_id }}"></span>` to `dashboard.html` colony rows
- [ ] Add vacation button to character cards in `characters.html`
- [ ] Add `vacation_mode` to dashboard `token_error_chars` filter and `_start_bg_refresh` char loop
- [ ] Add `vacation_mode` skip to `send_webhook_alerts_task` in `tasks.py`
- [ ] Run `alembic upgrade head` after deploying

---

## What NOT to do

- Do not add new Python dependencies — use only what is already installed (`requests`, `sqlalchemy`, `fastapi`, `celery`)
- Do not use `async def` for DB-heavy endpoints — use sync with `Session = Depends(get_db)` like the rest of the codebase
- Do not add error details to HTTP responses — log with `logger.exception(...)` and return generic messages
- Do not invent a separate caching layer — use `MarketCache` and `KillActivityCache` DB tables
- Do not add WebSocket support — use JS polling with `setInterval` / `fetch` as done elsewhere
- Do not change the existing colony data pipeline or `_build_dashboard_payload` signature
