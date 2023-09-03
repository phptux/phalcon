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

namespace Phalcon\Parsers\Scanner;

use Phalcon\Parsers\Enum;
use Phalcon\Parsers\Exception;

/**
 * Scanner token class
 *
 * @package Phalcon\Parsers\Scanner
 */
class ScannerToken
{
    /**
     * Token opcode
     *
     * @var int|string
     */
    protected int|string $opcode = 0;

    /**
     * Token length
     *
     * @var int
     */
    protected int $length = 0;

    /**
     * Token value
     *
     * @var string|null
     */
    protected ?string $value = null;

    /**
     * Line number
     *
     * @var int
     */
    protected int $line = 0;

    /**
     * Filename
     *
     * @var string|null
     */
    protected ?string $filename = null;

    /**
     * Token Names
     *
     * @var array
     */
    protected static array $tokenNames = [
        // ANNOTATIONS
        'START'     => 0,
        'IGNORE'     => Enum::PHANNOT_T_IGNORE,
        'INTEGER'    => Enum::PHANNOT_T_INTEGER,
        'DOUBLE'     => Enum::PHANNOT_T_DOUBLE,
        'STRING'     => Enum::PHANNOT_T_STRING,
        'IDENTIFIER' => Enum::PHANNOT_T_IDENTIFIER,
        'NULL'       => Enum::PHANNOT_T_NULL,
        'TRUE'       => Enum::PHANNOT_T_TRUE,
        'FASLE'      => Enum::PHANNOT_T_FALSE,
        '@'          => Enum::PHANNOT_T_AT,
        ','          => Enum::PHANNOT_T_COMMA,
        '='          => Enum::PHANNOT_T_EQUALS,
        ':'          => Enum::PHANNOT_T_COLON,
        '('          => Enum::PHANNOT_T_PARENTHESES_OPEN,
        ')'          => Enum::PHANNOT_T_PARENTHESES_CLOSE,
        '{'          => Enum::PHANNOT_T_BRACKET_OPEN,
        '}'          => Enum::PHANNOT_T_BRACKET_CLOSE,
        '['          => Enum::PHANNOT_T_SBRACKET_OPEN,
        ']'          => Enum::PHANNOT_T_SBRACKET_CLOSE,

        // PHQL
        /*
        'INTEGER'             => self::PHQL_T_INTEGER,
        'DOUBLE'              => self::PHQL_T_DOUBLE,
        'STRING'              => self::PHQL_T_STRING,
        'IDENTIFIER'          => self::PHQL_T_IDENTIFIER,
        'HEXAINTEGER'         => self::PHQL_T_HINTEGER,
        'MINUS'               => self::PHQL_T_MINUS,
        '+'                   => self::PHQL_T_ADD,
        '-'                   => self::PHQL_T_SUB,
        '*'                   => self::PHQL_T_MUL,
        '/'                   => self::PHQL_T_DIV,
        '&'                   => self::PHQL_T_BITWISE_AND,
        '|'                   => self::PHQL_T_BITWISE_OR,
        '%%'                  => self::PHQL_T_MOD,
        'AND'                 => self::PHQL_T_AND,
        'OR'                  => self::PHQL_T_OR,
        'LIKE'                => self::PHQL_T_LIKE,
        'ILIKE'               => self::PHQL_T_ILIKE,
        'DOT'                 => self::PHQL_T_DOT,
        'COLON'               => self::PHQL_T_COLON,
        'COMMA'               => self::PHQL_T_COMMA,
        'EQUALS'              => self::PHQL_T_EQUALS,
        'NOT EQUALS'          => self::PHQL_T_NOTEQUALS,
        'NOT'                 => self::PHQL_T_NOT,
        '<'                   => self::PHQL_T_LESS,
        '<='                  => self::PHQL_T_LESSEQUAL,
        '>'                   => self::PHQL_T_GREATER,
        '>='                  => self::PHQL_T_GREATEREQUAL,
        '('                   => self::PHQL_T_PARENTHESES_OPEN,
        ')'                   => self::PHQL_T_PARENTHESES_CLOSE,
        'NUMERIC PLACEHOLDER' => self::PHQL_T_NPLACEHOLDER,
        'STRING PLACEHOLDER'  => self::PHQL_T_SPLACEHOLDER,
        'UPDATE'              => self::PHQL_T_UPDATE,
        'SET'                 => self::PHQL_T_SET,
        'WHERE'               => self::PHQL_T_WHERE,
        'DELETE'              => self::PHQL_T_DELETE,
        'FROM'                => self::PHQL_T_FROM,
        'AS'                  => self::PHQL_T_AS,
        'INSERT'              => self::PHQL_T_INSERT,
        'INTO'                => self::PHQL_T_INTO,
        'VALUES'              => self::PHQL_T_VALUES,
        'SELECT'              => self::PHQL_T_SELECT,
        'ORDER'               => self::PHQL_T_ORDER,
        'BY'                  => self::PHQL_T_BY,
        'LIMIT'               => self::PHQL_T_LIMIT,
        'OFFSET'              => self::PHQL_T_OFFSET,
        'GROUP'               => self::PHQL_T_GROUP,
        'HAVING'              => self::PHQL_T_HAVING,
        'IN'                  => self::PHQL_T_IN,
        'ON'                  => self::PHQL_T_ON,
        'INNER'               => self::PHQL_T_INNER,
        'JOIN'                => self::PHQL_T_JOIN,
        'LEFT'                => self::PHQL_T_LEFT,
        'RIGHT'               => self::PHQL_T_RIGHT,
        'IS'                  => self::PHQL_T_IS,
        'NULL'                => self::PHQL_T_NULL,
        'NOT IN'              => self::PHQL_T_NOTIN,
        'CROSS'               => self::PHQL_T_CROSS,
        'OUTER'               => self::PHQL_T_OUTER,
        'FULL'                => self::PHQL_T_FULL,
        'ASC'                 => self::PHQL_T_ASC,
        'DESC'                => self::PHQL_T_DESC,
        'BETWEEN'             => self::PHQL_T_BETWEEN,
        'DISTINCT'            => self::PHQL_T_DISTINCT,
        'AGAINST'             => self::PHQL_T_AGAINST,
        'CAST'                => self::PHQL_T_CAST,
        'CONVERT'             => self::PHQL_T_CONVERT,
        'USING'               => self::PHQL_T_USING,
        'ALL'                 => self::PHQL_T_ALL,
        'EXISTS'              => self::PHQL_T_EXISTS,
        'CASE'                => self::PHQL_T_CASE,
        'WHEN'                => self::PHQL_T_WHEN,
        'THEN'                => self::PHQL_T_THEN,
        'ELSE'                => self::PHQL_T_ELSE,
        'END'                 => self::PHQL_T_END,
        'FOR'                 => self::PHQL_T_FOR,
        'WITH'                => self::PHQL_T_WITH
        */
    ];

    /**
     * ScannerToken constructor.
     *
     * @param int|string         $opcode
     * @param int         $length
     * @param int         $line
     * @param string|null $value
     */
    public function __construct(int|string $opcode = 0, int $length = 0, int $line = 0, ?string $value = null)
    {
        $this->opcode = $opcode;
        $this->length = $length;
        $this->value  = $value;
        $this->line   = $line;
    }

    /**
     * Returns the token opcode.
     *
     * @return int|string
     */
    public function getOpcode(): int|string
    {
        return $this->opcode;
    }

    /**
     * Set the token opcode.
     *
     * @param int $opcode
     *
     * @return $this
     */
    public function setOpcode(int $opcode): ScannerToken
    {
        $this->opcode = $opcode;

        return $this;
    }

    /**
     * Returns the token length.
     *
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * Set the token length.
     *
     * @param int $length
     *
     * @return $this
     */
    public function setLength(int $length): ScannerToken
    {
        $this->length = $length;

        return $this;
    }

    /**
     * Returns the token value.
     *
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * Set the token value.
     *
     * @param string|null $value
     *
     * @return $this
     */
    public function setValue(?string $value): ScannerToken
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Returns the line number.
     *
     * @return int
     */
    public function getLine(): int
    {
        return $this->line;
    }

    /**
     * Set the line number.
     *
     * @param int $line
     *
     * @return $this
     */
    public function setLine(int $line): ScannerToken
    {
        $this->line = $line;

        return $this;
    }

    /**
     * Returns the filename.
     *
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * Set the filename.
     *
     * @param string|null $filename
     *
     * @return $this
     */
    public function setFilename(?string $filename): ScannerToken
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get the Number from the Token.
     *
     * @param string $token
     *
     * @return string|int
     * @throws Exception
     */
    public static function getTokenNumber(string $token): int|string
    {
        if (isset(self::$tokenNames[$token])) {
            return self::$tokenNames[$token];
        }

        throw new Exception('Token "' . $token . '" not exists.');
    }

    /**
     * Get the Name from the Token.
     *
     * @param int|string $number
     *
     * @return string
     * @throws Exception
     */
    public static function getTokenName(int|string $number): string
    {
        $values = array_flip(self::$tokenNames);

        if (isset($values[$number])) {
            return $values[$number];
        }

        throw new Exception('Token "' . $number . '" not exists.');
    }
}
