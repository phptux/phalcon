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

namespace Phalcon\Parsers\Parser;

/**
 * The following structure represents a single element of the parser's stack.
 *
 * Information stored includes:
 *
 * - The state number for the parser at this level of the stack.
 *
 * - The value of the token stored at this level of the stack.
 *   (In other words, the "major" token.)
 *
 * - The semantic value stored at this level of the stack.  This is
 *   the information used by the action routines in the grammar.
 *   It is sometimes called the "minor" token.
 *
 * @package    Phalcon\Parsers\Parser
 * @author     Rene Dziuba <php.tux@web.de>
 * @copyright  Copyright (c) 2023 The Authors
 * @license    <http://opensource.org/licenses/bsd-license.php> New BSD License
 */
class StackEntry
{
    /**
     * The state-number
     *
     * @var mixed
     */
    public mixed $stateno = null;

    /**
     * The major token value.
     *
     * This is the code number for the token at this stack level
     *
     * @var mixed
     */
    public mixed $major = null;

    /**
     * The user-supplied minor token value.
     *
     * This is the value of the token
     *
     * @var mixed
     */
    public mixed $minor = null;
}
