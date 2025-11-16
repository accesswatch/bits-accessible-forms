# REST API (bits/v1)

## Auth
- Admin endpoints: nonce + capability checks.
- Public endpoints: read-only form render JSON; submission via nonce & rate limit (added in later sprint).

## Endpoints

### Forms
- GET /forms                → list (filter: status)
- POST /forms               → create (title, schema)
- GET /forms/{id}           → read (schema/settings)
- PUT /forms/{id}           → update
- DELETE /forms/{id}        → trash
- POST /forms/{id}/export   → JSON export
- POST /forms/import        → import JSON (new id or replace)
- POST /forms/{id}/lint     → heading + schema lint results (non-blocking)

### Reorder/Builder
- POST /forms/{id}/reorder  → absolute placements `{ node:{type,id}, destination:{type,id}, placement:'above'|'below' }`
  - Returns updated structure and a move diff for undo.
  - On invalid: returns reason strings (self-target, cross-type, before-first-page) for UI tooltips.

### Entries
- GET /forms/{id}/entries   → list (filters: status, date range, search)
- GET /entries/{id}         → detail
- PUT /entries/{id}         → edit (admin)
- POST /entries/export      → CSV generate (async job with download link)

### Submissions
- POST /forms/{id}/submit   → server validation, pricing/tax, payment, entry create
- POST /forms/{id}/partial  → save draft (token), returns resume URL

### Payments
- POST /entries/{id}/refund → refund (full/partial)
- POST /webhooks/stripe     → Stripe webhooks
- POST /webhooks/paypal     → PayPal webhooks

### Settings/Tax
- GET/PUT /settings         → global (security, retention, logs, captcha)
- GET/PUT /forms/{id}/tax   → per-form tax tables

### Diagnostics
- GET /forms/{id}/trace/{submission_id} → structured submission log
- POST /forms/{id}/test/stripe|paypal   → sandbox test calls

## Errors
- 400: validation errors (include error map `{ fieldId: message }` and/or schema paths)
- 401/403: auth/capability failures
- 409: illegal reorder/delete constraints
- 429: rate limited (Retry-After header)
