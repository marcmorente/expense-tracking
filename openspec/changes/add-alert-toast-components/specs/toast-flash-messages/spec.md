## ADDED Requirements

### Requirement: Flash messages render as Toast components

The `_flash_messages.html.twig` partial SHALL keep its current file name.

The partial SHALL render every Symfony flash message through the `Toast`
component. It SHALL pass the flash type and message to the component.

#### Scenario: A success flash renders as a Toast

- **GIVEN** the session contains a success flash message
- **WHEN** `_flash_messages.html.twig` renders
- **THEN** it SHALL contain a Toast with the success type
- **AND** it SHALL contain the flash message text

#### Scenario: Multiple flash messages render as separate Toasts

- **GIVEN** the session contains multiple flash messages
- **WHEN** `_flash_messages.html.twig` renders
- **THEN** it SHALL contain one Toast for each message

#### Scenario: No flash messages render no Toasts

- **GIVEN** the session contains no flash messages
- **WHEN** `_flash_messages.html.twig` renders
- **THEN** it SHALL contain no Toast elements

### Requirement: The existing flash target becomes a Toast stack

The `#flash-messages` element SHALL remain the integration target.

It SHALL position Toasts at the bottom right. It SHALL keep one rem of space
from the viewport edge on small screens and use a maximum width.

The stack SHALL show no more than five Toasts. New Toasts SHALL appear below
older Toasts.

#### Scenario: The flash target positions Toasts at the bottom right

- **GIVEN** a page renders the flash message area
- **WHEN** the page contains a Toast
- **THEN** the flash target SHALL use bottom-right fixed positioning

#### Scenario: The stack removes the oldest sixth Toast

- **GIVEN** the flash target contains five Toasts
- **WHEN** a sixth Toast is appended
- **THEN** the oldest Toast SHALL be removed
- **AND** five Toasts SHALL remain

### Requirement: Turbo appends new Toasts

The expense creation Turbo stream SHALL append new flash content to
`#flash-messages`.

It SHALL not replace Toasts that are already in the target.

#### Scenario: Recording an expense appends a Toast

- **GIVEN** a user records an expense through a Turbo request
- **WHEN** the server returns the Turbo stream
- **THEN** the stream SHALL append content to `#flash-messages`
- **AND** the content SHALL contain the success Toast
- **AND** existing Toasts SHALL remain in the target
