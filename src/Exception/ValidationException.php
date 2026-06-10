<?php

/**
 * ValidationException.php
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

use RuntimeException;

/**
 * Base runtime exception for the package.
 *
 * Thrown for problems detected while a validator is being configured or
 * executed (for example, referencing a rule that does not exist).
 */
class ValidationException extends RuntimeException implements ExceptionInterface
{
}
