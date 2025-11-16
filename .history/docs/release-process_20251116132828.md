# Release Process

## Versioning
- SemVer; bump plugin version and DB schema version when needed.

## Pre-release
- Feature freeze; run full test matrix; generate changelog.

## Migrations
- Bump db schema version; write migration scripts; verify roll-forward and rollback guidance.

## Docs
- Update decisions.md, schema.md, roadmap.md; publish examples.

## Build
- Tag & build: generate plugin zip; verify on clean WP instance.
