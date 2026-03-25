# SeAT PI Manager Porting Matrix

This document tracks functional parity between the existing FastAPI/Linux application
and the new SeAT-native plugin implementation in `seat-plugin/`.

## Principles

- The existing Python/FastAPI application remains untouched.
- The SeAT plugin is a clean Laravel/SeAT-native rewrite.
- Feature parity is built iteratively, but the target is full functional equivalence.

## Module Status

| Module | FastAPI Source | Current SeAT Status | Notes |
| --- | --- | --- | --- |
| Plugin shell / navigation | `base.html`, `main.py` | In progress | SeAT provider, permissions, menu, assets exist |
| Static planet import | `app/sde.py`, `StaticPlanet` usage | Done | Import command and DB table implemented |
| System Analyzer | `app/routers/system.py`, `system.html` | In progress | Search + system details + static planets implemented |
| PI Chain Planner | `app/routers/planner.py`, `planner.html`, `pi_data.py` | In progress | Static catalog/detail view implemented next |
| Compare | `app/routers/system.py`, `compare.html` | Not started | Depends on analyzer and catalog services |
| System Mix | `app/routers/system.py`, `system_mix.html` | Not started | Depends on analyzer and catalog services |
| Market | `app/routers/market.py`, `market.html` | Not started | Depends on price/trend cache layer |
| Dashboard | `app/routers/dashboard.py`, `dashboard.html` | Not started | Largest data/workflow port |
| Skyhooks | `app/routers/skyhook.py`, `skyhook.html` | Not started | Depends on PI/planet/account sync layer |
| Corporation | `app/routers/dashboard.py`, `corp_view.html` | Not started | Depends on corp aggregation and cache jobs |
| Character Views | `dashboard.py`, `characters.html` | Not started | Depends on dashboard/account model decisions |
| Manager / translations | `app/routers/admin.py`, `admin.html` | Not started | SeAT-permission-aware rewrite later |
| Auth / add-character flows | `app/routers/auth.py` | Deferred | Must map onto SeAT-native auth model, not duplicate FastAPI auth |

## Recommended Build Order

1. Plugin shell, routing, navigation, permissions
2. Shared PI catalog / static data services
3. System Analyzer
4. PI Chain Planner
5. Compare
6. System Mix
7. Market
8. Dashboard
9. Skyhooks
10. Corporation
11. Character views
12. Manager / translation tooling

## Shared Services Needed

- PI catalog service (P0-P4 relationships, planet resources, labels)
- System analyzer service
- Static planet import service
- Price / market cache service
- Account / colony sync service
- Corporation aggregation service
- Translation service

## Verification Targets

- Fresh SeAT install can install the package from local clone
- Fresh SeAT install can install the package from release artifact
- Package migrations are isolated
- No changes required in SeAT core tables
- Existing Python build remains runnable and unchanged
