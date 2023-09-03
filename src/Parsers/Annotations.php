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

namespace Phalcon\Parsers;

use Phalcon\Parsers\Annotations\Parser;
use Phalcon\Parsers\Annotations\Scanner;
use Phalcon\Parsers\Scanner\ScannerToken;

/**
 * Parser for class annotations
 *
 * @package Phalcon\Parsers
 */
class Annotations
{
    /**
     * Parse a comment for annotations.
     *
     * @param string      $comment  Comment
     * @param string|null $file     Filename
     * @param int|null    $line     File line number
     *
     * @return array
     */
    public function parseComment(string $comment, ?string $file = null, ?int $line = null): array
    {
        if (empty($comment)) {
            return [];
        }

        if (null === $file) {
            $file = 'eval code';
        }

        if ($line === null) {
            $line = 1;
        }

        if (strlen($comment) < 2) {
            return [];
        }

        $startLines    = 0;
        $rawComment    = $comment;
        $comment       = $this->removeCommentSeparators($rawComment, $startLines);
        $commentLength = strlen($comment);

        if ($commentLength < 2) {
            return [];
        }

        $token = new ScannerToken(0);
        $scannerState = new Scanner\ScannerState();
        $scannerState
            ->setActiveToken($token)
            ->setActiveLine($line)
            ->setActiveFile($file);

        $parserStatus = new Parser\ParserStatus(
            $scannerState,
            $token,
            Parser\ParserStatus::PHANNOT_PARSING_OK
        );

        $scanner = self::getScanner($scannerState, $comment);
        $parser  = self::getParser($parserStatus);

        while ($scanner->run()) {
            $token = $scannerState->getActiveToken();

            switch ($token->getOpcode()) {
                case Enum::PHANNOT_T_IGNORE:
                    break;

                case Enum::PHANNOT_T_AT:
                    $parser->phannot(Parser::PHANNOT_AT);
                    break;

                case Enum::PHANNOT_T_COMMA:
                    $parser->phannot(Parser::PHANNOT_COMMA);
                    break;

                case Enum::PHANNOT_T_EQUALS:
                    $parser->phannot(Parser::PHANNOT_EQUALS);
                    break;

                case Enum::PHANNOT_T_COLON:
                    $parser->phannot(Parser::PHANNOT_COLON);
                    break;

                case Enum::PHANNOT_T_PARENTHESES_OPEN:
                    $parser->phannot(Parser::PHANNOT_PARENTHESES_OPEN);
                    break;

                case Enum::PHANNOT_T_PARENTHESES_CLOSE:
                    $parser->phannot(Parser::PHANNOT_PARENTHESES_CLOSE);
                    break;

                case Enum::PHANNOT_T_BRACKET_OPEN:
                    $parser->phannot(Parser::PHANNOT_BRACKET_OPEN);
                    break;

                case Enum::PHANNOT_T_BRACKET_CLOSE:
                    $parser->phannot(Parser::PHANNOT_BRACKET_CLOSE);
                    break;

                case Enum::PHANNOT_T_SBRACKET_OPEN:
                    $parser->phannot(Parser::PHANNOT_SBRACKET_OPEN);
                    break;

                case Enum::PHANNOT_T_SBRACKET_CLOSE:
                    $parser->phannot(Parser::PHANNOT_SBRACKET_CLOSE);
                    break;

                case Enum::PHANNOT_T_NULL:
                    $parser->phannot(Parser::PHANNOT_NULL);
                    break;

                case Enum::PHANNOT_T_TRUE:
                    $parser->phannot(Parser::PHANNOT_TRUE);
                    break;

                case Enum::PHANNOT_T_FALSE:
                    $parser->phannot(Parser::PHANNOT_FALSE);
                    break;

                case Enum::PHANNOT_T_INTEGER:
                    $parser->phannot(Parser::PHANNOT_INTEGER, $token->getValue());
                    break;

                case Enum::PHANNOT_T_DOUBLE:
                    $parser->phannot(Parser::PHANNOT_DOUBLE, $token->getValue());
                    break;

                case Enum::PHANNOT_T_STRING:
                    $parser->phannot(Parser::PHANNOT_STRING, $token->getValue());
                    break;

                case Enum::PHANNOT_T_IDENTIFIER:
                    $parser->phannot(Parser::PHANNOT_IDENTIFIER, $token->getValue());
                    break;

                default:
                    $parserStatus->setStatus(Parser\ParserStatus::PHANNOT_PARSING_FAILED);
                    $parserStatus->setSyntaxError(sprintf('Scanner: Unknown opcode %d', $token->getOpcode()));
                    break;
            }

            if ($parserStatus->getStatus() === Parser\ParserStatus::PHANNOT_PARSING_FAILED) {
                throw new Exception($parserStatus->getSyntaxError());
            }
        }

        $parser->phannot(0, null);

        if ($parserStatus->getStatus() === Parser\ParserStatus::PHANNOT_PARSING_FAILED) {
            throw new Exception($parserStatus->getSyntaxError());
        }

        return $parser->retvalue;
    }

    /**
     * Remove the comment separators.
     *
     * @param string   $comment    Comment
     * @param int|null $startLines Start lines
     *
     * @return string
     */
    protected function removeCommentSeparators(string $comment, ?int &$startLines = 0): string
    {
        $length       = strlen($comment);
        $startMode    = true;
        $cleanComment = '';

        for ($i = 0; $i < $length; $i++) {
            $ch = $comment[$i];

            if ($startMode) {
                if ($ch == ' ' || $ch == '*' || $ch == '/' || $ch == '\t' || ord($ch) == 11) {
                    continue;
                }
                $startMode = false;
            }

            if ($ch == '@') {
                $cleanComment .= $ch;
                $i++;

                $openParentheses = 0;
                for ($j = $i; $j < $length; $j++) {
                    $ch = $comment[$j];

                    if ($startMode) {
                        if ($ch == ' ' || $ch == '*' || $ch == '/' || $ch == '\t' || ord($ch) == 11) {
                            continue;
                        }
                        $startMode = false;
                    }

                    if ($openParentheses === 0) {

                        if (ctype_alnum($ch) || $ch == '_' || $ch == '\\') {
                            $cleanComment .= $ch;
                            continue;
                        }

                        if ($ch == '(') {
                            $cleanComment .= $ch;
                            $openParentheses++;
                            continue;
                        }

                    } else {
                        $cleanComment .= $ch;

                        if ($ch == '(') {
                            $openParentheses++;
                        } elseif ($ch == ')') {
                            $openParentheses--;
                        } elseif ($ch == "\n") {
                            $startLines++;
                            $startMode = true;
                        }

                        if ($openParentheses > 0) {
                            continue;
                        }
                    }

                    $i            = $j;
                    $cleanComment .= ' ';
                    break;
                }
            }

            if ($ch == "\n") {
                $startLines++;
                $startMode = true;
            }
        }

        return $cleanComment;
    }

    /**
     * Get the Scanner
     *
     * @param Scanner\ScannerState $status
     * @param string $comment
     *
     * @return Scanner
     */
    protected static function getScanner(Scanner\ScannerState $status, string $comment): Scanner
    {
        return new Scanner($status, $comment);
    }

    /**
     * Get the lemon Parser
     *
     * @param Parser\ParserStatus $status
     *
     * @return Parser
     */
    protected static function getParser(Parser\ParserStatus $status): Parser
    {
        return new Parser($status);
    }
}
