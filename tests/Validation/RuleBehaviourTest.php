<?php

declare(strict_types=1);

namespace Tests\InitPHP\Validation;

use InitPHP\Validation\Validation;
use PHPUnit\Framework\TestCase;

/**
 * Behavioural edge cases for the built-in rules, including the regressions
 * fixed in 2.0.0 (argument trimming, open/closed bounds, null-safety, loose
 * comparison and array handling).
 */
class RuleBehaviourTest extends TestCase
{
    private Validation $validation;

    protected function setUp(): void
    {
        $this->validation = new Validation();
    }

    public function testRuleArgumentsAreTrimmed(): void
    {
        $this->validation->setData(['food' => 'tea']);

        $this->validation->rule('food', 'only(food, tea, meat)');
        $this->assertTrue($this->validation->validation());

        $this->validation->setData(['list' => 'b']);
        $this->validation->rule('list', 'strictOnly(a, b, c)');
        $this->assertTrue($this->validation->validation());
    }

    public function testEqualsComparesLoosely(): void
    {
        $this->validation->setData(['int' => 123, 'str' => '123']);

        $this->validation->rule('int', 'equals(123)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('str', 'equals(123)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('int', 'equals(124)');
        $this->assertFalse($this->validation->validation());
    }

    public function testAgainComparesLoosely(): void
    {
        $this->validation->setData(['a' => 5, 'b' => '5', 'c' => '6']);

        $this->validation->rule('a', 'again(b)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('a', 'again(c)');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('a', 'again(missing)');
        $this->assertFalse($this->validation->validation());
    }

    public function testLengthEnforcesOpenUpperBound(): void
    {
        $this->validation->setData([
            'short' => 'abc',
            'long'  => str_repeat('x', 300),
        ]);

        $this->validation->rule('long', 'length(...255)');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('short', 'length(...255)');
        $this->assertTrue($this->validation->validation());
    }

    public function testLengthSingleNumberIsMaximum(): void
    {
        $this->validation->setData(['v' => 'abcd']);

        $this->validation->rule('v', 'length(5)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('v', 'length(3)');
        $this->assertFalse($this->validation->validation());
    }

    public function testLengthOnNullValueDoesNotCrash(): void
    {
        $this->validation->setData(['v' => null]);

        $this->validation->rule('v', 'length(2...5)');
        $this->assertFalse($this->validation->validation());
    }

    public function testRangeSupportsDashAndNegativeBounds(): void
    {
        $this->validation->setData(['v' => 3]);

        $this->validation->rule('v', 'range(2-5)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('v', 'range(-5...10)');
        $this->assertTrue($this->validation->validation());

        $this->validation->setData(['v' => 9]);
        $this->validation->rule('v', 'range(2-5)');
        $this->assertFalse($this->validation->validation());
    }

    public function testMinMaxMeasureArrayLengthWithoutWarning(): void
    {
        $this->validation->setData(['list' => ['a', 'b', 'c']]);

        $this->validation->rule('list', 'min(2)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('list', 'max(2)');
        $this->assertFalse($this->validation->validation());
    }

    public function testRequiredHandlesArraysAndNumbers(): void
    {
        $this->validation->setData([
            'empty_array' => [],
            'full_array'  => ['x'],
            'zero'        => 0,
        ]);

        $this->validation->rule('empty_array', 'required');
        $this->assertFalse($this->validation->validation());

        $this->validation->rule('full_array', 'required');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('zero', 'required');
        $this->assertTrue($this->validation->validation());
    }

    public function testBooleanRejectsNonBooleanIntegers(): void
    {
        $this->validation->setData(['one' => 1, 'two' => 2]);

        $this->validation->rule('one', 'boolean');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('two', 'boolean');
        $this->assertFalse($this->validation->validation());
    }

    public function testAlphanumericAliasIsAvailable(): void
    {
        $this->validation->setData(['v' => 'abc123', 'bad' => 'abc 123']);

        $this->validation->rule('v', 'alphanumeric');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('bad', 'alphanumeric');
        $this->assertFalse($this->validation->validation());
    }

    public function testStartWithAndEndWithSupportArrays(): void
    {
        $this->validation->setData(['list' => ['first', 'middle', 'last']]);

        $this->validation->rule('list', 'startWith(first)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('list', 'endWith(last)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('list', 'startWith(middle)');
        $this->assertFalse($this->validation->validation());
    }

    public function testInAndNotInWithArrays(): void
    {
        $this->validation->setData(['roles' => ['admin', 'editor']]);

        $this->validation->rule('roles', 'in(admin)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('roles', 'notIn(guest)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('roles', 'in(guest)');
        $this->assertFalse($this->validation->validation());
    }

    public function testDateAcceptsDateTimeInstances(): void
    {
        $this->validation->setData(['when' => new \DateTime('2022-01-01')]);

        $this->validation->rule('when', 'date');
        $this->assertTrue($this->validation->validation());
    }

    public function testCreditCardIsCaseInsensitiveForType(): void
    {
        $this->validation->setData(['card' => '4988 4388 4388 4305']);

        $this->validation->rule('card', 'creditCard(VISA)');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('card', 'creditCard(amex)');
        $this->assertFalse($this->validation->validation());
    }

    public function testArrayRule(): void
    {
        $this->validation->setData(['list' => [1, 2], 'scalar' => 'x']);

        $this->validation->rule('list', 'array');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('scalar', 'array');
        $this->assertFalse($this->validation->validation());
    }
}
