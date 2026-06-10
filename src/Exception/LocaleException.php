<?php

/**
 * LocaleException.php
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
 * Thrown when a locale cannot be loaded: the language directory does not
 * exist, the language file is missing, or the file does not return an array
 * of message templates.
 */
class LocaleException extends ValidationException
{
}
