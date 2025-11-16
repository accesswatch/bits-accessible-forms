# Architecture

## Components
- Admin Builder (React, @wordpress/components): field library, canvas, settings panels, Jump Move tab; nested logic editor; undo/redo; linting; diagnostics.
- Renderer (server markup + client enhancement): shortcode and block; semantic HTML; client logic/pagination/calculations; file uploads; AJAX.
- Core Services (PHP): validation, logic evaluation, pricing/tax/coupons, submissions, notifications/confirmations, entries, privacy.
- Storage: custom DB tables + JSON exports; hybrid sync.
- Payments: Stripe Elements + Payment Intents; PayPal Smart Buttons; refund handlers; webhooks.
- Security: CSRF, validation, anti-spam; rate limiting; privacy.
- Diagnostics: trace logs; safe mode; logic overlay.
- REST API: forms CRUD, reorder, submissions, entries/list/export, refunds, tax settings, diagnostics.

## Key Flows
1) Render form → client logic bootstrap → user input → submit → security checks → server validation & logic re-eval → pricing/tax → gateway call → entry create → notifications/confirmations → logs.
2) Builder changes → autosave draft → undo/redo → validate schema/lint headings → save new version → file sync (optional).

## Extensibility
- Hooks/actions modeled after GF: `pre_render_form`, `pre_validate_form`, `field_validation`, `pre_submission`, `after_submission`, `pre_send_notification`, `entry_is_spam`.
- Add-on feed system scaffolded for v1.1.

## Performance
- Cache form JSON; enqueue only used assets; debounce logic recompute; lazy load payments widgets; server-side pagination for entries; indexes on `(form_id, created_at, status)`.

## Security Model
- Capabilities: `manage_bits_forms`, `edit_bits_forms`, `view_bits_entries`, `export_bits_entries`.
- CSRF with nonces on admin and front-end actions; strict sanitize/escape; server validation authoritative.
