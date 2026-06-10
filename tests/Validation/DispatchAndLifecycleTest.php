<?php

declare(strict_types=1);

namespace Tests\InitPHP\Validation;

use InitPHP\Validation\Exception\ExceptionInterface;
use InitPHP\Validation\Exception\InvalidArgumentException;
use InitPHP\Validation\Exception\UndefinedRuleException;
use InitPHP\Validation\Validation;
use PHPUnit\Framework\TestCase;

/**
 * Rule dispatch, the optional/clear lifecycle and the data API. Covers the
 * fail-loud behaviour for unknown rules and the removal of the arbitrary
 * global-function dispatch.
 */
class DispatchAndLifecycleTest extends TestCase
{
    private Validation $validation;

    protected function setUp(): void
    {
        $this->validation = new Validation();
    }

    public function testUnknownRuleThrows(): void
    {
        $this->validation->setData(['v' => 'x']);
        $this->validation->rule('v', 'no_such_rule');

        $this->expectException(UndefinedRuleException::class);
        $this->validation->validation();
    }

    public function testUnknownRuleIsCatchableViaInterface(): void
    {
        $this->validation->setData(['v' => 'x']);
        $this->validation->rule('v', 'no_such_rule');

        $this->expectException(ExceptionInterface::class);
        $this->validation->validation();
    }

    public function testGlobalFunctionsAreNotDispatchedAsRules(): void
    {
        // Regression/security: any global function name used to be callable as a
        // rule. It must now be rejected as an undefined rule.
        $this->validation->setData(['v' => 'x']);
        $this->validation->rule('v', 'ctype_digit');

        $this->expectException(UndefinedRuleException::class);
        $this->validation->validation();
    }

    public function testInvalidRuleEntryThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        /** @phpstan-ignore-next-line intentionally invalid rule entry */
        $this->validation->rule('v', [123]);
    }

    public function testInvalidKeyEntryThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        /** @phpstan-ignore-next-line intentionally invalid key entry */
        $this->validation->rule([123], 'integer');
    }

    public function testOptionalSkipsAbsentField(): void
    {
        $this->validation->setData([]);
        $this->validation->rule('nickname', 'optional|alpha');

        $this->assertTrue($this->validation->validation());
    }

    public function testOptionalStillValidatesPresentField(): void
    {
        $this->validation->setData(['nickname' => '123']);
        $this->validation->rule('nickname', 'optional|alpha');

        $this->assertFalse($this->validation->validation());
    }

    public function testOptionalDoesNotLeakAcrossRuns(): void
    {
        // Regression: a field marked optional stayed optional forever because
        // the optional list was never reset between validation() runs.
        $this->validation->setData([]);
        $this->validation->rule('email', 'optional|required');
        $this->assertTrue($this->validation->validation());

        $this->validation->rule('email', 'required');
        $this->assertFalse($this->validation->validation());
    }

    public function testClearResetsQueuedState(): void
    {
        $this->validation->setData(['v' => 'x']);
        $this->validation->rule('v', 'integer');
        $this->validation->clear();

        $this->assertTrue($this->validation->validation());
        $this->assertSame([], $this->validation->getError());
    }

    public function testValidationConsumesQueuedRules(): void
    {
        $this->validation->setData(['v' => 'x']);

        $this->validation->rule('v', 'integer');
        $this->assertFalse($this->validation->validation());

        // The failing rule was consumed; a fresh run has nothing to check.
        $this->assertTrue($this->validation->validation());
    }

    public function testDataApi(): void
    {
        $this->validation->setData(['a' => 1]);
        $this->validation->mergeData(['b' => 2, 'a' => 3]);

        $this->assertSame(['a' => 3, 'b' => 2], $this->validation->getData());
    }

    public function testMultipleFieldsAndPipedRules(): void
    {
        $this->validation->setData(['year' => '2022', 'name' => 'Muhammet']);

        $this->validation->rule('year', 'integer|range(1970...2099)');
        $this->validation->rule('name', 'string|alpha');

        $this->assertTrue($this->validation->validation());
    }

    public function testPipeSeparatedKeysShareRules(): void
    {
        $this->validation->setData(['a' => 'x', 'b' => '2']);
        $this->validation->rule('a|b', 'alpha');
        $this->validation->validation();

        $this->assertCount(1, $this->validation->getError());
    }

    public function testBuiltInSlugPatternMatchesMultipleCharacters(): void
    {
        // Regression: the slug pattern was missing its `+` quantifier and only
        // matched a single character.
        $this->validation->setData(['handle' => 'my-blog-post_2']);
        $this->validation->rule('handle', 'regex(slug)');

        $this->assertTrue($this->validation->validation());
    }

    public function testCustomPatternForRegexRule(): void
    {
        $this->validation->setData(['code' => 'AB-12']);
        $this->validation->pattern('code', '[A-Z]{2}-[0-9]{2}');
        $this->validation->rule('code', 'regex(code)');

        $this->assertTrue($this->validation->validation());
    }

    public function testVersionReflectsMajor(): void
    {
        $this->assertSame('2.0.0', $this->validation->version());
        $this->assertSame('2.0.0', Validation::VERSION);
    }

    public function testIsValidReflectsLastValidation(): void
    {
        $this->validation->setData(['v' => 'x']);

        $this->validation->rule('v', 'integer');
        $this->validation->validation();
        $this->assertFalse($this->validation->isValid());

        $this->validation->rule('v', 'string');
        $this->validation->validation();
        $this->assertTrue($this->validation->isValid());
    }

    public function testSetErrorAppendsInterpolatedMessage(): void
    {
        $this->validation->setError('{field} failed', ['field' => 'name']);

        $this->assertSame(['name failed'], $this->validation->getError());
    }
}
