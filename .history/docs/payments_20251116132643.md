# Payments (Stripe & PayPal)

## Scope (v1)
- One-time payments; immediate capture; refunds (full/partial where supported).
- Subscriptions: v1.1.

## Stripe
- Elements + Payment Intents.
- Flow: server creates PaymentIntent (amount from pricing/tax), client confirms, server verifies → entry update.
- Refunds: via REST `/entries/{id}/refund`; log in `bits_payments`.

## PayPal
- Smart Buttons (PayPal Checkout).
- Flow: client creates order, approve, server capture → entry update.
- Refunds: via REST `/entries/{id}/refund`; webhook reconcile.

## Consistency
- Totals computed server-side pre-payment from form values (tax-exclusive default).
- Prevent drift: lock pricing snapshot pre-authorize; re-check on webhook.

## Security
- Nonces; origin checks; redacted logs; accessible error handling.
