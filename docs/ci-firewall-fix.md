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

The solution involves a **multi-layered approach** combining Copilot Actions Setup Steps with firewall allowlist configuration to ensure Composer can install dependencies both before and after the firewall is enabled.

### Implementation

1. **Created `.github/copilot-firewall-allowlist.txt`**
   - Documents all domains required for Composer operations
   - Serves as reference for COPILOT_AGENT_FIREWALL_ALLOW_LIST_ADDITIONS
   - Includes: api.github.com, codeload.github.com, github.com

2. **Added Firewall Allowlist Environment Variable**
   - Set `COPILOT_AGENT_FIREWALL_ALLOW_LIST_ADDITIONS` in both workflows
   - Allows Composer to access GitHub API endpoints even after firewall is enabled
   - Format: comma-separated list of domains (no protocols, no paths)

3. **Created `.github/copilot-actions-setup.yml`**
   - This file defines setup steps that run before firewall rules are applied
   - Installs PHP 8.1 and Composer
   - Configures GitHub OAuth token for higher rate limits
   - Runs `composer install` to download all dependencies
   - Caches the `vendor` directory for reuse

4. **Updated `.github/workflows/ci.yml`**
   - Added `phpunit` job with proper PHP and Composer setup
   - Added firewall allowlist environment variable
   - Configured GitHub OAuth token authentication
   - Configured Composer caching to speed up subsequent runs
   - Set appropriate environment variables (`COMPOSER_PROCESS_TIMEOUT`, `COMPOSER_NO_INTERACTION`, `COMPOSER_NO_AUDIT`)
   - Added PHP syntax validation with vendor directory exclusion

### File Structure

```
.github/
├── copilot-firewall-allowlist.txt  # Allowlist documentation
├── copilot-actions-setup.yml       # Pre-firewall dependency installation
└── workflows/
    └── ci.yml                      # Main CI workflow with allowlist config
```

## How It Works

1. **Allowlist Configuration Phase**:
   - `COPILOT_AGENT_FIREWALL_ALLOW_LIST_ADDITIONS` environment variable is set globally
   - Adds GitHub domains (api.github.com, codeload.github.com, github.com) to the firewall allowlist
   - These domains remain accessible throughout the entire workflow execution

2. **Pre-Firewall Phase** (copilot-actions-setup.yml):
   - Copilot detects the presence of `composer.json`
   - Runs the setup workflow before enabling firewall rules
   - Configures GitHub OAuth token for authenticated API access
   - Downloads and installs all Composer dependencies
   - Caches the `vendor` directory

3. **Main Workflow Phase** (ci.yml):
   - Firewall rules are now active, but allowlisted domains remain accessible
   - Workflow checks for cached `vendor` directory
   - If cache hit: uses cached dependencies (fast)
   - If cache miss: runs `composer install` (succeeds due to allowlist)
   - GitHub token authentication provides higher rate limits

## Key Configuration Details

### Firewall Allowlist (.github/copilot-firewall-allowlist.txt)

```text
# GitHub API endpoints for package downloads
api.github.com
codeload.github.com
github.com
```

### Environment Variable Configuration

Both workflows include:

```yaml
env:
  COPILOT_AGENT_FIREWALL_ALLOW_LIST_ADDITIONS: api.github.com,codeload.github.com,github.com
```

### GitHub Token Authentication

```yaml
- name: Configure Composer GitHub token
  run: |
    if [ -n "${{ secrets.GITHUB_TOKEN }}" ]; then
      composer config -g github-oauth.github.com ${{ secrets.GITHUB_TOKEN }}
    fi
```

### Copilot Actions Setup (copilot-actions-setup.yml)

```yaml
- name: Install Composer dependencies
  run: composer install --no-interaction --prefer-dist --optimize-autoloader
  env:
    COMPOSER_PROCESS_TIMEOUT: 0
    COMPOSER_NO_INTERACTION: 1
    COMPOSER_NO_AUDIT: 1
    GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
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

1. **Unblocked Dependencies**: Composer can access GitHub's API through firewall allowlist
2. **Higher Rate Limits**: GitHub token authentication provides 5,000 requests/hour vs 60 anonymous
3. **Faster CI Runs**: Caching reduces repeated downloads
4. **No Manual Intervention**: Fully automated solution
5. **Layered Protection**: Pre-firewall setup + allowlist ensures reliability
6. **No Security Compromise**: Firewall remains active with controlled exceptions

## Alternative Solutions Considered

### Option 1: Allowlist Only (Not Sufficient Alone)
Adding specific GitHub API URLs to the firewall allowlist helps but doesn't solve rate limiting issues. Combined with pre-firewall setup, it provides comprehensive coverage.

**Why we use both approaches:**
- Allowlist ensures access during main workflow execution
- Pre-firewall setup provides cache warming and avoids race conditions
- GitHub token authentication prevents rate limiting

### Option 2: Use Composer's Packagist Mirror (Not Chosen)
While Packagist provides mirrors, many packages still reference GitHub for the actual downloads, leading to the same firewall issues.

### Option 3: Pre-download Dependencies (Not Chosen)
Committing the `vendor` directory would bloat the repository and go against best practices for dependency management.

## Testing

To verify the fix works:

1. The `.github/copilot-firewall-allowlist.txt` file must be present
2. The `COPILOT_AGENT_FIREWALL_ALLOW_LIST_ADDITIONS` environment variable must be set in workflows
3. Run the GitHub Actions workflow
4. Check that `composer install` completes successfully without firewall blocks
5. Verify PHPUnit tests execute without errors
6. Confirm no blocked URL warnings in workflow logs

### Manual Testing

```bash
# Test locally with firewall simulation
export COPILOT_AGENT_FIREWALL_ALLOW_LIST_ADDITIONS="api.github.com,codeload.github.com,github.com"
composer install --no-interaction --prefer-dist

# Verify dependencies installed
ls -la vendor/
vendor/bin/phpunit --version
```

## Maintenance

- **Adding New Dependencies**: Just update `composer.json` and `composer.lock`. The setup workflow will automatically handle the new packages.
- **Updating Dependencies**: Run `composer update` locally, commit the updated `composer.lock`, and the CI will use the new versions.
- **Cache Invalidation**: The cache key includes a hash of `composer.lock`, so any dependency changes automatically invalidate the cache.

## Related Issues

- PR #33: Sprint 1 implementation that first encountered this issue
- PR #34: Initial fix using pre-firewall setup only
- Current PR: Enhanced fix with allowlist configuration and GitHub token auth

## References

- [GitHub Actions Caching Documentation](https://docs.github.com/en/actions/using-workflows/caching-dependencies-to-speed-up-workflows)
- [Copilot Actions Setup Steps Documentation](https://gh.io/copilot/actions-setup-steps)
- [Copilot Firewall Allowlist Reference](https://docs.github.com/en/copilot/reference/allowlist-reference)
- [Customizing Copilot Agent Firewall](https://docs.github.com/en/copilot/how-tos/use-copilot-agents/coding-agent/customize-the-agent-firewall)
- [Composer Documentation](https://getcomposer.org/doc/)
- [Composer GitHub OAuth](https://getcomposer.org/doc/articles/authentication-for-private-packages.md#github-oauth)
