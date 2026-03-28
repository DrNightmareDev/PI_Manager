from contextlib import asynccontextmanager
from datetime import datetime, timezone, timedelta
import logging
import os

from fastapi import FastAPI, Request
from fastapi.responses import HTMLResponse, RedirectResponse
from fastapi.staticfiles import StaticFiles
from sqlalchemy import text

from app.config import get_settings
from app.database import engine, SessionLocal
from app.i18n import bootstrap_pi_type_translations, bootstrap_static_planets, bootstrap_translations
from app.models import SSOState
from app.routers import auth, dashboard, admin, pi, market, system, planner, skyhook, colony_plan, pi_templates
from app.templates_env import templates

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

settings = get_settings()

# Celery Beat handles scheduled jobs (market refresh, SSO cleanup, colony refresh).
# APScheduler is kept as a fallback only when CELERY_BROKER_URL is not set,
# so the app still works without RabbitMQ in dev/single-process setups.
_USE_CELERY = bool(os.getenv("CELERY_BROKER_URL"))


def _fallback_refresh_market_prices():
    """Fallback market refresh — only used when Celery is not configured."""
    from app.market import refresh_all_pi_prices
    from app.routers.dashboard import refresh_dashboard_price_cache
    from app.routers.skyhook import refresh_skyhook_value_cache
    db = SessionLocal()
    try:
        logger.info("Starte Marktpreis-Refresh (APScheduler fallback)...")
        refresh_all_pi_prices(db)
        refresh_dashboard_price_cache(db)
        refresh_skyhook_value_cache(db)
    except Exception as e:
        logger.warning(f"Marktpreis-Refresh fehlgeschlagen: {e}")
    finally:
        db.close()


def _fallback_cleanup_sso():
    """Fallback SSO cleanup — only used when Celery is not configured."""
    try:
        with SessionLocal() as db:
            cutoff = datetime.now(timezone.utc) - timedelta(hours=1)
            deleted = db.query(SSOState).filter(SSOState.created_at < cutoff).delete()
            db.commit()
            if deleted:
                logger.info("Bereinigt: %d abgelaufene SSO-States", deleted)
    except Exception as e:
        logger.warning(f"SSO-State-Bereinigung fehlgeschlagen: {e}")


@asynccontextmanager
async def lifespan(app: FastAPI):
    # Startup
    logger.info("EVE PI Manager startet...")
    from app import sde
    sde.init()
    inserted_translations = bootstrap_translations()
    if inserted_translations:
        logger.info("I18N: %s Uebersetzungen in DB gebootstrapped.", inserted_translations)
    inserted_type_translations = bootstrap_pi_type_translations()
    if inserted_type_translations:
        logger.info("I18N: %s PI-Type-Uebersetzungen aus SDE in DB gebootstrapped.", inserted_type_translations)
    inserted_static_planets = bootstrap_static_planets()
    if inserted_static_planets:
        logger.info("SDE: %s statische Planeten in DB gebootstrapped.", inserted_static_planets)

    _fallback_cleanup_sso()

    if not _USE_CELERY:
        # Dev mode: run scheduled jobs in-process via APScheduler
        from apscheduler.schedulers.background import BackgroundScheduler
        scheduler = BackgroundScheduler()
        scheduler.add_job(_fallback_refresh_market_prices, "interval", minutes=15)
        scheduler.add_job(_fallback_cleanup_sso, "interval", hours=1)
        scheduler.start()
        logger.info("APScheduler gestartet (dev-Fallback, kein Celery konfiguriert).")
    else:
        scheduler = None
        logger.info("Celery erkannt — APScheduler deaktiviert. Jobs laufen via Celery Beat.")

    yield

    # Shutdown
    if scheduler:
        scheduler.shutdown(wait=False)
    logger.info("EVE PI Manager beendet.")


app = FastAPI(
    title="EVE PI Manager",
    description="Planetary Industry Dashboard für EVE Online",
    version="1.0.0",
    lifespan=lifespan,
)

# Statische Dateien
app.mount("/static", StaticFiles(directory="app/static"), name="static")


@app.middleware("http")
async def impersonate_middleware(request: Request, call_next):
    from app.session import read_session
    session = read_session(request)
    request.state.is_impersonating = bool(session and session.get("real_owner_id"))
    request.state.real_owner_id = session.get("real_owner_id") if session else None
    return await call_next(request)

# Router einbinden
app.include_router(auth.router)
app.include_router(dashboard.router)
app.include_router(admin.router)
app.include_router(pi.router)
app.include_router(market.router)
app.include_router(system.router)
app.include_router(planner.router)
app.include_router(skyhook.router)
app.include_router(colony_plan.router)
app.include_router(pi_templates.router)


@app.get("/", response_class=HTMLResponse)
def index(request: Request):
    from app.session import read_session
    from app.database import get_db
    from app.models import Account

    session = read_session(request)
    if session:
        db = SessionLocal()
        try:
            account = db.query(Account).filter(
                Account.id == session.get("account_id")
            ).first()
            if account:
                return RedirectResponse(url="/dashboard", status_code=302)
        finally:
            db.close()

    error = request.query_params.get("error")
    db = SessionLocal()
    try:
        from app.models import Account
        has_owner = db.query(Account).filter(Account.is_owner == True).first() is not None
    finally:
        db.close()
    return templates.TemplateResponse("index.html", {"request": request, "error": error, "has_owner": has_owner})


@app.get("/health")
def health_check():
    try:
        with engine.connect() as conn:
            conn.execute(text("SELECT 1"))
        return {"status": "ok", "database": "connected"}
    except Exception as e:
        return {"status": "error", "database": str(e)}
