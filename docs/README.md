# BITS Accessible Forms

BITS Accessible Forms is a WCAG 2.2 AA–compliant WordPress form builder focused on professional, zero‑config visuals, human‑editable schemas, rich conditional logic (including nested rules), nested data (Repeater + Subform), robust validation, global manual tax tables, Stripe/PayPal payments, strong security, and comprehensive admin diagnostics.

- Slug: bits-accessible-forms
- PHP Namespace: BITS\AccessibleForms
- Text domain: bits-accessible-forms
- REST: /wp-json/bits/v1
- WordPress: ≥ 6.5, PHP: ≥ 8.1

## Goals
- Match Gravity Forms core logic and field coverage (Standard, Advanced, Pricing)
- Human-editable form definitions with hybrid source of truth (DB primary + file sync)
- WCAG 2.2 AA across builder (admin) and renderer (user)
- Zero-configuration professional visuals; theming via tokens
- Payments: Stripe & PayPal one-time, immediate capture, refunds
- Global manual tax tables (per-form currency, coupons, tax-exclusive default)
- Powerful conditional logic (nested groups, show/hide, disable/read-only, toggle required)
- Nested data: Repeater (inline) and Subform (child entries)
- Secure-by-default with rich diagnostics, safe mode, and logs

## Non-goals (v1)
- Subscriptions (Stripe/PayPal) — v1.1
- Provider tax engines (Stripe Tax/TaxJar/Avalara) — v1.1
- Post fields — v1.1
- Grid layout builder (full n‑column) — v1.1

See decisions in `decisions.md` and milestones in `roadmap.md`.
