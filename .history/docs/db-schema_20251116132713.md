# Database Schema

Prefix: `bits_`

## bits_forms
- id (BIGINT PK)
- title (VARCHAR 255)
- slug (VARCHAR 191 UNIQUE)
- status (ENUM 'active','inactive','trash')
- schema_json (LONGTEXT)  // canonical form JSON
- settings_json (LONGTEXT)
- next_field_id (INT)
- created_at (DATETIME UTC)
- updated_at (DATETIME UTC)
- version (VARCHAR 32)

## bits_entries
- id (BIGINT PK)
- form_id (BIGINT INDEX)
- status (ENUM 'active','spam','trash','failed')
- user_id (BIGINT NULL)
- user_email (VARCHAR 320 NULL)
- ip_hash (VARBINARY 64)
- ip_plain (VARBINARY 48 NULL) // gated by privacy setting
- user_agent (TEXT NULL)
- source_url (TEXT NULL)
- currency (CHAR 3)
- subtotal (DECIMAL 20,6)
- discount_total (DECIMAL 20,6)
- tax_total (DECIMAL 20,6)
- shipping_total (DECIMAL 20,6)
- grand_total (DECIMAL 20,6)
- coupon_code (VARCHAR 64 NULL)
- payment_status (ENUM 'none','authorized','captured','refunded','failed')
- gateway (ENUM 'stripe','paypal','none')
- transaction_id (VARCHAR 191 NULL)
- metadata_json (LONGTEXT) // snapshot: pricing/tax calc, visibility map
- created_at (DATETIME UTC)
- updated_at (DATETIME UTC)

## bits_entry_meta
- id (BIGINT PK)
- entry_id (BIGINT INDEX)
- field_id (VARCHAR 32) // supports multi-input like 3.2
- value_long (LONGTEXT NULL)
- value_num (DECIMAL 20,6 NULL)
- value_text (TEXT NULL)
- INDEX(field_id), INDEX(entry_id, field_id)

## bits_drafts
- id (BIGINT PK)
- form_id (BIGINT INDEX)
- token (CHAR 64 UNIQUE)
- values_json (LONGTEXT)
- expires_at (DATETIME UTC)
- created_at (DATETIME UTC)

## bits_payments
- id (BIGINT PK)
- entry_id (BIGINT INDEX)
- gateway (ENUM 'stripe','paypal')
- event (VARCHAR 64) // 'created','captured','refunded','webhook.*'
- amount (DECIMAL 20,6)
- currency (CHAR 3)
- payload_json (LONGTEXT) // redacted
- created_at (DATETIME UTC)

## Retention & Privacy
- Scheduled tasks purge spam after X days, traces after Y, payments logs after Z.
- IP handling via settings: store masked only vs store plain (gated).

## Migrations
- Store schema version; forward-only migrations with rollback guidance.
