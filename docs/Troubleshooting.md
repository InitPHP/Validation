# Troubleshooting

Concrete symptoms and their fixes. For the bigger picture see
[The Validation Lifecycle](The-Validation-Lifecycle.md).

## `UndefinedRuleException: The validation rule "..." is not defined.`

The rule name is unknown. Causes:

- **A typo** — `integerr`, `requierd`, `lenght`. Fix the spelling.
- **A custom rule used before it was registered** — call
  [`extend()`](Callable-and-Custom-Rules.md#named-rules-with-extend) before
  `validation()`.
- **A global function name** — 2.0 no longer dispatches to global functions.
  Wrap it: `$v->extend('ctype_digit', fn ($x) => ctype_digit((string) $x));`

## An `optional` field still fails validation

`optional` skips a field only when it is **missing or `null`**. An empty string
is *present*, so its rules run.

```php
$v = new Validation(['nick' => '']);
$v->rule('nick', 'optional|alpha');
$v->validation();   // false — '' is present and not alphabetic
```

Guard on the value first, or allow blanks explicitly. See
[Optional Fields](Optional-Fields.md#when-is-a-field-absent).

## My second `validation()` only checks the new rules

That is by design — `validation()` consumes the queue each run. Re-queue any
rules you want re-checked. See
[The Validation Lifecycle](The-Validation-Lifecycle.md#queue--validate--read).

## `regex(...)` with a comma in it doesn't work

DSL arguments are split on commas, so an inline body like `[a-z]{2,4}` is broken
apart. Register it as a [named pattern](Patterns-and-Regex.md#named-patterns)
instead:

```php
$v->pattern('two_to_four', '[a-z]{2,4}');
$v->rule('code', 'regex(two_to_four)');
```

## `in()` on an array of integers returns false

Array membership is **strict**, and DSL arguments are always strings, so
`in(2)` looks for the string `"2"`, not the integer `2`.

```php
$v = new Validation(['ids' => [1, 2, 3]]);
$v->rule('ids', 'in(2)');     // false — "2" !== 2
```

For typed arrays, use a [callback rule](Callable-and-Custom-Rules.md#callback-rules):

```php
$v->rule('ids', static fn ($value): bool => in_array(2, (array) $value, true));
```

## The message shows the raw field name (`user_email`)

Register a friendly label:

```php
$v->labels(['user_email' => 'E-mail address']);
```

See [Error Messages](Error-Messages.md#labels).

## `LocaleException` when calling `setLocale()`

The language file was not found. Check that the file exists at
`<locale-dir>/<locale>.php` and returns an array. Point at a custom directory
with [`setLocaleDir()`](API-Reference.md#setlocaledir) before `setLocale()`.

## A manually `setError()`'d message disappeared

`validation()` clears the error list at the **start** of each run. Add manual
errors *after* validating, or in a flow that does not call `validation()` again.
See [Error Messages](Error-Messages.md#adding-a-message-manually).

## `min`/`max` measured length when I meant the number

For a numeric value, `min`/`max` compare the **value**; for a non-numeric string
they compare the **length**. A numeric string is treated as a number. If you
always want length, use [`length`](Rules-Reference.md#length); for a value range use
[`range`](Rules-Reference.md#range).

## Next

- [FAQ](FAQ.md) — conceptual questions.
- [Exceptions](Exceptions.md) — what each exception means.
