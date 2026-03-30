from __future__ import annotations

import logging
import time
from datetime import datetime, timezone

import requests
from fastapi import APIRouter, Body, Depends, Query, Request
from fastapi.responses import HTMLResponse, JSONResponse
from sqlalchemy.orm import Session

from app import sde
from app.database import get_db
from app.dependencies import require_account
from app.esi import ensure_valid_token, get_character_location
from app.models import Character
from app.routers.dashboard import _compute_storage_value, _load_colony_cache
from app.templates_env import templates

logger = logging.getLogger(__name__)

router = APIRouter(prefix="/hauling", tags=["hauling"])

_LOCATION_CACHE_TTL = 300
_ROUTE_CACHE_TTL = 3600
_location_cache: dict[int, tuple[dict, float]] = {}
_route_cache: dict[tuple[int, int], tuple[int, float]] = {}


def _storage_has_items(storage: list[dict] | None) -> bool:
    return any(float(item.get("amount") or 0) > 0 for entry in (storage or []) for item in (entry.get("items") or []))


def _storage_summary(storage: list[dict] | None) -> tuple[list[str], int]:
    items = []
    for entry in storage or []:
        for item in entry.get("items") or []:
            amount = int(item.get("amount") or 0)
            if amount > 0:
                items.append(f"{item.get('name')} x{amount}")
    shown = items[:3]
    return shown, max(len(items) - len(shown), 0)


def _urgency_score(colony: dict) -> float:
    expiry_hours = float(colony.get("expiry_hours") or 0.0)
    storage_value = float(colony.get("storage_value") or 0.0)
    active_factor = 1.0 if colony.get("is_active") else 0.5
    return (storage_value / max(expiry_hours, 0.1)) * active_factor


def _urgency_percent(expiry_hours: float | None) -> int:
    if expiry_hours is None:
        return 0
    if expiry_hours <= 2:
        return 100
    if expiry_hours >= 48:
        return 0
    return max(0, min(100, int(((48 - expiry_hours) / 46) * 100)))


def _system_name(system_id: int) -> str:
    info = sde.get_system_local(system_id) or {}
    return info.get("name", f"System {system_id}")


def _get_cached_location(character: Character, db: Session) -> dict | None:
    cached = _location_cache.get(character.id)
    if cached and time.time() - cached[1] < _LOCATION_CACHE_TTL:
        return cached[0]
    token = ensure_valid_token(character, db)
    if not token:
        return None
    location = get_character_location(int(character.eve_character_id), token)
    if location:
        _location_cache[character.id] = (location, time.time())
    return location or None


def _jump_count(origin_system_id: int, destination_system_id: int) -> int:
    if origin_system_id == destination_system_id:
        return 0
    key = (int(origin_system_id), int(destination_system_id))
    cached = _route_cache.get(key)
    if cached and time.time() - cached[1] < _ROUTE_CACHE_TTL:
        return cached[0]
    resp = requests.get(
        f"https://esi.evetech.net/latest/route/{int(origin_system_id)}/{int(destination_system_id)}/",
        params={"datasource": "tranquility"},
        headers={"User-Agent": "EVE-PI-Manager/1.0 github.com/DrNightmareDev/PI_Manager"},
        timeout=15,
    )
    resp.raise_for_status()
    route = resp.json() or []
    jumps = max(len(route) - 1, 0) if isinstance(route, list) else 0
    _route_cache[key] = (jumps, time.time())
    _route_cache[(key[1], key[0])] = (jumps, time.time())
    return jumps


def _build_route(origin_system_id: int, system_ids: list[int]) -> tuple[list[dict], int]:
    remaining = list(dict.fromkeys(int(system_id) for system_id in system_ids if system_id and int(system_id) != int(origin_system_id)))[:20]
    ordered: list[dict] = [{
        "system_id": int(origin_system_id),
        "system_name": _system_name(int(origin_system_id)),
        "jumps_from_prev": 0,
    }]
    total_jumps = 0
    current = int(origin_system_id)
    while remaining:
        next_id = min(remaining, key=lambda candidate: _jump_count(current, candidate))
        jumps = _jump_count(current, next_id)
        total_jumps += jumps
        ordered.append({
            "system_id": int(next_id),
            "system_name": _system_name(int(next_id)),
            "jumps_from_prev": int(jumps),
        })
        remaining.remove(next_id)
        current = next_id
    return ordered, total_jumps


@router.get("", response_class=HTMLResponse)
def hauling_page(
    request: Request,
    character_id: int | None = Query(default=None),
    account=Depends(require_account),
    db: Session = Depends(get_db),
):
    characters = db.query(Character).filter(Character.account_id == account.id).all()
    selected_character = None
    if character_id:
        selected_character = next((char for char in characters if char.id == character_id), None)
    if selected_character is None:
        selected_character = next((char for char in characters if char.id == account.main_character_id), None) or (characters[0] if characters else None)

    cached = _load_colony_cache(account.id, db) or {}
    colonies = list(cached.get("colonies") or [])
    hauling_colonies = []
    for colony in colonies:
        if selected_character and colony.get("character_name") != selected_character.character_name:
            continue
        if not _storage_has_items(colony.get("storage")):
            continue
        storage_value = _compute_storage_value(colony.get("storage") or [], getattr(account, "price_mode", "sell"), db)
        summary_items, extra_count = _storage_summary(colony.get("storage"))
        entry = dict(colony)
        entry["storage_value"] = storage_value
        entry["storage_summary_items"] = summary_items
        entry["storage_extra_count"] = extra_count
        entry["urgency_score"] = _urgency_score(entry)
        entry["urgency_pct"] = _urgency_percent(entry.get("expiry_hours"))
        hauling_colonies.append(entry)

    hauling_colonies.sort(key=lambda colony: colony.get("urgency_score", 0.0), reverse=True)

    location = _get_cached_location(selected_character, db) if selected_character else None
    route_ordered: list[dict] = []
    route_total_jumps = 0
    if location and hauling_colonies:
        try:
            route_ordered, route_total_jumps = _build_route(
                int(location.get("solar_system_id") or 0),
                [int(colony.get("solar_system_id") or 0) for colony in hauling_colonies if colony.get("solar_system_id")],
            )
        except Exception:
            logger.exception("hauling: failed to build initial route")
            route_ordered = []
            route_total_jumps = 0

    location_name = _system_name(int(location.get("solar_system_id"))) if location and location.get("solar_system_id") else None
    dotlan_route_link = ""
    if route_ordered and len(route_ordered) > 1:
        names = [item["system_name"].replace(" ", "_") for item in route_ordered]
        dotlan_route_link = f"https://evemaps.dotlan.net/route/{':'.join(names)}"

    return templates.TemplateResponse("hauling.html", {
        "request": request,
        "account": account,
        "characters": characters,
        "selected_character_id": selected_character.id if selected_character else None,
        "selected_character_name": selected_character.character_name if selected_character else None,
        "location": location,
        "location_name": location_name,
        "hauling_colonies": hauling_colonies,
        "route_ordered": route_ordered,
        "route_total_jumps": route_total_jumps,
        "dotlan_route_link": dotlan_route_link,
        "hauling_total_value": sum(float(colony.get("storage_value") or 0.0) for colony in hauling_colonies),
    })


@router.get("/api/location")
def get_location(
    character_id: int | None = Query(default=None),
    account=Depends(require_account),
    db: Session = Depends(get_db),
):
    characters = db.query(Character).filter(Character.account_id == account.id).all()
    character = next((char for char in characters if char.id == character_id), None) if character_id else None
    if character is None:
        character = next((char for char in characters if char.id == account.main_character_id), None) or (characters[0] if characters else None)
    if character is None:
        return JSONResponse({"ok": False, "location": None})
    location = _get_cached_location(character, db)
    if not location or not location.get("solar_system_id"):
        return JSONResponse({"ok": False, "location": None})
    system_id = int(location["solar_system_id"])
    return JSONResponse({
        "ok": True,
        "character_id": character.id,
        "solar_system_id": system_id,
        "system_name": _system_name(system_id),
    })


@router.post("/api/route")
def get_route(
    payload: dict = Body(...),
    account=Depends(require_account),
    db: Session = Depends(get_db),
):
    origin_system_id = int(payload.get("origin_system_id") or 0)
    system_ids = [int(system_id) for system_id in (payload.get("system_ids") or []) if system_id]
    if not origin_system_id or not system_ids:
        return JSONResponse({"ordered": [], "total_jumps": 0})
    try:
        ordered, total_jumps = _build_route(origin_system_id, system_ids)
    except Exception:
        logger.exception("hauling: route rebuild failed")
        return JSONResponse({"ordered": [], "total_jumps": 0})
    return JSONResponse({"ordered": ordered, "total_jumps": total_jumps})
