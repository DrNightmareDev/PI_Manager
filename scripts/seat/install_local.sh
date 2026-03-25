#!/usr/bin/env bash
set -euo pipefail

if [[ $# -lt 2 ]]; then
  echo "Usage: $0 <seat-root> <repo-root>"
  exit 1
fi

SEAT_ROOT="$(cd "$1" && pwd)"
REPO_ROOT="$(cd "$2" && pwd)"
PLUGIN_PATH="${REPO_ROOT}/seat-plugin"

if [[ ! -f "${SEAT_ROOT}/artisan" ]]; then
  echo "SeAT root not found: artisan missing in ${SEAT_ROOT}"
  exit 1
fi

if [[ ! -f "${PLUGIN_PATH}/composer.json" ]]; then
  echo "Plugin composer.json not found in ${PLUGIN_PATH}"
  exit 1
fi

echo "[seat-plugin] configuring local composer path repository"
composer --working-dir "${SEAT_ROOT}" config repositories.drnightmare-seat path "${PLUGIN_PATH}"

echo "[seat-plugin] requiring local package"
composer --working-dir "${SEAT_ROOT}" require drnightmare/seat-pi-manager:* --no-interaction

echo "[seat-plugin] publishing config"
php "${SEAT_ROOT}/artisan" vendor:publish --tag=seat-pi-manager-config --force || true

echo "[seat-plugin] running migrations"
php "${SEAT_ROOT}/artisan" migrate --force

echo "[seat-plugin] importing static planet dataset"
php "${SEAT_ROOT}/artisan" seat-pi-manager:import-static-planets || true

echo "[seat-plugin] clearing caches"
php "${SEAT_ROOT}/artisan" optimize:clear

echo "[seat-plugin] local installation complete"
