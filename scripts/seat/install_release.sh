#!/usr/bin/env bash
set -euo pipefail

if [[ $# -lt 1 ]]; then
  echo "Usage: $0 <seat-root> [version]"
  exit 1
fi

SEAT_ROOT="$(cd "$1" && pwd)"
VERSION="${2:-*}"

if [[ ! -f "${SEAT_ROOT}/artisan" ]]; then
  echo "SeAT root not found: artisan missing in ${SEAT_ROOT}"
  exit 1
fi

echo "[seat-plugin] requiring release package drnightmare/seat-pi-manager:${VERSION}"
composer --working-dir "${SEAT_ROOT}" require "drnightmare/seat-pi-manager:${VERSION}" --no-interaction

echo "[seat-plugin] publishing config"
php "${SEAT_ROOT}/artisan" vendor:publish --tag=seat-pi-manager-config --force || true

echo "[seat-plugin] running migrations"
php "${SEAT_ROOT}/artisan" migrate --force

echo "[seat-plugin] clearing caches"
php "${SEAT_ROOT}/artisan" optimize:clear

echo "[seat-plugin] release installation complete"
