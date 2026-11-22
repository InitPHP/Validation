# Error Messages

When a rule fails, the validator builds a message from a template and adds it to
the error list. This page covers placeholders, custom messages and labels.
Switching language is covered in [Localization](Localization.md).

## Reading errors

```php
use InitPHP\Validation\Validation;

$v = new Validation(['age' => 5]);
$v->rule('age', 'min(18)');
$v->validation();

$v->getError();   // ["age must be greater than or equal to 18."]
```

`getError()` returns a **flat list** of message strings from the most recent
`validation()` run. The field name is embedded in each message (see
[per-field errors](#getting-errors-per-field) below).

## Placeholders

Templates support `{field}` and positional placeholders:

| Placeholder | Value |
| --- | --- |
| `{field}` / `{0}` | The field name (passed through [labels](#labels)). |
| `{1}` | The field value being validated. |
| `{2}` | The first rule argument. |
| `{3}`, `{4}`, ... | The second, third, ... rule arguments. |

```php
$v->rule('age', 'min(18)');
// template: "{field} must be greater than or equal to {2}."
// result:   "age must be greater than or equal to 18."   ({2} = 18)
```

## Per-rule custom messages

The third argument to `rule()` overrides the message for that call, with the
same placeholders available:

```php
$v->rule('age', 'min(18)', '{field} is too young (min {2}).');
// "age is too young (min 18)."
```

This works for callback rules too:

```php
$v->rule('number', static fn ($value): bool => ((int) $value % 2) === 0,
    '{field} must be even.');
```

## Labels

`labels()` maps raw field names to friendly display names. Only `{field}` is
passed through labels — **rule arguments are never label-substituted**, so a
value that happens to match a label is left untouched.

```php
$v->labels(['age' => 'Age', 'email' => 'E-mail address']);
$v->rule('age', 'min(18)');
// "Age must be greater than or equal to 18."
```

## Several errors

Every failed rule adds its own message, so one run can return many:

```php
$v = new Validation(['a' => 'x', 'b' => 'y']);
$v->rule('a', 'integer');
$v->rule('b', 'integer');
$v->validation();
$v->getError();   // 2 messages
```

## Getting errors per field

`getError()` is a flat list. When you need messages grouped by field, validate
each field in its own run and collect the results — remember that `validation()`
consumes the queue each time:

```php
$rules = [
    'username' => 'required|alphanum|length(3...20)',
    'email'    => 'required|mail',
    'age'      => 'optional|integer|range(18...120)',
];

$errors = [];
foreach ($rules as $field => $rule) {
    $v->rule($field, $rule);
    if (!$v->validation()) {
        $errors[$field] = $v->getError();
    }
}
// $errors['email'] => ["email must be an e-mail address."]
```

Alternatively, write each message with a custom template so it is
self-describing in a flat list.

## Adding a message manually

`setError()` appends an interpolated message to the current list:

```php
$v->setError('{field} could not be processed.', ['field' => 'avatar']);
```

Note that `validation()` clears the error list at the **start** of each run, so
add manual errors *after* validating.

## Next

- [Localization](Localization.md) — translate messages or ship your own language.
- [Rules Reference](Rules-Reference.md) — every built-in rule and its arguments.
