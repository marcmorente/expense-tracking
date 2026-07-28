---
name: symfony-ux-stimulus
description: Adds JavaScript behavior to existing HTML with Stimulus controllers, actions, targets, values, outlets, and CSS classes. Use when building, testing, or changing Stimulus controllers and their data attributes.
---

# Stimulus

Add focused behavior to existing HTML. Keep content and application state in the
document when possible.

## Workflow

1. Inspect the HTML, controller bootstrap, and nearby controller tests.
2. Define the useful baseline without JavaScript.
3. Choose one controller responsibility and identifier.
4. Add the HTML data attributes before controller code.
5. Add tests and lifecycle cleanup.
6. Run the project verification command.

## Controller shape

Place DOM behavior in `assets/controllers/[identifier]_controller.js`.
Always add `/* stimulusFetch: 'lazy' */` after the import.

```js
import { Controller } from "@hotwired/stimulus";
/* stimulusFetch: 'lazy' */
export default class extends Controller {
  static targets = ["output"];
  static values = { name: String };
  greet() {
    this.outputTarget.textContent = `Hello, ${this.nameValue}!`;
  }
}
```

```html
<div data-controller="hello" data-hello-name-value="Ada">
  <button data-action="hello#greet">Greet</button>
  <output data-hello-target="output"></output>
</div>
```

Use camelCase for JavaScript names. Use kebab-case for identifiers and HTML
attribute name parts.

## Choose the correct feature

- Use actions to connect DOM events to controller methods.
- Use targets to find important elements inside the controller scope.
- Use values for typed state on the controller element.
- Use classes to pass CSS class names from HTML into a controller.
- Use outlets to access another controller and its element.
- Use custom events for loose communication between controllers.
- Use action parameters for data that belongs to one action element.
- Put pure logic in a separate module when it does not need the DOM.

Check `has[Name]Target`, `has[Name]Value`, `has[Name]Class`, or
`has[Name]Outlet` before optional access. Singular target, class, and outlet
properties throw when the matching item does not exist.

## Actions and events

Write action descriptors as `event->identifier#method`. Use the supported event
shorthand when the element makes the event clear.

Use `@window` or `@document` for global events. Use keyboard filters such as
`.esc` only with keyboard events.

Use `:prevent`, `:stop`, `:self`, `:once`, `:capture`, and passive options when
their behavior is necessary. Name actions for their result, such as
`showDialog`, instead of their event.

Use `this.dispatch("name", { detail })` for custom events. Prefer events over
direct controller lookup.

## State and lifecycle

Use `initialize()` once for controller setup. Use `connect()` for work that starts when the element enters the document.
Use `disconnect()` to release timers, observers, subscriptions, and requests.
Stimulus can connect the same controller instance more than once.
Use `[name]TargetConnected` and `[name]TargetDisconnected` for target changes.
Use `[name]OutletConnected` and `[name]OutletDisconnected` for outlet changes.
Use `[name]ValueChanged(value, previousValue)` to react to state changes.
Values support `Array`, `Boolean`, `Number`, `Object`, and `String`.

## Design rules

- Keep each controller small and reusable.
- Do not use the `stimulus_controller`, `stimulus_action`, or `stimulus_target` Twig functions or filters. Use raw data attributes.
- Let HTML describe the controller, actions, targets, and state.
- Manipulate existing HTML instead of rendering the whole interface.
- Keep essential page behavior functional when JavaScript does not load.
- Test browser support before you expose dependent controls.
- Keep targets inside their controller scope.
- Use outlets only when another controller instance is required.

Use the [Hotwire Stimulus handbook](https://stimulus.hotwired.dev/handbook/introduction) and [reference](https://stimulus.hotwired.dev/reference/controllers) for API details.
