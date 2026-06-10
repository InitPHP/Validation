# Upgrading from 1.x to 2.0

Version 2.0 is a breaking release that fixes long-standing bugs and tightens the
API. Most existing rule strings keep working unchanged; the breaking changes are
about PHP version, error behaviour and a few corrected edge cases.

## Requirements

- **PHP 8.1 or newer** is now required (1.x supported 7.4+).

## Behaviour changes you may need to act on

### Unknown rules now throw

In 1.x a misspelled or unknown rule was silently ignored and the field counted
as valid. In 2.0 it throws `UndefinedRuleException`.

```php
$v->rule('age', 'integerr');   // 1.x: silently passes — 2.0: throws
```

Fix any typos, and register custom names with [`extend()`](custom-rules-and-callables.md).

### Rules are no longer dispatched to global functions

In 1.x a rule name that matched a global function (e.g. `ctype_digit`) was
invoked with the field value. This was surprising and unsafe and has been
removed. Such names now throw `UndefinedRuleException`. Wrap the logic in a
callback or `extend()` instead:

```php
$v->extend('ctype_digit', static fn ($value): bool => ctype_digit((string) $value));
```

### Callable rules now work

Passing a callback as the rule used to throw a `TypeError`. It now works as
documented:

```php
$v->rule('number', static fn ($value): bool => ($value % 2) === 0, '{field} must be even.');
```

### Error messages

- The `again` rule now resolves its English message (it previously fell back to
  the generic "not valid" text).
- Message keys resolve case-insensitively, so writing `creditcard` or
  `creditCard` produces the same message.
- The Turkish `notContains` message now reads "must not contain".

If you relied on the old (incorrect) message strings in tests, update those
expectations.

### Corrected edge cases

These were bugs; the new behaviour is what the documentation always implied:

- Arguments are trimmed, so `only(a, b, c)` matches the same way as `only(a,b,c)`.
- Open-ended `length` works: `length(...255)` now enforces the maximum.
- `length` on a `null` value returns `false` instead of raising a warning.
- `min`/`max` on arrays measure the element count without a type warning.
- `equals` and `again` compare loosely, so `equals(123)` matches the integer
  `123`.

## Exceptions

`setLocale()` / `setLocaleDir()` now throw `InitPHP\Validation\Exception\LocaleException`
(previously `\InvalidArgumentException` / `\Exception`), and `rule()` throws
`InitPHP\Validation\Exception\InvalidArgumentException`. All package exceptions
implement `InitPHP\Validation\Exception\ExceptionInterface`. If you catch the
SPL types, note that `InvalidArgumentException` still extends
`\InvalidArgumentException`; the locale errors now need the new type or the
marker interface.

## New in 2.0

- [`extend()`](custom-rules-and-callables.md) for reusable custom named rules.
- [`getData()`](api-reference.md#getdata-array) accessor.
- An `alphanumeric` alias for `alphanum`.
- Typed signatures, full PHPDoc, PHPStan (max) and PSR-12 compliance.

## Quick checklist

1. Bump your PHP requirement to 8.1+.
2. Search your rule strings for typos and any global-function names.
3. Update tests that asserted old message text.
4. Catch the new exception types where you load locales.
