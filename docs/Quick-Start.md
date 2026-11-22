# Quick Start

From `composer require` to a validated form in a few minutes.

## 1. Create the validator with your data

Any associative array works — `$_POST`, `$_GET`, a decoded JSON body, or a
plain array.

```php
<?php
declare(strict_types=1);
require __DIR__ . '/vendor/autoload.php';

use InitPHP\Validation\Validation;

$validation = new Validation($_POST);
```

You can also set or merge data later:

```php
$validation->setData(['email' => 'a@b.com']);
$validation->mergeData(['name' => 'Ada']);
```

## 2. Queue rules

Each `rule()` call adds checks for a field. A rule string is a pipe-separated
list, evaluated left to right. `rule()` is chainable.

```php
$validation
    ->rule('name', 'required|string')
    ->rule('email', 'required|mail')
    ->rule('age', 'integer|range(18...120)');
```

See the [Rules Reference](Rules-Reference.md) for every rule.

## 3. Validate

`validation()` runs the whole queue and returns a boolean.

```php
if ($validation->validation()) {
    // all good — use the data
} else {
    // something failed
}
```

## 4. Read the errors

`getError()` returns the messages from the most recent run.

```php
foreach ($validation->getError() as $message) {
    echo $message, "\n";
}
// e.g. "age must be greater than or equal to 18."
```

`isValid()` returns the same boolean as the last `validation()` call without
re-running it.

## A complete example

```php
<?php
declare(strict_types=1);
require __DIR__ . '/vendor/autoload.php';

use InitPHP\Validation\Validation;

$validation = new Validation([
    'username' => 'ada',
    'email'    => 'ada@example.com',
    'password' => 'sup3rsecret',
    'confirm'  => 'sup3rsecret',
    'age'      => '34',
]);

$valid = $validation
    ->rule('username', 'required|alphanum|length(3...20)')
    ->rule('email', 'required|mail')
    ->rule('password', 'required|length(8...)')
    ->rule('confirm', 'again(password)')
    ->rule('age', 'optional|integer|range(18...120)')
    ->validation();

if ($valid) {
    // persist the user
} else {
    $errors = $validation->getError();
}
```

## Add a quick custom check

Need something the built-in rules do not cover? Pass a callback as the rule and
give it a message:

```php
$validation->rule('number', static function ($value): bool {
    return ($value % 2) === 0;
}, '{field} must be an even number.');
```

More in [Callable & Custom Rules](Callable-and-Custom-Rules.md).

## Next

- [The Validation Lifecycle](The-Validation-Lifecycle.md) — the mental model.
- [Rules Reference](Rules-Reference.md) — the full catalogue.
- [Recipes](Recipes.md) — copy-paste patterns for common forms.
