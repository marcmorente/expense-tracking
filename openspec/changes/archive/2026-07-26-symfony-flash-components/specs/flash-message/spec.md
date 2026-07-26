## ADDED Requirements

### Requirement: Flash message renders with type-aware styling

The system SHALL render a flash message with Tailwind CSS classes that match
the flash type. The component SHALL accept `type` and `message` props.

The palette mapping SHALL be:

| Type | Background | Text |
|---|---|---|
| `success` | `bg-green-50` | `text-green-800` |
| `error` | `bg-red-50` | `text-red-800` |
| `warning` | `bg-yellow-50` | `text-yellow-800` |
| `info` | `bg-blue-50` | `text-blue-800` |
| any other value | `bg-gray-50` | `text-gray-800` |

The component SHALL render `role="alert"` on the root element.

#### Scenario: Default success message renders with green styling

- **GIVEN** a `<twig:Flash>` component rendered with `type="success"` and
  `message="Expense recorded."`
- **THEN** the output SHALL contain a `<div>` element with
  `class="bg-green-50 text-green-800"`
- **AND** the output SHALL contain the text "Expense recorded."
- **AND** the element SHALL have `role="alert"`

#### Scenario: Error type renders with red styling

- **GIVEN** a `<twig:Flash>` component rendered with `type="error"`
- **THEN** the output SHALL use `bg-red-50` and `text-red-800`

#### Scenario: Warning type renders with yellow styling

- **GIVEN** a `<twig:Flash>` component rendered with `type="warning"`
- **THEN** the output SHALL use `bg-yellow-50` and `text-yellow-800`

#### Scenario: Info type renders with blue styling

- **GIVEN** a `<twig:Flash>` component rendered with `type="info"`
- **THEN** the output SHALL use `bg-blue-50` and `text-blue-800`

#### Scenario: Unknown type falls back to gray

- **GIVEN** a `<twig:Flash>` component rendered with
  `type="unknown-type-value"`
- **THEN** the output SHALL use `bg-gray-50` and `text-gray-800`

---

### Requirement: Flash message MAY show a dismiss button

When the `dismissible` prop is `true`, the component SHALL render a close
button inside the flash message. The button SHALL use the Stimulus controller
`flash` with action `click->flash#dismiss`.

When `dismissible` is not set or `false`, the component SHALL NOT render a
close button.

#### Scenario: Dismissible flash renders a close button

- **GIVEN** a `<twig:Flash>` component rendered with `dismissible`
- **THEN** the output SHALL contain a `<button>` element with
  `data-action="click->flash#dismiss"`
- **AND** the button SHALL have `aria-label="Close"`

#### Scenario: Non-dismissible flash has no close button

- **GIVEN** a `<twig:Flash>` component rendered without `dismissible`
- **THEN** the output SHALL NOT contain a `<button>` element

---

### Requirement: Stimulus controller dismisses the flash

The `flash_controller.js` SHALL have a `dismiss` action that removes the
controller's root element from the DOM.

#### Scenario: Clicking close removes the flash

- **GIVEN** a flash message rendered with `dismissible`
- **WHEN** the user clicks the close button
- **THEN** the flash element SHALL be removed from the DOM

---

### Requirement: Partial renders all flash messages

The `_flash_messages.html.twig` partial SHALL iterate over `app.flashes()`
and render each message through the `<twig:Flash>` component with
`dismissible` always set.

The partial SHALL be wrapped in a `hasPreviousSession` guard.

#### Scenario: Partial renders all flash types

- **GIVEN** there are flash messages of types `success`, `error` in the session
- **WHEN** `_flash_messages.html.twig` is rendered
- **THEN** the output SHALL contain two flash message elements, one for each
  type

#### Scenario: No flash messages produce no output

- **GIVEN** there are no flash messages in the session
- **WHEN** `_flash_messages.html.twig` is rendered
- **THEN** the output SHALL be empty

#### Scenario: hasPreviousSession guard prevents output without session

- **GIVEN** there is no previous session
- **WHEN** `_flash_messages.html.twig` is rendered
- **THEN** the output SHALL be empty
