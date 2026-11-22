# Exceptions

Every exception the package throws lives under `InitPHP\Validation\Exception` and
implements the marker interface `ExceptionInterface`, so you can catch them all
at once.

```
Throwable
└── ExceptionInterface (interface)
    ├── ValidationException        (extends \RuntimeException)
    │   ├── UndefinedRuleException
    │   └── LocaleException
    └── InvalidArgumentException   (extends \InvalidArgumentException)
```

> A **failed validation is not an exception** — it is the normal `false` return
> of `validation()`, with messages in `getError()`. Exceptions are reserved for
> configuration and programming errors.

## UndefinedRuleException

Thrown by `validation()` when a string rule names a rule that is neither built-in
nor registered with [`extend()`](Callable-and-Custom-Rules.md). Unknown rules fail
loudly rather than silently passing — a misspelled rule is a bug, not a valid
field.

```php
use InitPHP\Validation\Exception\UndefinedRuleException;

$v->rule('age', 'integerr');   // typo
try {
    $v->validation();
} catch (UndefinedRuleException $e) {
    echo $e->getMessage();     // 'The validation rule "integerr" is not defined.'
}
```

## InvalidArgumentException

Thrown by `rule()` when a key or rule entry has an unsupported type — for example
an array rule entry that is neither a string nor a callable. It extends the SPL
`\InvalidArgumentException` and also implements `ExceptionInterface`.

```php
use InitPHP\Validation\Exception\InvalidArgumentException;

try {
    $v->rule('age', [123]);   // 123 is not a string or callable
} catch (InvalidArgumentException $e) {
    // ...
}
```

## LocaleException

Thrown by `setLocale()` and `setLocaleDir()` when a locale cannot be loaded: the
directory does not exist, the file is missing, or the file does not return an
array.

```php
use InitPHP\Validation\Exception\LocaleException;

try {
    $v->setLocale('xx');
} catch (LocaleException $e) {
    // could not find the language file
}
```

## Catching everything

```php
use InitPHP\Validation\Exception\ExceptionInterface;

try {
    $v->rule('age', 'integer')->validation();
} catch (ExceptionInterface $e) {
    // any failure originating from InitPHP Validation
}
```

## Next

- [API Reference](API-Reference.md) — which method throws what.
- [Troubleshooting](Troubleshooting.md) — common causes and fixes.
