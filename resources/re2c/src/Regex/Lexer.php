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

namespace Azirax\Re2c\Regex;

use Azirax\Re2c\Exception;

/**
 * PHP port of LEMON
 *
 * @package    Azirax\Re2c\Regex
 * @author     Rene Dziuba <php.tux@web.de>
 * @copyright  Copyright (c) 2023 The Authors
 * @license    <http://opensource.org/licenses/bsd-license.php> New BSD License
 */
class Lexer
{
    public const MATCHSTART         = Parser::MATCHSTART;
    public const MATCHEND           = Parser::MATCHEND;
    public const CONTROLCHAR        = Parser::CONTROLCHAR;
    public const OPENCHARCLASS      = Parser::OPENCHARCLASS;
    public const FULLSTOP           = Parser::FULLSTOP;
    public const TEXT               = Parser::TEXT;
    public const BACKREFERENCE      = Parser::BACKREFERENCE;
    public const OPENASSERTION      = Parser::OPENASSERTION;
    public const COULDBEBACKREF     = Parser::COULDBEBACKREF;
    public const NEGATE             = Parser::NEGATE;
    public const HYPHEN             = Parser::HYPHEN;
    public const CLOSECHARCLASS     = Parser::CLOSECHARCLASS;
    public const BAR                = Parser::BAR;
    public const MULTIPLIER         = Parser::MULTIPLIER;
    public const INTERNALOPTIONS    = Parser::INTERNALOPTIONS;
    public const COLON              = Parser::COLON;
    public const OPENPAREN          = Parser::OPENPAREN;
    public const CLOSEPAREN         = Parser::CLOSEPAREN;
    public const PATTERNNAME        = Parser::PATTERNNAME;
    public const POSITIVELOOKBEHIND = Parser::POSITIVELOOKBEHIND;
    public const NEGATIVELOOKBEHIND = Parser::NEGATIVELOOKBEHIND;
    public const POSITIVELOOKAHEAD  = Parser::POSITIVELOOKAHEAD;
    public const NEGATIVELOOKAHEAD  = Parser::NEGATIVELOOKAHEAD;
    public const ONCEONLY           = Parser::ONCEONLY;
    public const COMMENT            = Parser::COMMENT;
    public const RECUR              = Parser::RECUR;
    public const ESCAPEDBACKSLASH   = Parser::ESCAPEDBACKSLASH;

    public const INITIAL             = 1;
    public const CHARACTERCLASSSTART = 2;
    public const CHARACTERCLASS      = 3;
    public const RANGE               = 4;
    const        ASSERTION           = 5;

    /**
     * Input string
     *
     * @var string
     */
    private string $input;

    /**
     * @var int
     */
    private int $N;

    /**
     * Token number
     *
     * @var int
     */
    public int $token = 0;

    /**
     * Current value
     *
     * @var string|null
     */
    public ?string $value = null;

    /**
     * Current line number
     *
     * @var int
     */
    public int $line = 0;

    /**
     * Lexer state
     *
     * @var int
     */
    private int $_state = 1;

    /**
     * Lexer stack
     *
     * @var array
     */
    private array $_stack = [];

    /**
     * Constructor for Lexer
     *
     * @param string $data
     */
    public function __construct(string $data)
    {
        $this->input = $data;
        $this->N     = 0;
    }

    /**
     * Reset the lexer.
     *
     * @param string $data
     * @param int    $line
     *
     * @return void
     */
    public function reset(string $data, int $line): void
    {
        $this->input = $data;
        $this->N     = 0;

        // passed in from parent parser
        $this->line = $line;
        $this->begin(self::INITIAL);
    }

    /**
     * Run the lexer.
     *
     * @return mixed
     */
    public function run(): mixed
    {
        return $this->{'lex' . $this->_state}();
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
        array_push($this->_stack, $this->_state);
        $this->_state = $state;
    }

    /**
     * Pop the state.
     *
     * @return void
     */
    public function popState(): void
    {
        $this->_state = array_pop($this->_stack);
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
        $this->_state = $state;
    }

    /**
     * @throws Exception
     */
    protected function lex1(): mixed
    {
        $tokenMap = [
            1  => 0,
            2  => 0,
            3  => 0,
            4  => 0,
            5  => 0,
            6  => 0,
            7  => 0,
            8  => 0,
            9  => 0,
            10 => 0,
            11 => 0,
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
        ];
        if ($this->N >= strlen($this->input)) {
            return false; // end of input
        }
        $yy_global_pattern = '/\G(\\\\\\\\)|\G([^[\\\\^$.|()?*+{}]+)|\G(\\\\[][{}*.^$|?()+])|\G(\\[)|\G(\\|)|\G(\\\\[frnt]|\\\\x[0-9a-fA-F][0-9a-fA-F]?|\\\\[0-7][0-7][0-7]|\\\\x\\{[0-9a-fA-F]+\\})|\G(\\\\[0-9][0-9])|\G(\\\\[abBGcedDsSwW0C]|\\\\c\\\\)|\G(\\^)|\G(\\\\A)|\G(\\))|\G(\\$)|\G(\\*\\?|\\+\\?|[*?+]|\\{[0-9]+\\}|\\{[0-9]+,\\}|\\{[0-9]+,[0-9]+\\})|\G(\\\\[zZ])|\G(\\(\\?)|\G(\\()|\G(\\.)|\G(\\\\[1-9])|\G(\\\\p\\{\\^?..?\\}|\\\\P\\{..?\\}|\\\\X)|\G(\\\\p\\{C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p\\{\\^C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p[CLMNPSZ])|\G(\\\\)/';

        do {
            if (preg_match($yy_global_pattern, $this->input, $yymatches, 0, $this->N)) {
                $yysubmatches = $yymatches;
                $yymatches    = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                if (!count($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' .
                        ' an empty string.  Input "' . substr($this->input,
                            $this->N, 5) . '... state INITIAL');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                if ($tokenMap[$this->token]) {
                    // extract sub-patterns for passing to lex function
                    $yysubmatches = array_slice($yysubmatches, $this->token + 1,
                        $tokenMap[$this->token]);
                } else {
                    $yysubmatches = [];
                }
                $this->value = current($yymatches); // token value
                $r           = $this->{'lex_r1_' . $this->token}($yysubmatches);
                if ($r === null) {
                    $this->N    += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");

                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->run();
                } elseif ($r === false) {
                    $this->N    += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    if ($this->N >= strlen($this->input)) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                } else {
                    $yy_yymore_patterns = [
                        1  => [0, "\G([^[\\\\^$.|()?*+{}]+)|\G(\\\\[][{}*.^$|?()+])|\G(\\[)|\G(\\|)|\G(\\\\[frnt]|\\\\x[0-9a-fA-F][0-9a-fA-F]?|\\\\[0-7][0-7][0-7]|\\\\x\\{[0-9a-fA-F]+\\})|\G(\\\\[0-9][0-9])|\G(\\\\[abBGcedDsSwW0C]|\\\\c\\\\)|\G(\\^)|\G(\\\\A)|\G(\\))|\G(\\$)|\G(\\*\\?|\\+\\?|[*?+]|\\{[0-9]+\\}|\\{[0-9]+,\\}|\\{[0-9]+,[0-9]+\\})|\G(\\\\[zZ])|\G(\\(\\?)|\G(\\()|\G(\\.)|\G(\\\\[1-9])|\G(\\\\p\\{\\^?..?\\}|\\\\P\\{..?\\}|\\\\X)|\G(\\\\p\\{C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p\\{\\^C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p[CLMNPSZ])|\G(\\\\)"],
                        2  => [0, "\G(\\\\[][{}*.^$|?()+])|\G(\\[)|\G(\\|)|\G(\\\\[frnt]|\\\\x[0-9a-fA-F][0-9a-fA-F]?|\\\\[0-7][0-7][0-7]|\\\\x\\{[0-9a-fA-F]+\\})|\G(\\\\[0-9][0-9])|\G(\\\\[abBGcedDsSwW0C]|\\\\c\\\\)|\G(\\^)|\G(\\\\A)|\G(\\))|\G(\\$)|\G(\\*\\?|\\+\\?|[*?+]|\\{[0-9]+\\}|\\{[0-9]+,\\}|\\{[0-9]+,[0-9]+\\})|\G(\\\\[zZ])|\G(\\(\\?)|\G(\\()|\G(\\.)|\G(\\\\[1-9])|\G(\\\\p\\{\\^?..?\\}|\\\\P\\{..?\\}|\\\\X)|\G(\\\\p\\{C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p\\{\\^C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p[CLMNPSZ])|\G(\\\\)"],
                        3  => [0, "\G(\\[)|\G(\\|)|\G(\\\\[frnt]|\\\\x[0-9a-fA-F][0-9a-fA-F]?|\\\\[0-7][0-7][0-7]|\\\\x\\{[0-9a-fA-F]+\\})|\G(\\\\[0-9][0-9])|\G(\\\\[abBGcedDsSwW0C]|\\\\c\\\\)|\G(\\^)|\G(\\\\A)|\G(\\))|\G(\\$)|\G(\\*\\?|\\+\\?|[*?+]|\\{[0-9]+\\}|\\{[0-9]+,\\}|\\{[0-9]+,[0-9]+\\})|\G(\\\\[zZ])|\G(\\(\\?)|\G(\\()|\G(\\.)|\G(\\\\[1-9])|\G(\\\\p\\{\\^?..?\\}|\\\\P\\{..?\\}|\\\\X)|\G(\\\\p\\{C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p\\{\\^C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p[CLMNPSZ])|\G(\\\\)"],
                        4  => [0, "\G(\\|)|\G(\\\\[frnt]|\\\\x[0-9a-fA-F][0-9a-fA-F]?|\\\\[0-7][0-7][0-7]|\\\\x\\{[0-9a-fA-F]+\\})|\G(\\\\[0-9][0-9])|\G(\\\\[abBGcedDsSwW0C]|\\\\c\\\\)|\G(\\^)|\G(\\\\A)|\G(\\))|\G(\\$)|\G(\\*\\?|\\+\\?|[*?+]|\\{[0-9]+\\}|\\{[0-9]+,\\}|\\{[0-9]+,[0-9]+\\})|\G(\\\\[zZ])|\G(\\(\\?)|\G(\\()|\G(\\.)|\G(\\\\[1-9])|\G(\\\\p\\{\\^?..?\\}|\\\\P\\{..?\\}|\\\\X)|\G(\\\\p\\{C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p\\{\\^C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p[CLMNPSZ])|\G(\\\\)"],
                        5  => [0, "\G(\\\\[frnt]|\\\\x[0-9a-fA-F][0-9a-fA-F]?|\\\\[0-7][0-7][0-7]|\\\\x\\{[0-9a-fA-F]+\\})|\G(\\\\[0-9][0-9])|\G(\\\\[abBGcedDsSwW0C]|\\\\c\\\\)|\G(\\^)|\G(\\\\A)|\G(\\))|\G(\\$)|\G(\\*\\?|\\+\\?|[*?+]|\\{[0-9]+\\}|\\{[0-9]+,\\}|\\{[0-9]+,[0-9]+\\})|\G(\\\\[zZ])|\G(\\(\\?)|\G(\\()|\G(\\.)|\G(\\\\[1-9])|\G(\\\\p\\{\\^?..?\\}|\\\\P\\{..?\\}|\\\\X)|\G(\\\\p\\{C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p\\{\\^C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p[CLMNPSZ])|\G(\\\\)"],
                        6  => [0, "\G(\\\\[0-9][0-9])|\G(\\\\[abBGcedDsSwW0C]|\\\\c\\\\)|\G(\\^)|\G(\\\\A)|\G(\\))|\G(\\$)|\G(\\*\\?|\\+\\?|[*?+]|\\{[0-9]+\\}|\\{[0-9]+,\\}|\\{[0-9]+,[0-9]+\\})|\G(\\\\[zZ])|\G(\\(\\?)|\G(\\()|\G(\\.)|\G(\\\\[1-9])|\G(\\\\p\\{\\^?..?\\}|\\\\P\\{..?\\}|\\\\X)|\G(\\\\p\\{C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p\\{\\^C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p[CLMNPSZ])|\G(\\\\)"],
                        7  => [0, "\G(\\\\[abBGcedDsSwW0C]|\\\\c\\\\)|\G(\\^)|\G(\\\\A)|\G(\\))|\G(\\$)|\G(\\*\\?|\\+\\?|[*?+]|\\{[0-9]+\\}|\\{[0-9]+,\\}|\\{[0-9]+,[0-9]+\\})|\G(\\\\[zZ])|\G(\\(\\?)|\G(\\()|\G(\\.)|\G(\\\\[1-9])|\G(\\\\p\\{\\^?..?\\}|\\\\P\\{..?\\}|\\\\X)|\G(\\\\p\\{C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p\\{\\^C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p[CLMNPSZ])|\G(\\\\)"],
                        8  => [0, "\G(\\^)|\G(\\\\A)|\G(\\))|\G(\\$)|\G(\\*\\?|\\+\\?|[*?+]|\\{[0-9]+\\}|\\{[0-9]+,\\}|\\{[0-9]+,[0-9]+\\})|\G(\\\\[zZ])|\G(\\(\\?)|\G(\\()|\G(\\.)|\G(\\\\[1-9])|\G(\\\\p\\{\\^?..?\\}|\\\\P\\{..?\\}|\\\\X)|\G(\\\\p\\{C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p\\{\\^C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p[CLMNPSZ])|\G(\\\\)"],
                        9  => [0, "\G(\\\\A)|\G(\\))|\G(\\$)|\G(\\*\\?|\\+\\?|[*?+]|\\{[0-9]+\\}|\\{[0-9]+,\\}|\\{[0-9]+,[0-9]+\\})|\G(\\\\[zZ])|\G(\\(\\?)|\G(\\()|\G(\\.)|\G(\\\\[1-9])|\G(\\\\p\\{\\^?..?\\}|\\\\P\\{..?\\}|\\\\X)|\G(\\\\p\\{C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p\\{\\^C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p[CLMNPSZ])|\G(\\\\)"],
                        10 => [0, "\G(\\))|\G(\\$)|\G(\\*\\?|\\+\\?|[*?+]|\\{[0-9]+\\}|\\{[0-9]+,\\}|\\{[0-9]+,[0-9]+\\})|\G(\\\\[zZ])|\G(\\(\\?)|\G(\\()|\G(\\.)|\G(\\\\[1-9])|\G(\\\\p\\{\\^?..?\\}|\\\\P\\{..?\\}|\\\\X)|\G(\\\\p\\{C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p\\{\\^C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p[CLMNPSZ])|\G(\\\\)"],
                        11 => [0, "\G(\\$)|\G(\\*\\?|\\+\\?|[*?+]|\\{[0-9]+\\}|\\{[0-9]+,\\}|\\{[0-9]+,[0-9]+\\})|\G(\\\\[zZ])|\G(\\(\\?)|\G(\\()|\G(\\.)|\G(\\\\[1-9])|\G(\\\\p\\{\\^?..?\\}|\\\\P\\{..?\\}|\\\\X)|\G(\\\\p\\{C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p\\{\\^C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p[CLMNPSZ])|\G(\\\\)"],
                        12 => [0, "\G(\\*\\?|\\+\\?|[*?+]|\\{[0-9]+\\}|\\{[0-9]+,\\}|\\{[0-9]+,[0-9]+\\})|\G(\\\\[zZ])|\G(\\(\\?)|\G(\\()|\G(\\.)|\G(\\\\[1-9])|\G(\\\\p\\{\\^?..?\\}|\\\\P\\{..?\\}|\\\\X)|\G(\\\\p\\{C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p\\{\\^C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p[CLMNPSZ])|\G(\\\\)"],
                        13 => [0, "\G(\\\\[zZ])|\G(\\(\\?)|\G(\\()|\G(\\.)|\G(\\\\[1-9])|\G(\\\\p\\{\\^?..?\\}|\\\\P\\{..?\\}|\\\\X)|\G(\\\\p\\{C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p\\{\\^C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p[CLMNPSZ])|\G(\\\\)"],
                        14 => [0, "\G(\\(\\?)|\G(\\()|\G(\\.)|\G(\\\\[1-9])|\G(\\\\p\\{\\^?..?\\}|\\\\P\\{..?\\}|\\\\X)|\G(\\\\p\\{C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p\\{\\^C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p[CLMNPSZ])|\G(\\\\)"],
                        15 => [0, "\G(\\()|\G(\\.)|\G(\\\\[1-9])|\G(\\\\p\\{\\^?..?\\}|\\\\P\\{..?\\}|\\\\X)|\G(\\\\p\\{C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p\\{\\^C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p[CLMNPSZ])|\G(\\\\)"],
                        16 => [0, "\G(\\.)|\G(\\\\[1-9])|\G(\\\\p\\{\\^?..?\\}|\\\\P\\{..?\\}|\\\\X)|\G(\\\\p\\{C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p\\{\\^C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p[CLMNPSZ])|\G(\\\\)"],
                        17 => [0, "\G(\\\\[1-9])|\G(\\\\p\\{\\^?..?\\}|\\\\P\\{..?\\}|\\\\X)|\G(\\\\p\\{C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p\\{\\^C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p[CLMNPSZ])|\G(\\\\)"],
                        18 => [0, "\G(\\\\p\\{\\^?..?\\}|\\\\P\\{..?\\}|\\\\X)|\G(\\\\p\\{C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p\\{\\^C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p[CLMNPSZ])|\G(\\\\)"],
                        19 => [0, "\G(\\\\p\\{C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p\\{\\^C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p[CLMNPSZ])|\G(\\\\)"],
                        20 => [0, "\G(\\\\p\\{\\^C[cfnos]?|L[lmotu]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?\\})|\G(\\\\p[CLMNPSZ])|\G(\\\\)"],
                        21 => [0, "\G(\\\\p[CLMNPSZ])|\G(\\\\)"],
                        22 => [0, "\G(\\\\)"],
                        23 => [0, ""],
                    ];

                    // yymore is needed
                    do {
                        if (!strlen($yy_yymore_patterns[$this->token][1])) {
                            throw new Exception('cannot do yymore for the last token');
                        }
                        $yysubmatches = [];
                        if (preg_match('/' . $yy_yymore_patterns[$this->token][1] . '/',
                            $this->input, $yymatches, 0, $this->N)) {
                            $yysubmatches = $yymatches;
                            $yymatches    = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                            next($yymatches); // skip global match
                            $this->token += key($yymatches) + $yy_yymore_patterns[$this->token][0]; // token number
                            $this->value = current($yymatches); // token value
                            $this->line  = substr_count($this->value, "\n");
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
                        $this->N    += strlen($this->value);
                        $this->line += substr_count($this->value, "\n");
                        if ($this->N >= strlen($this->input)) {
                            return false; // end of input
                        }
                        // skip this token
                        continue;
                    } else {
                        // accept
                        $this->N    += strlen($this->value);
                        $this->line += substr_count($this->value, "\n");

                        return true;
                    }
                }
            } else {
                throw new Exception('Unexpected input at line' . $this->line .
                    ': ' . $this->input[$this->N]);
            }
            break;
        } while (true);

    } // end function


    protected function lex_r1_1($yy_subpatterns): void
    {
        $this->token = self::ESCAPEDBACKSLASH;
    }

    function lex_r1_2($yy_subpatterns): void
    {
        $this->token = self::TEXT;
    }

    protected function lex_r1_3($yy_subpatterns): void
    {
        $this->token = self::CONTROLCHAR;
    }

    protected function lex_r1_4($yy_subpatterns): void
    {
        $this->token = self::OPENCHARCLASS;
        $this->begin(self::CHARACTERCLASSSTART);
    }

    protected function lex_r1_5($yy_subpatterns): void
    {
        $this->token = self::BAR;
    }

    protected function lex_r1_6($yy_subpatterns): void
    {
        $this->token = self::TEXT;
    }

    protected function lex_r1_7($yy_subpatterns): void
    {
        $this->token = self::COULDBEBACKREF;
    }

    protected function lex_r1_8($yy_subpatterns): void
    {
        $this->token = self::CONTROLCHAR;
    }

    protected function lex_r1_9($yy_subpatterns): void
    {
        $this->token = self::MATCHSTART;
    }

    protected function lex_r1_10($yy_subpatterns): void
    {
        $this->token = self::MATCHSTART;
    }

    protected function lex_r1_11($yy_subpatterns): void
    {
        $this->token = self::CLOSEPAREN;
        $this->begin(self::INITIAL);
    }

    protected function lex_r1_12($yy_subpatterns): void
    {
        $this->token = self::MATCHEND;
    }

    protected function lex_r1_13($yy_subpatterns): void
    {
        $this->token = self::MULTIPLIER;
    }

    protected function lex_r1_14($yy_subpatterns): void
    {
        $this->token = self::MATCHEND;
    }

    protected function lex_r1_15($yy_subpatterns): void
    {
        $this->token = self::OPENASSERTION;
        $this->begin(self::ASSERTION);
    }

    protected function lex_r1_16($yy_subpatterns): void
    {
        $this->token = self::OPENPAREN;
    }

    protected function lex_r1_17($yy_subpatterns): void
    {
        $this->token = self::FULLSTOP;
    }

    protected function lex_r1_18($yy_subpatterns): void
    {
        $this->token = self::BACKREFERENCE;
    }

    protected function lex_r1_19($yy_subpatterns): void
    {
        $this->token = self::CONTROLCHAR;
    }

    protected function lex_r1_20($yy_subpatterns): void
    {
        $this->token = self::CONTROLCHAR;
    }

    protected function lex_r1_21($yy_subpatterns): void
    {
        $this->token = self::CONTROLCHAR;
    }

    protected function lex_r1_22($yy_subpatterns): void
    {
        $this->token = self::CONTROLCHAR;
    }

    protected function lex_r1_23($yy_subpatterns): bool
    {
        return false;
    }


    protected function lex2(): mixed
    {
        $tokenMap = [
            1 => 0,
            2 => 0,
            3 => 0,
        ];
        if ($this->N >= strlen($this->input)) {
            return false; // end of input
        }
        $yy_global_pattern = '/\G(\\^)|\G(\\])|\G(.)/';

        do {
            if (preg_match($yy_global_pattern, $this->input, $yymatches, 0, $this->N)) {
                $yysubmatches = $yymatches;
                $yymatches    = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                if (!count($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' .
                        ' an empty string.  Input "' . substr($this->input,
                            $this->N, 5) . '... state CHARACTERCLASSSTART');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                if ($tokenMap[$this->token]) {
                    // extract sub-patterns for passing to lex function
                    $yysubmatches = array_slice($yysubmatches, $this->token + 1,
                        $tokenMap[$this->token]);
                } else {
                    $yysubmatches = [];
                }
                $this->value = current($yymatches); // token value
                $r           = $this->{'lex_r2_' . $this->token}($yysubmatches);
                if ($r === null) {
                    $this->N    += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");

                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->run();
                } elseif ($r === false) {
                    $this->N    += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    if ($this->N >= strlen($this->input)) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                } else {
                    $yy_yymore_patterns = [
                        1 => [0, "\G(\\])|\G(.)"],
                        2 => [0, "\G(.)"],
                        3 => [0, ""],
                    ];

                    // yymore is needed
                    do {
                        if (!strlen($yy_yymore_patterns[$this->token][1])) {
                            throw new Exception('cannot do yymore for the last token');
                        }
                        $yysubmatches = [];
                        if (preg_match('/' . $yy_yymore_patterns[$this->token][1] . '/',
                            $this->input, $yymatches, 0, $this->N)) {
                            $yysubmatches = $yymatches;
                            $yymatches    = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                            next($yymatches); // skip global match
                            $this->token += key($yymatches) + $yy_yymore_patterns[$this->token][0]; // token number
                            $this->value = current($yymatches); // token value
                            $this->line  = substr_count($this->value, "\n");
                            if ($tokenMap[$this->token]) {
                                // extract sub-patterns for passing to lex function
                                $yysubmatches = array_slice($yysubmatches, $this->token + 1,
                                    $tokenMap[$this->token]);
                            } else {
                                $yysubmatches = [];
                            }
                        }
                        $r = $this->{'lex_r2_' . $this->token}($yysubmatches);
                    } while ($r !== null && !is_bool($r));
                    if ($r === true) {
                        // we have changed state
                        // process this token in the new state
                        return $this->run();
                    } elseif ($r === false) {
                        $this->N    += strlen($this->value);
                        $this->line += substr_count($this->value, "\n");
                        if ($this->N >= strlen($this->input)) {
                            return false; // end of input
                        }
                        // skip this token
                        continue;
                    } else {
                        // accept
                        $this->N    += strlen($this->value);
                        $this->line += substr_count($this->value, "\n");

                        return true;
                    }
                }
            } else {
                throw new Exception('Unexpected input at line' . $this->line .
                    ': ' . $this->input[$this->N]);
            }
            break;
        } while (true);

    } // end function


    protected function lex_r2_1($yy_subpatterns): void
    {
        $this->token = self::NEGATE;
    }

    protected function lex_r2_2($yy_subpatterns): void
    {
        $this->begin(self::CHARACTERCLASS);
        $this->token = self::TEXT;
    }

    protected function lex_r2_3($yy_subpatterns): bool
    {
        $this->begin(self::CHARACTERCLASS);

        return true;
    }


    /**
     * @throws Exception
     */
    function lex3(): mixed
    {
        $tokenMap = [
            1  => 0,
            2  => 0,
            3  => 0,
            4  => 0,
            5  => 0,
            6  => 0,
            7  => 0,
            8  => 0,
            9  => 0,
            10 => 0,
            11 => 0,
        ];
        if ($this->N >= strlen($this->input)) {
            return false; // end of input
        }
        $yy_global_pattern = '/\G(\\\\\\\\)|\G(\\])|\G(\\\\[frnt]|\\\\x[0-9a-fA-F][0-9a-fA-F]?|\\\\[0-7][0-7][0-7]|\\\\x\\{[0-9a-fA-F]+\\})|\G(\\\\[bacedDsSwW0C]|\\\\c\\\\|\\\\x\\{[0-9a-fA-F]+\\}|\\\\[0-7][0-7][0-7]|\\\\x[0-9a-fA-F][0-9a-fA-F]?)|\G(\\\\[0-9][0-9])|\G(\\\\[1-9])|\G(\\\\[]\.\-\^])|\G(-(?!]))|\G([^\-\\\\])|\G(\\\\)|\G(.)/';

        do {
            if (preg_match($yy_global_pattern, $this->input, $yymatches, 0, $this->N)) {
                $yysubmatches = $yymatches;
                $yymatches    = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                if (!count($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' .
                        ' an empty string.  Input "' . substr($this->input,
                            $this->N, 5) . '... state CHARACTERCLASS');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                if ($tokenMap[$this->token]) {
                    // extract sub-patterns for passing to lex function
                    $yysubmatches = array_slice($yysubmatches, $this->token + 1,
                        $tokenMap[$this->token]);
                } else {
                    $yysubmatches = [];
                }
                $this->value = current($yymatches); // token value
                $r           = $this->{'lex_r3_' . $this->token}($yysubmatches);
                if ($r === null) {
                    $this->N    += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");

                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->run();
                } elseif ($r === false) {
                    $this->N    += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    if ($this->N >= strlen($this->input)) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                } else {
                    $yy_yymore_patterns = [
                        1  => [0, "\G(\\])|\G(\\\\[frnt]|\\\\x[0-9a-fA-F][0-9a-fA-F]?|\\\\[0-7][0-7][0-7]|\\\\x\\{[0-9a-fA-F]+\\})|\G(\\\\[bacedDsSwW0C]|\\\\c\\\\|\\\\x\\{[0-9a-fA-F]+\\}|\\\\[0-7][0-7][0-7]|\\\\x[0-9a-fA-F][0-9a-fA-F]?)|\G(\\\\[0-9][0-9])|\G(\\\\[1-9])|\G(\\\\[]\.\-\^])|\G(-(?!]))|\G([^\-\\\\])|\G(\\\\)|\G(.)"],
                        2  => [0, "\G(\\\\[frnt]|\\\\x[0-9a-fA-F][0-9a-fA-F]?|\\\\[0-7][0-7][0-7]|\\\\x\\{[0-9a-fA-F]+\\})|\G(\\\\[bacedDsSwW0C]|\\\\c\\\\|\\\\x\\{[0-9a-fA-F]+\\}|\\\\[0-7][0-7][0-7]|\\\\x[0-9a-fA-F][0-9a-fA-F]?)|\G(\\\\[0-9][0-9])|\G(\\\\[1-9])|\G(\\\\[]\.\-\^])|\G(-(?!]))|\G([^\-\\\\])|\G(\\\\)|\G(.)"],
                        3  => [0, "\G(\\\\[bacedDsSwW0C]|\\\\c\\\\|\\\\x\\{[0-9a-fA-F]+\\}|\\\\[0-7][0-7][0-7]|\\\\x[0-9a-fA-F][0-9a-fA-F]?)|\G(\\\\[0-9][0-9])|\G(\\\\[1-9])|\G(\\\\[]\.\-\^])|\G(-(?!]))|\G([^\-\\\\])|\G(\\\\)|\G(.)"],
                        4  => [0, "\G(\\\\[0-9][0-9])|\G(\\\\[1-9])|\G(\\\\[]\.\-\^])|\G(-(?!]))|\G([^\-\\\\])|\G(\\\\)|\G(.)"],
                        5  => [0, "\G(\\\\[1-9])|\G(\\\\[]\.\-\^])|\G(-(?!]))|\G([^\-\\\\])|\G(\\\\)|\G(.)"],
                        6  => [0, "\G(\\\\[]\.\-\^])|\G(-(?!]))|\G([^\-\\\\])|\G(\\\\)|\G(.)"],
                        7  => [0, "\G(-(?!]))|\G([^\-\\\\])|\G(\\\\)|\G(.)"],
                        8  => [0, "\G([^\-\\\\])|\G(\\\\)|\G(.)"],
                        9  => [0, "\G(\\\\)|\G(.)"],
                        10 => [0, "\G(.)"],
                        11 => [0, ""],
                    ];

                    // yymore is needed
                    do {
                        if (!strlen($yy_yymore_patterns[$this->token][1])) {
                            throw new Exception('cannot do yymore for the last token');
                        }
                        $yysubmatches = [];
                        if (preg_match('/' . $yy_yymore_patterns[$this->token][1] . '/',
                            $this->input, $yymatches, 0, $this->N)) {
                            $yysubmatches = $yymatches;
                            $yymatches    = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                            next($yymatches); // skip global match
                            $this->token += key($yymatches) + $yy_yymore_patterns[$this->token][0]; // token number
                            $this->value = current($yymatches); // token value
                            $this->line  = substr_count($this->value, "\n");
                            if ($tokenMap[$this->token]) {
                                // extract sub-patterns for passing to lex function
                                $yysubmatches = array_slice($yysubmatches, $this->token + 1,
                                    $tokenMap[$this->token]);
                            } else {
                                $yysubmatches = [];
                            }
                        }
                        $r = $this->{'lex_r3_' . $this->token}($yysubmatches);
                    } while ($r !== null && !is_bool($r));
                    if ($r === true) {
                        // we have changed state
                        // process this token in the new state
                        return $this->run();
                    } elseif ($r === false) {
                        $this->N    += strlen($this->value);
                        $this->line += substr_count($this->value, "\n");
                        if ($this->N >= strlen($this->input)) {
                            return false; // end of input
                        }
                        // skip this token
                        continue;
                    } else {
                        // accept
                        $this->N    += strlen($this->value);
                        $this->line += substr_count($this->value, "\n");

                        return true;
                    }
                }
            } else {
                throw new Exception('Unexpected input at line' . $this->line .
                    ': ' . $this->input[$this->N]);
            }
            break;
        } while (true);

    } // end function

    protected function lex_r3_1($yy_subpatterns): void
    {
        $this->token = self::ESCAPEDBACKSLASH;
    }

    protected function lex_r3_2($yy_subpatterns): void
    {
        $this->begin(self::INITIAL);
        $this->token = self::CLOSECHARCLASS;
    }

    protected function lex_r3_3($yy_subpatterns): void
    {
        $this->token = self::TEXT;
    }

    protected function lex_r3_4($yy_subpatterns): void
    {
        $this->token = self::TEXT;
    }

    protected function lex_r3_5($yy_subpatterns): void
    {
        $this->token = self::COULDBEBACKREF;
    }

    protected function lex_r3_6($yy_subpatterns): void
    {
        $this->token = self::BACKREFERENCE;
    }

    protected function lex_r3_7($yy_subpatterns): void
    {
        $this->token = self::TEXT;
    }

    protected function lex_r3_8($yy_subpatterns): void
    {
        $this->token = self::HYPHEN;
        $this->begin(self::RANGE);
    }

    protected function lex_r3_9($yy_subpatterns): void
    {
        $this->token = self::TEXT;
    }

    protected function lex_r3_10($yy_subpatterns): bool
    {
        return false; // ignore escaping of normal text
    }

    protected function lex_r3_11($yy_subpatterns): void
    {
        $this->token = self::TEXT;
    }

    /**
     * @throws Exception
     */
    function lex4(): mixed
    {
        $tokenMap = [
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            6 => 0,
            7 => 0,
        ];
        if ($this->N >= strlen($this->input)) {
            return false; // end of input
        }
        $yy_global_pattern = '/\G(\\\\\\\\)|\G(\\\\\\])|\G(\\\\[bacedDsSwW0C]|\\\\c\\\\|\\\\x\\{[0-9a-fA-F]+\\}|\\\\[0-7][0-7][0-7]|\\\\x[0-9a-fA-F][0-9a-fA-F]?)|\G(\\\\[0-9][0-9])|\G(\\\\[1-9])|\G([^\-\\\\])|\G(\\\\)/';

        do {
            if (preg_match($yy_global_pattern, $this->input, $yymatches, 0, $this->N)) {
                $yysubmatches = $yymatches;
                $yymatches    = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                if (!count($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' .
                        ' an empty string.  Input "' . substr($this->input,
                            $this->N, 5) . '... state RANGE');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                if ($tokenMap[$this->token]) {
                    // extract sub-patterns for passing to lex function
                    $yysubmatches = array_slice($yysubmatches, $this->token + 1,
                        $tokenMap[$this->token]);
                } else {
                    $yysubmatches = [];
                }
                $this->value = current($yymatches); // token value
                $r           = $this->{'lex_r4_' . $this->token}($yysubmatches);
                if ($r === null) {
                    $this->N    += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");

                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->run();
                } elseif ($r === false) {
                    $this->N    += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    if ($this->N >= strlen($this->input)) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                } else {
                    $yy_yymore_patterns = [
                        1 => [0, "\G(\\\\\\])|\G(\\\\[bacedDsSwW0C]|\\\\c\\\\|\\\\x\\{[0-9a-fA-F]+\\}|\\\\[0-7][0-7][0-7]|\\\\x[0-9a-fA-F][0-9a-fA-F]?)|\G(\\\\[0-9][0-9])|\G(\\\\[1-9])|\G([^\-\\\\])|\G(\\\\)"],
                        2 => [0, "\G(\\\\[bacedDsSwW0C]|\\\\c\\\\|\\\\x\\{[0-9a-fA-F]+\\}|\\\\[0-7][0-7][0-7]|\\\\x[0-9a-fA-F][0-9a-fA-F]?)|\G(\\\\[0-9][0-9])|\G(\\\\[1-9])|\G([^\-\\\\])|\G(\\\\)"],
                        3 => [0, "\G(\\\\[0-9][0-9])|\G(\\\\[1-9])|\G([^\-\\\\])|\G(\\\\)"],
                        4 => [0, "\G(\\\\[1-9])|\G([^\-\\\\])|\G(\\\\)"],
                        5 => [0, "\G([^\-\\\\])|\G(\\\\)"],
                        6 => [0, "\G(\\\\)"],
                        7 => [0, ""],
                    ];

                    // yymore is needed
                    do {
                        if (!strlen($yy_yymore_patterns[$this->token][1])) {
                            throw new Exception('cannot do yymore for the last token');
                        }
                        $yysubmatches = [];
                        if (preg_match('/' . $yy_yymore_patterns[$this->token][1] . '/',
                            $this->input, $yymatches, 0, $this->N)) {
                            $yysubmatches = $yymatches;
                            $yymatches    = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                            next($yymatches); // skip global match
                            $this->token += key($yymatches) + $yy_yymore_patterns[$this->token][0]; // token number
                            $this->value = current($yymatches); // token value
                            $this->line  = substr_count($this->value, "\n");
                            if ($tokenMap[$this->token]) {
                                // extract sub-patterns for passing to lex function
                                $yysubmatches = array_slice($yysubmatches, $this->token + 1,
                                    $tokenMap[$this->token]);
                            } else {
                                $yysubmatches = [];
                            }
                        }
                        $r = $this->{'lex_r4_' . $this->token}($yysubmatches);
                    } while ($r !== null && !is_bool($r));
                    if ($r === true) {
                        // we have changed state
                        // process this token in the new state
                        return $this->run();
                    } elseif ($r === false) {
                        $this->N    += strlen($this->value);
                        $this->line += substr_count($this->value, "\n");
                        if ($this->N >= strlen($this->input)) {
                            return false; // end of input
                        }
                        // skip this token
                        continue;
                    } else {
                        // accept
                        $this->N    += strlen($this->value);
                        $this->line += substr_count($this->value, "\n");

                        return true;
                    }
                }
            } else {
                throw new Exception('Unexpected input at line' . $this->line .
                    ': ' . $this->input[$this->N]);
            }
            break;
        } while (true);

    } // end function

    protected function lex_r4_1($yy_subpatterns): void
    {
        $this->token = self::ESCAPEDBACKSLASH;
    }

    protected function lex_r4_2($yy_subpatterns): void
    {
        $this->token = self::TEXT;
        $this->begin(self::CHARACTERCLASS);
    }

    protected function lex_r4_3($yy_subpatterns): void
    {
        $this->token = self::TEXT;
        $this->begin(self::CHARACTERCLASS);
    }

    protected function lex_r4_4($yy_subpatterns): void
    {
        $this->token = self::COULDBEBACKREF;
    }

    protected function lex_r4_5($yy_subpatterns): void
    {
        $this->token = self::BACKREFERENCE;
    }

    protected function lex_r4_6($yy_subpatterns): void
    {
        $this->token = self::TEXT;
        $this->begin(self::CHARACTERCLASS);
    }

    protected function lex_r4_7($yy_subpatterns): bool
    {
        return false; // ignore escaping of a normal text
    }


    protected function lex5(): mixed
    {
        $tokenMap = [
            1  => 0,
            2  => 0,
            3  => 0,
            4  => 0,
            5  => 0,
            6  => 0,
            7  => 0,
            8  => 0,
            9  => 0,
            10 => 0,
            11 => 0,
            12 => 0,
            13 => 0,
        ];
        if ($this->N >= strlen($this->input)) {
            return false; // end of input
        }
        $yy_global_pattern = '/\G([imsxUX]+-[imsxUX]+|[imsxUX]+|-[imsxUX]+)|\G(:)|\G(\\))|\G(P<[^>]+>)|\G(<=)|\G(<!)|\G(=)|\G(!)|\G(>)|\G(\\(\\?)|\G(#[^)]+)|\G(R)|\G(.)/';

        do {
            if (preg_match($yy_global_pattern, $this->input, $yymatches, 0, $this->N)) {
                $yysubmatches = $yymatches;
                $yymatches    = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                if (!count($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' .
                        ' an empty string.  Input "' . substr($this->input,
                            $this->N, 5) . '... state ASSERTION');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                if ($tokenMap[$this->token]) {
                    // extract sub-patterns for passing to lex function
                    $yysubmatches = array_slice($yysubmatches, $this->token + 1,
                        $tokenMap[$this->token]);
                } else {
                    $yysubmatches = [];
                }
                $this->value = current($yymatches); // token value
                $r           = $this->{'lex_r5_' . $this->token}($yysubmatches);
                if ($r === null) {
                    $this->N    += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");

                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->run();
                } elseif ($r === false) {
                    $this->N    += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    if ($this->N >= strlen($this->input)) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                } else {
                    $yy_yymore_patterns = [
                        1  => [0, "\G(:)|\G(\\))|\G(P<[^>]+>)|\G(<=)|\G(<!)|\G(=)|\G(!)|\G(>)|\G(\\(\\?)|\G(#[^)]+)|\G(R)|\G(.)"],
                        2  => [0, "\G(\\))|\G(P<[^>]+>)|\G(<=)|\G(<!)|\G(=)|\G(!)|\G(>)|\G(\\(\\?)|\G(#[^)]+)|\G(R)|\G(.)"],
                        3  => [0, "\G(P<[^>]+>)|\G(<=)|\G(<!)|\G(=)|\G(!)|\G(>)|\G(\\(\\?)|\G(#[^)]+)|\G(R)|\G(.)"],
                        4  => [0, "\G(<=)|\G(<!)|\G(=)|\G(!)|\G(>)|\G(\\(\\?)|\G(#[^)]+)|\G(R)|\G(.)"],
                        5  => [0, "\G(<!)|\G(=)|\G(!)|\G(>)|\G(\\(\\?)|\G(#[^)]+)|\G(R)|\G(.)"],
                        6  => [0, "\G(=)|\G(!)|\G(>)|\G(\\(\\?)|\G(#[^)]+)|\G(R)|\G(.)"],
                        7  => [0, "\G(!)|\G(>)|\G(\\(\\?)|\G(#[^)]+)|\G(R)|\G(.)"],
                        8  => [0, "\G(>)|\G(\\(\\?)|\G(#[^)]+)|\G(R)|\G(.)"],
                        9  => [0, "\G(\\(\\?)|\G(#[^)]+)|\G(R)|\G(.)"],
                        10 => [0, "\G(#[^)]+)|\G(R)|\G(.)"],
                        11 => [0, "\G(R)|\G(.)"],
                        12 => [0, "\G(.)"],
                        13 => [0, ""],
                    ];

                    // yymore is needed
                    do {
                        if (!strlen($yy_yymore_patterns[$this->token][1])) {
                            throw new Exception('cannot do yymore for the last token');
                        }
                        $yysubmatches = [];
                        if (preg_match('/' . $yy_yymore_patterns[$this->token][1] . '/',
                            $this->input, $yymatches, 0, $this->N)) {
                            $yysubmatches = $yymatches;
                            $yymatches    = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                            next($yymatches); // skip global match
                            $this->token += key($yymatches) + $yy_yymore_patterns[$this->token][0]; // token number
                            $this->value = current($yymatches); // token value
                            $this->line  = substr_count($this->value, "\n");
                            if ($tokenMap[$this->token]) {
                                // extract sub-patterns for passing to lex function
                                $yysubmatches = array_slice($yysubmatches, $this->token + 1,
                                    $tokenMap[$this->token]);
                            } else {
                                $yysubmatches = [];
                            }
                        }
                        $r = $this->{'lex_r5_' . $this->token}($yysubmatches);
                    } while ($r !== null && !is_bool($r));
                    if ($r === true) {
                        // we have changed state
                        // process this token in the new state
                        return $this->run();
                    } elseif ($r === false) {
                        $this->N    += strlen($this->value);
                        $this->line += substr_count($this->value, "\n");
                        if ($this->N >= strlen($this->input)) {
                            return false; // end of input
                        }
                        // skip this token
                        continue;
                    } else {
                        // accept
                        $this->N    += strlen($this->value);
                        $this->line += substr_count($this->value, "\n");

                        return true;
                    }
                }
            } else {
                throw new Exception('Unexpected input at line' . $this->line .
                    ': ' . $this->input[$this->N]);
            }
            break;
        } while (true);

    } // end function


    protected function lex_r5_1($yy_subpatterns): void
    {
        $this->token = self::INTERNALOPTIONS;
    }

    protected function lex_r5_2($yy_subpatterns): void
    {
        $this->token = self::COLON;
        $this->begin(self::INITIAL);
    }

    protected function lex_r5_3($yy_subpatterns): void
    {
        $this->token = self::CLOSEPAREN;
        $this->begin(self::INITIAL);
    }

    protected function lex_r5_4($yy_subpatterns): void
    {
        $this->token = self::PATTERNNAME;
        $this->begin(self::INITIAL);
    }

    protected function lex_r5_5($yy_subpatterns): void
    {
        $this->token = self::POSITIVELOOKBEHIND;
        $this->begin(self::INITIAL);
    }

    protected function lex_r5_6($yy_subpatterns): void
    {
        $this->token = self::NEGATIVELOOKBEHIND;
        $this->begin(self::INITIAL);
    }

    protected function lex_r5_7($yy_subpatterns): void
    {
        $this->token = self::POSITIVELOOKAHEAD;
        $this->begin(self::INITIAL);
    }

    protected function lex_r5_8($yy_subpatterns): void
    {
        $this->token = self::NEGATIVELOOKAHEAD;
        $this->begin(self::INITIAL);
    }

    protected function lex_r5_9($yy_subpatterns): void
    {
        $this->token = self::ONCEONLY;
        $this->begin(self::INITIAL);
    }

    protected function lex_r5_10($yy_subpatterns): void
    {
        $this->token = self::OPENASSERTION;
    }

    protected function lex_r5_11($yy_subpatterns): void
    {
        $this->token = self::COMMENT;
        $this->begin(self::INITIAL);
    }

    protected function lex_r5_12($yy_subpatterns): void
    {
        $this->token = self::RECUR;
    }

    protected function lex_r5_13($yy_subpatterns): bool
    {
        $this->begin(self::INITIAL);

        return true;
    }
}
