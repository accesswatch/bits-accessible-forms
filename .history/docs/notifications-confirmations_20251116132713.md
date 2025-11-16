# Notifications & Confirmations

## Notifications
- Multiple per form; HTML default, text optional; conditional logic supported.
- To/Cc/Bcc routing (static, merge tags); From/Reply-To mapping with SPF/DMARC safety (fallback to site address).
- Attach uploads (size/type checks).
- Suppress on spam or failed payments.
- Test Send endpoint for admins.

## Confirmations
- Default: Text; Additional: Page/Redirect with query-string merge tags.
- Conditional logic supported; progress bar on confirmation optional.
- Evaluation after successful validation and payment when present.

## Merge Tags
- Form/entry/field values; computed tags (totals, tax breakdown, coupon summary).
- Context-aware escaping of HTML (headers vs body vs URL params).
