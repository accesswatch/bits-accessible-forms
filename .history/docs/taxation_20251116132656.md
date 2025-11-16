# Taxation (Manual Global Rates)

## Scope
- Per-form currency; tax-exclusive default (form override allowed).
- Manual rules by region (country/state/ZIP), global scope.
- Address source: billing address from our form; required: country + region/state + postal (+ city where needed).
- B2B tax IDs: v1.1.

## Rules
- Rule: `{ region: {country, state?, zip?}, rate: number, targets: ['products','options','shipping?'], shippingTaxable: bool }`.
- Nexus: declare where you collect (countries/regions).
- Coupons: apply before tax; line item taxability respected.
- Rounding: configurable (line vs total).

## Evidence
- Store address used for tax; optionally IP evidence (configurable) with privacy controls.

## UI
- Global settings: default nexus, defaults for price mode, rounding, display format.
- Per-form: custom tax table; enable/disable tax.
