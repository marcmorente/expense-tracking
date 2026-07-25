# AGENTS.md

Read this file before you write code. It tells you the one correct way to do each
thing in this project. If a rule here disagrees with the code, the code is wrong.

## The one command you must run

```
composer verify
```

It runs every gate in this order: PHP style, PHPStan, deptrac, YAML lint, Twig
lint, container lint, PHPUnit, then `npm run verify` (Prettier check, ESLint,
Stylelint, Vitest). Run it before you report a task as done. Do not run the gates
one by one. Do not add a new way to run them.

Fix commands: `composer cs:fix`, `npm run lint:fix`, `npm run lint:css:fix`,
`npm run format`.

## The golden path

Copy this vertical slice. Every new feature has the same shape.

| Layer      | File                                             | Test                                                     |
| ---------- | ------------------------------------------------ | -------------------------------------------------------- |
| Entity     | `src/Entity/Expense.php`                         | `tests/Unit/Entity/ExpenseTest.php`                      |
| Repository | `src/Repository/ExpenseRepository.php`           | `tests/Integration/Repository/ExpenseRepositoryTest.php` |
| Service    | `src/Service/ExpenseService.php`                 | `tests/Integration/Service/ExpenseServiceTest.php`       |
| Controller | `src/Controller/ExpenseController.php`           | `tests/Functional/Controller/ExpenseControllerTest.php`  |
| Form       | `src/Form/ExpenseType.php`                       | —                                                        |
| Form       | `src/Form/SubmittedValues.php`                   | —                                                        |
| Template   | `templates/expense/index.html.twig`              | —                                                        |
| Migration  | `migrations/Version20260725144704.php`           | —                                                        |
| JS module  | `assets/expense/amount.js`                       | `assets/expense/amount.test.js`                          |
| JS control | `assets/controllers/expense_total_controller.js` | `assets/controllers/expense_total_controller.test.js`    |

## PHP rules you must follow

The `haspadar` ruleset is strict. These constraints shape every class:

1. **Every concrete class is `final`.** Abstract or final, nothing else.
2. **Every property is `readonly`.** Prefer `final readonly class`.
3. **A constructor may only assign properties.** No conditions, no method calls,
   no validation. Allowed values are a parameter, `new X()`, an array, a constant
   or a literal. This is why `Expense` cannot validate itself.
4. **Validation lives in the Service layer.** `ExpenseService::record()` checks
   the values and throws `\InvalidArgumentException`. Entities are pure data.
5. **Maximum three parameters per method.** Bundle values in an object if you
   need more. An entity that creates its own identifier saves one parameter.
6. **No static methods in `src/`.** No named constructors. Use `__construct`.
7. **No public constants.** Use `private const string Name = '...';` with a type.
8. **No class name may end in `-er` or `-or`**, unless a parent class comes from
   `Symfony\` or `Doctrine\`. `ExpenseController` is allowed because it extends
   `AbstractController`.
9. **Forbidden class suffixes:** Manager, Handler, Processor, Coordinator,
   Helper, Util, Utils, Utility, Data, Info, Information, Wrapper.
10. **No nullable return types and no `return null`.** Return an empty list, or
    throw.
11. **Never catch or throw** `Exception`, `Throwable`, `RuntimeException` or
    `Error`. Use a specific class such as `\InvalidArgumentException`.
12. **One `@throws` tag per method.** `LogicException`, `UnexpectedValueException`
    and `Error` count as unchecked, so you never document them. See the
    `exceptions` section of `phpstan.neon`.
13. **Parameter names need three characters or more** and use camelCase. `$id` is
    rejected. Write `$expenseId`.
14. **Every class needs a docblock** with a capital letter and a full stop.
    Methods do not need one. If you write one, it must start with a summary
    line. A docblock that holds only `@param` or `@throws` is rejected
    (`haspadar.phpdocEmpty`).
15. **No magic numbers** except 0 and 1. Use a private typed constant.
16. **Maximum one level of nested `if`,** three `return` statements, five fields
    and 20 methods per class.

When PHPStan reports a rule, read the identifier and fix the cause. Never add an
ignore entry.

**Warning:** an entry in `ignoreErrors` must match the rule identifier exactly.
The identifier comes from the PHPStan output, not from the rule class name. A
wrong name ignores nothing, and `reportUnmatchedIgnoredErrors` is `false`, so the
mistake stays silent.

## Layers

Deptrac enforces these directions. Nothing else is allowed:

```
Controller -> Service, Entity, Form, Validator, Event
Service    -> Repository, Entity, Event
Repository -> Entity
Listener   -> Event, Service
Form       -> Entity, Validator
Validator  -> Entity
Entity     -> nothing
Event      -> nothing
```

A class must live in a namespace that `deptrac.yaml` matches. If you need a new
namespace, add a layer to `deptrac.yaml` in the same change.

## Tests

- The test database is in-memory SQLite, so every test builds its own schema.
  Use the `App\Tests\DatabaseSchema` trait and call `$this->createDatabaseSchema()`.
- A functional test must call `$client->disableReboot()` before the first
  request. A reboot drops the in-memory database.
- `self::getContainer()->get(...)` returns `object`. Narrow it with
  `self::assertInstanceOf(...)` before you use it.
- Mark every test class `@internal` and add `#[CoversClass(...)]`.
- `App\Repository\ExpenseRepository` is `final readonly`, so PHPUnit cannot mock
  it. Test services against the real repository and an in-memory database.
- PHPUnit fails on warnings, notices, risky tests and an empty suite. Do not
  write a test that cannot fail.

## Migrations

After you change an entity, generate a migration:

```
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

Then remove the auto-generated comments and write a real `getDescription()`.

## Forms and validation

`symfony/form`, `symfony/validator` and `symfony/security-csrf` are installed.

- Put form types in `src/Form/`. A form type is `final` and extends
  `AbstractType`, so the `-er`/`-or` name rule does not apply to it.
- Prefer built-in constraints as attributes. Put them on the form type, or on the
  entity properties.
- **Do not write custom constraint classes without reading this first.** Symfony
  constraints need public constants for error codes and public mutable properties
  for options. Both break the ruleset: `haspadar.noPublicConstants` and
  `haspadar.immutable`. `readonly` does not help, because the parent constructor
  assigns the options. If you need a custom constraint, add a path-scoped ignore
  for `src/Validator/*` in `phpstan.neon` in the same change, and say why.
- CSRF protection is stateless. `config/packages/csrf.yaml` sets the token id to
  `submit`. Symfony adds `data-controller="csrf-protection"` to every rendered
  form, and `assets/controllers/csrf_protection_controller.js` sends the token.
  Never remove that Stimulus controller.

### How the golden path uses the form

1. `ExpenseType` declares the fields and the constraints. It sets no
   `data_class`, because `Expense` is immutable and Symfony cannot write into it.
2. `ExpenseController::create()` calls `handleRequest()`, then returns status 400
   with the same template when the form is not valid.
3. `SubmittedValues` turns each valid field into a typed value.
   `FormInterface::getData()` returns `mixed`, and PHPStan runs at level 10, so
   the narrowing happens once in that class and never in a controller.
4. `ExpenseService::record()` checks the values again. Keep both checks. The form
   guards the HTTP boundary; the service guards every caller.

If the service rejects what the form accepted, the two disagree. That is a bug,
and a 500 response is the correct loud signal. Do not catch it in the controller.

## Frontend

- Put pure logic in a module under `assets/<feature>/`, and test it directly.
- Put DOM work in a Stimulus controller under `assets/controllers/`.
  `assets/stimulus_bootstrap.js` registers every file in that directory.
- Prettier owns the format of every file except PHP, Twig and Markdown. Never
  fight it. `.editorconfig` and `.prettierrc` must agree.
- Markdown has no formatter. Wrap prose by hand near 80 columns. A table row may
  pass 80 columns, because a row cannot wrap.
- ESLint and Stylelint run with `--max-warnings=0`. A warning fails the build.

## Stack

- PHP 8.4 or later, Symfony 8.1, Doctrine ORM 3
- SQLite in every environment
- Twig, AssetMapper, Stimulus, Turbo
- PHPUnit 13, Vitest 3 with jsdom, Infection
- PHPStan level 10 with `haspadar/phpstan-rules`, deptrac, PHP-CS-Fixer
- ESLint 9 flat config, Prettier, Stylelint 17

## The SQLite invariant

SQLite runs in every environment. Three files must agree. Change them together:

- `.env` — `sqlite:///%kernel.project_dir%/var/expense.db`
- `phpunit.xml.dist` — `sqlite:///:memory:`
- `config/packages/doctrine.yaml` — no platform-specific settings

No gate checks this. If you change one file, change the other two and this
section in the same commit.

The SQLite file lives directly in `var/`, because SQLite does not create parent
directories.

## Mutation testing

```
composer infection
```

`minMsi` and `minCoveredMsi` are both 100. Every mutant must die. Run
`composer verify:full` before you finish a feature. It runs every gate and then
mutation testing.

Mutation testing needs Xdebug. The `composer infection` script sets
`XDEBUG_MODE=coverage` for you, so Xdebug stays off for every other command. Do
not set that variable yourself.

If Infection reports `CoverageChecker`, Xdebug is not loaded.

An escaped mutant marks a missing test, most often a missing boundary case. Read
`var/infection/infection.log`, then write the test. Never lower `minMsi`.

## Commit style

- Write all commits in lower case
- Start with a present-tense imperative verb: `add`, `fix`, `remove`, `refactor`,
  `update`, `rename`
- Keep to one logical change per commit
- Use a single-line subject only (no body)
- Use bare description for a single focus: `add openspec skills`
- Use parenthetical details for grouped items:
  `add php tooling (cs fixer, phpstan, deptrac)`

## Writing style

Use ASD-STE100 Simplified Technical English in code comments, docblocks, commit
messages, UI text and documentation. Short sentences. Active voice. Present
tense. One instruction per sentence.
