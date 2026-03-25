# SeAT PI Manager Plugin

This directory contains the SeAT-native rewrite of the existing EVE PI Manager.

## Current state

The plugin is currently in bootstrap phase. The package skeleton, SeAT registration points,
basic configuration, translations, sidebar integration, migrations, and release tooling are in place.

## Goal

Provide a generically installable SeAT plugin that reproduces the functionality of the existing
FastAPI-based PI tool without changing or breaking the current production build.

## Planned implementation order

1. Plugin skeleton and package integration
2. Static planet data and import pipeline
3. System Analyzer
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
