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
 | Authors: Rene Dziuba <php.tux@web.de>                                  |
 +------------------------------------------------------------------------+
 */
declare(strict_types=1);

namespace Phalcon\Parser\Annotations;

use Phalcon\Parser\Annotations\Parser\ParserStatus;
use Phalcon\Parser\Enum;
use Phalcon\Parser\Scanner\ScannerToken;

/**
 * Parser helper class
 *
 * @package Phalcon\Parser\Annotations
 */
class Helper
{
    /**
     * Add list.
     *
     * @param $ret
     * @param $right
     * @param $left
     *
     * @return void
     */
    public function list(&$ret, $right, $left): void
    {
        if ($right) {
            $ret = $right;
        }

        if (is_array($left)) {
            if (isset($left[0])) {
                $ret = $this->addItemToList($ret, $left);
            } else {
                $ret[] = $left;
            }
        }
    }

    public function annotation(&$ret, $token, $arguments, ParserStatus $status): void
    {
        $ret = ['type' => Enum::PHANNOT_T_ANNOTATION];

        if ($token) {
            $ret['name'] = $token;
        }

        if ($arguments) {
            $ret['arguments'] = $arguments;
        }

        $ret['file'] = $status->getScannerState()->getActiveFile();
        $ret['line'] = $status->getScannerState()->getActiveLine();
    }

    public function namedItem(&$ret, $token, $expression): void
    {
        $ret = ['expr' => $expression];

        if (is_string($token)) {
            $ret['name'] = trim($token, '"');
        }
    }

    public function literal(&$ret, $tokenType, $token): void
    {
        $ret = ['type' => $tokenType];

        if ($token) {
            $ret['value'] = trim($token, '"');// $token->getValue();
        }
    }

    public function arrays(&$ret, $items): void
    {
        $ret = ['type' => Enum::PHANNOT_T_ARRAY];

        if (is_array($items)) {
            $ret['items'] = $items;
        }
    }

    protected function addItemToList(array $array, $item): array
    {
        foreach ($item as $value) {
            $array[] = $value;
        }

        return $array;
    }
}
