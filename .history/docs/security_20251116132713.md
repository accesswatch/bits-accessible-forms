# Security & Anti-Spam

## Core
- CSRF nonces; capability checks; sanitize/escape everywhere.
- Server-side validation authoritative; skip hidden/disabled fields.
- Honeypot (versioned); submission speed check (default 2000ms, normal mode).
- Rate limiting (default 5/min, 200/day per IP).
- IP logging ON by default (configurable/masked); retention configurable.

## External (optional per form)
- Turnstile/reCAPTCHA v3.
- Akismet content checks.

## Uploads
- Allowed types and max size per field; server-side MIME sniffing; quarantined storage; signed URLs; no direct execution.

## Privacy & Retention (defaults; configurable)
- IP: 365d; Spam: 30d; Traces: 30d; Payment/webhook: 90d.
- Logs redaction ON; admin “reveal once” per view gated by capability.
