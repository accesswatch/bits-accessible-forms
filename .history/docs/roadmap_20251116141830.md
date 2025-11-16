# Roadmap & Milestones

## M1: Data Model & Import/Export
- Deliverables: DB migrations; schema.json; forms CRUD; JSON export/import.
- Acceptance: create→export→import reproduces form identical; unit tests.

## M2: Builder Canvas (MVP)
- Field library; two-column layout; drag + keyboard + buttons reorder; sections/pages; field settings.
- Acceptance: build a two-page form with sections + 5 fields entirely via UI.

## M3: Logic & Validation
- Nested logic groups; actions (show/hide/disable/toggle required); client/server evaluator; per-field validators.
- Acceptance: hidden fields not validated/stored; logic parity UI/server.

## M4: Renderer & Submission
- Accessible markup; steps progress; top error summary with jump links; Save & Continue; partial entries.
- Acceptance: keyboard-only submit succeeds; error summary navigable.

## M5: Pricing, Coupons, Manual Tax
- Product/Quantity/Options/Total; coupons; tax-exclusive calc with manual tables; global scope.
- Acceptance: totals match expectations across US/EU examples.

## M6: Payments (Stripe/PayPal) + Refunds
- Elements + Payment Intents; Smart Buttons; refunds; webhooks; logs.
- Acceptance: successful/failed/refunded paths; amounts reconcile; logs populated.

## M7: Entries, Export & GDPR
- Entries list/detail/spam; CSV export with pricing/tax; repeaters JSON; subforms separate CSV; WP Export/Erase.
- Acceptance: CSVs correct; GDPR tools include/erase flagged data.

## M8: Security & Diagnostics
- CSRF, honeypot, speed check, rate limit; Turnstile/Akismet toggles; safe mode; logic overlay; trace logs.
- Acceptance: automated tests for spam/rate limits; diagnostics usable.

## M9: Theming & WCAG Gates
- Tokens + form.css; Stripe/PayPal harmony; axe/Lighthouse CI; visual diffs.
- Acceptance: pass WCAG 2.2 AA checks; theming does not break contrast/targets.

## M10: Polish & Docs
- Authoring guide; admin help; examples; stability fixes; performance profiling.
- Acceptance: doc set complete; E2E suite stable; release notes ready.

## Builder Acceptance Addenda
- Presets: Yes/No, True/False, Weekdays, Months, US States/DC, Countries ISO‑3166, Timezones, Likert; preview; Append/Replace; unique values; label-as-value/official codes toggles; JSON import v1.1.
- Delete safeguards: hard blocks (only Submit/Page); move-children flow; references listing with Auto-clean/Review; disabled with reason until constraints satisfied; undo; focus return.
- Multi-select delete (optional v1): single confirm; one undo step; aria-live announces count.
- Tabs per type: appear as defined; keyboard reachable; SR-labelled; lint warnings link to controls; no serious axe violations; targets ≥24×24; focus visible and not obscured.
