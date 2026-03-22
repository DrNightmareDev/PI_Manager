from fastapi import APIRouter, Depends, Request
from fastapi.responses import HTMLResponse, JSONResponse
from pydantic import BaseModel
from sqlalchemy.orm import Session

from app.database import get_db
from app.dependencies import require_account
from app.models import PiFavorite
from app.pi_data import (
    P0_TO_P1, P1_TO_P2, P2_TO_P3, P3_TO_P4,
    PLANET_RESOURCES, PLANET_TYPE_COLORS,
    ALL_P1, ALL_P2, ALL_P3, ALL_P4,
)
from app.templates_env import templates

router = APIRouter(prefix="/planner", tags=["planner"])


@router.get("", response_class=HTMLResponse)
@router.get("/", response_class=HTMLResponse)
def planner_page(request: Request, account=Depends(require_account)):
    all_products = (
        [{"name": n, "tier": "P1"} for n in sorted(ALL_P1)] +
        [{"name": n, "tier": "P2"} for n in sorted(ALL_P2)] +
        [{"name": n, "tier": "P3"} for n in sorted(ALL_P3)] +
        [{"name": n, "tier": "P4"} for n in sorted(ALL_P4)]
    )
    return templates.TemplateResponse("planner.html", {
        "request": request,
        "account": account,
        "p0_to_p1": P0_TO_P1,
        "p1_to_p2": P1_TO_P2,
        "p2_to_p3": P2_TO_P3,
        "p3_to_p4": P3_TO_P4,
        "planet_resources": PLANET_RESOURCES,
        "planet_type_colors": PLANET_TYPE_COLORS,
        "all_products": all_products,
    })


@router.get("/favorites")
def get_favorites(account=Depends(require_account), db: Session = Depends(get_db)):
    favs = db.query(PiFavorite).filter(PiFavorite.account_id == account.id).all()
    return JSONResponse([f.product_name for f in favs])


class FavoriteToggle(BaseModel):
    product_name: str


@router.post("/favorites/toggle")
def toggle_favorite(
    body: FavoriteToggle,
    account=Depends(require_account),
    db: Session = Depends(get_db),
):
    existing = db.query(PiFavorite).filter(
        PiFavorite.account_id == account.id,
        PiFavorite.product_name == body.product_name,
    ).first()
    if existing:
        db.delete(existing)
        db.commit()
        return JSONResponse({"favorited": False})
    db.add(PiFavorite(account_id=account.id, product_name=body.product_name))
    db.commit()
    return JSONResponse({"favorited": True})
