# SeAT PI Manager Implementation Plan

## Goal

Build a SeAT-native Laravel plugin in parallel to the existing FastAPI PI Manager without modifying the current production build.

## Constraints

- The existing Python application must remain untouched and deployable.
- The SeAT plugin must be generically publishable.
- Installation should work from a local clone and from release artifacts.

## Current bootstrap status

- Composer package scaffolded
- Service provider scaffolded
- Sidebar entry scaffolded
- Permission registration scaffolded
- Route + controller + first view scaffolded
- Plugin config scaffolded
- Base migrations scaffolded
- Build/install scripts scaffolded
- GitHub Actions CI/release workflows scaffolded

## Next implementation phases

1. Verify package install inside a clean SeAT 5 environment
2. Add static planet import command and importer service
3. Implement System Analyzer page and data flow
4. Port planner and compare logic
5. Port market, dashboard, skyhooks, corporation, and character views
6. Add tests and release hardening
