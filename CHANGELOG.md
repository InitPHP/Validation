# Change Log

## 2.0.0 [2026.06.10]

A breaking release focused on correctness, safety and developer experience.
See [docs/upgrading-from-1.x.md](./docs/upgrading-from-1.x.md) for migration
notes.

### Breaking

- **Requires PHP 8.1+** (was 7.4+).
- **Unknown rules now throw** `UndefinedRuleException` instead of silently
  passing validation.
- **Removed dispatch to arbitrary global functions.** A rule name is now only
  resolved to a built-in rule or a rule registered with `extend()`. Use a
  callback or `extend()` for custom logic.
- `setLocale()` / `setLocaleDir()` throw `LocaleException`, and `rule()` throws
  `Exception\InvalidArgumentException` (both implement `ExceptionInterface`).

### Fixed

- **Callable rules no longer throw a `TypeError`** — passing a closure to
  `rule()` now works as documented.
- The `optional` flag no longer leaks across `validation()` runs.
- The English `again` message resolves correctly (was keyed `repeat`).
- Message keys resolve case-insensitively (`creditcard` and `creditCard` both
  produce the credit-card message).
- Argument values are no longer accidentally passed through `labels` (operator
  precedence bug in interpolation).
- Open-ended `length` works: `length(...255)` now enforces the maximum.
- `length` on a `null` value returns `false` instead of raising a warning.
- `min` / `max` on arrays measure element count without an "array to string"
  warning.
- `equals` and `again` compare loosely (`equals(123)` matches integer `123`).
- Rule arguments are trimmed (`only(a, b, c)` behaves like `only(a,b,c)`).
- The built-in `slug` regex pattern matches multiple characters (it was missing
  its `+` quantifier).
- Corrected the Turkish `notContains` message.

### Added

- `extend()` for registering reusable custom named rules with optional messages.
- `getData()` accessor.
- `alphanumeric` as an alias of `alphanum`.
- A typed exception hierarchy under `InitPHP\Validation\Exception` behind the
  `ExceptionInterface` marker.

### Internal / tooling

- `declare(strict_types=1)`, typed signatures and full PHPDoc throughout.
- PSR-12 via PHP-CS-Fixer and PHPStan at max level — both clean.
- Expanded test suite (86 tests) covering rules, messages, locales, the
  optional lifecycle, callables, `extend()` and the fixed edge cases.
- GitHub Actions CI (PHP 8.1–8.4, lowest/highest deps, style, static analysis,
  coverage) and `composer` scripts (`test`, `stan`, `cs-check`, `cs-fix`, `ci`).
- Documentation under [`docs/`](./docs/README.md).

### 1.0.2 [2022.07.05]

- Unit test written.

### 1.0.1 [2022.03.19]

- Fixed the issue where errors were not handled in optional data.
- The result of the last validation operation `isValid()` method can be used.
