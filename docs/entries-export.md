# Entries & Export

## Entries
- List with filters (status/date/search); spam queue; partials.
- Entry detail: values, pricing/tax breakdown, notes, re-send notifications.
- Edit entries (admin).

## Export (CSV)
- Core columns: entry_id, form_id, created_at (UTC ISO), status, user_id/email, ip(masked), source_url, payment_status, transaction_id.
- Field columns: one per field; multi-input split (e.g., name_first, name_last).
- Pricing: currency, subtotal, discount_total, tax_total, shipping_total, grand_total, coupon_code.
- Tax detail: tax_rate, tax_region, taxable_amount.
- Repeaters: JSON in single cell per repeater.
- Subforms: separate CSV with parent_entry_id foreign key.

## GDPR
- WP Export/Erase integration with per-field flags.
