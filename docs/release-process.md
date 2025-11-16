# Release Process

## Versioning
- SemVer; bump plugin version and DB schema version when needed.
- Documentation releases: bump version in CHANGELOG.md and README.md.

## Pre-release
- Feature freeze; run full test matrix; generate changelog.
- For documentation releases: review and sync all docs, update examples.

## Migrations
- Bump db schema version; write migration scripts; verify roll-forward and rollback guidance.

## Docs
- Update decisions.md, schema.md, roadmap.md; publish examples.
- Sync CHANGELOG.md with all changes since last release.
- Update README.md with current version and any new information.

## Build
- Tag & build: generate plugin zip; verify on clean WP instance.

## Documentation Release Process

For documentation-only releases (this repository):

### 1. Sync Documentation
```bash
# Ensure all documentation is current and consistent
# Review: docs/README.md, decisions.md, schema.md, roadmap.md
# Update: CHANGELOG.md with new entries
```

### 2. Update Version Information
```bash
# Update version in:
# - README.md (top-level project info)
# - CHANGELOG.md (new release section with date)
```

### 3. Build Release Package
```bash
# Run the build script with version number
./build-release.sh 1.0.0

# This creates:
# - build/bits-accessible-forms-docs-v1.0.0.zip
# - Includes: docs/, examples/, README.md, CHANGELOG.md, VERSION, RELEASE_INFO.txt
```

### 4. Tag Release
```bash
# Create annotated tag
git tag -a v1.0.0 -m "Release v1.0.0: Description"

# Push tag to remote
git push origin v1.0.0
```

### 5. Smoke Test
```bash
# Extract and verify release package
cd /tmp
unzip bits-accessible-forms-docs-v1.0.0.zip
cd bits-accessible-forms-docs-v1.0.0

# Verify:
# - All documentation files present
# - README.md displays correctly
# - CHANGELOG.md is up to date
# - Examples are included
# - VERSION file contains correct version
```

### 6. Publish Release
- Create GitHub Release using the tag
- Attach the ZIP file as a release asset
- Copy CHANGELOG.md section to release notes
- Mark as latest release

## Plugin Release Process

For WordPress plugin releases (when plugin code is added):

### 1. Pre-release Checklist
- [ ] Feature freeze
- [ ] Run full test matrix (PHPUnit, Jest, E2E, axe)
- [ ] Update CHANGELOG.md
- [ ] Update version in plugin header
- [ ] Update db schema version if needed
- [ ] Write migration scripts if needed

### 2. Documentation Sync
- [ ] Update all relevant docs
- [ ] Add/update code examples
- [ ] Review API documentation
- [ ] Update roadmap if milestones completed

### 3. Build Plugin
```bash
# Run plugin build script (to be created)
./build-plugin.sh 1.0.0
```

### 4. Tag & Push
```bash
git tag -a v1.0.0 -m "Release v1.0.0"
git push origin v1.0.0
```

### 5. Smoke Test
- Install on clean WordPress instance
- Test core functionality
- Verify database migrations
- Test payments in sandbox mode
- Run accessibility checks

### 6. Publish
- Create GitHub Release
- Attach plugin ZIP
- Submit to WordPress.org (if applicable)
- Update documentation site
