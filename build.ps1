# Prereqs:
# - gh CLI installed and authenticated
# - Repo AccessWatch/bits-accessible-forms exists and you have write access

# Safety: show target repo
gh repo view AccessWatch/bits-accessible-forms --json name, owner, url | ConvertTo-Json

# 1) Labels (idempotent)
$labels = @(
    @{n = 'Infra'; c = '5319E7'; d = 'Infrastructure/CI' },
    @{n = 'API'; c = '0052CC'; d = 'Public REST API' },
    @{n = 'Backend'; c = '0E8A16'; d = 'PHP services/db' },
    @{n = 'Admin-UI'; c = '1D76DB'; d = 'Builder app (admin)' },
    @{n = 'Renderer'; c = 'BFD4F2'; d = 'Front-end render (user)' },
    @{n = 'Payments'; c = '5319E7'; d = 'Stripe/PayPal' },
    @{n = 'Tax'; c = 'B60205'; d = 'Manual tax rules' },
    @{n = 'Security'; c = 'D93F0B'; d = 'CSRF/rate/spam/uploads' },
    @{n = 'Diagnostics'; c = 'C2E0C6'; d = 'Safe mode/overlay/traces' },
    @{n = 'A11y'; c = '5319E7'; d = 'Accessibility/WCAG' },
    @{n = 'Tests'; c = 'F9D0C4'; d = 'Unit/Integration/E2E' },
    @{n = 'Perf'; c = '5319E7'; d = 'Performance budgets' },
    @{n = 'Docs'; c = '0E8A16'; d = 'Documentation' },
    @{n = 'Feature'; c = '1D76DB'; d = 'New feature' },
    @{n = 'Bug'; c = 'D73A4A'; d = 'Defect' }
)
foreach ($l in $labels) {
    try { gh label create $l.n --color $l.c --description $l.d 2>$null | Out-Null } catch {}
}

# 2) Milestones (idempotent)
$milestones = @(
    'Sprint 1: DB & Forms REST',
    'Sprint 2: Reorder & Jump Move',
    'Sprint 3: Pricing & Tax',
    'Sprint 4: Payments & Refunds',
    'Sprint 5: Entries & Export',
    'Sprint 6: Security & Uploads',
    'Sprint 7: Diagnostics',
    'Sprint 8: Theming & WCAG',
    'Sprint 9: Notifications & Confirms',
    'Sprint 10: Polish & Release'
)
foreach ($m in $milestones) { try { gh milestone create $m 2>$null | Out-Null } catch {} }

function New-Issue {
    param(
        [string]$Title,
        [string]$Body,
        [string[]]$Labels,
        [string]$Milestone
    )
    # Combine labels into a comma-separated string (gh supports multiple -l)
    $labelString = ($Labels -join ",")
    gh issue create `
        -R AccessWatch/bits-accessible-forms `
        -t $Title `
        -b $Body `
        -l $labelString `
        -m $Milestone
}

# 3) Sprint 1 issues
$ms1 = 'Sprint 1: DB & Forms REST'

New-Issue -Title 'chore(infra): composer autoload + plugin bootstrap' -Labels @('Infra', 'Backend') -Milestone $ms1 -Body @"
Add Composer PSR-4 autoload for inc/ and minimal plugin bootstrap.
Acceptance:
- Composer autoload maps BITS\AccessibleForms\ to inc/
- bits-accessible-forms.php loads without fatal on WP ≥ 6.5
- Activation hook wired (no-ops for now)
"@

New-Issue -Title 'feat(db): create migrations for bits_forms (+ schema version)' -Labels @('Backend', 'Infra', 'Feature', 'Tests') -Milestone $ms1 -Body @"
Create bits_forms table (id, title, slug, status, schema_json, settings_json, next_field_id, timestamps) and persist schema version option.
Acceptance:
- Activation creates tables; re-activation no-op
- Schema version option stored
- PHPUnit migration/install test passes
"@

New-Issue -Title 'feat(rest): Forms CRUD (bits/v1)' -Labels @('API', 'Backend', 'Feature', 'Tests') -Milestone $ms1 -Body @"
Endpoints: GET/POST/GET{id}/PUT/DELETE /forms with caps+nonce checks.
- Validate schema/form.schema.json on write
Acceptance:
- CRUD integration tests pass
- Invalid schema yields error list (paths + messages)
- Capabilities and nonce enforced
"@

New-Issue -Title 'feat(rest): forms import/export' -Labels @('API', 'Backend', 'Feature', 'Tests') -Milestone $ms1 -Body @"
POST /forms/{id}/export, POST /forms/import (create/replace).
Acceptance:
- Export→Import round-trip keeps schema/settings identical
- New id assigned on create
- Integration tests pass
"@

New-Issue -Title 'feat(rest): lint endpoint (headings)' -Labels @('API', 'Backend', 'Feature') -Milestone $ms1 -Body @"
POST /forms/{id}/lint returns heading-level warnings and suggestions (non-blocking).
Acceptance:
- Known bad samples produce expected warnings
- Good samples return none
"@

New-Issue -Title 'feat(render): minimal shortcode/block' -Labels @('Renderer', 'Feature', 'A11y', 'Tests') -Milestone $ms1 -Body @"
Shortcode [bits_form id=\"...\"] and block registration; semantic wrapper (title/description) + client bootstrap stub.
Acceptance:
- Renders without console errors
- axe: no serious violations on minimal form
"@

New-Issue -Title 'test(ci): PHPUnit + REST baseline, basic axe gate' -Labels @('Infra', 'Tests') -Milestone $ms1 -Body @"
Configure PHPUnit and GitHub Actions CI; run REST CRUD tests and basic axe on minimal render page.
Acceptance:
- CI green on PR
- Tests execute in GH Actions
"@

# 4) Sprint 2 issues
$ms2 = 'Sprint 2: Reorder & Jump Move'

New-Issue -Title 'feat(rest): reorder endpoint (above/below absolute)' -Labels @('API', 'Backend', 'Feature', 'Tests') -Milestone $ms2 -Body @"
POST /forms/{id}/reorder with {node,destination,placement}.
Acceptance:
- Returns updated structure + undo token + move diff
- Disallowed moves return reasons (self, cross-type, before-first-page)
- Integration tests cover legal/illegal cases
"@

New-Issue -Title 'feat(admin-ui): Jump Move (combobox + Place Above/Below)' -Labels @('Admin-UI', 'A11y', 'Feature', 'Tests') -Milestone $ms2 -Body @"
Inspector 'Move' tab: ARIA 1.2 combobox grouped results; Place Above/Below buttons.
Acceptance:
- Keyboard: Alt+M, type-to-filter, Enter select, Ctrl+Enter Above, Ctrl+Shift+Enter Below
- SR announcements; disabled states show reasons
- Virtualized list performs ≤100ms at 500+ nodes
"@

New-Issue -Title 'feat(admin-ui): drag + keyboard reorder + undo/redo' -Labels @('Admin-UI', 'A11y', 'Feature') -Milestone $ms2 -Body @"
Space pick, Arrow move, Enter drop; buttons Move Up/Down; 20-step history with announcements.
Acceptance:
- Works across fields/sections/pages
- aria-live for pick/drop/undo/redo
"@

New-Issue -Title 'feat(client): logic evaluator + visibility map' -Labels @('Renderer', 'Feature', 'Tests') -Milestone $ms2 -Body @"
Nested groups; operators parity; visibility map drives tab order and storage skipping.
Acceptance:
- Client/server evaluator parity on sample forms
"@

New-Issue -Title 'feat(renderer): error summary + jump links' -Labels @('Renderer', 'A11y', 'Feature', 'Tests') -Milestone $ms2 -Body @"
Top summary with links to invalid fields; aria-live; skip hidden.
Acceptance:
- Keyboard focus moves to first invalid; links navigate without trapping
- axe passes
"@

# 5) Sprint 3 issues
$ms3 = 'Sprint 3: Pricing & Tax'

New-Issue -Title 'feat(service): pricing engine (products/options/qty/total)' -Labels @('Backend', 'Feature', 'Tests') -Milestone $ms3 -Body @"
Compute line items and subtotal; rounding policy; currency format.
Acceptance:
- Unit tests for combinations pass
"@

New-Issue -Title 'feat(service): coupons (percent/amount; before tax)' -Labels @('Backend', 'Feature', 'Tests') -Milestone $ms3 -Body @"
Apply coupons before tax with scope and limits.
Acceptance:
- Unit tests cover stacking rules and invalid coupons
"@

New-Issue -Title 'feat(service): manual tax (exclusive default)' -Labels @('Backend', 'Tax', 'Feature', 'Tests') -Milestone $ms3 -Body @"
Resolve tax by region; nexus; line vs total rounding; taxable targets.
Acceptance:
- US/EU sample cases match expected totals
"@

New-Issue -Title 'feat(renderer): totals display (currency)' -Labels @('Renderer', 'Feature') -Milestone $ms3 -Body @"
Render subtotal/discount/tax/shipping/grand total; consistent formatting.
Acceptance:
- Values match server snapshot
"@

# 6) Sprint 4 issues
$ms4 = 'Sprint 4: Payments & Refunds'

New-Issue -Title 'feat(payments): Stripe (Elements/Intents) immediate capture' -Labels @('Payments', 'Backend', 'Renderer', 'Security', 'Tests') -Milestone $ms4 -Body @"
Create/confirm PaymentIntent; lock snapshot; capture + log.
Acceptance:
- Happy/failure paths covered; errors accessible; logs redacted
"@

New-Issue -Title 'feat(payments): PayPal (Smart Buttons) immediate capture' -Labels @('Payments', 'Backend', 'Renderer', 'Security', 'Tests') -Milestone $ms4 -Body @"
Create/approve/capture order; log events; reconcile.
Acceptance:
- Happy/failure paths covered; sandbox verified
"@

New-Issue -Title 'feat(payments): refunds + webhooks reconcile' -Labels @('Payments', 'Backend', 'Tests') -Milestone $ms4 -Body @"
REST refunds; webhook reconciliation for Stripe/PayPal; status transitions.
Acceptance:
- Refunds recorded; entries updated; logs captured
"@

# 7) Sprint 5 issues
$ms5 = 'Sprint 5: Entries & Export'

New-Issue -Title 'feat(admin): entries list/detail/spam/notes/edit/resend' -Labels @('Admin-UI', 'Backend', 'Feature', 'Tests') -Milestone $ms5 -Body @"
Entries UI with filters and spam queue; resend notifications.
Acceptance:
- CRUD actions and spam flow covered
"@

New-Issue -Title 'feat(export): CSV (fields/pricing/tax/repeaters/subforms)' -Labels @('Backend', 'Feature', 'Tests') -Milestone $ms5 -Body @"
Export core columns; split multi-input; pricing/tax; repeaters JSON; subforms separate CSV with parent id.
Acceptance:
- CSV structure validated in tests
"@

New-Issue -Title 'feat(gdpr): WP Export/Erase integration' -Labels @('Backend', 'Feature', 'Tests') -Milestone $ms5 -Body @"
Implement export/erase for flagged fields.
Acceptance:
- WordPress privacy tools work end-to-end
"@

# 8) Sprint 6 issues
$ms6 = 'Sprint 6: Security & Uploads'

New-Issue -Title 'feat(security): nonces, rate limiter, honeypot, speed check' -Labels @('Security', 'Backend', 'Tests') -Milestone $ms6 -Body @"
Enforce security across endpoints and submission.
Acceptance:
- 401/403/429 behaviors verified; spam flags applied
"@

New-Issue -Title 'feat(uploads): policy, MIME sniff, quarantine, signed URLs' -Labels @('Security', 'Backend', 'Tests') -Milestone $ms6 -Body @"
Upload controls and secure serving strategy.
Acceptance:
- Disallowed types rejected; signed URLs function; no executable paths
"@

# 9) Sprint 7 issues
$ms7 = 'Sprint 7: Diagnostics'

New-Issue -Title 'feat(diagnostics): safe mode + logic overlay' -Labels @('Diagnostics', 'Admin-UI', 'A11y', 'Feature', 'Tests') -Milestone $ms7 -Body @"
Admin-only safe mode; overlay shows rule evaluations.
Acceptance:
- Keyboard/SR accessible; does not interfere with editing
"@

New-Issue -Title 'feat(diagnostics): submission trace logs + viewer/export + retention' -Labels @('Diagnostics', 'Backend', 'Tests') -Milestone $ms7 -Body @"
Capture validation/visibility/pricing/gateway (redacted); retention jobs.
Acceptance:
- Viewer renders; JSON export; purge jobs work
"@

# 10) Sprint 8 issues
$ms8 = 'Sprint 8: Theming & WCAG'

New-Issue -Title 'feat(theme): tokens.css + form.css + payments harmony' -Labels @('Renderer', 'Admin-UI', 'A11y', 'Feature') -Milestone $ms8 -Body @"
Design tokens and default theme; Stripe/PayPal appearance.
Acceptance:
- Contrast passes; focus visible; target sizes ≥ 24x24
"@

New-Issue -Title 'test(a11y): axe/Lighthouse CI + visual diffs' -Labels @('Tests', 'A11y', 'Perf', 'Infra') -Milestone $ms8 -Body @"
Axe gates across key screens; Lighthouse a11y ≥ 95; Playwright visual baselines.
Acceptance:
- CI gates enforced on PRs
"@

# 11) Sprint 9 issues
$ms9 = 'Sprint 9: Notifications & Confirms'

New-Issue -Title 'feat(admin): notifications editor + test send' -Labels @('Admin-UI', 'Backend', 'API', 'Security', 'Feature', 'Tests') -Milestone $ms9 -Body @"
Routing (To/Cc/Bcc), From/Reply-To safety, attach uploads, conditional logic, test send endpoint.
Acceptance:
- Suppressed on spam/failed; merge tags escaped by context
"@

New-Issue -Title 'feat(admin): confirmations editor (Text/Page/Redirect) + preview' -Labels @('Admin-UI', 'Backend', 'API', 'Feature', 'Tests') -Milestone $ms9 -Body @"
Conditional confirmations; query-string builder; preview endpoint (no redirect).
Acceptance:
- Correct confirmation selected after payment when present
"@

# 12) Sprint 10 issues
$ms10 = 'Sprint 10: Polish & Release'

New-Issue -Title 'chore(perf): cache form JSON, lazy payments, DB indexes' -Labels @('Perf', 'Backend') -Milestone $ms10 -Body @"
Optimize critical paths; add indexes (form_id, created_at, status).
Acceptance:
- Perf budgets met for Jump Move filter ≤100ms at 500+ nodes
"@

New-Issue -Title 'docs: sync and freeze + release packaging' -Labels @('Docs', 'Infra') -Milestone $ms10 -Body @"
Sync decisions/schema/REST/UX; changelog; tag; zip; smoke test on clean site.
Acceptance:
- Release build verified
"@

Write-Host "`nAll labels, milestones, and issues have been created (check GitHub)."
