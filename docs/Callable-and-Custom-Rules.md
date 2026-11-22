# Callable & Custom Rules

When the [built-in rules](Rules-Reference.md) are not enough, there are three ways
to add your own logic: callback rules, mixed rule arrays, and registered named
rules.

## Callback rules

Pass a callable as the rule. It receives the field value and returns a boolean.
The third argument to `rule()` is the message used when it returns `false`.

```php
use InitPHP\Validation\Validation;

$v = new Validation(['number' => 13]);

$v->rule('number', static function ($value): bool {
    return ($value % 2) === 0;
}, '{field} must be an even number.');

$v->validation();   // false
$v->getError();     // ["number must be an even number."]
```

The callback is always called with the field value, or `null` when the field is
absent. Without a custom message a failed callback uses the generic message
("The {field} value is not valid.").

```php
// the value is null here because 'missing' has no data
$v->rule('missing', static function ($value): bool {
    return $value !== null;
});
```

## Mixed rule arrays

A rule can be an array that mixes DSL strings and callbacks. They run in order,
left to right.

```php
$v->rule('quantity', [
    'required',
    'integer',
    static fn ($value): bool => $value > 0,
]);
```

## Named rules with `extend()`

Callbacks are per-field. To reuse a rule across fields — and call it from a DSL
string **with arguments** — register it with `extend()`:

```php
$v->extend(
    'divisible',
    static fn ($value, $by): bool => ((int) $value % (int) $by) === 0,
    '{field} must be divisible by {2}.'
);

$v->rule('quantity', 'divisible(5)');
$v->rule('weight', 'divisible(10)');
```

`extend(string $name, callable $callback, ?string $message = null)`:

- `$name` is matched **case-insensitively**, like the built-in rules.
- `$callback` receives the value followed by any DSL arguments (always strings)
  and returns a boolean.
- `$message` is optional; when given it becomes the template for that rule and
  can use `{field}` and [positional placeholders](Error-Messages.md).

### Registered rules persist

A registered rule survives `validation()` runs and is **not** cleared by
`clear()`, so register it once during setup:

```php
$v->extend('even', static fn ($value): bool => ((int) $value % 2) === 0);

$v->setData(['a' => 4])->rule('a', 'even')->validation();   // true
$v->setData(['a' => 5])->rule('a', 'even')->validation();   // false
```

### Overriding a built-in rule

A registered rule with the same name as a built-in **overrides** it — useful for
swapping in a stricter check:

```php
$v->extend('mail', static function ($value): bool {
    return is_string($value)
        && filter_var($value, FILTER_VALIDATE_EMAIL)
        && str_ends_with($value, '@example.com');
});
```

## Which one to use?

| Need | Use |
| --- | --- |
| One-off check for a single field | Callback rule |
| Several checks, some custom, on one field | Mixed rule array |
| A reusable rule shared across fields, usable in the DSL string | `extend()` |

## A worked example: a domain rule

```php
use InitPHP\Validation\Validation;

$v = new Validation(['website' => 'https://example.org']);

$v->extend(
    'httpsUrl',
    static fn ($value): bool => is_string($value) && str_starts_with($value, 'https://'),
    '{field} must use HTTPS.'
);

$valid = $v->rule('website', 'required|url|httpsUrl')->validation();
```

## Next

- [Error Messages](Error-Messages.md) — message templates and placeholders.
- [Patterns & Regex](Patterns-and-Regex.md) — reusable regex via `pattern()`.
- [API Reference](API-Reference.md) — `extend()` and the rest of the surface.
