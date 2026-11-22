# InitPHP Validation — Wiki

Welcome to the official documentation for **`initphp/validation`** — a fast,
dependency-free data-validation library for **PHP 8.2+**.

You describe what each field should look like with a small rule DSL, callbacks,
or your own named rules, and get back localized error messages.

```bash
composer require initphp/validation
```

```php
use InitPHP\Validation\Validation;

$validation = new Validation([
    'name' => 'Muhammet',
    'year' => '2022',
]);

$valid = $validation
    ->rule('name', 'required|string')
    ->rule('year', 'integer|range(1970...2099)')
    ->validation();

if (!$valid) {
    foreach ($validation->getError() as $message) {
        echo $message, "\n";
    }
}
```

## The package in one table

| Symbol | Role |
| --- | --- |
| [`Validation`](API-Reference.md) | The single entry point: holds the data, the rule queue, the errors and the active locale. |
| [`extend()`](Callable-and-Custom-Rules.md#named-rules-with-extend) | Register a reusable named rule usable from the DSL string. |
| [`ExceptionInterface`](Exceptions.md) | Marker implemented by every exception the package throws. |
| [`UndefinedRuleException`](Exceptions.md#undefinedruleexception) | Thrown when a rule name is unknown — unknown rules fail loudly, they never silently pass. |

## Start here

- **New to the package?** Read [Installation](Installation.md), then
  [Quick Start](Quick-Start.md).
- **Want the mental model?**
  [The Validation Lifecycle](The-Validation-Lifecycle.md) explains the
  *queue → validate → read* loop.
- **Looking for a rule?** Every built-in rule is in the
  [Rules Reference](Rules-Reference.md).
- **Need custom logic?**
  [Callable & Custom Rules](Callable-and-Custom-Rules.md).
- **Localizing messages?** [Error Messages](Error-Messages.md) and
  [Localization](Localization.md).
- **Coming from 1.x?** Read [Migration from 1.x](Migration-from-1.x.md) — 2.0 is a
  breaking release.

## What you get

- **A readable rule DSL.** Pipe rules together per field:
  `"required|integer|range(1...10)"`. Arguments go in parentheses and are
  trimmed. See the [Rules Reference](Rules-Reference.md).
- **30+ built-in rules** for types, text, contact info, sizes, dates, patterns
  and comparisons.
- **Callbacks and custom named rules.** Drop in a closure for a one-off check,
  or register a reusable rule with [`extend()`](Callable-and-Custom-Rules.md).
- **Localized messages.** Ships with English and Turkish; placeholders, labels
  and per-rule overrides are all supported. See [Localization](Localization.md).
- **Fails loud, not silent.** An unknown rule throws instead of quietly passing
  a field. See [Exceptions](Exceptions.md).
- **Zero runtime dependencies** beyond `ext-mbstring`.

## At a glance — a few rules

| Rule | Passes | Fails |
| --- | --- | --- |
| `integer` | `"12"`, `-3` | `"3.5"`, `"abc"` |
| `mail` | `a@b.com` | `not-an-email` |
| `range(1970...2099)` | `2022` | `1900` |
| `length(3...20)` | `"hello"` | `"ab"` |
| `again(password)` | confirm equals `password` | they differ |
| `only(small, medium, large)` | `"Medium"` | `"xl"` |

## Package metadata

- **License**: MIT
- **Minimum PHP**: 8.2
- **Required extensions**: `ext-mbstring`
- **Runtime dependencies**: none
- **Packagist**: [`initphp/validation`](https://packagist.org/packages/initphp/validation)
- **Source**: [github.com/InitPHP/Validation](https://github.com/InitPHP/Validation)
- **Issues**: [github.com/InitPHP/Validation/issues](https://github.com/InitPHP/Validation/issues)
- **Discussions**: [github.com/orgs/InitPHP/discussions](https://github.com/orgs/InitPHP/discussions)

If something in this wiki is unclear, wrong, or out of date,
[open an issue](https://github.com/InitPHP/Validation/issues/new) — documentation
fixes are merged eagerly.
