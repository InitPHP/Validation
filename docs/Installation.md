# Installation

## Requirements

- **PHP 8.2 or newer**
- **`ext-mbstring`** (used for Unicode-aware length and case handling)
- No runtime dependencies beyond the PHP core

## Install with Composer

```bash
composer require initphp/validation
```

Then make sure Composer's autoloader is loaded:

```php
require __DIR__ . '/vendor/autoload.php';
```

## Verify the install

```php
<?php
declare(strict_types=1);
require __DIR__ . '/vendor/autoload.php';

use InitPHP\Validation\Validation;

echo Validation::VERSION; // e.g. "2.0.0"
```

## The class you import

Everything is reachable through one class:

```php
use InitPHP\Validation\Validation;
```

Exceptions live under the `InitPHP\Validation\Exception` namespace — see
[Exceptions](Exceptions.md).

## Next

- [Quick Start](Quick-Start.md) — your first validation in a few lines.
- [The Validation Lifecycle](The-Validation-Lifecycle.md) — how `rule()` and
  `validation()` work together.
