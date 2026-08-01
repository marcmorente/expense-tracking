## 1. Refactor the inline component

- [x] 1.1 Rename `Flash.html.twig` to `Alert.html.twig` and preserve its
  inline props, palette, role, and manual close behavior. Acceptance: the
  component uses `type`, `message`, and `dismissible`.
- [x] 1.2 Rename `flash_controller.js` to `alert_controller.js` and update the
  dismiss action. Acceptance: the controller removes a dismissible Alert.
- [x] 1.3 Update the Alert component tests. Acceptance: tests cover rendering,
  all types, the fallback palette, and manual dismissal.

## 2. Build the Toast component

- [x] 2.1 Create `Toast.html.twig` with the shared palette and Toast props.
  Acceptance: roles, icons, close button, and reduced-motion classes match the
  Toast specification.
- [x] 2.2 Create `toast_controller.js` with duration, zero-duration,
  dismissal, hover pause, and focus pause behavior. Acceptance: the timer
  removes Toasts at the correct time and resumes after interaction ends.
- [x] 2.3 Add Toast controller tests. Acceptance: tests cover default and
  custom durations, zero duration, close behavior, hover, focus, and the
  five-Toast limit.

## 3. Connect session messages

- [x] 3.1 Update `_flash_messages.html.twig` to render Toast components while
  keeping its file name. Acceptance: every session message renders once.
- [x] 3.2 Update `base.html.twig` to make `#flash-messages` a responsive,
  bottom-right Toast stack. Acceptance: the target keeps its existing id and
  limits visible Toasts to five.
- [x] 3.3 Update the expense Turbo stream to append Toast content. Acceptance:
  existing Toasts remain when a new expense creates a message.
- [x] 3.4 Update functional tests for Toast roles, rendering, and Turbo
  append behavior. Acceptance: success and error messages have the specified
  roles and the stream targets `#flash-messages`.

## 4. Verify the change

- [x] 4.1 Run `composer verify` and fix every reported issue. Acceptance: all
  PHP, Twig, container, JavaScript, and CSS gates pass.
