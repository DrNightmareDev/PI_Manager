#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
PLUGIN_DIR="${ROOT_DIR}/seat-plugin"
DIST_DIR="${PLUGIN_DIR}/dist"
PACKAGE_NAME="seat-pi-manager"
VERSION="${1:-$(git -C "${ROOT_DIR}" rev-parse --short HEAD)}"

mkdir -p "${DIST_DIR}"

echo "[seat-plugin] validating composer.json"
composer validate --working-dir "${PLUGIN_DIR}" --no-check-lock

echo "[seat-plugin] creating release archive"
ARCHIVE_PATH="${DIST_DIR}/${PACKAGE_NAME}-${VERSION}.zip"
rm -f "${ARCHIVE_PATH}"
export SEAT_PLUGIN_VERSION="${VERSION}"
(
  cd "${ROOT_DIR}"
  python - <<'PY'
import os, zipfile
root = os.path.abspath("seat-plugin")
dist = os.path.join(root, "dist")
version = os.environ.get("SEAT_PLUGIN_VERSION")
archive = os.path.join(dist, f"seat-pi-manager-{version}.zip")
with zipfile.ZipFile(archive, "w", zipfile.ZIP_DEFLATED) as zf:
    for current_root, dirs, files in os.walk(root):
        dirs[:] = [d for d in dirs if d not in {"vendor", "node_modules", "dist"}]
        for name in files:
            path = os.path.join(current_root, name)
            rel = os.path.relpath(path, root)
            zf.write(path, rel)
print(archive)
PY
)

echo "[seat-plugin] archive ready: ${ARCHIVE_PATH}"
