## ADDED Requirements

### Requirement: Alert renders an inline message

The system SHALL render an independent anonymous `Alert` component from
`templates/components/Alert.html.twig`.

The component SHALL accept `type`, `message`, and `dismissible` props.

#### Scenario: Alert renders a success message

- **GIVEN** an `Alert` has type `success` and message `Expense recorded.`
- **WHEN** the component renders
- **THEN** the root element SHALL have `role="alert"`
- **AND** the root element SHALL contain the message text
- **AND** the root element SHALL use the green success palette

#### Scenario: Alert renders an error message

- **GIVEN** an `Alert` has type `error`
- **WHEN** the component renders
- **THEN** the root element SHALL use the red error palette

#### Scenario: Alert renders a warning message

- **GIVEN** an `Alert` has type `warning`
- **WHEN** the component renders
- **THEN** the root element SHALL use the yellow warning palette

#### Scenario: Alert renders an info message

- **GIVEN** an `Alert` has type `info`
- **WHEN** the component renders
- **THEN** the root element SHALL use the blue info palette

#### Scenario: Alert uses a fallback palette

- **GIVEN** an `Alert` has an unknown type
- **WHEN** the component renders
- **THEN** the root element SHALL use the gray fallback palette

### Requirement: Alert remains inline

The `Alert` component SHALL render in normal document flow. It SHALL NOT use
fixed positioning or automatic dismissal.

#### Scenario: Alert does not start a timer

- **GIVEN** a dismissible `Alert` renders
- **WHEN** the component connects
- **THEN** no automatic dismissal SHALL occur

### Requirement: Alert supports manual dismissal

The `Alert` component SHALL use the `alert` Stimulus controller when
`dismissible` is true.

The close button SHALL use `data-action="click->alert#dismiss"` and
`aria-label="Close"`.

#### Scenario: Dismissible Alert shows a close button

- **GIVEN** an `Alert` has `dismissible` set to true
- **WHEN** the component renders
- **THEN** it SHALL contain a close button
- **AND** the button SHALL use the alert dismiss action

#### Scenario: Non-dismissible Alert hides the close button

- **GIVEN** an `Alert` has `dismissible` set to false
- **WHEN** the component renders
- **THEN** it SHALL not contain a close button

#### Scenario: Closing an Alert removes it

- **GIVEN** a dismissible `Alert` is visible
- **WHEN** the user activates its close button
- **THEN** the Alert root element SHALL be removed
