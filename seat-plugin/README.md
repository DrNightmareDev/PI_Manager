# SeAT PI Manager Plugin

This directory contains the SeAT-native rewrite of the existing EVE PI Manager.

## Current state

The plugin is now beyond pure bootstrap. The current SeAT-native implementation includes:

- package skeleton and SeAT registration
- permissions and sidebar integration
- plugin migrations
- static planet import pipeline
- first working `System Analyzer` screen
- SeAT lab validation on a clean VM

## Goal

Provide a generically installable SeAT plugin that reproduces the functionality of the existing
FastAPI-based PI tool without changing or breaking the current production build.

## Planned implementation order

1. Plugin skeleton and package integration
2. System Analyzer
3. PI Chain Planner
4. Compare
5. System Mix
6. Market / Jita views
7. Dashboard
8. Skyhooks
9. Corporation and character views
10. Translation editor / advanced management

## Local installation target

The intended local installation flow inside a SeAT instance is:

1. Clone this repository somewhere on the SeAT server
2. Add `seat-plugin/` as a local Composer path repository
3. Require `drnightmare/seat-pi-manager`
4. Publish configuration
5. Run migrations
6. Clear SeAT caches

See the root `scripts/seat/` directory for helper scripts.

## Runtime requirements

In addition to normal SeAT requirements, the current bootstrap implementation expects:

- PHP `bz2` extension for static planet import
- network access from the SeAT host to download `mapDenormalize.sql.bz2` from Fuzzwork

## Verified lab flow

The following flow has already been verified against a fresh SeAT lab instance:

1. Install the plugin from a local path repository
2. Run plugin migrations
3. Run `php artisan seat-pi-manager:import-static-planets --force`
4. Confirm that static planets are written into `seat_pi_manager_static_planets`
5. Open the plugin page in SeAT
6. Search a system and render static planet details

The current import successfully loads the static planet dataset, including:

- `planet_id`
- `system_id`
- `planet_name`
- `planet_number`
- `radius`

## Current System Analyzer scope

The current first functional page already supports:

- searching systems by exact name, prefix, substring, or numeric system ID
- resolving region and constellation from SeAT data
- displaying static planet count per system
- listing planet number, planet name, type fallback, and radius

This is intentionally the first usable vertical slice before the PI planner, comparison,
market, dashboard, and corporation features are ported.

## Important SeAT lab notes

During the verified lab setup, the following SeAT-side prerequisites were necessary:

- SeAT web assets had to be published so `/web/css`, `/web/js`, and `/web/img` exist
- `config/seat-queues.php` had to exist so Horizon can boot
- Horizon had to run for queue-backed public data updates
- the lab service had to run as `www-data`
- when using `php artisan serve` on port `80`, the systemd service required
  `AmbientCapabilities=CAP_NET_BIND_SERVICE`

These are SeAT runtime concerns, not plugin-specific business logic, but they are relevant
for reproducible local testing.
