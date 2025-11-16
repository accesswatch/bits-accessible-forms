#!/bin/bash
# Release Build Script for BITS Accessible Forms Documentation
# This script creates a release package of the documentation

set -e

# Configuration
VERSION=${1:-"1.0.0"}
BUILD_DIR="build"
RELEASE_NAME="bits-accessible-forms-docs-v${VERSION}"
RELEASE_DIR="${BUILD_DIR}/${RELEASE_NAME}"
ZIP_FILE="${BUILD_DIR}/${RELEASE_NAME}.zip"

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}================================${NC}"
echo -e "${BLUE}BITS Accessible Forms Docs Build${NC}"
echo -e "${BLUE}Version: ${VERSION}${NC}"
echo -e "${BLUE}================================${NC}"
echo ""

# Clean and create build directory
echo -e "${GREEN}[1/5] Cleaning build directory...${NC}"
rm -rf "${BUILD_DIR}"
mkdir -p "${RELEASE_DIR}"

# Copy documentation files
echo -e "${GREEN}[2/5] Copying documentation files...${NC}"
cp -r docs "${RELEASE_DIR}/"
cp -r examples "${RELEASE_DIR}/"
cp README.md "${RELEASE_DIR}/"
cp CHANGELOG.md "${RELEASE_DIR}/"

# Create VERSION file
echo -e "${GREEN}[3/5] Creating VERSION file...${NC}"
echo "${VERSION}" > "${RELEASE_DIR}/VERSION"

# Create release info file
echo -e "${GREEN}[4/5] Creating release info...${NC}"
cat > "${RELEASE_DIR}/RELEASE_INFO.txt" << EOF
BITS Accessible Forms Documentation
Version: ${VERSION}
Release Date: $(date +"%Y-%m-%d")
Build Date: $(date +"%Y-%m-%d %H:%M:%S %Z")

Contents:
- docs/          Complete documentation set
- examples/      Sample form definitions
- README.md      Project overview and quick links
- CHANGELOG.md   Version history and changes
- VERSION        Version number

For the latest documentation and updates, visit:
https://github.com/accesswatch/bits-accessible-forms
EOF

# Create ZIP archive
echo -e "${GREEN}[5/5] Creating ZIP archive...${NC}"
cd "${BUILD_DIR}"
zip -r "${RELEASE_NAME}.zip" "${RELEASE_NAME}" > /dev/null
cd ..

# Summary
echo ""
echo -e "${BLUE}================================${NC}"
echo -e "${GREEN}✓ Build completed successfully!${NC}"
echo -e "${BLUE}================================${NC}"
echo ""
echo "Release package: ${ZIP_FILE}"
echo "Size: $(du -h "${ZIP_FILE}" | cut -f1)"
echo ""
echo "Contents:"
unzip -l "${ZIP_FILE}" | head -20
echo ""
echo "To test the release:"
echo "  1. Extract: unzip ${ZIP_FILE}"
echo "  2. Review: cd ${RELEASE_NAME} && cat README.md"
echo "  3. Verify: check all documentation files are present"
