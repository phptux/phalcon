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

namespace Azirax\Re2c\Parser;

use ArrayAccess;

/**
 * This can be used to store both the string representation of
 * a token, and any useful meta-data associated with the token.
 *
 * @package    Azirax\Re2c\Regex\Parser
 * @author     Rene Dziuba <php.tux@web.de>
 * @copyright  Copyright (c) 2023 The Authors
 * @license    <http://opensource.org/licenses/bsd-license.php> New BSD License
 */
class Token implements ArrayAccess
{
    /**
     * Token string
     *
     * @var string
     */
    public string $string = '';

    /**
     * Token metadata
     *
     * @var array
     */
    public array $metadata = [];

    public function __construct(mixed $s, array|Token $m = [])
    {
        if ($s instanceof Token) {
            $this->string   = $s->string;
            $this->metadata = $s->metadata;
        } else {
            $this->string = (string)$s;
            if ($m instanceof Token) {
                $this->metadata = $m->metadata;
            } elseif (is_array($m)) {
                $this->metadata = $m;
            }
        }
    }

    /**
     * Returns the token string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->string;
    }

    /**
     * Check, if exists in metadata.
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->metadata[$offset]);
    }

    /**
     * Return a value from the metadata.
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->metadata[$offset];
    }

    /**
     * Set a metadata value.
     *
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            if (isset($value[0])) {
                $x              = ($value instanceof Token) ? $value->metadata : $value;
                $this->metadata = array_merge($this->metadata, $x);

                return;
            }
            $offset = count($this->metadata);
        }

        if ($value === null) {
            return;
        }

        if ($value instanceof Token) {
            if ($value->metadata) {
                $this->metadata[$offset] = $value->metadata;
            }
        } elseif ($value) {
            $this->metadata[$offset] = $value;
        }
    }

    /**
     * Remove a metadata key.
     *
     * @param mixed $offset
     *
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->metadata[$offset]);
    }
}
