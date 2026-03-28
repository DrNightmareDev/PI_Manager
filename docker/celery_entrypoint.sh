#!/bin/sh
set -e

echo "Warte auf RabbitMQ..."
until python -c "
import amqp, os, time
url = os.environ.get('CELERY_BROKER_URL', 'amqp://guest:guest@rabbitmq:5672//')
for i in range(30):
    try:
        conn = amqp.Connection(url)
        conn.connect()
        conn.close()
        break
    except Exception:
        time.sleep(2)
" 2>/dev/null; do
    sleep 2
done

echo "Warte auf Datenbank..."
until python -c "
import psycopg2, os, re, time
url = os.environ.get('DATABASE_URL', '')
m = re.match(r'postgresql://([^:]+):([^@]+)@([^/]+)/(.+)', url)
if m:
    user, pw, host, db = m.groups()
    for i in range(30):
        try:
            psycopg2.connect(host=host, user=user, password=pw, dbname=db)
            break
        except Exception:
            time.sleep(1)
" 2>/dev/null; do
    sleep 1
done

echo "Starte Celery..."
exec "$@"
