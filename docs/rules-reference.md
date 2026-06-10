# Rules reference

A rule string is a pipe-separated list of rules. Arguments go in parentheses
and are comma-separated; each argument is trimmed, so `only(a, b)` equals
`only(a,b)`. Rule names are matched case-insensitively (`creditCard` and
`creditcard` are the same rule).

All examples below assume:

```php
use InitPHP\Validation\Validation;

$v = new Validation($data);
```

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
skipped instead of failing. When the field is present, the other rules apply.

```php
$v->rule('nickname', 'optional|alpha');
```

See [Optional fields & patterns](optional-and-patterns.md).

### `empty`

The value is empty once trimmed.

```php
$v->rule('honeypot', 'empty');
```

## Types

### `integer`

An integer or an integer-looking string (`"-12"`, `"12"`).

### `float`

A float or int, or a float-looking string (`"13.50"`, `"-2.00"`). Every integer
is also a valid float.

### `numeric`

Any numeric value: ints, floats and numeric strings.

### `string`

A real string. This checks the **type**, so `12` (int) fails but `"12"` passes.

### `boolean`

A real boolean, or one of `true`, `false`, `1`, `0` (as int or string).

### `array`

An array.

```php
$v->rule('id', 'integer');
$v->rule('price', 'float');
$v->rule('tags', 'array');
```

## Text

### `alpha`

Letters only, Unicode-aware (`[\p{L}]+`). Spaces and digits fail.

### `alphanum` / `alphanumeric`

Letters and digits only. The two names are aliases.

```php
$v->rule('first_name', 'alpha');
$v->rule('slug', 'alphanumeric');
```

## Contact & network

### `mail`

A valid e-mail address (`FILTER_VALIDATE_EMAIL`).

### `mailHost(host, ...)`

A valid e-mail whose host is one of the given hosts.

```php
$v->rule('email', 'mailHost(gmail.com, outlook.com)');
```

### `url`

A valid URL (`FILTER_VALIDATE_URL`).

### `urlHost(domain, ...)`

A URL whose host equals one of the domains, or is a subdomain of it.

```php
$v->rule('site', 'urlHost(example.com)');
// passes for http://example.com and http://www.example.com
```

### `ip` / `ipv4` / `ipv6`

A valid IP address of the requested family.

```php
$v->rule('remote', 'ip');
$v->rule('gateway', 'ipv4');
```

## Size & length

For `min`/`max`, numbers are compared by value and strings/arrays by their
length/element count. `range` is value-only; `length` is length/count-only.

### `min(n)`

```php
$v->rule('age', 'min(18)');        // age >= 18 (number)
$v->rule('title', 'min(3)');       // at least 3 characters
$v->rule('items', 'min(1)');       // at least 1 element
```

### `max(n)`

```php
$v->rule('discount', 'max(100)');
$v->rule('bio', 'max(280)');
```

### `range(min...max)`

A number within the range. Accepts `min-max` as well, and open bounds with
`...max` or `min...`.

```php
$v->rule('year', 'range(1970...2099)');
$v->rule('score', 'range(0-10)');
$v->rule('temp', 'range(-40...60)');
```

### `length(spec)`

String length or array element count. `spec` may be:

- a single number — treated as the **maximum** (`length(255)`),
- a closed range — `length(3...20)` or `length(3-20)`,
- an open range — `length(...255)` (max only) or `length(10...)` (min only).

```php
$v->rule('username', 'length(3...20)');
$v->rule('comment', 'length(...500)');
```

## Pattern & format

### `regex(name | body)`

Matches a [named pattern](optional-and-patterns.md#named-patterns) or an inline
regex body (without delimiters).

```php
$v->rule('email', 'regex(email)');          // built-in named pattern
$v->pattern('code', '[A-Z]{2}-[0-9]{4}');
$v->rule('product', 'regex(code)');          // custom named pattern
```

> Inline bodies are matched as `^(...)$`. Avoid commas inside an inline body —
> they are parsed as argument separators; register a named pattern instead.

### `date`

A `DateTimeInterface` instance, or any string `strtotime()` can parse
(`2022-01-01`, `10 September 2000`, `next Thursday`).

### `dateFormat(format)`

A date string in the given `date()` format.

```php
$v->rule('published_at', 'dateFormat(Y/m/d)');
```

### `creditCard(type?)`

A credit card number, ignoring spaces. With no argument, any supported brand
passes. With a type, only that brand passes: `amex`, `visa`, `mastercard`,
`maestro`, `jcb`, `solo`, `switch`.

```php
$v->rule('card', 'creditCard');
$v->rule('card', 'creditCard(visa)');
```

## Comparison

### `again(field)`

Loosely equals the value of another field in the data set — handy for password
and e-mail confirmation.

```php
$v->rule('confirm', 'again(password)');
```

### `equals(value)`

Loosely equals the given value.

```php
$v->rule('agree', 'equals(1)');
```

### `startWith(value)` / `endWith(value)`

A string that starts/ends with the value. For arrays, compares the first/last
element.

```php
$v->rule('phone', 'startWith(+90)');
$v->rule('file', 'endWith(.pdf)');
```

### `in(needle)` / `notIn(needle)`

For strings/numbers: a case-insensitive substring test. For arrays: strict
membership.

```php
$v->rule('text', 'in(dolor)');     // 'dolor' appears in the string
$v->rule('roles', 'in(admin)');    // 'admin' is an element of the array
$v->rule('text', 'notIn(spam)');
```

### `contains(needle)` / `notContains(needle)`

A case-sensitive substring test on the string form of the value.

```php
$v->rule('text', 'contains(Dolor)');
$v->rule('text', 'notContains(http)');
```

### `only(a, b, ...)` / `strictOnly(a, b, ...)`

The value must equal one of the options. `only` is case-insensitive;
`strictOnly` is case-sensitive.

```php
$v->rule('size', 'only(small, medium, large)');
$v->rule('flag', 'strictOnly(Y, N)');
```

## Combining rules

Pipe rules together; they run left to right and each failure adds a message.

```php
$v->rule('username', 'required|alphanum|length(3...20)');
$v->rule('email|backup_email', 'optional|mail');   // both fields, same rules
```

Continue with [custom rules & callables](custom-rules-and-callables.md).
