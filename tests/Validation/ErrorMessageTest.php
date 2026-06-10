<?php

declare(strict_types=1);

namespace Tests\InitPHP\Validation;

use InitPHP\Validation\Validation;
use PHPUnit\Framework\TestCase;

/**
 * Error message generation: default templates, positional placeholders,
 * labels, custom messages and the interpolation precedence fix.
 */
class ErrorMessageTest extends TestCase
{
    private Validation $validation;

    protected function setUp(): void
    {
        $this->validation = new Validation();
    }

    public function testDefaultMessageUsesFieldName(): void
    {
        $this->validation->setData(['age' => 'abc']);
        $this->validation->rule('age', 'integer');
        $this->validation->validation();

        $this->assertSame(['age must be an integer.'], $this->validation->getError());
    }

    public function testPositionalArgumentPlaceholder(): void
    {
        $this->validation->setData(['age' => 5]);
        $this->validation->rule('age', 'min(18)');
        $this->validation->validation();

        $this->assertSame(
            ['age must be greater than or equal to 18.'],
            $this->validation->getError()
        );
    }

    public function testLabelsReplaceFieldNameInMessages(): void
    {
        $this->validation->setData(['age' => 'abc']);
        $this->validation->labels(['age' => 'Age']);
        $this->validation->rule('age', 'integer');
        $this->validation->validation();

        $this->assertSame(['Age must be an integer.'], $this->validation->getError());
    }

    public function testArgumentValuesAreNotPassedThroughLabels(): void
    {
        // Regression: argument values used to be label-substituted because of an
        // operator-precedence bug, leaking labels into {2} placeholders.
        $this->validation->setData(['mail' => 'someone@example.com']);
        $this->validation->labels(['gmail.com' => 'GoogleMail']);
        $this->validation->rule('mail', 'mailHost(gmail.com)');
        $this->validation->validation();

        $this->assertSame(
            ['mail must be an e-mail at gmail.com.'],
            $this->validation->getError()
        );
    }

    public function testCustomMessageOverridesDefaultAndInterpolates(): void
    {
        $this->validation->setData(['age' => 5]);
        $this->validation->rule('age', 'min(18)', '{field} is too small (min {2}).');
        $this->validation->validation();

        $this->assertSame(
            ['age is too small (min 18).'],
            $this->validation->getError()
        );
    }

    public function testCallableFailureUsesCustomMessage(): void
    {
        $this->validation->setData(['number' => 13]);
        $this->validation->rule(
            'number',
            static fn (int $value): bool => ($value % 2) === 0,
            '{field} must be an even number.'
        );
        $this->validation->validation();

        $this->assertSame(
            ['number must be an even number.'],
            $this->validation->getError()
        );
    }

    public function testCallableFailureFallsBackToDefaultMessage(): void
    {
        $this->validation->setData(['number' => 13]);
        $this->validation->rule('number', static fn ($value): bool => false);
        $this->validation->validation();

        $this->assertSame(
            ['The number value is not valid.'],
            $this->validation->getError()
        );
    }

    public function testMultipleErrorsAreCollected(): void
    {
        $this->validation->setData(['a' => 'x', 'b' => 'y']);
        $this->validation->rule('a', 'integer');
        $this->validation->rule('b', 'integer');
        $this->validation->validation();

        $this->assertCount(2, $this->validation->getError());
    }

    public function testValidationResetsErrorsEachRun(): void
    {
        $this->validation->setData(['a' => 'x']);

        $this->validation->rule('a', 'integer');
        $this->assertFalse($this->validation->validation());
        $this->assertCount(1, $this->validation->getError());

        $this->validation->rule('a', 'string');
        $this->assertTrue($this->validation->validation());
        $this->assertSame([], $this->validation->getError());
    }
}
