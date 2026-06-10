# Exceptions

Every exception thrown by the package lives under
`InitPHP\Validation\Exception` and implements the marker interface
`ExceptionInterface`, so you can catch them all at once.

```
Throwable
└── ExceptionInterface (interface)
    ├── ValidationException        (extends RuntimeException)
    │   ├── UndefinedRuleException
    │   └── LocaleException
    └── InvalidArgumentException   (extends \InvalidArgumentException)
```

## `UndefinedRuleException`

Thrown by `validation()` when a string rule names a rule that is neither
built-in nor registered with `extend()`. Unknown rules fail loudly rather than
silently passing — a misspelled rule is a bug, not a valid field.

```php
use InitPHP\Validation\Exception\UndefinedRuleException;

$v->rule('age', 'integerr');   // typo
try {
    $v->validation();
} catch (UndefinedRuleException $e) {
    // 'The validation rule "integerr" is not defined.'
}
```

## `InvalidArgumentException`

Thrown by `rule()` when a key or rule entry has an unsupported type — for
example an array rule entry that is neither a string nor a callable. It extends
the SPL `\InvalidArgumentException` and also implements `ExceptionInterface`.

```php
$v->rule('age', [123]);   // 123 is not a string or callable → thrown here
```

## `LocaleException`

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

Note that a failed **validation** is not an exception — it is the normal
`false` return of `validation()`, with messages in `getError()`. Exceptions are
reserved for configuration and programming errors.
