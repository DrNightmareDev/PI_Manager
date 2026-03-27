from __future__ import annotations

import json
import logging
from typing import Any

from fastapi import APIRouter, Depends, HTTPException, Request
from fastapi.responses import HTMLResponse, JSONResponse, Response
from sqlalchemy.orm import Session

from app.database import get_db
from app.dependencies import require_account
from app.models import PlanetTemplate
from app.templates_env import templates

logger = logging.getLogger(__name__)

router = APIRouter(prefix="/templates", tags=["pi-templates"])

# EVE type IDs for PI buildings
_BUILDING_NAMES: dict[int, str] = {
    2542: "Command Center",
    2524: "Launch Pad",
    2562: "Storage Facility",
    3068: "Extractor Control Unit",
    2481: "Extractor Head",
    2474: "Advanced Industrial Facility",
    2552: "Basic Industrial Facility",
    2256: "High-Tech Production Plant",
}

# Planet type IDs → readable names
_PLANET_TYPES: dict[int, str] = {
    2014: "Temperate",
    2015: "Ice",
    2016: "Gas",
    2017: "Oceanic",
    2018: "Barren",
    2019: "Storm",
    2063: "Plasma",
    13: "Lava",
}


def _parse_template_meta(layout_json: str) -> dict:
    """Extract display metadata from the raw layout JSON."""
    try:
        data = json.loads(layout_json)
    except Exception:
        return {}

    pins = data.get("P", [])
    building_counts: dict[str, int] = {}
    for pin in pins:
        t = pin.get("T")
        name = _BUILDING_NAMES.get(t, f"TypeID {t}")
        building_counts[name] = building_counts.get(name, 0) + 1

    planet_type_id = data.get("Pln")
    planet_type = _PLANET_TYPES.get(planet_type_id, f"Type {planet_type_id}" if planet_type_id else "Unknown")

    return {
        "cmd_center_level": data.get("CmdCtrLv", "?"),
        "comment": data.get("Cmt", ""),
        "planet_type": planet_type,
        "pin_count": len(pins),
        "link_count": len(data.get("L", [])),
        "route_count": len(data.get("R", [])),
        "building_counts": building_counts,
    }


def _guess_planet_type(name: str, layout_json: str) -> str | None:
    """Guess planet type from name or embedded comment."""
    for t in ("Barren", "Gas", "Oceanic", "Temperate", "Ice", "Storm", "Plasma", "Lava"):
        if t.lower() in name.lower():
            return t
    try:
        data = json.loads(layout_json)
        cmt = data.get("Cmt", "")
        for t in ("Barren", "Gas", "Oceanic", "Temperate", "Ice", "Storm", "Plasma", "Lava"):
            if t.lower() in cmt.lower():
                return t
        pln = data.get("Pln")
        if pln in _PLANET_TYPES:
            return _PLANET_TYPES[pln]
    except Exception:
        pass
    return None


@router.get("", response_class=HTMLResponse)
@router.get("/", response_class=HTMLResponse)
def list_templates(
    request: Request,
    account=Depends(require_account),
    db: Session = Depends(get_db),
):
    community = db.query(PlanetTemplate).filter_by(is_community=True).order_by(PlanetTemplate.name).all()
    own = db.query(PlanetTemplate).filter_by(is_community=False, account_id=account.id).order_by(PlanetTemplate.name).all()

    def _enrich(tmpl: PlanetTemplate) -> dict:
        meta = _parse_template_meta(tmpl.layout_json)
        return {
            "id": tmpl.id,
            "name": tmpl.name,
            "description": tmpl.description,
            "planet_type": tmpl.planet_type or meta.get("planet_type", ""),
            "is_community": tmpl.is_community,
            "cmd_center_level": meta.get("cmd_center_level"),
            "pin_count": meta.get("pin_count", 0),
            "building_counts": meta.get("building_counts", {}),
            "layout_json": tmpl.layout_json,
        }

    return templates.TemplateResponse("pi_templates.html", {
        "request": request,
        "account": account,
        "community_templates": [_enrich(t) for t in community],
        "own_templates": [_enrich(t) for t in own],
    })


@router.get("/{template_id}", response_class=HTMLResponse)
def template_detail(
    template_id: int,
    request: Request,
    account=Depends(require_account),
    db: Session = Depends(get_db),
):
    tmpl = db.get(PlanetTemplate, template_id)
    if not tmpl:
        raise HTTPException(status_code=404, detail="Template not found")
    if not tmpl.is_community and tmpl.account_id != account.id and not account.is_admin:
        raise HTTPException(status_code=403, detail="Not your template")

    meta = _parse_template_meta(tmpl.layout_json)
    return templates.TemplateResponse("pi_template_detail.html", {
        "request": request,
        "account": account,
        "tmpl": tmpl,
        "meta": meta,
        "layout_json": tmpl.layout_json,
    })


@router.get("/{template_id}/download")
def download_template(
    template_id: int,
    account=Depends(require_account),
    db: Session = Depends(get_db),
):
    tmpl = db.get(PlanetTemplate, template_id)
    if not tmpl:
        raise HTTPException(status_code=404, detail="Template not found")
    if not tmpl.is_community and tmpl.account_id != account.id and not account.is_admin:
        raise HTTPException(status_code=403, detail="Not your template")

    safe_name = "".join(c if c.isalnum() or c in "- _" else "_" for c in tmpl.name)
    return Response(
        content=tmpl.layout_json,
        media_type="application/json",
        headers={"Content-Disposition": f'attachment; filename="{safe_name}.json"'},
    )


@router.post("/upload")
async def upload_template(
    request: Request,
    account=Depends(require_account),
    db: Session = Depends(get_db),
):
    body = await request.json()
    name = (body.get("name") or "").strip()
    description = (body.get("description") or "").strip() or None
    raw_json = body.get("layout_json") or ""

    if not name:
        raise HTTPException(status_code=400, detail="Name is required")
    try:
        parsed = json.loads(raw_json)
    except Exception:
        raise HTTPException(status_code=400, detail="Invalid JSON")
    if "P" not in parsed:
        raise HTTPException(status_code=400, detail="JSON does not look like a PI template (missing 'P' array)")

    planet_type = _guess_planet_type(name, raw_json)

    tmpl = PlanetTemplate(
        account_id=account.id,
        name=name,
        description=description,
        planet_type=planet_type,
        layout_json=raw_json,
        is_community=False,
    )
    db.add(tmpl)
    db.commit()
    db.refresh(tmpl)
    return JSONResponse({"ok": True, "id": tmpl.id})


@router.delete("/{template_id}")
def delete_template(
    template_id: int,
    account=Depends(require_account),
    db: Session = Depends(get_db),
):
    tmpl = db.get(PlanetTemplate, template_id)
    if not tmpl:
        raise HTTPException(status_code=404, detail="Template not found")
    if not tmpl.is_community and tmpl.account_id != account.id and not account.is_admin:
        raise HTTPException(status_code=403, detail="Not your template")
    if tmpl.is_community and not account.is_admin:
        raise HTTPException(status_code=403, detail="Only admins can delete community templates")

    db.delete(tmpl)
    db.commit()
    return JSONResponse({"ok": True})


@router.post("/admin/seed")
async def seed_community_templates(
    request: Request,
    account=Depends(require_account),
    db: Session = Depends(get_db),
):
    """Fetch all templates from DalShooth/EVE_PI_Templates on GitHub and seed them as community templates."""
    if not account.is_admin:
        raise HTTPException(status_code=403, detail="Admin only")

    import urllib.request

    GITHUB_API = "https://api.github.com/repos/DalShooth/EVE_PI_Templates/contents/PlanetaryInteractionTemplates"
    BRANCH = "main"

    try:
        req = urllib.request.Request(GITHUB_API, headers={"User-Agent": "EVE-PI-Manager/1.0"})
        with urllib.request.urlopen(req, timeout=15) as resp:
            file_list: list[dict] = json.loads(resp.read().decode())
    except Exception as exc:
        raise HTTPException(status_code=502, detail=f"GitHub API error: {exc}")

    inserted = 0
    skipped = 0
    errors = 0

    for entry in file_list:
        if not entry.get("name", "").endswith(".json"):
            continue
        download_url: str = entry.get("download_url", "")
        raw_name = entry["name"].removesuffix(".json")

        # Skip if already seeded
        existing = db.query(PlanetTemplate).filter_by(
            is_community=True, source_url=download_url
        ).first()
        if existing:
            skipped += 1
            continue

        try:
            req2 = urllib.request.Request(download_url, headers={"User-Agent": "EVE-PI-Manager/1.0"})
            with urllib.request.urlopen(req2, timeout=15) as resp2:
                raw_json = resp2.read().decode("utf-8")
            parsed = json.loads(raw_json)
        except Exception as exc:
            logger.warning("seed: failed to fetch %s: %s", download_url, exc)
            errors += 1
            continue

        planet_type = _guess_planet_type(raw_name, raw_json)
        description = parsed.get("Cmt") or None

        db.add(PlanetTemplate(
            account_id=None,
            name=raw_name,
            description=description,
            planet_type=planet_type,
            layout_json=raw_json,
            is_community=True,
            source_url=download_url,
        ))
        inserted += 1

    db.commit()
    return JSONResponse({"ok": True, "inserted": inserted, "skipped": skipped, "errors": errors})
