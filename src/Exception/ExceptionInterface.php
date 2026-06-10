<?php

/**
 * ExceptionInterface.php
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

use Throwable;

/**
 * Marker interface implemented by every exception thrown by this package.
 *
 * Catch this interface to handle any failure that originates from
 * InitPHP Validation regardless of the concrete exception type.
 */
interface ExceptionInterface extends Throwable
{
}
