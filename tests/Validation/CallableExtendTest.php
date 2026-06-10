<?php

declare(strict_types=1);

namespace Tests\InitPHP\Validation;

use InitPHP\Validation\Validation;
use PHPUnit\Framework\TestCase;

/**
 * Callable rules and the extend() extension point. Callable rules used to
 * crash the rule() builder with a TypeError; they are a documented feature.
 */
class CallableExtendTest extends TestCase
{
    private Validation $validation;

    protected function setUp(): void
    {
        $this->validation = new Validation();
    }

    public function testCallableRulePasses(): void
    {
        $this->validation->setData(['number' => 12]);
        $this->validation->rule('number', static fn (int $value): bool => ($value % 2) === 0);

        $this->assertTrue($this->validation->validation());
    }

    public function testCallableRuleFails(): void
    {
        $this->validation->setData(['number' => 13]);
        $this->validation->rule('number', static fn (int $value): bool => ($value % 2) === 0);

        $this->assertFalse($this->validation->validation());
    }

    public function testCallableReceivesNullForMissingField(): void
    {
        $received = 'untouched';
        $this->validation->rule('missing', static function ($value) use (&$received): bool {
            $received = $value;

            return true;
        });
        $this->validation->validation();

        $this->assertNull($received);
    }

    public function testArrayOfMixedRules(): void
    {
        $this->validation->setData(['number' => 8]);
        $this->validation->rule('number', [
            'integer',
            static fn ($value): bool => $value > 5,
        ]);

        $this->assertTrue($this->validation->validation());
    }

    public function testExtendRegistersNamedRule(): void
    {
        $this->validation->setData(['age' => 7]);
        $this->validation->extend('even', static fn (int $value): bool => ($value % 2) === 0);
        $this->validation->rule('age', 'even');

        $this->assertFalse($this->validation->validation());
    }

    public function testExtendRuleReceivesArguments(): void
    {
        $this->validation->setData(['value' => 10]);
        $this->validation->extend(
            'divisible',
            static fn (int $value, string $by): bool => ($value % (int) $by) === 0
        );

        $this->validation->rule('value', 'divisible(5)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('value', 'divisible(3)');
        $this->assertFalse($this->validation->validation());
    }

    public function testExtendRegistersCustomMessage(): void
    {
        $this->validation->setData(['age' => 7]);
        $this->validation->extend(
            'even',
            static fn (int $value): bool => ($value % 2) === 0,
            '{field} must be an even number.'
        );
        $this->validation->rule('age', 'even');
        $this->validation->validation();

        $this->assertSame(['age must be an even number.'], $this->validation->getError());
    }

    public function testExtensionsSurviveValidationRuns(): void
    {
        $this->validation->extend('even', static fn (int $value): bool => ($value % 2) === 0);

        $this->validation->setData(['a' => 4])->rule('a', 'even');
        $this->assertTrue($this->validation->validation());

        $this->validation->setData(['a' => 5])->rule('a', 'even');
        $this->assertFalse($this->validation->validation());
    }
}
