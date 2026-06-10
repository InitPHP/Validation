# Custom rules & callables

When the built-in rules are not enough, there are three ways to add your own
logic: callback rules, mixed rule arrays, and registered named rules.

## Callback rules

Pass a callable as the rule. It receives the field value and returns a boolean.
The third argument to `rule()` is the message used when it returns `false`.

```php
use InitPHP\Validation\Validation;

$v = new Validation(['number' => 13]);

$v->rule('number', static function ($value): bool {
    return ($value % 2) === 0;
}, '{field} must be an even number.');

$v->validation();        // false
$v->getError();          // ["number must be an even number."]
```

The callback is always called with the field value, or `null` when the field is
absent. Without a custom message, a failed callback uses the generic
`callable` message ("The {field} value is not valid.").

## Mixed rule arrays

A rule can be an array that mixes DSL strings and callbacks. They run in order.

```php
$v->rule('quantity', [
    'required',
    'integer',
    static fn ($value): bool => $value > 0,
]);
```

## Named rules with `extend()`

Callbacks are per-field. To reuse a rule across fields — and use it inside a DSL
string with arguments — register it with `extend()`:

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

- `$name` is matched case-insensitively, like the built-in rules.
- `$callback` receives the value followed by any DSL arguments (as strings) and
  returns a boolean.
- `$message` is optional; when given, it becomes the template for that rule and
  can use `{field}` and positional placeholders.

Registered rules persist across `validation()` runs and are **not** cleared by
`clear()`, so register them once during setup:

```php
$v->extend('even', static fn ($value): bool => ((int) $value % 2) === 0);

$v->setData(['a' => 4])->rule('a', 'even')->validation();   // true
$v->setData(['a' => 5])->rule('a', 'even')->validation();   // false
```

A registered rule can also override a built-in one by using the same name.

## Which one to use?

| Need | Use |
| ---- | --- |
| One-off check for a single field | Callback rule |
| Several checks, some custom, on one field | Mixed rule array |
| A reusable rule shared across fields, usable in the DSL string | `extend()` |

See also [localization & messages](localization-and-messages.md) for message
placeholders.
