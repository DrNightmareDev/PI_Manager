from __future__ import annotations

from datetime import datetime, timedelta, timezone

from fastapi import APIRouter, Depends, Query, Request
from fastapi.responses import HTMLResponse, JSONResponse
from sqlalchemy.orm import Session

from app.database import get_db
from app.dependencies import require_owner
from app.templates_env import templates

router = APIRouter(prefix="/intel", tags=["intel"])


REGIONS = {
    "tribute": {
        "name": "Tribute",
        "neighbors": ["vale_of_the_silent"],
        "systems": [
            {"id": 30002768, "name": "M-OEE8", "x": 130, "y": 180, "security": "-0.6"},
            {"id": 30002769, "name": "Q-EHMJ", "x": 290, "y": 120, "security": "-0.5"},
            {"id": 30002770, "name": "EOY-BG", "x": 470, "y": 185, "security": "-0.3"},
            {"id": 30002771, "name": "PVH8-0", "x": 335, "y": 315, "security": "-0.7"},
            {"id": 30002772, "name": "9-4RP2", "x": 170, "y": 360, "security": "-0.4"},
            {"id": 30002773, "name": "6RCQ-V", "x": 525, "y": 360, "security": "-0.8"},
        ],
        "connections": [
            [30002768, 30002769],
            [30002769, 30002770],
            [30002769, 30002771],
            [30002768, 30002772],
            [30002771, 30002772],
            [30002771, 30002773],
            [30002770, 30002773],
        ],
    },
    "vale_of_the_silent": {
        "name": "Vale of the Silent",
        "neighbors": ["tribute"],
        "systems": [
            {"id": 30002801, "name": "P3EN-E", "x": 150, "y": 145, "security": "-0.7"},
            {"id": 30002802, "name": "KQK1-2", "x": 300, "y": 115, "security": "-0.4"},
            {"id": 30002803, "name": "Y-2ANO", "x": 475, "y": 165, "security": "-0.6"},
            {"id": 30002804, "name": "XVV-21", "x": 210, "y": 330, "security": "-0.2"},
            {"id": 30002805, "name": "T-ZWA1", "x": 395, "y": 310, "security": "-0.8"},
            {"id": 30002806, "name": "B-DBYQ", "x": 555, "y": 360, "security": "-0.5"},
        ],
        "connections": [
            [30002801, 30002802],
            [30002802, 30002803],
            [30002801, 30002804],
            [30002802, 30002804],
            [30002804, 30002805],
            [30002803, 30002805],
            [30002805, 30002806],
        ],
    },
}

WINDOW_FACTORS = {
    "5m": 0.45,
    "15m": 0.75,
    "60m": 1.0,
    "24h": 1.9,
}

KILL_TYPES = ("all", "ship", "pod")
SHIP_POOL = (
    "Ishtar",
    "Sabre",
    "Drake Navy Issue",
    "Kikimora",
    "Nighthawk",
    "Hecate",
    "Vagabond",
    "Cerberus",
)
CORP_POOL = (
    "Shadow Assembly",
    "Northwind Raiders",
    "Aegis Frontier",
    "Dread Signal",
    "Tenebris Fleet",
)


def _get_region(region_key: str) -> dict:
    return REGIONS.get(region_key, REGIONS["tribute"])


def _activity_snapshot(region_key: str, window: str, kill_type: str) -> tuple[list[dict], list[dict]]:
    region = _get_region(region_key)
    factor = WINDOW_FACTORS.get(window, 1.0)
    type_factor = {"all": 1.0, "ship": 0.82, "pod": 0.36}.get(kill_type, 1.0)
    now = datetime.now(timezone.utc)
    tick = int(now.timestamp() // 30)
    systems = []
    feed = []

    for index, system in enumerate(region["systems"]):
        base = ((tick + index * 5 + len(region_key)) % 9) + index % 3
        weighted = max(0, round(base * factor * type_factor))
        heat = min(1.0, weighted / 10.0)
        systems.append({
            "system_id": system["id"],
            "kill_count": weighted,
            "heat": heat,
            "danger": "hot" if weighted >= 7 else "warm" if weighted >= 3 else "cold",
        })

        entries = max(1, min(4, weighted // 2 + 1))
        for offset in range(entries):
            minutes_ago = (index * 4 + offset * 3 + tick) % 55
            kill_time = now - timedelta(minutes=minutes_ago, seconds=(offset + 1) * 11)
            ship = SHIP_POOL[(index + offset + tick) % len(SHIP_POOL)]
            corp = CORP_POOL[(index * 2 + offset + tick) % len(CORP_POOL)]
            feed.append({
                "killmail_id": int(f"{system['id']}{index}{offset}") % 2_147_483_647,
                "timestamp": kill_time.isoformat(),
                "system_id": system["id"],
                "system_name": system["name"],
                "region_name": region["name"],
                "victim_name": f"Pilot-{(tick + index + offset) % 97:02d}",
                "ship_type": ship,
                "alliance_name": corp,
                "attackers": 2 + ((index + offset + tick) % 7),
                "isk_value": 24_000_000 + ((index + 1) * (offset + 2) * 8_500_000),
            })

    feed.sort(key=lambda item: item["timestamp"], reverse=True)
    return systems, feed[:18]


@router.get("/map", response_class=HTMLResponse)
def intel_map(
    request: Request,
    region: str = Query("tribute"),
    account=Depends(require_owner),
):
    region_data = _get_region(region)
    system_activity, kill_feed = _activity_snapshot(region, "60m", "all")
    return templates.TemplateResponse("intel_map.html", {
        "request": request,
        "account": account,
        "regions": [{"key": key, "name": value["name"]} for key, value in REGIONS.items()],
        "selected_region": region,
        "region_data": region_data,
        "initial_activity": system_activity,
        "initial_feed": kill_feed,
    })


@router.get("/map/live")
def intel_map_live(
    region: str = Query("tribute"),
    window: str = Query("60m"),
    kill_type: str = Query("all"),
    account=Depends(require_owner),
    db: Session = Depends(get_db),
):
    region_data = _get_region(region)
    system_activity, kill_feed = _activity_snapshot(region, window, kill_type if kill_type in KILL_TYPES else "all")
    return JSONResponse({
        "region": {
            "key": region,
            "name": region_data["name"],
            "neighbors": region_data["neighbors"],
            "systems": region_data["systems"],
            "connections": region_data["connections"],
        },
        "window": window,
        "kill_type": kill_type,
        "activity": system_activity,
        "feed": kill_feed,
        "updated_at": datetime.now(timezone.utc).isoformat(),
    })
