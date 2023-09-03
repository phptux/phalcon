<?php
/*
 +------------------------------------------------------------------------+
 | Azirax re2c                                                            |
 +------------------------------------------------------------------------+
 | Copyright (c) 2023 Azirax Team (http://mrversion.de)                   |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | <http://opensource.org/licenses/bsd-license.php> New BSD License       |
 +------------------------------------------------------------------------+
 | Authors: Rene Dziuba <php.tux@web.de>                                  |
 +------------------------------------------------------------------------+
 */
declare(strict_types=1);

namespace Azirax\Re2c;

/**
 * Helper for generate the PHP code.
 *
 * @package    Azirax\Re2c
 * @author     Rene Dziuba <php.tux@web.de>
 * @copyright  Copyright (c) 2023 The Authors
 * @license    <http://opensource.org/licenses/bsd-license.php> New BSD License
 */
class Code
{
    public static function quotePatter(string $patter): string
    {
        $quoted = '';
        $length = strlen($patter);

        for ($i = 0; $i < $length; $i++) {
            if ($patter[$i] === "'" && $patter[$i - 1] !== '\\') {
                $quoted .= '\\';
            }
            $quoted .= $patter[$i];
        }

        return $quoted;
    }

    /**
     * Returns PHP code for a class method.
     *
     * @param string $name
     * @param array  $arguments
     * @param string $body
     * @param string $return
     *
     * @return string
     */
    public static function getMethod(string $name, array $arguments, string $body, string $return = 'void'): string
    {
        $docArgs = '';

        foreach ($arguments as $arg) {
            $docArgs .= "@param " . $arg;
        }

        $funcArgs = implode(', ', $arguments);

        return <<<CODE

    /**
     * $docArgs
     *
     * @return $return
     */
    public function $name($funcArgs): $return
    {
        $body
    }
CODE;

    }

    /**
     * Returns PHP code for a class method.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return string
     */
    public static function getSimpleMethod(string $name, array $arguments): string
    {
        $docArgs = '';

        foreach ($arguments as $arg) {
            $docArgs .= "\t * @param " . $arg;
        }

        $funcArgs = implode(', ', $arguments);

        return <<<CODE

    /**
     * $docArgs
     */
    public function $name($funcArgs)
    {
CODE;

    }

    /**
     * Returns PHP code for a constant.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return string
     */
    public static function getConstant(string $name, mixed $value): string
    {
        $hint = self::getHint($value);

        return <<<CODE


    /**
     * @const $hint
     */
    public const $name = $value;
CODE;

    }

    /**
     * Returns the hint from the variable value.
     *
     * @param mixed $value
     *
     * @return string
     */
    protected static function getHint(mixed $value): string
    {
        if ($value === null) {
            return 'null';
        }

        return match (gettype($value)) {
            'boolean' => 'bool',
            'integer' => 'int',
            'double'  => 'float',
            'string'  => 'string',
            default   => 'mixed',
        };
    }
}
