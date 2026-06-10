<?php

/**
 * UndefinedRuleException.php
 *
 * This file is part of InitPHP Validation.
 *
 * @author     Muhammet ŞAFAK <info@muhammetsafak.com.tr>
 * @copyright  Copyright © 2022 InitPHP
 * @license    http://initphp.github.io/license.txt  MIT
 * @link       https://www.muhammetsafak.com.tr
 */

declare(strict_types=1);

namespace InitPHP\Validation\Exception;

/**
 * Thrown when a validation references a rule name that is neither a built-in
 * rule nor a callback registered with {@see \InitPHP\Validation\Validation::extend()}.
 *
 * Unknown rules are treated as programming errors and fail loudly instead of
 * silently passing validation.
 */
class UndefinedRuleException extends ValidationException
{
    /**
     * Build the exception for a specific rule name.
     *
     * @param string $rule The rule name that could not be resolved.
     */
    public static function forRule(string $rule): self
    {
        return new self(\sprintf('The validation rule "%s" is not defined.', $rule));
    }
}
