# API reference

All methods live on `InitPHP\Validation\Validation`. Every setter returns
`$this` for chaining unless noted otherwise.

## Data

### `__construct(array $data = [])`

Create a validator with an optional initial data set.

### `setData(array $data = []): self`

Replace the entire data set.

### `mergeData(array $data = []): self`

Merge values into the current data set. Existing keys are overwritten.

### `getData(): array`

Return the current data set.

## Rules

### `rule(string|array $key, string|callable|array $rule, ?string $err = null): self`

Queue one or more rules for one or more fields.

- `$key` — a field name, a pipe-separated list (`"a|b"`), or an array of names.
- `$rule` — a pipe-separated DSL string, a callable, or an array mixing both.
  The pseudo-rule `optional` marks the field(s) as allowed to be absent.
- `$err` — an optional custom message template for these rules.

Throws `Exception\InvalidArgumentException` if a key or rule entry has an
unsupported type.

### `extend(string $name, callable $callback, ?string $message = null): self`

Register a custom named rule usable from the DSL string. The callback receives
the value followed by any rule arguments and returns a boolean. Registered rules
persist across runs.

### `pattern(string $name, string $pattern = '[\w]+'): self`

Register or override a named pattern for the `regex` rule. Names are
case-insensitive; the body has no delimiters and is matched as `^(...)$`.

## Running

### `validation(): bool`

Evaluate every queued rule. Clears the error list, runs each rule (skipping
optional absent fields), then empties the rule and optional queues. Returns
`true` when nothing failed.

Throws `Exception\UndefinedRuleException` if a string rule names a rule that is
neither built-in nor registered with `extend()`.

### `isValid(): bool`

Return whether the most recent `validation()` run had no errors, without
re-running.

### `clear(): self`

Drop queued rules, optional flags and errors. Registered rules, patterns and the
loaded locale are kept.

## Errors

### `getError(): array`

Return the list of messages from the most recent `validation()` run.

### `setError(string $error, array $context = []): self`

Append an interpolated message to the current error list. Note `validation()`
resets the list at the start of each run.

## Localization

### `setLocale(string $locale = 'en'): self`

Load `<locale>.php` from the locale directory and merge it over the active
messages. Throws `Exception\LocaleException` if the file is missing or does not
return an array.

### `setLocaleDir(string $dir = …): self`

Set the directory language files are loaded from. Throws
`Exception\LocaleException` if the directory does not exist.

### `setLocaleArray(array $assoc): self`

Merge a partial set of message templates over the active locale.

### `labels(array $assoc): self`

Register display labels that replace raw field names in messages.

## Misc

### `version(): string`

Return the library version (`Validation::VERSION`).

## Exceptions

See [exceptions](exceptions.md). All implement
`InitPHP\Validation\Exception\ExceptionInterface`.
