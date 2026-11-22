# Rules Reference

Every built-in rule, grouped by purpose. Rules are written in a pipe-separated
DSL string; arguments go in parentheses and are comma-separated and **trimmed**
(`only(a, b)` equals `only(a,b)`). Rule names are matched **case-insensitively**
(`creditCard` and `creditcard` are the same rule).

All examples assume:

```php
use InitPHP\Validation\Validation;

$v = new Validation($data);
```

> Need something custom? See [Callable & Custom Rules](Callable-and-Custom-Rules.md).

## Contents

- **Presence**: [`required`](#required) · [`optional`](#optional) · [`empty`](#empty)
- **Types**: [`integer`](#integer) · [`float`](#float) · [`numeric`](#numeric) · [`string`](#string) · [`boolean`](#boolean) · [`array`](#array)
- **Text**: [`alpha`](#alpha) · [`alphanum` / `alphanumeric`](#alphanum--alphanumeric)
- **Contact & network**: [`mail`](#mail) · [`mailHost`](#mailhost) · [`url`](#url) · [`urlHost`](#urlhost) · [`ip` / `ipv4` / `ipv6`](#ip--ipv4--ipv6)
- **Size & length**: [`min`](#min) · [`max`](#max) · [`range`](#range) · [`length`](#length)
- **Pattern & format**: [`regex`](#regex) · [`date`](#date) · [`dateFormat`](#dateformat) · [`creditCard`](#creditcard)
- **Comparison**: [`again`](#again) · [`equals`](#equals) · [`startWith`](#startwith) · [`endWith`](#endwith) · [`in`](#in) · [`notIn`](#notin) · [`contains`](#contains) · [`notContains`](#notcontains) · [`only`](#only) · [`strictOnly`](#strictonly)

---

## Presence

### `required`

The value is present and not a blank string. Numbers (including `0`) and
non-empty arrays pass; `null`, `''`, whitespace-only strings and empty arrays
fail.

```php
$v->rule('name', 'required');
```

### `optional`

A pseudo-rule, not a check. When the field has no value, its other rules are
skipped instead of failing. See [Optional Fields](Optional-Fields.md).

```php
$v->rule('nickname', 'optional|alpha');
```

### `empty`

The value is empty once trimmed.

```php
$v->rule('honeypot', 'empty');   // passes for "", "   "
```

---

## Types

### `integer`

An integer or an integer-looking string (`"-12"`, `"12"`). Floats and
non-numeric strings fail.

### `float`

A float or int, or a float-looking string (`"13.50"`, `"-2.00"`). Every integer
is also a valid float.

### `numeric`

Any numeric value: ints, floats and numeric strings.

### `string`

A real string — this checks the **type**, so `12` (int) fails but `"12"` passes.

### `boolean`

A real boolean, or one of `true`, `false`, `1`, `0` (as int or string). Other
integers such as `2` fail.

### `array`

An array.

```php
$v->rule('id', 'integer');
$v->rule('price', 'float');
$v->rule('active', 'boolean');
$v->rule('tags', 'array');
```

---

## Text

### `alpha`

Letters only, Unicode-aware (`\p{L}+`). Spaces and digits fail.

### `alphanum` / `alphanumeric`

Letters and digits only. The two names are aliases.

```php
$v->rule('first_name', 'alpha');        // "José" passes, "John Doe" fails
$v->rule('slug', 'alphanumeric');        // "abc123" passes, "abc 123" fails
```

---

## Contact & network

### `mail`

A valid e-mail address (`FILTER_VALIDATE_EMAIL`).

### `mailHost`

`mailHost(host, ...)` — a valid e-mail whose host is one of the given hosts.

```php
$v->rule('email', 'mailHost(gmail.com, outlook.com)');
```

### `url`

A valid URL (`FILTER_VALIDATE_URL`).

### `urlHost`

`urlHost(domain, ...)` — a URL whose host equals one of the domains, **or is a
subdomain of it**.

```php
$v->rule('site', 'urlHost(example.com)');
// passes for http://example.com and http://www.example.com
```

### `ip` / `ipv4` / `ipv6`

A valid IP address of the requested family.

```php
$v->rule('remote', 'ip');
$v->rule('gateway', 'ipv4');
$v->rule('client', 'ipv6');
```

---

## Size & length

For `min`/`max`, **numbers are compared by value** and **strings/arrays by their
length/element count**. `range` is value-only; `length` is length/count-only.

### `min`

`min(n)`:

```php
$v->rule('age', 'min(18)');     // age >= 18 (number)
$v->rule('title', 'min(3)');    // at least 3 characters
$v->rule('items', 'min(1)');    // at least 1 element
```

### `max`

`max(n)`:

```php
$v->rule('discount', 'max(100)');
$v->rule('bio', 'max(280)');
```

### `range`

`range(min...max)` — a number within the range. Also accepts `min-max`, and open
bounds `...max` or `min...`. Negative bounds work with the `...` separator.

```php
$v->rule('year', 'range(1970...2099)');
$v->rule('score', 'range(0-10)');
$v->rule('temp', 'range(-40...60)');
```

### `length`

`length(spec)` — string length or array element count. `spec` may be:

- a single number — treated as the **maximum** (`length(255)`),
- a closed range — `length(3...20)` or `length(3-20)`,
- an open range — `length(...255)` (max only) or `length(10...)` (min only).

```php
$v->rule('username', 'length(3...20)');
$v->rule('comment', 'length(...500)');
$v->rule('password', 'length(8...)');
```

---

## Pattern & format

### `regex`

`regex(name | body)` — matches a [named pattern](Patterns-and-Regex.md) or an
inline regex body (without delimiters). The value is matched anchored as
`^(...)$`.

```php
$v->rule('email', 'regex(email)');          // built-in named pattern
$v->pattern('code', '[A-Z]{2}-[0-9]{4}');
$v->rule('product', 'regex(code)');          // your own named pattern
```

> Avoid commas inside an inline body — they are parsed as argument separators.
> Register a [named pattern](Patterns-and-Regex.md) instead.

### `date`

A `DateTimeInterface` instance, or any string `strtotime()` can parse
(`2022-01-01`, `10 September 2000`, `next Thursday`).

```php
$v->rule('published_at', 'date');
```

### `dateFormat`

`dateFormat(format)` — a date string in the given `date()` format.

```php
$v->rule('published_at', 'dateFormat(Y/m/d)');   // "2022/01/01" passes
```

### `creditCard`

`creditCard(type?)` — a credit card number, ignoring spaces. With no argument any
supported brand passes; with a type, only that brand: `amex`, `visa`,
`mastercard`, `maestro`, `jcb`, `solo`, `switch`.

```php
$v->rule('card', 'creditCard');
$v->rule('card', 'creditCard(visa)');
```

---

## Comparison

### `again`

`again(field)` — loosely equals the value of another field in the data set.
Ideal for password and e-mail confirmation.

```php
$v->rule('confirm', 'again(password)');
```

### `equals`

`equals(value)` — loosely equals the given value, so `equals(123)` matches both
`123` and `"123"`.

```php
$v->rule('agree', 'equals(1)');
```

### `startWith`

`startWith(value)` — a string that starts with the value, or an array whose
first element equals it.

```php
$v->rule('phone', 'startWith(+90)');
```

### `endWith`

`endWith(value)` — a string that ends with the value, or an array whose last
element equals it.

```php
$v->rule('file', 'endWith(.pdf)');
```

### `in`

`in(needle)` — for strings/numbers: a **case-insensitive substring** test. For
arrays: **strict membership**.

```php
$v->rule('text', 'in(dolor)');    // 'dolor' appears in the string
$v->rule('roles', 'in(admin)');   // 'admin' is an element of the array
```

### `notIn`

`notIn(needle)` — the inverse of [`in`](#in).

```php
$v->rule('text', 'notIn(spam)');
```

### `contains`

`contains(needle)` — a **case-sensitive** substring test on the string form of
the value.

```php
$v->rule('text', 'contains(Dolor)');
```

### `notContains`

`notContains(needle)` — the inverse of [`contains`](#contains).

```php
$v->rule('text', 'notContains(http)');
```

### `only`

`only(a, b, ...)` — the value **case-insensitively** equals one of the options.

```php
$v->rule('size', 'only(small, medium, large)');   // "Medium" passes
```

### `strictOnly`

`strictOnly(a, b, ...)` — the value equals one of the options, **case-sensitive**.

```php
$v->rule('flag', 'strictOnly(Y, N)');
```

---

## Combining rules

Pipe rules together; they run left to right and each failure adds a message.

```php
$v->rule('username', 'required|alphanum|length(3...20)');

// the same rules for two fields at once
$v->rule('email|backup_email', 'optional|mail');
```

## Next

- [Optional Fields](Optional-Fields.md) — the `optional` pseudo-rule in depth.
- [Callable & Custom Rules](Callable-and-Custom-Rules.md) — when the built-ins are
  not enough.
- [Error Messages](Error-Messages.md) — placeholders, labels and custom messages.
