# Getting started

## Install

```bash
composer require initphp/validation
```

Requires PHP 8.1+ and `ext-mbstring`.

## The `Validation` object

Create a validator and give it the data to check. Any associative array works —
`$_POST`, `$_GET`, a decoded JSON body, or a plain array.

```php
use InitPHP\Validation\Validation;

$validation = new Validation($_POST);

// or set/merge data later
$validation->setData(['email' => 'a@b.com']);
$validation->mergeData(['name' => 'Ada']);
```

## Queue rules, then validate

Every `rule()` call queues one or more checks. `validation()` runs the whole
queue and returns a boolean. `rule()` is chainable.

```php
$valid = $validation
    ->rule('email', 'required|mail')
    ->rule('name', 'required|alpha')
    ->validation();
```

A rule string is a pipe-separated list. The first field of `email` above is
checked for `required` first, then `mail`.

## Read the errors

`getError()` returns the messages produced by the **most recent**
`validation()` call.

```php
if (!$valid) {
    foreach ($validation->getError() as $message) {
        echo $message, "\n";
    }
}
```

`isValid()` returns the boolean result of that same run without re-running it.

## The validation lifecycle

`validation()` does three things in order:

1. Clears the error list.
2. Runs every queued rule (skipping [optional](optional-and-patterns.md) fields
   that have no value).
3. Empties the rule queue.

Because the queue is consumed, the natural pattern is **queue → validate → read,
then repeat**:

```php
$validation->rule('a', 'integer');
$validation->validation();          // checks only 'a'

$validation->rule('b', 'integer');
$validation->validation();          // checks only 'b'
```

Call `clear()` to drop any queued rules, optional flags and errors without
validating.

## A full example

```php
require 'vendor/autoload.php';

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

Next: the [rules reference](rules-reference.md).
