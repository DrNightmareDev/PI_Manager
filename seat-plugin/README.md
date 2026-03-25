# SeAT PI Manager Plugin

This directory contains the SeAT-native rewrite of the existing EVE PI Manager.

## Current state

The plugin is currently in bootstrap phase. The package skeleton, SeAT registration points,
basic configuration, translations, sidebar integration, migrations, release tooling, and
the first static planet import pipeline are in place.

## Goal

Provide a generically installable SeAT plugin that reproduces the functionality of the existing
FastAPI-based PI tool without changing or breaking the current production build.

## Planned implementation order

1. Plugin skeleton and package integration
2. System Analyzer
3. Static systems search and analyzer data flow
4. PI Chain Planner
5. Compare
6. System Mix
7. Market / Jita views
8. Dashboard
9. Skyhooks
10. Corporation and character views
11. Translation editor / advanced management

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

## Verified bootstrap flow

The following flow has already been verified against a fresh SeAT lab instance:

1. Install the plugin from a local path repository
2. Run plugin migrations
3. Run `php artisan seat-pi-manager:import-static-planets --force`
4. Confirm that static planets are written into `seat_pi_manager_static_planets`

The current import successfully loads the static planet dataset, including:

- `planet_id`
- `system_id`
- `planet_name`
- `planet_number`
- `radius`
