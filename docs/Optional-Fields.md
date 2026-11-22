# Optional Fields

Not every field is required. The `optional` pseudo-rule lets a field be absent
without failing, while still validating it when it **is** present.

## What `optional` does

`optional` is not a check — it is a flag. When the field has no value, every
other rule queued for it is **skipped**. When the field is present, the other
rules apply as normal.

```php
use InitPHP\Validation\Validation;

// 'nickname' is missing entirely
$v = new Validation([]);
$v->rule('nickname', 'optional|alpha');
$v->validation();   // true — skipped, nothing to check

// 'nickname' is present but invalid
$v = new Validation(['nickname' => '123']);
$v->rule('nickname', 'optional|alpha');
$v->validation();   // false — present, so 'alpha' runs and fails
```

## When is a field "absent"?

A field counts as absent when its key is **missing** or its value is **`null`**.
An empty string (`''`) is considered **present**, so `optional` will not skip it:

```php
$v = new Validation(['nickname' => '']);
$v->rule('nickname', 'optional|alpha');
$v->validation();   // false — '' is present, and '' is not alphabetic
```

If you want to allow blanks too, combine `optional` with a rule that accepts
empty input, or branch on the value before queueing rules:

```php
if (($data['nickname'] ?? '') !== '') {
    $v->rule('nickname', 'alpha|length(2...20)');
}
```

## `optional` and the lifecycle

`optional` flags are reset at the end of each `validation()` run, exactly like
the rule queue. So an `optional` mark only applies to the run it was added in:

```php
$v = new Validation([]);

$v->rule('email', 'optional|required');
$v->validation();   // true — email is optional this run

$v->rule('email', 'required');
$v->validation();   // false — the optional flag did not carry over
```

This keeps each *queue → validate* cycle independent. See
[The Validation Lifecycle](The-Validation-Lifecycle.md).

## Marking several fields optional

`optional` works with piped field names and across separate `rule()` calls:

```php
$v->rule('phone|fax', 'optional');
$v->rule('phone', 'tel');     // a named pattern, see Patterns & Regex
$v->rule('fax', 'tel');
```

You can also place `optional` anywhere in the pipe; order does not matter for it:

```php
$v->rule('middle_name', 'alpha|optional');   // same as 'optional|alpha'
```

## Next

- [Rules Reference](Rules-Reference.md) — the rules you combine with `optional`.
- [The Validation Lifecycle](The-Validation-Lifecycle.md) — why flags reset each run.
