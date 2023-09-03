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

namespace Phalcon\Parser\Annotations\Scanner;

use Phalcon\Parser\Enum;
use Phalcon\Parser\Scanner\ScannerToken;

/**
 * Scanner state class
 *
 * @package Phalcon\Parser\Annotations\Scanner
 */
class ScannerState
{
    /**
     * Scanner mode raw
     */
    public const PHANNOT_MODE_RAW = 0;

    /**
     * Scanner mode annotation
     */
    public const PHANNOT_MODE_ANNOTATION = 1;

    public const PHANNOT_SCANNER_RETCODE_EOF        = -1;
    public const PHANNOT_SCANNER_RETCODE_ERR        = -2;
    public const PHANNOT_SCANNER_RETCODE_IMPOSSIBLE = -3;

    /**
     * Active token
     *
     * @var ScannerToken|null
     */
    protected ?ScannerToken $activeToken = null;

    /**
     * Scanner mode
     *
     * @var int
     */
    protected int $mode = self::PHANNOT_MODE_RAW;

    /**
     * Scanner status
     * @var int
     */
    protected int $status = self::PHANNOT_SCANNER_RETCODE_IMPOSSIBLE;

        /**
         * Start char
         *
         * @var string|null
         */
    protected ? string $start = null;

    /**
     * End char
     *
     * @var string|null
     */
    protected ?string $end = null;

    /**
     * Marker
     *
     * @var string|null
     */
    protected ?string $marker = null;

    /**
     * Start length
     *
     * @var int
     */
    protected int $startLength = 0;

    /**
     * Active file line
     *
     * @var int
     */
    protected int $activeLine = 1;

    /**
     * Active file name
     *
     * @var string
     */
    protected string $activeFile = 'eval code';

    /**
     * Returns the active token
     *
     * @return ScannerToken|null
     */
    public function getActiveToken(): ?ScannerToken
    {
        return $this->activeToken;
    }

    /**
     * Set the active token
     *
     * @param ScannerToken $activeToken
     *
     * @return $this
     */
    public function setActiveToken(ScannerToken $activeToken): ScannerState
    {
        $this->activeToken = $activeToken;

        return $this;
    }

    /**
     * Returns the scanner mode
     *
     * @return int
     */
    public function getMode(): int
    {
        return $this->mode;
    }

    /**
     * Set the scanner mode.
     *
     * @param int $mode
     *
     * @return $this
     */
    public function setMode(int $mode): ScannerState
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * Returns the scanner status.
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Set the scanner status
     *
     * @param int $status
     *
     * @return $this
     */
    public function setStatus(int $status): ScannerState
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Returns the start string
     *
     * @return string|null
     */
    public function getStart(): ?string
    {
        return $this->start;
    }

    /**
     * Set the start string
     *
     * @param string $start
     *
     * @return $this
     */
    public function setStart(string $start): ScannerState
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Returns the end string
     *
     * @return string|null
     */
    public function getEnd(): ?string
    {
        return $this->end;
    }

    /**
     * Set the end string
     *
     * @param string $end
     *
     * @return $this
     */
    public function setEnd(string $end): ScannerState
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Returns the marker.
     *
     * @return string|null
     */
    public function getMarker(): ?string
    {
        return $this->marker;
    }

    /**
     * Set the marker.
     *
     * @param string|null $marker
     *
     * @return $this
     */
    public function setMarker(?string $marker): ScannerState
    {
        $this->marker = $marker;

        return $this;
    }

    /**
     * Returns the start length.
     *
     * @return int
     */
    public function getStartLength(): int
    {
        return $this->startLength;
    }

    /**
     * Set the start length.
     *
     * @param int $startLength
     *
     * @return $this
     */
    public function setStartLength(int $startLength): ScannerState
    {
        $this->startLength = $startLength;

        return $this;
    }

    /**
     * Returns the active line
     *
     * @return int
     */
    public function getActiveLine(): int
    {
        return $this->activeLine;
    }

    /**
     * Set the active line
     *
     * @param int $activeLine
     *
     * @return $this
     */
    public function setActiveLine(int $activeLine): ScannerState
    {
        $this->activeLine = $activeLine;

        return $this;
    }

    /**
     * Returns the active file
     *
     * @return string
     */
    public function getActiveFile(): string
    {
        return $this->activeFile;
    }

    /**
     * Set the active file.
     *
     * @param string $activeFile
     *
     * @return $this
     */
    public function setActiveFile(string $activeFile): ScannerState
    {
        $this->activeFile = $activeFile;

        return $this;
    }
}
