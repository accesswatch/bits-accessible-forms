# Theming & Visual System

## Goals
- Professional, consistent visuals; zero configuration required; WCAG AA ready.

## Design Tokens (CSS variables)
- Colors: `--color-bg`, `--color-surface`, `--color-text`, `--color-muted`, `--color-accent`, `--color-danger`, `--focus-ring`.
- Typography: `--font-body`, `--font-size`, `--line-height`.
- Spacing/Radii: `--space-*`, `--radius-*`.
- Focus/Outline: `--focus-width`, `--focus-color`.

## Assets
- `theme/tokens.css`: variables.
- `theme/form.css`: components (inputs, radios/checkboxes, selects/combos, buttons, alerts, progress).
- `theme/overrides.css`: minimal hook for brand tweaks (maintain contrast automatically).

## Stripe/PayPal
- Stripe Elements Appearance config to mirror tokens.
- PayPal buttons styled within supported parameters; focus visible.

## Isolation
- Namespaced classes; cascade layers; :where() resets to avoid clobbering.
