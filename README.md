# BITS Accessible Forms

**Version:** 1.0.0  
**Documentation Release Date:** November 16, 2025

BITS Accessible Forms is a WCAG 2.2 AA–compliant WordPress form builder focused on professional, zero‑config visuals, human‑editable schemas, rich conditional logic (including nested rules), nested data (Repeater + Subform), robust validation, global manual tax tables, Stripe/PayPal payments, strong security, and comprehensive admin diagnostics.

## Quick Links

- **Documentation:** [docs/](docs/)
- **Changelog:** [CHANGELOG.md](CHANGELOG.md)
- **Contributing:** [docs/contributing.md](docs/contributing.md)
- **Release Process:** [docs/release-process.md](docs/release-process.md)

## Project Information

- **Slug:** bits-accessible-forms
- **PHP Namespace:** BITS\AccessibleForms
- **Text Domain:** bits-accessible-forms
- **REST API:** /wp-json/bits/v1
- **Requirements:** WordPress ≥ 6.5, PHP ≥ 8.1

## Goals

- Match Gravity Forms core logic and field coverage (Standard, Advanced, Pricing)
- Human-editable form definitions with hybrid source of truth (DB primary + file sync)
- WCAG 2.2 AA across builder (admin) and renderer (user)
- Zero-configuration professional visuals; theming via tokens
- Payments: Stripe & PayPal one-time, immediate capture, refunds
- Global manual tax tables (per-form currency, coupons, tax-exclusive default)
- Powerful conditional logic (nested groups, show/hide, disable/read-only, toggle required)
- Nested data: Repeater (inline) and Subform (child entries)
- Secure-by-default with rich diagnostics, safe mode, and logs

## Non-goals (v1)

- Subscriptions (Stripe/PayPal) — v1.1
- Provider tax engines (Stripe Tax/TaxJar/Avalara) — v1.1
- Post fields — v1.1
- Grid layout builder (full n‑column) — v1.1

## Documentation

This repository contains the complete specification and documentation for BITS Accessible Forms. For detailed information, see:

- [Architecture](docs/architecture.md) - System design and components
- [Schema](docs/schema.md) - Form JSON structure and field definitions
- [Decisions](docs/decisions.md) - Key architectural decisions
- [Roadmap](docs/roadmap.md) - Development milestones and acceptance criteria
- [Accessibility](docs/a11y.md) - WCAG 2.2 AA compliance
- [Security](docs/security.md) - Security model and best practices
- [REST API](docs/rest-api.md) - API endpoints and usage

## Examples

Sample form definitions can be found in the [examples/forms](examples/forms/) directory.

## Contributing

We welcome contributions! Please see our [Contributing Guide](docs/contributing.md) for details on:

- Development requirements
- Branching strategy
- Testing approach
- Commit conventions

## License

Copyright © 2025 AccessWatch. All rights reserved.

## Release Notes

See [CHANGELOG.md](CHANGELOG.md) for detailed release notes and version history.
