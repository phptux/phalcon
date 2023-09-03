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

namespace Phalcon\Parsers;

/**
 * Parser core class
 *
 * @package    Phalcon\Parsers
 * @author     Rene Dziuba <php.tux@web.de>
 * @copyright  Copyright (c) 2023 The Authors
 * @license    <http://opensource.org/licenses/bsd-license.php> New BSD License
 */
class Parser
{
    /**
     * Parse a comment for annotations.
     *
     * @param string      $comment
     * @param string|null $file
     * @param int|null    $line
     *
     * @return array
     */
    public static function annotationsParse(string $comment, ?string $file = null, ?int $line = null): array
    {
        $parser = new Annotations();

        return $parser->parseComment($comment, $file, $line);
    }
}
