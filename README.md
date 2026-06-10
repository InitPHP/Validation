# InitPHP Validation

Fast, dependency-free data validation for PHP. Describe what each field should
look like with a small rule DSL (`"required|integer|range(1...10)"`), callbacks,
or custom named rules, and get back localized error messages.

[![CI](https://github.com/InitPHP/Validation/actions/workflows/ci.yml/badge.svg)](https://github.com/InitPHP/Validation/actions/workflows/ci.yml)
[![Latest Stable Version](http://poser.pugx.org/initphp/validation/v)](https://packagist.org/packages/initphp/validation) [![Total Downloads](http://poser.pugx.org/initphp/validation/downloads)](https://packagist.org/packages/initphp/validation) [![License](http://poser.pugx.org/initphp/validation/license)](https://packagist.org/packages/initphp/validation) [![PHP Version Require](http://poser.pugx.org/initphp/validation/require/php)](https://packagist.org/packages/initphp/validation)

- [Documentation](./docs/README.md)
- [Changelog](./CHANGELOG.md)

## Requirements

- PHP 8.1 or higher
- `ext-mbstring`

## Installation

```bash
composer require initphp/validation
```

## Quick start

```php
require 'vendor/autoload.php';

use InitPHP\Validation\Validation;

// GET /?name=Muhammet&year=2022
$validation = new Validation($_GET);

$validation->rule('name', 'required|string');
$validation->rule('year', 'integer|range(1970...2099)');

if ($validation->validation()) {
    // ... the data is valid
} else {
    foreach ($validation->getError() as $message) {
        echo $message . "\n";
    }
}
```

## How it works

1. **Give it data.** Pass an associative array to the constructor, or use
   `setData()` / `mergeData()`.
2. **Queue rules.** Each `rule()` call adds checks for one or more fields.
3. **Validate.** `validation()` runs every queued rule, returns `true` when
   nothing failed, and **consumes** the queued rules — so the usual flow is
   *queue → validate → read errors*, repeated as needed.
4. **Read errors.** `getError()` returns the messages from the most recent run.

```php
$validation = new Validation([
    'email'    => 'someone@example.com',
    'password' => 'secret',
    'confirm'  => 'secret',
]);

$valid = $validation
    ->rule('email', 'required|mail')
    ->rule('password', 'required|length(8...)')
    ->rule('confirm', 'again(password)')
    ->validation();
```

## Rules

A rule string is a pipe-separated list. Arguments go in parentheses and are
comma-separated; arguments are trimmed, so `only(a, b, c)` and `only(a,b,c)` are
equivalent. Rule names are matched case-insensitively.

| Rule | Description |
| ---- | ----------- |
| `required` | The value is present and not a blank string (numbers and non-empty arrays pass). |
| `optional` | Pseudo-rule. If the field is absent, its other rules are skipped instead of failing. |
| `empty` | The value is empty once trimmed. |
| `integer` | An integer or an integer-looking string. |
| `float` | A float/int or a float-looking string. |
| `numeric` | A numeric value. |
| `string` | A string. |
| `boolean` | A real boolean or one of `true`, `false`, `1`, `0`. |
| `array` | An array. |
| `alpha` | Letters only (Unicode-aware). |
| `alphanum` / `alphanumeric` | Letters and digits only. |
| `mail` | A valid e-mail address. |
| `mailHost(host, ...)` | A valid e-mail at one of the given hosts. |
| `url` | A valid URL. |
| `urlHost(domain, ...)` | A URL whose host equals, or is a subdomain of, one of the domains. |
| `ip` / `ipv4` / `ipv6` | A valid IP address of the given family. |
| `min(n)` | Numbers `>= n`; for strings/arrays the length/count `>= n`. |
| `max(n)` | Numbers `<= n`; for strings/arrays the length/count `<= n`. |
| `range(min...max)` | A number within the range. Also accepts `min-max`, `...max`, `min...`. |
| `length(min...max)` | String length / array count within range. A single number is the maximum; open bounds (`...max`, `min...`) are allowed. |
| `regex(name\|body)` | Matches a [named pattern](#named-patterns) or an inline regex body. |
| `date` | A `DateTimeInterface`, or any string `strtotime()` understands. |
| `dateFormat(format)` | A date string in the given `date()` format, e.g. `dateFormat(Y/m/d)`. |
| `again(field)` | Loosely equals the value of another field. |
| `equals(value)` | Loosely equals the given value. |
| `startWith(value)` | A string starting with the value, or an array whose first element equals it. |
| `endWith(value)` | A string ending with the value, or an array whose last element equals it. |
| `in(needle)` | Case-insensitive substring (strings) or strict membership (arrays). |
| `notIn(needle)` | The inverse of `in`. |
| `contains(needle)` | Case-sensitive substring. |
| `notContains(needle)` | The inverse of `contains`. |
| `only(a, b, ...)` | Case-insensitively equals one of the options. |
| `strictOnly(a, b, ...)` | Case-sensitively equals one of the options. |
| `creditCard(type?)` | A credit card number. Optional type: `amex`, `visa`, `mastercard`, `maestro`, `jcb`, `solo`, `switch`. |

See [`docs/rules-reference.md`](./docs/rules-reference.md) for a per-rule
reference with examples.

## Callable rules

Pass a callback as the rule. It receives the field value and returns a boolean.
The third argument to `rule()` is a custom message.

```php
$validation = new Validation(['number' => 13]);

$validation->rule('number', static function ($value): bool {
    return ($value % 2) === 0;
}, '{field} must be an even number.');

$validation->validation();            // false
$validation->getError();              // ["number must be an even number."]
```

You can also mix strings and callbacks in one array:

```php
$validation->rule('number', ['integer', static fn ($v): bool => $v > 0]);
```

## Custom named rules

Register reusable rules with `extend()` so they work inside the DSL string,
arguments and all:

```php
$validation->extend(
    'divisible',
    static fn ($value, $by): bool => ((int) $value % (int) $by) === 0,
    '{field} must be divisible by {2}.'
);

$validation->rule('quantity', 'divisible(5)');
```

## Optional fields

`optional` skips a field's rules when it has no value, but still validates it
when present:

```php
$validation->rule('nickname', 'optional|alpha|length(3...20)');
```

## Named patterns

The `regex` rule can reference a named pattern. Built-in names include `uri`,
`slug`, `url`, `alpha`, `words`, `alphanum`, `int`, `float`, `tel`, `text`,
`file`, `folder`, `address`, `date_dmy`, `date_ymd` and `email`. Register your
own with `pattern()`:

```php
$validation->pattern('product_code', '[A-Z]{2}-[0-9]{4}');
$validation->rule('code', 'regex(product_code)');
```

## Error messages and localization

Messages support `{field}` and positional placeholders: `{1}` is the value and
`{2}` is the first rule argument.

```php
$validation->rule('age', 'min(18)');
// "age must be greater than or equal to 18."
```

Replace raw field names with friendly labels:

```php
$validation->labels(['age' => 'Age']);
// "Age must be greater than or equal to 18."
```

Switch language (the package ships with `en` and `tr`), or override individual
messages:

```php
$validation->setLocale('tr');
$validation->setLocaleArray(['integer' => '{field} is not a whole number.']);
```

Point at your own language directory with `setLocaleDir()`. See
[`docs/localization-and-messages.md`](./docs/localization-and-messages.md).

## Exceptions

Everything thrown by the package implements
`InitPHP\Validation\Exception\ExceptionInterface`:

- `UndefinedRuleException` — a rule name was used that is neither built-in nor
  registered with `extend()`. Unknown rules fail loudly instead of silently
  passing.
- `InvalidArgumentException` — a key or rule entry had an unsupported type.
- `LocaleException` — a locale directory or file could not be loaded.

## Upgrading from 1.x

Version 2.0 is a breaking release. The headline changes:

- **Requires PHP 8.1+** (was 7.4+).
- **Callable rules now work** — they previously threw a `TypeError`.
- **Unknown rules now throw** `UndefinedRuleException` instead of silently
  passing validation.
- **Rules are no longer dispatched to arbitrary global functions** (a
  surprising and unsafe behaviour). Use `extend()` for custom logic.
- Error-message keys resolve case-insensitively, the `again` message now
  resolves in English, and several edge cases were fixed (argument trimming,
  open-ended `length`/`range` bounds, null-safety, loose `equals`/`again`).

Full notes: [`docs/upgrading-from-1.x.md`](./docs/upgrading-from-1.x.md).

## Testing

```bash
composer test       # PHPUnit
composer stan       # PHPStan (max level)
composer cs-check   # PHP-CS-Fixer (dry run)
composer ci         # all of the above
```

## Contributing

Contributions are welcome. Please read the org-wide
[Contributing guide](https://github.com/InitPHP/.github/blob/main/CONTRIBUTING.md)
and open a pull request against `main`. New behaviour should come with tests,
and `composer ci` should pass.

## Credits

- [Muhammet ŞAFAK](https://www.muhammetsafak.com.tr) <<info@muhammetsafak.com.tr>>

## License

Released under the [MIT License](./LICENSE). Copyright &copy; 2022 InitPHP.
