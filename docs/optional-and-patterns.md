# Optional fields & patterns

## The `optional` pseudo-rule

`optional` is not a check — it marks a field as allowed to be absent. When the
field has no value (it is missing or `null`), every other rule queued for it is
skipped. When the field is present, the other rules apply normally.

```php
use InitPHP\Validation\Validation;

$v = new Validation([]);                 // no 'nickname' key
$v->rule('nickname', 'optional|alpha');
$v->validation();                        // true — skipped

$v = new Validation(['nickname' => '123']);
$v->rule('nickname', 'optional|alpha');
$v->validation();                        // false — present but not alphabetic
```

A field counts as "absent" when its key is missing or its value is `null`. An
empty string (`''`) is considered present, so pair `optional` with `empty` or a
length rule if you want to allow blanks.

## The validation lifecycle and `optional`

`validation()` consumes the queued rules **and** the optional flags at the end
of each run, so an `optional` mark only applies to the run it was added in:

```php
$v = new Validation([]);

$v->rule('email', 'optional|required');
$v->validation();                        // true — email is optional this run

$v->rule('email', 'required');
$v->validation();                        // false — optional did not carry over
```

This keeps each *queue → validate* cycle independent. Use `clear()` to discard a
queue (rules, optional flags and errors) without validating.

## Named patterns

The `regex` rule accepts either an inline regex body or the name of a pattern.
Built-in pattern names:

| Name | Matches |
| ---- | ------- |
| `uri` | URI path characters |
| `slug` | URL slug characters |
| `url` | URL characters |
| `alpha` | Unicode letters |
| `words` | Letters and whitespace |
| `alphanum` | Letters and digits |
| `int` | Digits |
| `float` | Digits, dot and comma |
| `tel` | Phone-number characters |
| `text` | Common prose characters |
| `file` | A file name with extension |
| `folder` | A folder name |
| `address` | Street-address characters |
| `date_dmy` | `d-m-Y` style dates |
| `date_ymd` | `Y-m-d` style dates |
| `email` | A basic e-mail shape |

```php
$v->rule('handle', 'regex(slug)');
```

### Registering your own pattern

`pattern(string $name, string $body)` adds or overrides a named pattern. The
body is a regular expression **without** delimiters; it is matched anchored as
`^(...)$`.

```php
$v->pattern('product_code', '[A-Z]{2}-[0-9]{4}');
$v->rule('code', 'regex(product_code)');
```

Pattern names are matched case-insensitively. Registered patterns persist across
`validation()` runs.

> Because DSL arguments are split on commas, an inline body containing a comma
> (e.g. `[a-z]{2,4}`) will not parse correctly inside `regex(...)`. Register it
> as a named pattern instead.
