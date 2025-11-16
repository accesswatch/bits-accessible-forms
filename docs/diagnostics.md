# Diagnostics & Debugging

## Safe Mode (per form)
- Admin-only; disables conditional logic/animations; renders all fields; aids troubleshooting.

## Logic Debug Overlay
- Live evaluation state; shows rules/groups, outcomes, source values; copyable JSON; console structured logs (admin only).

## Payments Test Mode
- Stripe test keys; PayPal sandbox; simulate declines; webhook test panel.

## Submission Trace Logs
- Capture: speed check timing, rate limit state, spam flags, field validation results, visibility, pricing/tax snapshot, gateway responses (redacted).
- Admin viewer; export JSON; retention default 30d (configurable).

## Feature Flags
- Toggle specific protections/animations/logic at runtime for troubleshooting; not persisted to schema.

## PII Redaction
- Default ON; per-view reveal for admins; never logs raw card data.
