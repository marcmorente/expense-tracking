## Why

The application has one component for both inline alerts and temporary
notifications. This makes the component contract unclear and prevents flash
messages from using a proper toast experience.

## What Changes

- Rename the existing `Flash` component to `Alert`.
- Keep `Alert` inline with manual dismissal and the current type colors.
- Add an independent `Toast` component with the same message API.
- Render session flash messages through `Toast` in the existing flash partial.
- Position toasts at the bottom right and stack up to five messages.
- Auto-dismiss toasts after five seconds by default.
- Support a configurable duration in milliseconds.
- Pause toast dismissal during pointer hover and keyboard focus.
- Add separate `alert` and `toast` Stimulus controllers.
- Keep the `_flash_messages.html.twig` partial and `#flash-messages` target.
- Add component, controller, Turbo, and accessibility tests.

## Capabilities

### New Capabilities

- `alert-component`: Render an inline, type-aware, optionally dismissible alert.
- `toast-component`: Render a bottom-right notification with timer controls.
- `toast-flash-messages`: Render Symfony flash messages through the Toast component.

### Modified Capabilities

None.

## Impact

- Modify anonymous Twig components in `templates/components/`.
- Add and rename Stimulus controllers in `assets/controllers/`.
- Modify the global flash partial and base layout.
- Modify the Turbo stream response for expense creation.
- Add browser behavior tests and functional controller assertions.
- Add no database changes and no new PHP classes.

## Rollback

Restore `Flash.html.twig` and `flash_controller.js`.
Revert the partial, layout, Turbo stream, and test changes.
Delete the `Alert` and `Toast` component files and their controllers.
