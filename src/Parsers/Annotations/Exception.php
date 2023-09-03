<?php
/*
 +------------------------------------------------------------------------+
 | Phalcon Framework                                                      |
 +------------------------------------------------------------------------+
 | Copyright (c) 2011-2023 Phalcon Team (https://phalconphp.com)          |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file docs/LICENSE.txt.                        |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com, so we can send you a copy immediately.      |
 +------------------------------------------------------------------------+
 | Authors: Phalcon Team <team@phalcon.io>                                |
 |          Rene Dziuba <php.tux@web.de>                                  |
 +------------------------------------------------------------------------+
 */
declare(strict_types=1);

namespace Phalcon\Parsers\Annotations;

/**
 * Exception for namespace `Phalcon\Parsers\Annotations`.
 *
 * @package Phalcon\Parsers\Annotations
 */
class Exception extends \Exception
{
    /**
     * Create a new `Exception` with the error message.
     *
     * @param string $message
     *
     * @return Exception
     */
    public static function syntaxError(string $message): Exception
    {
        return new self('[Syntax Error] ' . $message);
    }
}
