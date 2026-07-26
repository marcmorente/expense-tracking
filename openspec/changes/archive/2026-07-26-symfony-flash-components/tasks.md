## 1. Stimulus controller

- [x] 1.1 Create `assets/controllers/flash_controller.js` with a `dismiss`
      action that calls `this.element.remove()`
- [x] 1.2 Create `assets/controllers/flash_controller.test.js` that tests
      the dismiss action removes the element from the DOM
- [x] 1.3 Run `npm run verify` — Vitest passes

## 2. Anonymous Flash component

- [x] 2.1 Create `templates/components/Flash.html.twig` with `{% props %}`,
      Twig array palette lookup, type-aware Tailwind classes, `role="alert"`,
      and conditional dismiss button with `data-controller="flash"`
      and `data-action="click->flash#dismiss"`
- [x] 2.2 Verify the component renders via `{{ component('Flash', {...}) }}`
      or `<twig:Flash ... />` without errors

## 3. Flash messages partial

- [x] 3.1 Create `templates/_flash_messages.html.twig` with
      `{{ app.request.hasPreviousSession }}` guard
- [x] 3.2 Loop over `app.flashes()` and render `<twig:Flash>` for each
      message with `dismissible`

## 4. Wire into templates

- [x] 4.1 Add `{% include '_flash_messages.html.twig' %}` to
      `templates/base.html.twig` inside the `{% block body %}` area,
      before the `{% block body %}{% endblock %}` call — or alternatively
      as a separate `{% block flashes %}` before `{% block body %}`
- [x] 4.2 Remove the inline flash loop from
      `templates/expense/index.html.twig` (lines 21-23)
- [x] 4.3 Verify both templates render without errors

## 5. Functional test

- [x] 5.1 Update `tests/Functional/Controller/ExpenseControllerTest.php`
      to assert that the flash message `<div>` with `role="alert"` and text
      "Expense recorded." is present after a successful POST to the create
      route

## 6. Final verification

- [x] 6.1 Run `composer verify` — all gates pass (PHP style, PHPStan,
      deptrac, YAML lint, Twig lint, container lint, PHPUnit,
      `npm run verify`)
