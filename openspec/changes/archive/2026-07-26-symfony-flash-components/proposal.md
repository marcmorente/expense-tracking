## Why

Flash messages are currently rendered as inline HTML in a single template.
There is no reusable component, no consistent styling across types,
no dismiss behavior, and no way to render flashes globally.

The Twig Component infrastructure is already installed but unused.
This change introduces the first component and establishes the pattern.

## What Changes

- Create an anonymous Twig component `Flash` that renders a single flash
  message with type-aware Tailwind styling and optional close button.
- Create a Twig partial `_flash_messages.html.twig` that loops over
  `app.flashes()` and renders each message through the component.
- Create a Stimulus controller for the dismiss action.
- Replace the inline flash block in `expense/index.html.twig` with the
  partial.
- Render the partial in `base.html.twig` behind a `hasPreviousSession`
  guard so every page can show flash messages.
- Add a `flash` test helper to the `ExpenseController` functional test.

## Capabilities

### New Capabilities

- `flash-message`: Render a single flash message with type-aware styling
  and optional dismiss action.

### Modified Capabilities

None.

## Impact

- New file: `templates/components/Flash.html.twig`
- New file: `templates/_flash_messages.html.twig`
- New file: `assets/controllers/flash_controller.js`
- New file: `assets/controllers/flash_controller.test.js`
- Modified: `templates/base.html.twig` (add flash block)
- Modified: `templates/expense/index.html.twig` (replace inline flash)
- Modified: `tests/Functional/Controller/ExpenseControllerTest.php`
  (assert flash message appears after recording an expense)

## Rollback

Revert the modified files. Delete the four new files.
