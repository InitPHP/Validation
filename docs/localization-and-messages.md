# Localization & messages

## Placeholders

Message templates support two kinds of placeholder:

- `{field}` — the field name (passed through [labels](#labels) if one is set).
- Positional `{0}`, `{1}`, `{2}`, ... — `{1}` is the field value and `{2}` is
  the first rule argument, `{3}` the second, and so on.

```php
use InitPHP\Validation\Validation;

$v = new Validation(['age' => 5]);
$v->rule('age', 'min(18)');
$v->validation();
$v->getError();      // ["age must be greater than or equal to 18."]  ({2} = 18)
```

## Per-rule custom messages

The third argument to `rule()` overrides the message for that call. It is
interpolated with the same placeholders.

```php
$v->rule('age', 'min(18)', '{field} is too young (min {2}).');
// "age is too young (min 18)."
```

## Labels

`labels()` maps raw field names to friendly display names. Only `{field}` is
passed through labels — rule arguments are never label-substituted.

```php
$v->labels(['age' => 'Age', 'email' => 'E-mail address']);
$v->rule('age', 'min(18)');
// "Age must be greater than or equal to 18."
```

## Switching language

The package ships with `en` (the default) and `tr`. `setLocale()` loads a
language file and merges it over the active messages.

```php
$v->setLocale('tr');
$v->rule('age', 'integer');
// "age bir tam sayı olmalıdır."
```

## Overriding individual messages

`setLocaleArray()` merges a partial set of templates over the active locale —
useful for tweaking a few messages without a whole language file.

```php
$v->setLocaleArray([
    'integer'  => '{field} must be a whole number.',
    'required' => 'Please fill in {field}.',
]);
```

Message keys are matched case-insensitively against rule names, so a key of
`creditCard` or `creditcard` both apply to the `creditCard` rule.

## Using your own language directory

`setLocaleDir()` points the loader at a directory of `<locale>.php` files. Each
file returns an associative array of templates.

```php
$v->setLocaleDir(__DIR__ . '/lang');
$v->setLocale('de');     // loads __DIR__/lang/de.php
```

A language file looks like this:

```php
<?php
// lang/de.php
return [
    'required' => '{field} darf nicht leer sein.',
    'integer'  => '{field} muss eine Ganzzahl sein.',
    'mail'     => '{field} muss eine E-Mail-Adresse sein.',
    // ... one entry per rule you want to translate
];
```

Any rule without a matching key falls back to the `notValidDefault` template
("The {field} value is not valid."). The full list of keys is in
[`src/languages/en.php`](../src/languages/en.php).

## Manually adding an error

`setError()` appends a message to the current error list, interpolating any
context you pass:

```php
$v->setError('{field} could not be processed.', ['field' => 'avatar']);
```

Note that `validation()` clears the error list at the start of each run, so add
manual errors *after* validating (or in a flow that does not call
`validation()` again).
