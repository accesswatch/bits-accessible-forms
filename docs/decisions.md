# Decisions

## Platform/Identity
- WP ≥ 6.5; PHP ≥ 8.1
- Slug: bits-accessible-forms; Namespace: BITS\AccessibleForms; REST: bits/v1

## Data/Storage
- Custom tables (forms/entries/meta/drafts/payments)
- Hybrid source: DB primary + file import/export/sync

## Field Scope (v1)
- Standard + Advanced + Pricing (Post fields v1.1)

## Logic
- Nested rule groups; actions: show/hide, disable/read-only, toggle required
- Targets: fields, sections, pages, Next/Prev, notifications/confirmations

## Nested Data
- Repeater (inline) + Subform (child entries linked to parent)

## Payments
- Stripe + PayPal, one-time, immediate capture, refunds enabled

## Pricing & Tax
- Per-form currency; coupons; global manual tax tables
- Default tax price mode: tax-exclusive
- Billing address from our form; required: country + region/state + postal (+ city where needed)
- B2B tax IDs: deferred v1.1
- Global scope

## Accessibility
- WCAG 2.2 AA required; error summary with jump links; 24×24 targets; focus-not-obscured

## Layout
- Two-column responsive; grid later

## Headings
- Auto-outline with per-section override; save-time linting

## Pagination
- Steps indicator; block Next on invalid; button logic allowed; top error summary

## Security
- Enable all: CSRF, server validation, honeypot, speed check, rate limit, IP logging (configurable), MIME checks
- Optional per-form: Turnstile/reCAPTCHA, Akismet

## Diagnostics
- Safe mode; logic overlay; payments test mode; submission trace logs; feature flags
- Retention defaults: IP 365d; spam 30d; traces 30d; payment logs 90d; log redaction ON

## Identity/Names
- REST `bits/v1`; block `bits/form`; DB prefix `bits_`
