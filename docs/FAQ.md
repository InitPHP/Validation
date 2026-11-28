# FAQ

### Does a failed validation throw an exception?

No. A failed validation is the normal `false` return of `validation()`, with the
messages available from `getError()`. Exceptions are reserved for programming and
configuration errors — see [Exceptions](Exceptions.md).

### Why did my rule throw `UndefinedRuleException`?

The rule name is not a built-in rule or a rule registered with `extend()` —
usually a typo (`integerr`, `requierd`). Unknown rules fail loudly so a typo
never silently skips validation. See
[Migration from 1.x](Migration-from-1.x.md#unknown-rules-now-throw).

### Why doesn't my second `validation()` re-check earlier rules?

`validation()` **consumes** the rule queue. Each run validates only the rules
queued since the last run. Queue rules again if you need them re-checked — see
[The Validation Lifecycle](The-Validation-Lifecycle.md).

### How do I get errors grouped by field?

`getError()` returns a flat list. Validate each field in its own run and key the
results, or use self-describing custom messages. See
[Error Messages](Error-Messages.md#getting-errors-per-field) and the
[Recipes](Recipes.md#errors-grouped-by-field).

### My `optional` field still fails — why?

`optional` only skips a field that is **missing or `null`**. An empty string
(`''`) counts as present, so its rules still run. See
[Optional Fields](Optional-Fields.md#when-is-a-field-absent).

### How do I add a rule the library doesn't have?

Three ways: a [callback rule](Callable-and-Custom-Rules.md#callback-rules) for a
one-off check, a [mixed rule array](Callable-and-Custom-Rules.md#mixed-rule-arrays),
or [`extend()`](Callable-and-Custom-Rules.md#named-rules-with-extend) for a reusable
named rule.

### Can I validate arrays / nested data?

The value of a field can be an array — rules like
[`array`](Rules-Reference.md#array), [`in`](Rules-Reference.md#in),
[`min`](Rules-Reference.md#min) (element count) and
[`startWith`](Rules-Reference.md#startwith) understand arrays. For deeply nested
structures, validate each sub-array with its own `setData()` /
`validation()` cycle (see [Recipes → Validate a list](Recipes.md#validate-a-list-of-items)).

### Can I reuse one validator instance?

Yes — and you should. Configure the locale, labels, patterns and custom rules
once; they survive every run. Only the rule queue and errors reset. See
[Recipes](Recipes.md#a-reusable-pre-configured-validator).

### Which languages ship with the package?

English (`en`, default) and Turkish (`tr`). You can override messages or load
your own language directory — see [Localization](Localization.md).

### Which PHP versions are supported?

PHP 8.2 and newer, with `ext-mbstring`. CI runs 8.2, 8.3, 8.4, 8.5 and 8.6. Older
runtimes need an earlier release: 2.0.0 supports PHP 8.1, and the 1.x line
supports PHP 7.4.

### Is this a PSR validation implementation?

No — there is no PSR for validation. The package is standalone and
dependency-free.

## Next

- [Troubleshooting](Troubleshooting.md) — concrete problems and fixes.
- [Rules Reference](Rules-Reference.md) — the full rule catalogue.
