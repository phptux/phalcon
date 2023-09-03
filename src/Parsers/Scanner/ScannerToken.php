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

namespace Phalcon\Parser\Scanner;

/**
 * Scanner token class
 *
 * @package Phalcon\Parser\Scanner
 */
class ScannerToken
{
    /**
     * Token opcode
     *
     * @var int
     */
    protected int $opcode = 0;

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
     * ScannerToken constructor.
     *
     * @param int         $opcode
     * @param int         $length
     * @param int         $line
     * @param string|null $value
     */
    public function __construct(int $opcode = 0, int $length = 0, int $line = 0, ?string $value = null)
    {
        $this->opcode = $opcode;
        $this->length = $length;
        $this->value  = $value;
        $this->line   = $line;
    }

    /**
     * Returns the token opcode.
     *
     * @return int
     */
    public function getOpcode(): int
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
}
