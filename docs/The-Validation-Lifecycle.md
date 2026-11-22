# The Validation Lifecycle

Understanding the order of operations makes everything else click. A validator
holds four pieces of state: the **data**, a **rule queue**, a list of
**optional** fields, and the **errors** from the last run.

## Queue → validate → read

```php
use InitPHP\Validation\Validation;

$validation = new Validation(['age' => 'abc']);

$validation->rule('age', 'integer');   // 1. queue
$ok = $validation->validation();        // 2. validate  → false
$errors = $validation->getError();      // 3. read       → ["age must be an integer."]
```

`rule()` does **not** check anything — it only appends to the queue.
`validation()` is what actually runs the checks.

## What `validation()` does, in order

1. **Clears the error list.** Each run starts clean.
2. **Runs every queued rule.** For each rule it reads the field value from the
   data, runs the check, and appends a message on failure.
   [Optional](Optional-Fields.md) fields with no value are skipped.
3. **Empties the rule queue and the optional list.**
4. **Returns** `true` when no rule failed.

Because step 3 consumes the queue, the natural pattern is **queue → validate →
read, then repeat**:

```php
$validation->setData(['a' => 'x', 'b' => '5']);

$validation->rule('a', 'integer');
$validation->validation();   // checks only 'a' → false

$validation->rule('b', 'integer');
$validation->validation();   // checks only 'b' → true (the 'a' rule is gone)
```

If you expected the second run to re-check `a`, queue it again — rules are not
sticky.

## The data API

| Method | Effect |
| --- | --- |
| `new Validation($data)` | Start with a data set. |
| `setData($data)` | Replace the whole data set. |
| `mergeData($data)` | Merge in values; existing keys are overwritten. |
| `getData()` | Read the current data set back. |

```php
$validation = new Validation(['a' => 1]);
$validation->mergeData(['b' => 2, 'a' => 3]);
$validation->getData(); // ['a' => 3, 'b' => 2]
```

## `validation()` vs `isValid()`

- `validation()` **runs** the queue and returns the result.
- `isValid()` returns the result of the **last** run without re-running.

```php
$validation->rule('a', 'integer')->validation(); // runs
$validation->isValid();                            // same answer, no re-run
```

## Resetting with `clear()`

`clear()` throws away the queued rules, the optional flags and the errors —
without validating. Registered custom rules, named patterns and the loaded
locale are kept.

```php
$validation->rule('a', 'integer');
$validation->clear();          // queue dropped
$validation->validation();      // nothing to check → true
```

## What survives a run

| State | Cleared by `validation()` | Cleared by `clear()` |
| --- | --- | --- |
| Errors | Yes (at the start) | Yes |
| Rule queue | Yes (at the end) | Yes |
| Optional flags | Yes (at the end) | Yes |
| Data | No | No |
| [Custom rules](Callable-and-Custom-Rules.md) (`extend`) | No | No |
| [Named patterns](Patterns-and-Regex.md) (`pattern`) | No | No |
| Loaded [locale](Localization.md) | No | No |

So register your custom rules, patterns and locale **once** during setup, then
loop over *queue → validate → read* as many times as you like.

## Next

- [Rules Reference](Rules-Reference.md) — the full catalogue of checks.
- [Optional Fields](Optional-Fields.md) — how absent fields are handled.
- [Error Messages](Error-Messages.md) — customizing what users see.
