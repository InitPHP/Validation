<?php

declare(strict_types=1);

namespace Tests\InitPHP\Validation;

use InitPHP\Validation\Exception\LocaleException;
use InitPHP\Validation\Validation;
use PHPUnit\Framework\TestCase;

/**
 * Locale loading and message-key resolution, including the message-key
 * synchronisation fixes for the `again` rule and case-insensitive lookup.
 */
class LocaleTest extends TestCase
{
    private Validation $validation;

    protected function setUp(): void
    {
        $this->validation = new Validation();
    }

    public function testEnglishAgainMessageResolves(): void
    {
        // Regression: the English locale keyed this message as `repeat`, so the
        // `again` rule used to fall back to the generic "not valid" message.
        $this->validation->setData(['password' => 'a', 'confirm' => 'b']);
        $this->validation->setLocale('en');
        $this->validation->rule('password', 'again(confirm)');
        $this->validation->validation();

        $this->assertSame(
            ['password must be the same as confirm.'],
            $this->validation->getError()
        );
    }

    public function testTurkishMessages(): void
    {
        $this->validation->setData(['age' => 'abc']);
        $this->validation->setLocale('tr');
        $this->validation->rule('age', 'integer');
        $this->validation->validation();

        $this->assertSame(['age bir tam sayı olmalıdır.'], $this->validation->getError());
    }

    public function testTurkishNotContainsMessageIsCorrect(): void
    {
        // Regression: the Turkish `notContains` message used to read the same as
        // `contains` ("must contain") instead of "must not contain".
        $this->validation->setData(['v' => 'abcdef']);
        $this->validation->setLocale('tr');
        $this->validation->rule('v', 'notContains(cd)');
        $this->validation->validation();

        $this->assertSame(['v cd içeremez.'], $this->validation->getError());
    }

    public function testMessageResolutionIsCaseInsensitive(): void
    {
        // The dispatcher lowercases rule names; message lookup must match
        // regardless of how the rule was written.
        $lower = new Validation(['card' => 'nope']);
        $lower->rule('card', 'creditcard');
        $lower->validation();

        $upper = new Validation(['card' => 'nope']);
        $upper->rule('card', 'creditCard');
        $upper->validation();

        $expected = ['card must be a credit card number.'];
        $this->assertSame($expected, $lower->getError());
        $this->assertSame($expected, $upper->getError());
    }

    public function testSetLocaleArrayOverridesIndividualMessages(): void
    {
        $this->validation->setData(['age' => 'abc']);
        $this->validation->setLocaleArray(['integer' => '{field} is not a whole number.']);
        $this->validation->rule('age', 'integer');
        $this->validation->validation();

        $this->assertSame(['age is not a whole number.'], $this->validation->getError());
    }

    public function testUnknownLocaleThrows(): void
    {
        $this->expectException(LocaleException::class);
        $this->validation->setLocale('does-not-exist');
    }

    public function testUnknownLocaleDirectoryThrows(): void
    {
        $this->expectException(LocaleException::class);
        $this->validation->setLocaleDir('/path/that/does/not/exist');
    }
}
