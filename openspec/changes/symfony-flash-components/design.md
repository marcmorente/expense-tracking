## Context

Twig Component infrastructure (`symfony/ux-twig-component`) is installed and
configured at `config/packages/twig_component.yaml`:

```yaml
twig_component:
  anonymous_template_directory: "components/"
  defaults:
    App\Twig\Components\: "components/"
```

Flash messages are used in exactly one place:

```
src/Controller/ExpenseController.php:54  $this->addFlash('success', 'Expense recorded.')
templates/expense/index.html.twig:21-23  inline loop with Tailwind classes
```

Only the `success` type exists. The Stimulus pattern is established
(`assets/controllers/expense_total_controller.js`).

This project must run `composer verify` before reporting done.

## Goals / Non-Goals

**Goals:**
- Replace the 3-line inline flash block with a reusable anonymous component.
- Support four flash types: `success`, `error`, `warning`, `info`.
- Add a Stimulus-based dismiss button.
- Render flash messages globally through `base.html.twig`.
- Test the Stimulus controller and the rendered flash in the functional test.

**Non-Goals:**
- Auto-dismiss after timeout (can be added later).
- Animated entry/exit transitions.
- Flash message abstraction in PHP (no `FlashType` enum or service).
- Live Component integration.
- Toast or notification positioning beyond the inline block pattern.

## Decisions

| Decision | Choice | Rationale |
|---|---|---|
| Component type | Anonymous Twig component | Avoids the `readonly` property conflict between the project ruleset and Twig Component hydration. Zero PHP boilerplate. |
| Type-to-style mapping | Twig array literal in component template | Self-contained, no extra CSS file, no PHP class. One file to read for the full rendering. |
| Dismiss mechanism | Stimulus controller | Fits the project's existing JS pattern. `flash_controller.js` with a single `dismiss()` method that calls `this.element.remove()`. |
| Global rendering | `base.html.twig` with `hasPreviousSession` guard | Every page gets flash messages without opt-in. The guard prevents starting sessions for anonymous visitors. |
| Partial vs. direct `<twig:Flash>` | Partial (`_flash_messages.html.twig`) | Keeps the loop logic out of the base template. Single component, single partial, clear separation. |
| Flash types | `success`, `error`, `warning`, `info` | Standard Symfony convention. Current code uses `success`; the palette supports future types. Unknown types default to `gray`. |
| Palette colors | Tailwind v4 utility classes per type | Follows the project's existing Tailwind usage. No custom CSS. |

### Flow for a typical request

```
                         base.html.twig
                              │
                              ▼
                    ┌─────────────────────┐
                    │  hasPreviousSession? │──no──→ skip
                    └─────────┬───────────┘
                              │ yes
                              ▼
                    _flash_messages.html.twig
                              │
                              ▼
              ┌───────────────────────────┐
              │  app.flashes()            │
              │  for each (type, messages) │
              │    for each message        │
              │      <twig:Flash>          │
              └───────────────────────────┘
                              │
                              ▼
                    Flash.html.twig
                    (anonymous component)
                    ┌───────────────────┐
                    │  type → palette   │
                    │  dismissible btn  │
                    │  role="alert"     │
                    │  data-controller  │
                    │  = "flash"        │
                    └───────────────────┘
                              │
                              ▼
                    flash_controller.js
                    ┌───────────────────┐
                    │  dismiss() {      │
                    │    remove()       │
                    │  }                │
                    └───────────────────┘
```

## Risks / Trade-offs

| Risk | Mitigation |
|---|---|
| Anonymous components are less discoverable than PHP classes | The naming convention (`Flash.html.twig` in `components/`) is self-documenting. The partial `_flash_messages.html.twig` provides a clear entry point. |
| `hasPreviousSession` may not show flashes if the session was not started by a previous request | This is the documented Symfony recommendation. The trade-off (cacheability vs. flash availability) is acceptable for this app. |
| `role="alert"` may be announced by screen readers on every render | Correct. This is intentional — flash messages are time-sensitive notifications. |
| Stimulus controller is hardcoded in the anonymous template (not configurable) | The `dismissible` prop controls whether the button renders. If no button, the controller does nothing on connect. |
