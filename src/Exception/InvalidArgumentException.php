<?php

/**
 * InvalidArgumentException.php
 *
 * This file is part of InitPHP Validation.
 *
 * @author     Muhammet ŞAFAK <info@muhammetsafak.com>
 * @copyright  Copyright © 2022 InitPHP
 * @license    http://initphp.github.io/license.txt  MIT
 * @link       https://muhammetsafak.com
 */

declare(strict_types=1);

namespace InitPHP\Validation\Exception;

/**
 * Thrown when a public method receives an argument that cannot be used,
 * such as a rule list that contains a value which is neither a string nor a
 * callable.
 *
 * Extends the SPL {@see \InvalidArgumentException} while still implementing
 * {@see ExceptionInterface}, so it can be caught either way.
 */
class InvalidArgumentException extends \InvalidArgumentException implements ExceptionInterface
{
}
