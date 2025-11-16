# Builder UI Spec

## Canvas
- Two-column responsive layout with auto-stack; fields can be assigned left/right or full width.
- Sections and Page Breaks delineate structure; page toolbar provides Next/Prev labels and logic.

## Field Library
- Standard, Advanced, Pricing groups; searchable; fully keyboard navigable.

## Reordering (WCAG aligned)
- Drag handles (mouse/touch): pick (Space), move (Arrow), drop (Enter), Esc cancels.
- Buttons: Move Up/Down, Move To Section/Page (24×24 targets).
- Move across sections/pages; move entire sections and pages.
- Announcements via aria-live on pick/drop/invalid moves.
- Undo/Redo: 20-step history; each move is one step; status announced.

## Jump Move (Combo + Buttons)
- Placement: "Move" tab in Inspector for field/section/page; context menu "Move…" and toolbar shortcut.
- Destination Combobox: ARIA 1.2 (`role=combobox` + `listbox`), grouped results (Pages, Sections(by page), Fields(by section)), type-to-filter, announce "X results", Enter selects, Esc closes, Home/End jump, optional Ctrl+K focus shortcut.
- Action Buttons: "Place Above", "Place Below" (≥24×24). Keyboard: Enter/Space; Ctrl+Enter Above; Ctrl+Shift+Enter Below. Disabled states show SR-described reasons (self-target, cross-type, before-first-page).
- Announcements: aria-live "Moved 'Email' above 'Billing Address' in 'Contact' (Page 1)"; invalid moves announce reason.
- Undo/Redo: each jump move = one undo step; announce undo/redo outcomes.
- Performance: virtualized list, debounced filtering for large forms.

## Delete Flow & Safeguards
- Trigger: Delete/Backspace or Delete button (≥24×24). Confirmation dialog (role=dialog) with title "Delete Field 'Email'?" and impact summary (references in logic/notifications/confirmations).
- Safeguards: block deleting the only Submit; block deleting the only Page; Sections/Pages with children require "Move children to …" or "Delete all". Dependencies surfaced with "Auto-clean" (safe remove rules) or "Review references". Disabled Delete includes reason tooltip + SR description.
- After delete: one undo step; aria-live announcement; focus returns to nearest sibling; options to move children then delete.
- Multi-select delete (optional v1): select multiple items; single confirm dialog shows grouped impacts; undo as one step; aria-live "Deleted N items".

## Properties Panels (Tabs)
- Common tabs (most fields):
  - Basics: Label, Description, Help, Required, Placeholder (when valid), Admin Label, Autocomplete.
  - Validation: Required, Min/Max (length/value), Pattern, No Duplicates, Custom error.
  - Logic: Nested rule groups; actions Show/Hide, Disable/Read-only, Toggle Required.
  - Appearance: Size (small/medium/large), Column (left/right/full), CSS class.
  - Move: Jump Move (combobox + Place Above/Below).
  - Accessibility: ARIA description binding, instructions placement, required indicator override.
  - Advanced: Input name (prepopulation), internal IDs, Admin-only visibility.
- Choice fields (Radio, Checkboxes, Select, Multi-select, Image Choice):
  - Choices: Table editor (Label, Value, Default, Image, Price when pricing). Keyboard reordering Alt+Arrow; space toggles default.
  - Bulk Add: multi-line (Label | Value | Price). CSV paste supported; preview parses with inline errors; Ctrl+Enter applies; SR announces results.
  - Presets: Yes/No, True/False, Weekdays, Months, US States (+DC, USPS), Countries (ISO‑3166‑1 alpha‑2), Timezones, Likert scales; preview + Append/Replace; "Use label as value" or official codes.
  - Sort A→Z/Z→A; Generate N items; defaults enforcement (radios single default, checkboxes multiple allowed).
- Textual inputs (Text, Textarea, Email, Phone, Number, Website, Password):
  - Type‑Specific: Email (format, confirm), Phone (E.164/National/Custom), Number (Min/Max/Step, separators), Website (protocols), Password (strength meter, confirm), Textarea (rows, max, auto‑expand).
- Grouped inputs (Name, Address, Date, Time, List):
  - Name (subparts, order, required subparts), Address (subparts + sources + autocomplete), Date (picker type, format, min/max/relative), Time (12/24h, step, window), List (columns, min/max rows).
- File Upload: allowed types presets, max size, multi‑upload, max files, preview, scan hook.
- Pricing: Product (price, display, taxable, fixed/user input), Options (per‑option pricing, link to product, UI), Quantity (defaults/min/max, link), User‑Defined Price (min/max/taxable), Total (display), Shipping (if enabled).
- Consent: statement editor, required checkbox, policy link, store timestamp/IP (configurable).

## Section & Page Inspectors
- Section: Basics (Label, Heading Level with auto‑outline override, Description), Appearance (full‑width, spacing, CSS), Logic (affects children), Accessibility (heading semantics, landmark hint), Move, Advanced (anchor/id, admin‑only).
- Page: Basics (Title, Progress label), Navigation (Next/Previous text + logic), Appearance (break style), Move (reorder pages).

## Keyboard Cheat Sheet (Admin)
- Navigation: Tab/Shift+Tab focus traversal; `[`/`]` to jump prev/next item; Alt+O opens Outline.
- Reorder: Space pick, Arrow Up/Down move, Enter drop; Alt+M Move tab; combobox type to filter; Enter choose; Ctrl+Enter Above; Ctrl+Shift+Enter Below.
- Edit: Enter open Inspector; Ctrl+Arrow switch tabs; Esc back to Canvas.
- Delete/Duplicate: Delete/Backspace delete; Ctrl+D duplicate; F2 rename.
- Choices: Alt+B bulk; Alt+S sort; Alt+L presets.
