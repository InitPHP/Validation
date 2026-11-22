# Localization

The package ships with **English (`en`, the default)** and **Turkish (`tr`)**,
and lets you override individual messages or load your own language files.

## Switching language

`setLocale()` loads a language file and merges it over the active messages.

```php
use InitPHP\Validation\Validation;

$v = new Validation(['age' => 'abc']);
$v->setLocale('tr');
$v->rule('age', 'integer');
$v->validation();
$v->getError();   // ["age bir tam sayı olmalıdır."]
```

The loaded locale persists across `validation()` runs, so call `setLocale()`
once during setup.

## Overriding individual messages

`setLocaleArray()` merges a partial set of templates over the active locale —
handy for tweaking a few messages without a whole file.

```php
$v->setLocaleArray([
    'integer'  => '{field} must be a whole number.',
    'required' => 'Please fill in {field}.',
]);
```

Message keys are matched **case-insensitively** against rule names, so a key of
`creditCard` or `creditcard` both apply to the `creditCard` rule. Placeholders
work exactly as in [Error Messages](Error-Messages.md).

## Using your own language directory

`setLocaleDir()` points the loader at a directory of `<locale>.php` files; then
`setLocale()` loads from there.

```php
$v->setLocaleDir(__DIR__ . '/lang');
$v->setLocale('de');     // loads __DIR__/lang/de.php
```

A language file returns an associative array of templates:

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
("The {field} value is not valid.").

## The message keys

Keys are rule names. The full set ships in
[`src/languages/en.php`](https://github.com/InitPHP/Validation/blob/main/src/languages/en.php):

```
notValidDefault, callable, integer, float, numeric, string, boolean, array,
mail, mailHost, url, urlHost, empty, required, min, max, length, range, regex,
date, dateFormat, ip, ipv4, ipv6, again, equals, startWith, endWith, in, notIn,
alpha, alphaNum, alphanumeric, creditCard, only, strictOnly, contains, notContains
```

The special `callable` key is used when a [callback rule](Callable-and-Custom-Rules.md)
fails without its own message; `notValidDefault` is the catch-all fallback.

## Order matters

`setLocale()` and `setLocaleArray()` **merge** over what is already active.
Apply the base language first, then your overrides:

```php
$v->setLocale('tr');                                  // base language
$v->setLocaleArray(['mail' => '{field} hatalı.']);    // then tweak
```

## Next

- [Error Messages](Error-Messages.md) — placeholders, labels and custom messages.
- [API Reference](API-Reference.md) — the locale methods in full.
