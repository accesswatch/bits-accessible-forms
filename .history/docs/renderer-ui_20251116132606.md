# Renderer UI Spec

## Rendering
- Server-generated semantic HTML; client enhancer for logic, pagination, and calculations.
- Shortcode: `[bits_form id="123" ajax="true" tabindex="0" field_values="a:1,b:2"]`.
- Block: `bits/form` (attributes: id, ajax, theme, tabindex).

## Accessibility
- Labels with `for`; fieldset/legend for groups; `aria-describedby` for help/errors; `aria-invalid` on errors.
- Hidden/disabled fields are removed from tab order and skipped in validation/storage.
- Focus visible with high-contrast outline; maintain scroll padding to keep focus not obscured.

## Error Handling
- Validation summary at top with heading; list of error links to fields; `aria-live="polite"` status.
- Auto-focus first invalid field; in-page links move focus to each field; hidden fields are skipped.

## Pagination & Progress
- Steps indicator; auto-update with visibility changes.
- Block Next on invalid; focus first error; conditional logic for Next/Previous supported.

## Conditional Logic
- Client evaluator; state changes do not steal focus; server re-evaluation on submit; hidden fields not stored.

## File Uploads
- Accept + max size; async uploads where possible; server MIME sniffing.

## Performance
- Initialize logic observers only for relevant fields; throttle recalculations; minimal DOM churn.
