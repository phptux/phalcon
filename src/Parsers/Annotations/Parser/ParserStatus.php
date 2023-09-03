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

namespace Phalcon\Parsers\Annotations\Parser;

use Phalcon\Parsers\Annotations\Scanner\ScannerState;
use Phalcon\Parsers\Scanner\ScannerToken;

/**
 * Parser status class.
 *
 * @package Phalcon\Parsers\Annotations\Parser
 */
class ParserStatus
{
    public const PHANNOT_PARSING_OK = 1;
    public const PHANNOT_PARSING_FAILED = 0;

    /**
     * @var mixed|null
     */
    protected mixed $ret = null;

    /**
     * @var ScannerState
     */
    protected ScannerState $scannerState;

    /**
     * @var int
     */
    protected int $status;

    /**
     * @var int
     */
    protected int $syntaxErrorLength = 0;

    /**
     * @var string|null
     */
    protected ?string $syntaxError = null;

    /**
     * @var ScannerToken
     */
    protected ScannerToken $token;

    /**
     * ParserStatus constructor.
     *
     * @param ScannerState $scannerState
     * @param ScannerToken $token
     * @param int          $status
     */
    public function __construct(ScannerState $scannerState, ScannerToken $token, int $status)
    {
        $this->scannerState = $scannerState;
        $this->token = $token;
        $this->status = $status;
    }

    /**
     * Set the parser status.
     *
     * @param int $status
     *
     * @return $this
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Returns the parser status.
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    public function getRet(): mixed
    {
        return $this->ret;
    }

    public function setRet(mixed $ret): ParserStatus
    {
        $this->ret = $ret;
        return $this;
    }

    public function getScannerState(): ScannerState
    {
        return $this->scannerState;
    }

    public function setScannerState(ScannerState $scannerState): ParserStatus
    {
        $this->scannerState = $scannerState;
        return $this;
    }

    public function getSyntaxErrorLength(): int
    {
        return $this->syntaxErrorLength;
    }

    public function setSyntaxErrorLength(int $syntaxErrorLength): ParserStatus
    {
        $this->syntaxErrorLength = $syntaxErrorLength;
        return $this;
    }

    public function getSyntaxError(): ?string
    {
        return $this->syntaxError;
    }

    public function setSyntaxError(?string $syntaxError): ParserStatus
    {
        $this->syntaxError = $syntaxError;
        return $this;
    }

    public function getToken(): ScannerToken
    {
        return $this->token;
    }

    public function setToken(ScannerToken $token): ParserStatus
    {
        $this->token = $token;
        return $this;
    }
}
