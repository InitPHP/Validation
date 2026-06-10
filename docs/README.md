# InitPHP Validation — Documentation

InitPHP Validation checks that an associative data set matches a list of rules.
Rules are written as a small DSL string (`"required|integer|range(1...10)"`),
callbacks, or custom named rules, and failures become localized messages.

## Contents

| Guide | What it covers |
| ----- | -------------- |
| [Getting started](getting-started.md) | Install, the `Validation` object, the queue → validate → read loop. |
| [Rules reference](rules-reference.md) | Every built-in rule, grouped, with signatures and examples. |
| [Custom rules & callables](custom-rules-and-callables.md) | Callback rules, mixed rule arrays and `extend()`. |
| [Optional fields & patterns](optional-and-patterns.md) | The `optional` pseudo-rule, the validation lifecycle and named regex patterns. |
| [Localization & messages](localization-and-messages.md) | Placeholders, labels, locales, custom messages and language files. |
| [API reference](api-reference.md) | Every public method, its signature and behaviour. |
| [Exceptions](exceptions.md) | What is thrown, when, and how to catch it. |
| [Upgrading from 1.x](upgrading-from-1.x.md) | Breaking changes in 2.0 and how to migrate. |

## At a glance

```php
require 'vendor/autoload.php';

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

## Requirements

- PHP 8.1 or newer
- `ext-mbstring`
