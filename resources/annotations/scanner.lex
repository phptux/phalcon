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
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
 | Authors: Rene Dziuba <php.tux@web.de>                                  |
 +------------------------------------------------------------------------+
 */
namespace Phalcon\Parsers\Annotations;

use Phalcon\Parsers\Enum;
use Phalcon\Parsers\Scanner\ScannerToken;
use Phalcon\Parsers\Annotations\Scanner\ScannerState;

/**
 * Annotation scanner
 *
 * @package Phalcon\Parser\Annotations
 */
class Scanner
{
    /**
     * Token number
     *
     * @var int|string
     */
    public int|string $token = 0;

    /**
     * Token value
     *
     * @var mixed|null
     */
    public mixed $value = null;

    /**
     * Input data
     *
     * @var string
     */
    protected string $_data;

    /**
     * Current line number
     * @var int
     */
    protected int $_line;

    /**
     * Counter
     *
     * @var int
     */
    protected int $_counter = 0;

    /**
     * Scanner state
     *
     * @var ScannerState
     */
    private ScannerState $_state;

    /**
     * PhqlLexer constructor.
     *
     * @param ScannerState $state
     * @param string $data
     */
    public function __construct(ScannerState $state, string $data)
    {
        $this->_state = $state;
        $this->_data = $data;
        $this->_counter = 0;
        $this->_line = 1;
    }

    /**
     * Save the token in the scanner state
     *
     * @param int|string $opcode   Token number
     *
     * @return void
     */
    protected function saveToken(int|string $opcode): void
    {
        $token = new ScannerToken(
            $opcode,
            strlen($this->value),
            $this->_counter,
            $this->value
        );
        $token->setFilename($this->_state->getActiveFile());

        $this->_state->setActiveToken($token);
    }

/*!lex2php
%input $this->_data
%counter $this->_counter
%token $this->token
%value $this->value
%line $this->_line
%caseinsensitive 1
%unicode 1

DOUBLE=/[\-]?[0-9]+[.][0-9]+/
INTEGER=/[\-]?[0-9]+/
STRING=/"(?:[^"\\]|\\.)*"|'(?:[^'\\\\]|\\\\.)*'/
IDENTIFIER=/(\x5C?[a-zA-Z_]([a-zA-Z0-9_]*)(\x5C[a-zA-Z_]([a-zA-Z0-9_]*))*)/
IGNORE=/[\s\t\r;]+/
NEWLINE=/[\n]+/
*/
/*!lex2php
%statename START

DOUBLE {
    $this->saveToken(Parser::PHANNOT_DOUBLE);
}

INTEGER {
    $this->saveToken(Parser::PHANNOT_INTEGER);
}

"null" {
	$this->saveToken(Enum::PHANNOT_T_NULL);
}

"false" {
    $this->saveToken(Enum::PHANNOT_T_FALSE);
}

"true" {
    $this->saveToken(Enum::PHANNOT_T_TRUE);
}

STRING {
    $this->saveToken(Parser::PHANNOT_STRING);
}

IDENTIFIER {
    $this->saveToken(Parser::PHANNOT_IDENTIFIER);
}

"(" {
    $this->saveToken(Enum::PHANNOT_T_PARENTHESES_OPEN);
}

")" {
    $this->saveToken(Enum::PHANNOT_T_PARENTHESES_CLOSE);
}

"{" {
    $this->saveToken(Enum::PHANNOT_T_BRACKET_OPEN);
}

"}" {
    $this->saveToken(Enum::PHANNOT_T_BRACKET_CLOSE);
}

"[" {
    $this->saveToken(Enum::PHANNOT_T_SBRACKET_OPEN);
}

"]" {
    $this->saveToken(Enum::PHANNOT_T_SBRACKET_CLOSE);
}

"@" {
    $this->saveToken(Enum::PHANNOT_T_AT);
}

"=" {
    $this->saveToken(Enum::PHANNOT_T_EQUALS);
}

":" {
    $this->saveToken(Enum::PHANNOT_T_COLON);
}

"," {
    $this->saveToken(Enum::PHANNOT_T_COMMA);
}

IGNORE {
	$this->saveToken(Enum::PHANNOT_T_IGNORE);
}

NEWLINE {
    $this->_state->setActiveLine($this->_state->getActiveLine() + 1);
    $this->saveToken(Enum::PHANNOT_T_IGNORE);
}

"\000" {
    $this->_state->setStatus(ScannerState::PHANNOT_SCANNER_RETCODE_EOF);
}

'[^]' {
    $this->_state->setStatus(ScannerState::PHANNOT_SCANNER_RETCODE_ERR);
}
*/
}
