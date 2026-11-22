# API Reference

Everything lives on `InitPHP\Validation\Validation`. Every setter returns
`$this` for chaining unless noted otherwise.

```php
use InitPHP\Validation\Validation;
```

## Contents

- [Data](#data): [`__construct`](#__construct) · [`setData`](#setdata) · [`mergeData`](#mergedata) · [`getData`](#getdata)
- [Rules](#rules): [`rule`](#rule) · [`extend`](#extend) · [`pattern`](#pattern)
- [Running](#running): [`validation`](#validation) · [`isValid`](#isvalid) · [`clear`](#clear)
- [Errors](#errors): [`getError`](#geterror) · [`setError`](#seterror)
- [Localization](#localization): [`setLocale`](#setlocale) · [`setLocaleDir`](#setlocaledir) · [`setLocaleArray`](#setlocalearray) · [`labels`](#labels)
- [Misc](#misc): [`version`](#version) · [`VERSION`](#version-constant)

---

## Data

### `__construct`

```php
public function __construct(array $data = [])
```

Create a validator with an optional initial data set.

### `setData`

```php
public function setData(array $data = []): self
```

Replace the entire data set.

### `mergeData`

```php
public function mergeData(array $data = []): self
```

Merge values into the current data set. Existing keys are overwritten.

### `getData`

```php
public function getData(): array
```

Return the current data set.

---

## Rules

### `rule`

```php
public function rule(string|array $key, string|callable|array $rule, ?string $err = null): self
```

Queue one or more rules for one or more fields.

- `$key` — a field name, a pipe-separated list (`"a|b"`), or an array of names.
- `$rule` — a pipe-separated DSL string, a callable, or an array mixing both.
  The pseudo-rule `optional` marks the field(s) as allowed to be absent.
- `$err` — an optional custom message template for these rules.

Throws [`InvalidArgumentException`](Exceptions.md#invalidargumentexception) if a key
or rule entry has an unsupported type.

### `extend`

```php
public function extend(string $name, callable $callback, ?string $message = null): self
```

Register a custom named rule usable from the DSL string. The callback receives
the value followed by any rule arguments and returns a boolean. Registered rules
persist across runs. See [Callable & Custom Rules](Callable-and-Custom-Rules.md).

### `pattern`

```php
public function pattern(string $name, string $pattern = '[\w]+'): self
```

Register or override a named pattern for the [`regex`](Rules-Reference.md#regex)
rule. Names are case-insensitive; the body has no delimiters and is matched as
`^(...)$`. See [Patterns & Regex](Patterns-and-Regex.md).

---

## Running

### `validation`

```php
public function validation(): bool
```

Evaluate every queued rule. Clears the error list, runs each rule (skipping
[optional](Optional-Fields.md) absent fields), then empties the rule and optional
queues. Returns `true` when nothing failed.

Throws [`UndefinedRuleException`](Exceptions.md#undefinedruleexception) if a string
rule names a rule that is neither built-in nor registered with `extend()`.

### `isValid`

```php
public function isValid(): bool
```

Return whether the most recent `validation()` run had no errors, without
re-running.

### `clear`

```php
public function clear(): self
```

Drop queued rules, optional flags and errors. Registered rules, patterns and the
loaded locale are kept.

---

## Errors

### `getError`

```php
public function getError(): array
```

Return the list of messages from the most recent `validation()` run.

### `setError`

```php
public function setError(string $error, array $context = []): self
```

Append an interpolated message to the current error list. Note `validation()`
resets the list at the start of each run.

---

## Localization

### `setLocale`

```php
public function setLocale(string $locale = 'en'): self
```

Load `<locale>.php` from the locale directory and merge it over the active
messages. Throws [`LocaleException`](Exceptions.md#localeexception) if the file is
missing or does not return an array.

### `setLocaleDir`

```php
public function setLocaleDir(string $dir = __DIR__ . '/languages/'): self
```

Set the directory language files are loaded from. Throws
[`LocaleException`](Exceptions.md#localeexception) if the directory does not exist.

### `setLocaleArray`

```php
public function setLocaleArray(array $assoc): self
```

Merge a partial set of message templates over the active locale.

### `labels`

```php
public function labels(array $assoc): self
```

Register display labels that replace raw field names in messages.

---

## Misc

### `version`

```php
public function version(): string
```

Return the library version.

### `VERSION` constant

```php
public const VERSION = '2.0.0';
```

The library version as a constant.

## Next

- [Exceptions](Exceptions.md) — the types thrown above.
- [Rules Reference](Rules-Reference.md) — what goes inside `rule()`.
