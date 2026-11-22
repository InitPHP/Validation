# Testing

## Running the package's own test suite

If you cloned the repository, install the dev dependencies and use the Composer
scripts:

```bash
composer install
composer test        # PHPUnit
composer stan        # PHPStan (max level)
composer cs-check    # PHP-CS-Fixer (dry run)
composer ci          # all three: cs-check, stan, test
```

`composer cs-fix` applies the coding-style fixes in place.

## Testing your own validation logic

Because a validator is just an object, testing your rules is straightforward
PHPUnit. Assert both the boolean result and the messages.

```php
use InitPHP\Validation\Validation;
use PHPUnit\Framework\TestCase;

final class RegistrationValidationTest extends TestCase
{
    private function validate(array $data): Validation
    {
        $v = new Validation($data);
        $v->rule('username', 'required|alphanum|length(3...20)')
          ->rule('email', 'required|mail')
          ->rule('password', 'required|length(8...)')
          ->rule('confirm', 'again(password)');
        $v->validation();

        return $v;
    }

    public function testValidPayloadPasses(): void
    {
        $v = $this->validate([
            'username' => 'ada',
            'email'    => 'ada@example.com',
            'password' => 'sup3rsecret',
            'confirm'  => 'sup3rsecret',
        ]);

        $this->assertTrue($v->isValid());
        $this->assertSame([], $v->getError());
    }

    public function testMismatchedConfirmFails(): void
    {
        $v = $this->validate([
            'username' => 'ada',
            'email'    => 'ada@example.com',
            'password' => 'sup3rsecret',
            'confirm'  => 'typo',
        ]);

        $this->assertFalse($v->isValid());
    }
}
```

## Asserting on messages

Switch to a known locale so message assertions are stable, or assert with your
own custom messages:

```php
$v = new Validation(['age' => 'abc']);
$v->setLocale('en');
$v->rule('age', 'integer');
$v->validation();

$this->assertSame(['age must be an integer.'], $v->getError());
```

## Testing a custom rule

```php
public function testEvenRule(): void
{
    $v = new Validation(['n' => 5]);
    $v->extend('even', static fn ($value): bool => ((int) $value % 2) === 0);

    $this->assertFalse($v->rule('n', 'even')->validation());
}
```

## Next

- [Recipes](Recipes.md) — patterns worth covering with tests.
- [Error Messages](Error-Messages.md) — what to assert against.
