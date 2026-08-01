## Context

The application uses `templates/components/Flash.html.twig` for one
success message flow. The component has inline styling and manual dismissal.

Symfony flash messages render through `_flash_messages.html.twig`. Turbo uses
`#flash-messages` when an expense is recorded without a full page load.

The new design keeps these integration names. It separates inline alerts from
temporary notifications without adding PHP classes or external dependencies.

## Goals / Non-Goals

**Goals:**

- Rename the current anonymous component to `Alert`.
- Add an independent anonymous `Toast` component.
- Render Symfony flash messages as bottom-right toasts.
- Support timers, pause behavior, manual close, stacking, and accessibility.
- Keep the existing partial and Turbo target names.

**Non-Goals:**

- Add a PHP component class.
- Add a database change.
- Replace field-level form errors with `Alert`.
- Add HTML message slots.
- Add a new message service or flash type enum.

## Decisions

### Use two independent anonymous components

`Alert.html.twig` and `Toast.html.twig` each define their own markup and
palette lookup. Neither component includes or extends the other component.

This keeps each component easy to change. A shared Twig partial would couple
the inline and positioned layouts.

### Keep a small component API

`Alert` exposes `type`, `message`, and `dismissible`. Its dismissible default
is `false`.

`Toast` exposes the same props plus `duration`. Its dismissible default is
`true`. The default duration is `5000` milliseconds. A duration of `0` disables
automatic dismissal but does not remove the close button.

Both components accept `success`, `error`, `warning`, and `info`. Unknown
types use the gray palette.

### Keep Alert inline

`Alert` keeps the current inline layout, colors, manual close behavior, and
`role="alert"`. It does not use a timer or fixed positioning.

`alert_controller.js` removes the root element when the user activates the
close button.

### Put toast positioning on the existing container

The `#flash-messages` element becomes the toast stack. It uses fixed
bottom-right positioning, a high stacking order, a one-rem mobile inset, and
a maximum width. `Toast.html.twig` keeps a normal-width root element.

The stack appends new toasts at the bottom. It keeps at most five toasts and
removes the oldest toast when a sixth toast connects.

### Use a dedicated Toast controller

`toast_controller.js` starts the timer from the `duration` value. It clears
the timer when the toast is removed. It pauses the timer during pointer hover
and keyboard focus. It resumes only when both states end.

The controller also handles close actions and limits the sibling toast count.
`duration="0"` skips timer creation.

### Append Turbo updates

The expense creation stream appends new toast content to `#flash-messages`.
This preserves timers on toasts that are already visible. A full page render
still includes every current session flash through `_flash_messages.html.twig`.

The request flow is:

```text
ExpenseController
      |
      | addFlash(success)
      v
create.stream.html.twig
      |
      | append #flash-messages
      v
_flash_messages.html.twig -> Toast.html.twig -> toast_controller.js
```

### Use type-aware accessibility roles

`Alert` always uses `role="alert"`.

`Toast` uses `role="status"` for success, warning, and info. It uses
`role="alert"` for error messages. Icons are decorative and use
`aria-hidden="true"`. Close buttons use an accessible label and a button type.

### Add a short entrance animation

The toast uses a short entrance animation. Its controller removes it without
an exit delay. The animation uses motion-safe classes and disables movement
for users who request reduced motion.

## Risks / Trade-offs

- [A sixth toast can remove a message before the user reads it] → Keep the
  limit at five and show the newest message at the bottom.
- [Turbo append can preserve a stale message longer than expected] → Use the
  five-second default and keep manual dismissal available.
- [Screen readers can announce an error twice] → Use `role="alert"` only for
  error toasts and use `role="status"` for other toast types.
- [Independent templates can duplicate palette changes] → Keep the palette
  keys and color meanings identical in both templates.
- [A timer can remove focused content] → Pause during keyboard focus and keep
  the close button keyboard accessible.

## Migration Plan

1. Rename `Flash.html.twig` to `Alert.html.twig`.
2. Rename `flash_controller.js` to `alert_controller.js`.
3. Add `Toast.html.twig` and `toast_controller.js`.
4. Update `_flash_messages.html.twig` to render `Toast`.
5. Update the Turbo stream to append toast content.
6. Add tests for both components and their controllers.
7. Run `composer verify`.

To roll back, restore the Flash component and controller. Restore the stream
replace behavior and the original partial component name.

## Open Questions

None. The design decisions are complete.
