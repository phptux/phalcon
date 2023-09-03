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
 * Annotation Scanner
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

    private int $_yy_state = 1;
    private array $_yy_stack = [];

    /**
     * Run the lexer.
     *
     * @return mixed
     */
    public function run(): mixed
    {
        if ($this->_state->getMode() === ScannerState::PHANNOT_MODE_RAW) {
			$next = $this->_data[$this->_counter + 1];

			if ($this->_data[$this->_counter] == '\0' || $this->_data[$this->_counter] == '@') {
                if (($next >= 'A' && $next <= 'Z') || ($next >= 'a' && $next <= 'z')) {
                    $this->_state->setMode(ScannerState::PHANNOT_MODE_ANNOTATION);

                    return $this->run();
                }
            }

			$this->_counter++;
			$this->saveToken(Enum::PHANNOT_T_IGNORE);

			return true;

		}

        return $this->{'lex' . $this->_yy_state}();
    }

    /**
     * Push the state.
     *
     * @param int $state
     *
     * @return void
     */
    public function pushState(int $state): void
    {
        array_push($this->_yy_stack, $this->_yy_state);
        $this->_yy_state = $state;
    }

    /**
     * Pop the state.
     *
     * @return void
     */
    public function popState(): void
    {
        $this->_yy_state = array_pop($this->_yy_stack);
    }

    /**
     * Start the lexer from state.
     *
     * @param int $state
     *
     * @return void
     */
    public function begin(int $state): void
    {
        $this->_yy_state = $state;
    }


    /**
     *
     * @throws Exception
     */
    public function lex1()
    {
        $tokenMap = array (
              1 => 0,
              2 => 0,
              3 => 0,
              4 => 0,
              5 => 0,
              6 => 0,
              7 => 4,
              12 => 0,
              13 => 0,
              14 => 0,
              15 => 0,
              16 => 0,
              17 => 0,
              18 => 0,
              19 => 0,
              20 => 0,
              21 => 0,
              22 => 0,
              23 => 0,
              24 => 0,
              25 => 0,
            );
        if ($this->_counter >= strlen($this->_data)) {
            return false; // end of input
        }
        $yy_global_pattern = '/\G([\-]?[0-9]+[.][0-9]+)|\G([\-]?[0-9]+)|\G(null)|\G(false)|\G(true)|\G(\"(?:[^\"\\\\]|\\\\.)*\"|\'(?:[^\'\\\\\\\\]|\\\\\\\\.)*\')|\G((\x5C?[a-zA-Z_]([a-zA-Z0-9_]*)(\x5C[a-zA-Z_]([a-zA-Z0-9_]*))*))|\G(\\()|\G(\\))|\G(\\{)|\G(\\})|\G(\\[)|\G(\\])|\G(@)|\G(=)|\G(:)|\G(,)|\G([\s\t\r;]+)|\G([\n]+)|\G(\\\\000)|\G(\\[\\^\\])/iu';

        do {
            if (preg_match($yy_global_pattern,$this->_data, $yymatches, 0, $this->_counter)) {
                $yysubmatches = $yymatches;
                $yymatches = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                if (!count($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' .
                        ' an empty string.  Input "' . substr($this->_data,
                        $this->_counter, 5) . '... state START');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                if ($tokenMap[$this->token]) {
                    // extract sub-patterns for passing to lex function
                    $yysubmatches = array_slice($yysubmatches, $this->token + 1,
                        $tokenMap[$this->token]);
                } else {
                    $yysubmatches = array();
                }
                $this->value = current($yymatches); // token value
                $r = $this->{'lex_r1_' . $this->token}($yysubmatches);
                if ($r === null) {
                    $this->_counter += strlen($this->value);
                    $this->_line += substr_count($this->value, "\n");
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->run();
                } elseif ($r === false) {
                    $this->_counter += strlen($this->value);
                    $this->_line += substr_count($this->value, "\n");
                    if ($this->_counter >= strlen($this->_data)) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                } else {
                    $yy_yymore_patterns = [
        1 => [0, "\G([\-]?[0-9]+)|\G(null)|\G(false)|\G(true)|\G(\"(?:[^\"\\\\]|\\\\.)*\"|\'(?:[^\'\\\\\\\\]|\\\\\\\\.)*\')|\G((\x5C?[a-zA-Z_]([a-zA-Z0-9_]*)(\x5C[a-zA-Z_]([a-zA-Z0-9_]*))*))|\G(\\()|\G(\\))|\G(\\{)|\G(\\})|\G(\\[)|\G(\\])|\G(@)|\G(=)|\G(:)|\G(,)|\G([\s\t\r;]+)|\G([\n]+)|\G(\\\\000)|\G(\\[\\^\\])"],
        2 => [0, "\G(null)|\G(false)|\G(true)|\G(\"(?:[^\"\\\\]|\\\\.)*\"|\'(?:[^\'\\\\\\\\]|\\\\\\\\.)*\')|\G((\x5C?[a-zA-Z_]([a-zA-Z0-9_]*)(\x5C[a-zA-Z_]([a-zA-Z0-9_]*))*))|\G(\\()|\G(\\))|\G(\\{)|\G(\\})|\G(\\[)|\G(\\])|\G(@)|\G(=)|\G(:)|\G(,)|\G([\s\t\r;]+)|\G([\n]+)|\G(\\\\000)|\G(\\[\\^\\])"],
        3 => [0, "\G(false)|\G(true)|\G(\"(?:[^\"\\\\]|\\\\.)*\"|\'(?:[^\'\\\\\\\\]|\\\\\\\\.)*\')|\G((\x5C?[a-zA-Z_]([a-zA-Z0-9_]*)(\x5C[a-zA-Z_]([a-zA-Z0-9_]*))*))|\G(\\()|\G(\\))|\G(\\{)|\G(\\})|\G(\\[)|\G(\\])|\G(@)|\G(=)|\G(:)|\G(,)|\G([\s\t\r;]+)|\G([\n]+)|\G(\\\\000)|\G(\\[\\^\\])"],
        4 => [0, "\G(true)|\G(\"(?:[^\"\\\\]|\\\\.)*\"|\'(?:[^\'\\\\\\\\]|\\\\\\\\.)*\')|\G((\x5C?[a-zA-Z_]([a-zA-Z0-9_]*)(\x5C[a-zA-Z_]([a-zA-Z0-9_]*))*))|\G(\\()|\G(\\))|\G(\\{)|\G(\\})|\G(\\[)|\G(\\])|\G(@)|\G(=)|\G(:)|\G(,)|\G([\s\t\r;]+)|\G([\n]+)|\G(\\\\000)|\G(\\[\\^\\])"],
        5 => [0, "\G(\"(?:[^\"\\\\]|\\\\.)*\"|\'(?:[^\'\\\\\\\\]|\\\\\\\\.)*\')|\G((\x5C?[a-zA-Z_]([a-zA-Z0-9_]*)(\x5C[a-zA-Z_]([a-zA-Z0-9_]*))*))|\G(\\()|\G(\\))|\G(\\{)|\G(\\})|\G(\\[)|\G(\\])|\G(@)|\G(=)|\G(:)|\G(,)|\G([\s\t\r;]+)|\G([\n]+)|\G(\\\\000)|\G(\\[\\^\\])"],
        6 => [0, "\G((\x5C?[a-zA-Z_]([a-zA-Z0-9_]*)(\x5C[a-zA-Z_]([a-zA-Z0-9_]*))*))|\G(\\()|\G(\\))|\G(\\{)|\G(\\})|\G(\\[)|\G(\\])|\G(@)|\G(=)|\G(:)|\G(,)|\G([\s\t\r;]+)|\G([\n]+)|\G(\\\\000)|\G(\\[\\^\\])"],
        7 => [4, "\G(\\()|\G(\\))|\G(\\{)|\G(\\})|\G(\\[)|\G(\\])|\G(@)|\G(=)|\G(:)|\G(,)|\G([\s\t\r;]+)|\G([\n]+)|\G(\\\\000)|\G(\\[\\^\\])"],
        12 => [4, "\G(\\))|\G(\\{)|\G(\\})|\G(\\[)|\G(\\])|\G(@)|\G(=)|\G(:)|\G(,)|\G([\s\t\r;]+)|\G([\n]+)|\G(\\\\000)|\G(\\[\\^\\])"],
        13 => [4, "\G(\\{)|\G(\\})|\G(\\[)|\G(\\])|\G(@)|\G(=)|\G(:)|\G(,)|\G([\s\t\r;]+)|\G([\n]+)|\G(\\\\000)|\G(\\[\\^\\])"],
        14 => [4, "\G(\\})|\G(\\[)|\G(\\])|\G(@)|\G(=)|\G(:)|\G(,)|\G([\s\t\r;]+)|\G([\n]+)|\G(\\\\000)|\G(\\[\\^\\])"],
        15 => [4, "\G(\\[)|\G(\\])|\G(@)|\G(=)|\G(:)|\G(,)|\G([\s\t\r;]+)|\G([\n]+)|\G(\\\\000)|\G(\\[\\^\\])"],
        16 => [4, "\G(\\])|\G(@)|\G(=)|\G(:)|\G(,)|\G([\s\t\r;]+)|\G([\n]+)|\G(\\\\000)|\G(\\[\\^\\])"],
        17 => [4, "\G(@)|\G(=)|\G(:)|\G(,)|\G([\s\t\r;]+)|\G([\n]+)|\G(\\\\000)|\G(\\[\\^\\])"],
        18 => [4, "\G(=)|\G(:)|\G(,)|\G([\s\t\r;]+)|\G([\n]+)|\G(\\\\000)|\G(\\[\\^\\])"],
        19 => [4, "\G(:)|\G(,)|\G([\s\t\r;]+)|\G([\n]+)|\G(\\\\000)|\G(\\[\\^\\])"],
        20 => [4, "\G(,)|\G([\s\t\r;]+)|\G([\n]+)|\G(\\\\000)|\G(\\[\\^\\])"],
        21 => [4, "\G([\s\t\r;]+)|\G([\n]+)|\G(\\\\000)|\G(\\[\\^\\])"],
        22 => [4, "\G([\n]+)|\G(\\\\000)|\G(\\[\\^\\])"],
        23 => [4, "\G(\\\\000)|\G(\\[\\^\\])"],
        24 => [4, "\G(\\[\\^\\])"],
        25 => [4, ""],
    ];

                    // yymore is needed
                    do {
                        if (!strlen($yy_yymore_patterns[$this->token][1])) {
                            throw new Exception('cannot do yymore for the last token');
                        }
                        $yysubmatches = array();
                        if (preg_match('/' . $yy_yymore_patterns[$this->token][1] . '/iu',
                              $this->_data, $yymatches, 0, $this->_counter)) {
                            $yysubmatches = $yymatches;
                            $yymatches = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                            next($yymatches); // skip global match
                            $this->token += key($yymatches) + $yy_yymore_patterns[$this->token][0]; // token number
                            $this->value = current($yymatches); // token value
                            $this->_line = substr_count($this->value, "\n");
                            if ($tokenMap[$this->token]) {
                                // extract sub-patterns for passing to lex function
                                $yysubmatches = array_slice($yysubmatches, $this->token + 1,
                                    $tokenMap[$this->token]);
                            } else {
                                $yysubmatches = [];
                            }
                        }
                        $r = $this->{'lex_r1_' . $this->token}($yysubmatches);
                    } while ($r !== null && !is_bool($r));
                    if ($r === true) {
                        // we have changed state
                        // process this token in the new state
                        return $this->run();
                    } elseif ($r === false) {
                        $this->_counter += strlen($this->value);
                        $this->_line += substr_count($this->value, "\n");
                        if ($this->_counter >= strlen($this->_data)) {
                            return false; // end of input
                        }
                        // skip this token
                        continue;
                    } else {
                        // accept
                        $this->_counter += strlen($this->value);
                        $this->_line += substr_count($this->value, "\n");
                        return true;
                    }
                }
            } else {
                throw new Exception('Unexpected input at line ' . $this->_line .
                    ': ' . $this->_data[$this->_counter]);
            }
            break;
        } while (true);

    } // end function



    /**
     * @const int
     */
    public const START = 1;
    /**
     * @param mixed $subPatterns
     *
     * @return void
     */
    public function lex_r1_1(mixed $subPatterns): void
    {

    $this->saveToken(Enum::PHANNOT_T_DOUBLE);

    }
    /**
     * @param mixed $subPatterns
     *
     * @return void
     */
    public function lex_r1_2(mixed $subPatterns): void
    {

    $this->saveToken(Enum::PHANNOT_T_INTEGER);

    }
    /**
     * @param mixed $subPatterns
     *
     * @return void
     */
    public function lex_r1_3(mixed $subPatterns): void
    {

	$this->saveToken(Enum::PHANNOT_T_NULL);

    }
    /**
     * @param mixed $subPatterns
     *
     * @return void
     */
    public function lex_r1_4(mixed $subPatterns): void
    {

    $this->saveToken(Enum::PHANNOT_T_FALSE);

    }
    /**
     * @param mixed $subPatterns
     *
     * @return void
     */
    public function lex_r1_5(mixed $subPatterns): void
    {

    $this->saveToken(Enum::PHANNOT_T_TRUE);

    }
    /**
     * @param mixed $subPatterns
     *
     * @return void
     */
    public function lex_r1_6(mixed $subPatterns): void
    {

    $this->saveToken(Enum::PHANNOT_T_STRING);

    }
    /**
     * @param mixed $subPatterns
     *
     * @return void
     */
    public function lex_r1_7(mixed $subPatterns): void
    {

    $this->saveToken(Enum::PHANNOT_T_IDENTIFIER);

    }
    /**
     * @param mixed $subPatterns
     *
     * @return void
     */
    public function lex_r1_12(mixed $subPatterns): void
    {

    $this->saveToken(Enum::PHANNOT_T_PARENTHESES_OPEN);

    }
    /**
     * @param mixed $subPatterns
     *
     * @return void
     */
    public function lex_r1_13(mixed $subPatterns): void
    {

    $this->saveToken(Enum::PHANNOT_T_PARENTHESES_CLOSE);

    }
    /**
     * @param mixed $subPatterns
     *
     * @return void
     */
    public function lex_r1_14(mixed $subPatterns): void
    {

    $this->saveToken(Enum::PHANNOT_T_BRACKET_OPEN);

    }
    /**
     * @param mixed $subPatterns
     *
     * @return void
     */
    public function lex_r1_15(mixed $subPatterns): void
    {

    $this->saveToken(Enum::PHANNOT_T_BRACKET_CLOSE);

    }
    /**
     * @param mixed $subPatterns
     *
     * @return void
     */
    public function lex_r1_16(mixed $subPatterns): void
    {

    $this->saveToken(Enum::PHANNOT_T_SBRACKET_OPEN);

    }
    /**
     * @param mixed $subPatterns
     *
     * @return void
     */
    public function lex_r1_17(mixed $subPatterns): void
    {

    $this->saveToken(Enum::PHANNOT_T_SBRACKET_CLOSE);

    }
    /**
     * @param mixed $subPatterns
     *
     * @return void
     */
    public function lex_r1_18(mixed $subPatterns): void
    {

    $this->saveToken(Enum::PHANNOT_T_AT);

    }
    /**
     * @param mixed $subPatterns
     *
     * @return void
     */
    public function lex_r1_19(mixed $subPatterns): void
    {

    $this->saveToken(Enum::PHANNOT_T_EQUALS);

    }
    /**
     * @param mixed $subPatterns
     *
     * @return void
     */
    public function lex_r1_20(mixed $subPatterns): void
    {

    $this->saveToken(Enum::PHANNOT_T_COLON);

    }
    /**
     * @param mixed $subPatterns
     *
     * @return void
     */
    public function lex_r1_21(mixed $subPatterns): void
    {

    $this->saveToken(Enum::PHANNOT_T_COMMA);

    }
    /**
     * @param mixed $subPatterns
     *
     * @return void
     */
    public function lex_r1_22(mixed $subPatterns): void
    {

	$this->saveToken(Enum::PHANNOT_T_IGNORE);

    }
    /**
     * @param mixed $subPatterns
     *
     * @return void
     */
    public function lex_r1_23(mixed $subPatterns): void
    {

    $this->_state->setActiveLine($this->_state->getActiveLine() + 1);
    $this->saveToken(Enum::PHANNOT_T_IGNORE);

    }
    /**
     * @param mixed $subPatterns
     *
     * @return void
     */
    public function lex_r1_24(mixed $subPatterns): void
    {

    $this->_state->setStatus(ScannerState::PHANNOT_SCANNER_RETCODE_EOF);

    }
    /**
     * @param mixed $subPatterns
     *
     * @return void
     */
    public function lex_r1_25(mixed $subPatterns): void
    {

    $this->_state->setStatus(ScannerState::PHANNOT_SCANNER_RETCODE_ERR);

    }
}
