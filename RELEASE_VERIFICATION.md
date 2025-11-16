# Release Verification Checklist

## v1.0.0 Documentation Release - Smoke Test

**Date:** November 16, 2025  
**Release Package:** bits-accessible-forms-docs-v1.0.0.zip  
**Tester:** _______________

### Pre-Test Setup
- [ ] Downloaded release package from build directory
- [ ] Have a clean temporary directory for extraction
- [ ] Have text editor and markdown viewer available

### Package Integrity

#### File Extraction
- [ ] ZIP file extracts without errors
- [ ] All directories present: docs/, examples/
- [ ] Root files present: README.md, CHANGELOG.md, VERSION, RELEASE_INFO.txt

#### Version Information
- [ ] VERSION file contains: `1.0.0`
- [ ] RELEASE_INFO.txt shows correct version and date
- [ ] README.md shows Version: 1.0.0
- [ ] CHANGELOG.md has [1.0.0] section with date 2025-11-16

### Documentation Completeness

#### Core Documentation Files
- [ ] docs/README.md - Project overview and goals
- [ ] docs/architecture.md - System architecture
- [ ] docs/schema.md - Form schema specification
- [ ] docs/decisions.md - Architectural decisions
- [ ] docs/roadmap.md - Development milestones

#### Feature Documentation
- [ ] docs/a11y.md - Accessibility guidelines
- [ ] docs/security.md - Security model
- [ ] docs/payments.md - Payment integration
- [ ] docs/taxation.md - Tax calculation
- [ ] docs/builder-ui.md - Builder interface
- [ ] docs/renderer-ui.md - Form renderer
- [ ] docs/rest-api.md - API endpoints

#### Process Documentation
- [ ] docs/release-process.md - Release workflow
- [ ] docs/contributing.md - Contribution guidelines
- [ ] docs/diagnostics.md - Diagnostic tools

#### Additional Documentation
- [ ] docs/authoring.md - Form authoring guide
- [ ] docs/db-schema.md - Database schema
- [ ] docs/entries-export.md - Entry export
- [ ] docs/notifications-confirmations.md - Notifications
- [ ] docs/theming.md - Theme customization

### Examples

#### Sample Forms
- [ ] examples/forms/sample.json exists
- [ ] sample.json is valid JSON
- [ ] sample.json contains registration form example
- [ ] Form has payment and tax configuration

### Content Quality

#### README.md (Root)
- [ ] Version number is correct
- [ ] All links work (docs/, CHANGELOG.md, etc.)
- [ ] Project information is accurate
- [ ] Quick links section is helpful
- [ ] Goals and non-goals are clear

#### CHANGELOG.md
- [ ] Version 1.0.0 entry is complete
- [ ] Date is correct (2025-11-16)
- [ ] All added features are listed
- [ ] Documentation structure is described
- [ ] Link format is correct

#### Documentation Files
- [ ] All markdown files render correctly
- [ ] Code blocks are properly formatted
- [ ] Lists and tables display correctly
- [ ] Headings are hierarchical
- [ ] No broken internal references

### Verification Results

#### Package Statistics
- Package size: _______ KB
- Number of files: _______
- Total documentation files: 20
- Example files: 1

#### Issues Found
List any issues discovered during testing:

1. _______________________________________________
2. _______________________________________________
3. _______________________________________________

#### Overall Assessment
- [ ] **PASS** - Release package is ready for distribution
- [ ] **FAIL** - Issues must be resolved before release

#### Notes
_____________________________________________________
_____________________________________________________
_____________________________________________________

#### Sign-off
Tester: _______________  
Date: _______________  
Signature: _______________

---

## Automated Verification Script

For future releases, run this verification script:

```bash
#!/bin/bash
# verify-release.sh - Automated release package verification

VERSION=$1
PACKAGE="bits-accessible-forms-docs-v${VERSION}.zip"

if [ ! -f "$PACKAGE" ]; then
    echo "❌ Package not found: $PACKAGE"
    exit 1
fi

echo "Verifying release package: $PACKAGE"
echo ""

# Extract to temp directory
TEMP_DIR=$(mktemp -d)
unzip -q "$PACKAGE" -d "$TEMP_DIR"
EXTRACT_DIR="$TEMP_DIR/bits-accessible-forms-docs-v${VERSION}"

# Check VERSION file
if [ ! -f "$EXTRACT_DIR/VERSION" ]; then
    echo "❌ VERSION file missing"
    exit 1
fi

VERSION_CONTENT=$(cat "$EXTRACT_DIR/VERSION")
if [ "$VERSION_CONTENT" != "$VERSION" ]; then
    echo "❌ VERSION mismatch: expected $VERSION, got $VERSION_CONTENT"
    exit 1
fi
echo "✅ VERSION file correct"

# Check required files
REQUIRED_FILES=(
    "README.md"
    "CHANGELOG.md"
    "RELEASE_INFO.txt"
    "docs/README.md"
    "docs/architecture.md"
    "docs/schema.md"
    "examples/forms/sample.json"
)

for file in "${REQUIRED_FILES[@]}"; do
    if [ ! -f "$EXTRACT_DIR/$file" ]; then
        echo "❌ Missing required file: $file"
        exit 1
    fi
done
echo "✅ All required files present"

# Count documentation files
DOC_COUNT=$(find "$EXTRACT_DIR/docs" -name "*.md" | wc -l)
if [ "$DOC_COUNT" -lt 20 ]; then
    echo "⚠️  Expected 20+ documentation files, found $DOC_COUNT"
fi
echo "✅ Found $DOC_COUNT documentation files"

# Verify JSON is valid
if ! cat "$EXTRACT_DIR/examples/forms/sample.json" | python3 -m json.tool > /dev/null 2>&1; then
    echo "❌ sample.json is not valid JSON"
    exit 1
fi
echo "✅ sample.json is valid JSON"

# Cleanup
rm -rf "$TEMP_DIR"

echo ""
echo "✅ All verifications passed!"
echo "Release package is ready for distribution."
```

Make the script executable and run:
```bash
chmod +x verify-release.sh
./verify-release.sh 1.0.0
```
