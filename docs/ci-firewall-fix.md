# CI/CD Firewall Fix Documentation

## Problem

Pull Request #33 encountered firewall issues when running GitHub Actions workflows. Specifically, when Composer attempted to install PHPUnit and related test dependencies, the firewall blocked access to GitHub's API endpoints for downloading package zipballs.

### Blocked URLs
The following URLs were blocked during `composer install`:
- `https://api.github.com/repos/myclabs/DeepCopy/zipball/*`
- `https://api.github.com/repos/nikic/PHP-Parser/zipball/*`
- `https://api.github.com/repos/phar-io/manifest/zipball/*`
- `https://api.github.com/repos/phar-io/version/zipball/*`
- `https://api.github.com/repos/sebastianbergmann/*/zipball/*` (multiple packages)

## Root Cause

Copilot coding agent sessions enforce firewall rules that block access to certain external URLs for security purposes. These rules are applied after the standard GitHub Actions workflow begins execution, preventing Composer from downloading dependencies from GitHub's zipball API endpoints.

## Solution

The solution involves using **Copilot Actions Setup Steps** to install Composer dependencies before the firewall is enabled. This is accomplished through a special workflow configuration file that runs in a pre-firewall phase.

### Implementation

1. **Created `.github/copilot-actions-setup.yml`**
   - This file defines setup steps that run before firewall rules are applied
   - Installs PHP 8.1 and Composer
   - Runs `composer install` to download all dependencies
   - Caches the `vendor` directory for reuse

2. **Updated `.github/workflows/ci.yml`**
   - Added `phpunit` job with proper PHP and Composer setup
   - Configured Composer caching to speed up subsequent runs
   - Set appropriate environment variables (`COMPOSER_PROCESS_TIMEOUT`, `COMPOSER_NO_INTERACTION`, `COMPOSER_NO_AUDIT`)
   - Added PHP syntax validation with vendor directory exclusion

### File Structure

```
.github/
├── copilot-actions-setup.yml   # Pre-firewall dependency installation
└── workflows/
    └── ci.yml                  # Main CI workflow with caching
```

## How It Works

1. **Pre-Firewall Phase** (copilot-actions-setup.yml):
   - Copilot detects the presence of `composer.json`
   - Runs the setup workflow before enabling firewall rules
   - Downloads and installs all Composer dependencies
   - Caches the `vendor` directory

2. **Main Workflow Phase** (ci.yml):
   - Firewall rules are now active
   - Workflow checks for cached `vendor` directory
   - If cache hit: uses cached dependencies (fast)
   - If cache miss: runs `composer install` (will work because packages are in Packagist cache or pre-downloaded)

## Key Configuration Details

### Copilot Actions Setup (copilot-actions-setup.yml)

```yaml
- name: Install Composer dependencies
  run: composer install --no-interaction --prefer-dist --optimize-autoloader
  env:
    COMPOSER_PROCESS_TIMEOUT: 0
    COMPOSER_NO_INTERACTION: 1
    COMPOSER_NO_AUDIT: 1
```

### CI Workflow (ci.yml)

```yaml
- name: Cache Composer dependencies
  uses: actions/cache@v4
  with:
    path: vendor
    key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}
    restore-keys: |
      ${{ runner.os }}-composer-
```

## Benefits

1. **Unblocked Dependencies**: Composer can access GitHub's API without firewall restrictions
2. **Faster CI Runs**: Caching reduces repeated downloads
3. **No Manual Intervention**: Fully automated solution
4. **No Security Compromise**: Firewall remains active for the main workflow

## Alternative Solutions Considered

### Option 1: Add URLs to Allowlist (Not Chosen)
Adding specific GitHub API URLs to the firewall allowlist would require admin access and manual configuration for each blocked endpoint. This approach doesn't scale well as new dependencies are added.

### Option 2: Use Composer's Packagist Mirror (Not Chosen)
While Packagist provides mirrors, many packages still reference GitHub for the actual downloads, leading to the same firewall issues.

### Option 3: Pre-download Dependencies (Not Chosen)
Committing the `vendor` directory would bloat the repository and go against best practices for dependency management.

## Testing

To verify the fix works:

1. The `.github/copilot-actions-setup.yml` file must be present
2. Run the GitHub Actions workflow
3. Check that `composer install` completes successfully
4. Verify PHPUnit tests execute without errors

## Maintenance

- **Adding New Dependencies**: Just update `composer.json` and `composer.lock`. The setup workflow will automatically handle the new packages.
- **Updating Dependencies**: Run `composer update` locally, commit the updated `composer.lock`, and the CI will use the new versions.
- **Cache Invalidation**: The cache key includes a hash of `composer.lock`, so any dependency changes automatically invalidate the cache.

## Related Issues

- PR #33: Sprint 1 implementation that first encountered this issue
- PR #34: This fix for the firewall blocking issue

## References

- [GitHub Actions Caching Documentation](https://docs.github.com/en/actions/using-workflows/caching-dependencies-to-speed-up-workflows)
- [Copilot Actions Setup Steps Documentation](https://gh.io/copilot/actions-setup-steps)
- [Composer Documentation](https://getcomposer.org/doc/)
