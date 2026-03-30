#!/bin/sh
set -e

echo "Warte auf Datenbank..."
until python -c "
import os, re, sys, time
import psycopg2
url = os.environ.get('DATABASE_URL', '')
m = re.match(r'postgresql://([^:]+):([^@]+)@([^/]+)/(.+)', url)
ok = False
if m:
    user, pw, host, db = m.groups()
    for i in range(30):
        try:
            conn = psycopg2.connect(host=host, user=user, password=pw, dbname=db)
            conn.close()
            ok = True
            break
        except Exception:
            time.sleep(1)
sys.exit(0 if ok else 1)
" 2>/dev/null; do
    sleep 1
done

echo "Datenbank erreichbar. Starte Migrationen..."
alembic upgrade head

echo "Starte Anwendung..."
exec gunicorn app.main:app \
    -k uvicorn.workers.UvicornWorker \
    --workers "${WEB_WORKERS:-4}" \
    --bind 0.0.0.0:8000 \
    --timeout 120 \
    --access-logfile - \
    --error-logfile -
