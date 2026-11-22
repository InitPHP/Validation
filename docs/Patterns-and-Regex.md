# Patterns & Regex

The [`regex`](Rules-Reference.md#regex) rule matches a value against a regular
expression — either an inline body or the name of a pattern you registered.

## Inline regex

Pass the regex **body without delimiters**. The value is matched anchored as
`^(...)$`, so the whole value must match.

```php
use InitPHP\Validation\Validation;

$v = new Validation(['code' => 'AB12']);
$v->rule('code', 'regex([A-Z]{2}[0-9]{2})');   // passes
```

> **Commas are argument separators.** An inline body containing a comma — such as
> `[a-z]{2,4}` — is split before it reaches the regex engine. Register it as a
> named pattern instead (below).

## Named patterns

Give a pattern a name once and reference it by name. Names are matched
case-insensitively and persist across `validation()` runs.

```php
$v->pattern('product_code', '[A-Z]{2}-[0-9]{4}');
$v->rule('sku', 'regex(product_code)');
```

`pattern(string $name, string $body)` adds or overrides a named pattern. The
body has no delimiters and is matched as `^(...)$`.

## Built-in patterns

These names are available to `regex` out of the box:

| Name | Matches |
| --- | --- |
| `uri` | URI path characters |
| `slug` | URL slug characters (letters, digits, `-`, `_`) |
| `url` | URL characters |
| `alpha` | Unicode letters |
| `words` | Letters and whitespace |
| `alphanum` | Letters and digits |
| `int` | Digits |
| `float` | Digits, dot and comma |
| `tel` | Phone-number characters (`0-9 + space ( ) -`) |
| `text` | Common prose characters |
| `file` | A file name with an extension |
| `folder` | A folder name |
| `address` | Street-address characters |
| `date_dmy` | `d-m-Y` style dates |
| `date_ymd` | `Y-m-d` style dates |
| `email` | A basic e-mail shape |

```php
$v->rule('handle', 'regex(slug)');     // "my-blog-post_2" passes
$v->rule('phone', 'regex(tel)');
```

## `regex` vs the dedicated rules

For common cases, prefer the purpose-built rule over a raw pattern — they are
clearer and handle edge cases (types, `filter_var`) for you:

| Instead of | Prefer |
| --- | --- |
| `regex(email)` | [`mail`](Rules-Reference.md#mail) |
| `regex(int)` | [`integer`](Rules-Reference.md#integer) |
| `regex(alphanum)` | [`alphanum`](Rules-Reference.md#alphanum--alphanumeric) |

Reach for `regex` when you need a shape the built-in rules do not express.

## Next

- [Rules Reference](Rules-Reference.md#regex) — the `regex` rule entry.
- [Callable & Custom Rules](Callable-and-Custom-Rules.md) — for logic regex cannot
  express.
