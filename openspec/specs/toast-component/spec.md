# Toast Component

## Purpose

The Toast component displays temporary notifications with timed and manual
dismissal.

## Requirements

### Requirement: Toast renders a type-aware notification

The system SHALL render an independent anonymous `Toast` component from
`templates/components/Toast.html.twig`.

The component SHALL accept `type`, `message`, `dismissible`, and `duration`
props. The default duration SHALL be `5000` milliseconds.

#### Scenario: Toast renders a success notification

- **GIVEN** a `Toast` has type `success`
- **WHEN** the component renders
- **THEN** the root element SHALL use `role="status"`
- **AND** the root element SHALL use the green success palette

#### Scenario: Toast renders an error notification

- **GIVEN** a `Toast` has type `error`
- **WHEN** the component renders
- **THEN** the root element SHALL use `role="alert"`
- **AND** the root element SHALL use the red error palette

#### Scenario: Toast renders warning and info notifications

- **GIVEN** a `Toast` has type `warning` or `info`
- **WHEN** the component renders
- **THEN** the root element SHALL use `role="status"`
- **AND** the root element SHALL use the matching palette

#### Scenario: Toast uses a fallback palette

- **GIVEN** a `Toast` has an unknown type
- **WHEN** the component renders
- **THEN** the root element SHALL use the gray fallback palette

### Requirement: Toast supports timed dismissal

The `Toast` component SHALL connect to the `toast` Stimulus controller.

The controller SHALL read the duration in milliseconds. It SHALL remove the
Toast after the duration expires.

#### Scenario: Toast uses its default duration

- **GIVEN** a `Toast` has no duration prop
- **WHEN** it connects
- **THEN** it SHALL start a five-second dismissal timer

#### Scenario: Toast uses a custom duration

- **GIVEN** a `Toast` has duration `3000`
- **WHEN** it connects
- **THEN** it SHALL start a three-second dismissal timer

#### Scenario: Zero duration disables automatic dismissal

- **GIVEN** a `Toast` has duration `0`
- **WHEN** it connects
- **THEN** it SHALL not start a dismissal timer

### Requirement: Toast pauses during user interaction

The controller SHALL pause the dismissal timer while the pointer is over the
Toast or keyboard focus is inside the Toast.

The controller SHALL resume the timer when neither condition is true.

#### Scenario: Pointer hover pauses a Toast

- **GIVEN** a timed Toast is visible
- **WHEN** the pointer enters the Toast
- **THEN** the dismissal timer SHALL pause

#### Scenario: Keyboard focus pauses a Toast

- **GIVEN** a timed Toast is visible
- **WHEN** keyboard focus enters the Toast
- **THEN** the dismissal timer SHALL pause

#### Scenario: Toast resumes after interaction ends

- **GIVEN** a Toast timer is paused
- **WHEN** the pointer leaves and focus leaves the Toast
- **THEN** the timer SHALL resume

### Requirement: Toast supports manual dismissal

The Toast SHALL show a close button by default. The button SHALL use
`data-action="click->toast#dismiss"` and `aria-label="Close"`.

The `dismissible` prop SHALL control the close button only. The timer SHALL
remain active when `dismissible` is false.

#### Scenario: Toast shows its default close button

- **GIVEN** a Toast uses its default props
- **WHEN** the component renders
- **THEN** it SHALL contain a close button

#### Scenario: Non-dismissible Toast hides the close button

- **GIVEN** a Toast has `dismissible` set to false
- **WHEN** the component renders
- **THEN** it SHALL not contain a close button

#### Scenario: Closing a Toast removes it

- **GIVEN** a Toast is visible
- **WHEN** the user activates its close button
- **THEN** the Toast root element SHALL be removed

### Requirement: Toast supports reduced motion

The Toast SHALL use a short entrance animation for users who allow motion.

The Toast SHALL disable movement for users who request reduced motion.

#### Scenario: Reduced motion disables the Toast animation

- **GIVEN** a user requests reduced motion
- **WHEN** a Toast appears
- **THEN** the Toast SHALL not use movement animation
