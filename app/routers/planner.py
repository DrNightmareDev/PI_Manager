from fastapi import APIRouter, Depends, Request
from fastapi.responses import HTMLResponse

from app.dependencies import require_account
from app.pi_data import (
    P0_TO_P1, P1_TO_P2, P2_TO_P3, P3_TO_P4,
    PLANET_RESOURCES, PLANET_TYPE_COLORS,
    ALL_P2, ALL_P3, ALL_P4,
)
from app.templates_env import templates

router = APIRouter(prefix="/planner", tags=["planner"])


@router.get("", response_class=HTMLResponse)
@router.get("/", response_class=HTMLResponse)
def planner_page(request: Request, account=Depends(require_account)):
    all_products = (
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
