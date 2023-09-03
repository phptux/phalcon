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

namespace Azirax\Re2c;

use Azirax\Re2c\Parser\StackEntry;
use Azirax\Re2c\Regex\Lexer as RegexLexer;
use Azirax\Re2c\Regex\Parser as RegexParser;

/**
 * Token parser for plex files.
 *
 * @package    Azirax\Re2c
 * @author     Rene Dziuba <php.tux@web.de>
 * @copyright  Copyright (c) 2023 The Authors
 * @license    <http://opensource.org/licenses/bsd-license.php> New BSD License
 */
class Parser
{
    public const PHPCODE            = 1;
    public const COMMENTSTART       = 2;
    public const COMMENTEND         = 3;
    public const PI                 = 4;
    public const SUBPATTERN         = 5;
    public const CODE               = 6;
    public const PATTERN            = 7;
    public const QUOTE              = 8;
    public const SINGLEQUOTE        = 9;
    public const YY_NO_ACTION       = 99;
    public const YY_ACCEPT_ACTION   = 98;
    public const YY_ERROR_ACTION    = 97;
    public const YY_SZ_ACTTAB       = 91;
    public const YY_SHIFT_USE_DFLT  = -4;
    public const YY_SHIFT_MAX       = 35;
    public const YY_REDUCE_USE_DFLT = -10;
    public const YY_REDUCE_MAX      = 17;
    public const YYNOCODE           = 23;
    public const YYSTACKDEPTH       = 100;
    public const YYNSTATE           = 61;
    public const YYNRULE            = 36;
    public const YYERRORSYMBOL      = 10;
    public const YYERRSYMDT         = 'yy0';
    public const YYFALLBACK         = 0;

    /**
     * A single table containing all actions.
     *
     * @var array|int[]
     */
    static public array $yy_action = [
        /*     0 */
        25, 50, 49, 31, 49, 54, 53, 54, 53, 35,
        /*    10 */
        11, 49, 18, 22, 54, 53, 14, 59, 51, 28,
        /*    20 */
        55, 57, 58, 59, 47, 1, 55, 57, 32, 15,
        /*    30 */
        49, 29, 49, 54, 53, 54, 53, 30, 52, 49,
        /*    40 */
        42, 46, 54, 53, 98, 56, 5, 13, 38, 18,
        /*    50 */
        49, 43, 40, 54, 53, 12, 39, 18, 3, 37,
        /*    60 */
        36, 17, 7, 8, 2, 10, 33, 18, 9, 2,
        /*    70 */
        41, 44, 1, 24, 16, 34, 45, 27, 60, 48,
        /*    80 */
        4, 1, 2, 1, 20, 19, 21, 26, 23, 6,
        /*    90 */
        7,
    ];

    /**
     * A table containing the lookahead for each entry in yy_action.
     *
     * Used to detect hash collisions.
     *
     * @var array|int[]
     */
    static public array $yy_lookahead = [
        /*     0 */
        3, 1, 5, 4, 5, 8, 9, 8, 9, 3,
        /*    10 */
        19, 5, 21, 4, 8, 9, 7, 5, 6, 14,
        /*    20 */
        8, 9, 3, 5, 6, 20, 8, 9, 3, 7,
        /*    30 */
        5, 4, 5, 8, 9, 8, 9, 3, 1, 5,
        /*    40 */
        5, 6, 8, 9, 11, 12, 13, 19, 5, 21,
        /*    50 */
        5, 8, 9, 8, 9, 19, 5, 21, 5, 8,
        /*    60 */
        9, 1, 2, 1, 2, 19, 14, 21, 1, 2,
        /*    70 */
        5, 6, 20, 15, 16, 14, 2, 14, 1, 1,
        /*    80 */
        5, 20, 2, 20, 18, 21, 18, 17, 4, 13,
        /*    90 */
        2,
    ];

    /**
     * For each state, the offset into self::$yy_action for
     * shifting terminals.
     *
     * @var array|int[]
     */
    static public array $yy_shift_ofst = [
        /*     0 */
        60, 27, -1, 45, 45, 62, 67, 84, 80, 80,
        /*    10 */
        34, 25, -3, 6, 51, 51, 9, 88, 12, 18,
        /*    20 */
        43, 43, 65, 35, 19, 0, 22, 74, 74, 75,
        /*    30 */
        78, 53, 77, 74, 74, 37,
    ];

    /**
     * For each state, the offset into self::$yy_action for
     * shifting non-terminals after a reduction.
     *
     * @var array|int[]
     */
    static public array $yy_reduce_ofst = [
        /*     0 */
        33, 28, -9, 46, 36, 52, 63, 58, 61, 5,
        /*    10 */
        64, 64, 64, 64, 66, 68, 70, 76,
    ];

    /**
     * Expected tokens
     *
     * @var array
     */
    static public array $yyExpectedTokens = [
        /* 0 */
        [1, 2,],
        /* 1 */
        [4, 5, 8, 9,],
        /* 2 */
        [4, 5, 8, 9,],
        /* 3 */
        [5, 8, 9,],
        /* 4 */
        [5, 8, 9,],
        /* 5 */
        [1, 2,],
        /* 6 */
        [1, 2,],
        /* 7 */
        [4,],
        /* 8 */
        [2,],
        /* 9 */
        [2,],
        /* 10 */
        [3, 5, 8, 9,],
        /* 11 */
        [3, 5, 8, 9,],
        /* 12 */
        [3, 5, 8, 9,],
        /* 13 */
        [3, 5, 8, 9,],
        /* 14 */
        [5, 8, 9,],
        /* 15 */
        [5, 8, 9,],
        /* 16 */
        [4, 7,],
        /* 17 */
        [2,],
        /* 18 */
        [5, 6, 8, 9,],
        /* 19 */
        [5, 6, 8, 9,],
        /* 20 */
        [5, 8, 9,],
        /* 21 */
        [5, 8, 9,],
        /* 22 */
        [5, 6,],
        /* 23 */
        [5, 6,],
        /* 24 */
        [3,],
        /* 25 */
        [1,],
        /* 26 */
        [7,],
        /* 27 */
        [2,],
        /* 28 */
        [2,],
        /* 29 */
        [5,],
        /* 30 */
        [1,],
        /* 31 */
        [5,],
        /* 32 */
        [1,],
        /* 33 */
        [2,],
        /* 34 */
        [2,],
        /* 35 */
        [1,],
        /* 36 */
        [],
        /* 37 */
        [],
        /* 38 */
        [],
        /* 39 */
        [],
        /* 40 */
        [],
        /* 41 */
        [],
        /* 42 */
        [],
        /* 43 */
        [],
        /* 44 */
        [],
        /* 45 */
        [],
        /* 46 */
        [],
        /* 47 */
        [],
        /* 48 */
        [],
        /* 49 */
        [],
        /* 50 */
        [],
        /* 51 */
        [],
        /* 52 */
        [],
        /* 53 */
        [],
        /* 54 */
        [],
        /* 55 */
        [],
        /* 56 */
        [],
        /* 57 */
        [],
        /* 58 */
        [],
        /* 59 */
        [],
        /* 60 */
        [],
    ];

    /**
     * Default action for each state.
     *
     * @var array|int[]
     */
    static public array $yy_default = [
        /*     0 */
        97, 97, 97, 97, 97, 97, 97, 97, 97, 97,
        /*    10 */
        97, 97, 97, 97, 97, 97, 97, 97, 97, 97,
        /*    20 */
        72, 73, 97, 97, 97, 79, 67, 64, 65, 97,
        /*    30 */
        75, 97, 74, 62, 63, 78, 92, 91, 96, 93,
        /*    40 */
        95, 70, 68, 94, 71, 82, 69, 84, 77, 87,
        /*    50 */
        81, 83, 80, 86, 85, 88, 61, 89, 66, 90,
        /*    60 */
        76,
    ];

    /** The next table maps tokens into fallback tokens.  If a construct
     * like the following:
     *
     *      %fallback ID X Y Z.
     *
     * appears in the grammar, then ID becomes a fallback token for X, Y,
     * and Z.  Whenever one of the tokens X, Y, or Z is input to the parser,
     * but it does not parse, the type of the token is changed to ID and
     * the parse is retried before an error is thrown.
     */
    static public array $yyFallback = [];

    /**
     * For tracing shifts, the names of all terminals and non-terminals
     * are required.
     *
     * The following table supplies these names
     *
     * @var array
     */
    static public array $yyTokenName = [
        '$', 'PHPCODE', 'COMMENTSTART', 'COMMENTEND',
        'PI', 'SUBPATTERN', 'CODE', 'PATTERN',
        'QUOTE', 'SINGLEQUOTE', 'error', 'start',
        'lexfile', 'declare', 'rules', 'declarations',
        'processing_instructions', 'pattern_declarations', 'subpattern', 'rule',
        'reset_rules', 'rule_subpattern',
    ];

    /**
     * For tracing, reduce actions, the names of all rules are required.
     *
     * @var array
     */
    static public array $yyRuleName = [
        /*   0 */
        "start ::= lexfile",
        /*   1 */
        "lexfile ::= declare rules",
        /*   2 */
        "lexfile ::= declare PHPCODE rules",
        /*   3 */
        "lexfile ::= PHPCODE declare rules",
        /*   4 */
        "lexfile ::= PHPCODE declare PHPCODE rules",
        /*   5 */
        "declare ::= COMMENTSTART declarations COMMENTEND",
        /*   6 */
        "declarations ::= processing_instructions pattern_declarations",
        /*   7 */
        "processing_instructions ::= PI SUBPATTERN",
        /*   8 */
        "processing_instructions ::= PI CODE",
        /*   9 */
        "processing_instructions ::= processing_instructions PI SUBPATTERN",
        /*  10 */
        "processing_instructions ::= processing_instructions PI CODE",
        /*  11 */
        "pattern_declarations ::= PATTERN subpattern",
        /*  12 */
        "pattern_declarations ::= pattern_declarations PATTERN subpattern",
        /*  13 */
        "rules ::= COMMENTSTART rule COMMENTEND",
        /*  14 */
        "rules ::= COMMENTSTART PI SUBPATTERN rule COMMENTEND",
        /*  15 */
        "rules ::= COMMENTSTART rule COMMENTEND PHPCODE",
        /*  16 */
        "rules ::= COMMENTSTART PI SUBPATTERN rule COMMENTEND PHPCODE",
        /*  17 */
        "rules ::= reset_rules rule COMMENTEND",
        /*  18 */
        "rules ::= reset_rules PI SUBPATTERN rule COMMENTEND",
        /*  19 */
        "rules ::= reset_rules rule COMMENTEND PHPCODE",
        /*  20 */
        "rules ::= reset_rules PI SUBPATTERN rule COMMENTEND PHPCODE",
        /*  21 */
        "reset_rules ::= rules COMMENTSTART",
        /*  22 */
        "rule ::= rule_subpattern CODE",
        /*  23 */
        "rule ::= rule rule_subpattern CODE",
        /*  24 */
        "rule_subpattern ::= QUOTE",
        /*  25 */
        "rule_subpattern ::= SINGLEQUOTE",
        /*  26 */
        "rule_subpattern ::= SUBPATTERN",
        /*  27 */
        "rule_subpattern ::= rule_subpattern QUOTE",
        /*  28 */
        "rule_subpattern ::= rule_subpattern SINGLEQUOTE",
        /*  29 */
        "rule_subpattern ::= rule_subpattern SUBPATTERN",
        /*  30 */
        "subpattern ::= QUOTE",
        /*  31 */
        "subpattern ::= SINGLEQUOTE",
        /*  32 */
        "subpattern ::= SUBPATTERN",
        /*  33 */
        "subpattern ::= subpattern QUOTE",
        /*  34 */
        "subpattern ::= subpattern SINGLEQUOTE",
        /*  35 */
        "subpattern ::= subpattern SUBPATTERN",
    ];

    /**
     * The following table contains information about every rule that
     * is used during the reduce.
     *
     * ```
     * array(
     *  array(
     *   int $lhs;         Symbol on the left-hand side of the rule
     *   int $nrhs;     Number of right-hand side symbols in the rule
     *  ),...
     * );
     * ```
     *
     * @var array
     */
    static public array $yyRuleInfo = [
        ['lhs' => 11, 'rhs' => 1],
        ['lhs' => 12, 'rhs' => 2],
        ['lhs' => 12, 'rhs' => 3],
        ['lhs' => 12, 'rhs' => 3],
        ['lhs' => 12, 'rhs' => 4],
        ['lhs' => 13, 'rhs' => 3],
        ['lhs' => 15, 'rhs' => 2],
        ['lhs' => 16, 'rhs' => 2],
        ['lhs' => 16, 'rhs' => 2],
        ['lhs' => 16, 'rhs' => 3],
        ['lhs' => 16, 'rhs' => 3],
        ['lhs' => 17, 'rhs' => 2],
        ['lhs' => 17, 'rhs' => 3],
        ['lhs' => 14, 'rhs' => 3],
        ['lhs' => 14, 'rhs' => 5],
        ['lhs' => 14, 'rhs' => 4],
        ['lhs' => 14, 'rhs' => 6],
        ['lhs' => 14, 'rhs' => 3],
        ['lhs' => 14, 'rhs' => 5],
        ['lhs' => 14, 'rhs' => 4],
        ['lhs' => 14, 'rhs' => 6],
        ['lhs' => 20, 'rhs' => 2],
        ['lhs' => 19, 'rhs' => 2],
        ['lhs' => 19, 'rhs' => 3],
        ['lhs' => 21, 'rhs' => 1],
        ['lhs' => 21, 'rhs' => 1],
        ['lhs' => 21, 'rhs' => 1],
        ['lhs' => 21, 'rhs' => 2],
        ['lhs' => 21, 'rhs' => 2],
        ['lhs' => 21, 'rhs' => 2],
        ['lhs' => 18, 'rhs' => 1],
        ['lhs' => 18, 'rhs' => 1],
        ['lhs' => 18, 'rhs' => 1],
        ['lhs' => 18, 'rhs' => 2],
        ['lhs' => 18, 'rhs' => 2],
        ['lhs' => 18, 'rhs' => 2],
    ];

    /**
     * The following table contains a mapping of reduce action to method name
     * that handles the reduction.
     *
     * If a rule is not set, it has no handler.
     *
     * @var array
     */
    static public array $yyReduceMap = [
        1  => 1,
        2  => 2,
        3  => 3,
        4  => 4,
        5  => 5,
        6  => 6,
        7  => 7,
        8  => 7,
        9  => 9,
        10 => 9,
        11 => 11,
        12 => 12,
        13 => 13,
        14 => 14,
        15 => 15,
        16 => 16,
        17 => 17,
        18 => 18,
        19 => 19,
        20 => 20,
        21 => 21,
        22 => 22,
        23 => 23,
        24 => 24,
        25 => 25,
        26 => 26,
        27 => 27,
        28 => 28,
        29 => 29,
        30 => 30,
        31 => 31,
        32 => 32,
        33 => 33,
        34 => 34,
        35 => 35,
    ];

    /**
     * File resource for debug output
     *
     * @var resource|0
     */
    static public mixed $yyTraceFILE = 0;

    /**
     * String to prepend to debug output
     *
     * @var string|int
     */
    static public string|int $yyTracePrompt = 0;

    /**
     * Index of a top element in stack
     *
     * @var int|null
     */
    public ?int $yyidx = null;

    /**
     * Shifts left before out of the error
     *
     * @var int
     */
    public int $yyerrcnt = 0;

    /**
     * The parser's stack
     *
     * @var array
     */
    public array $yystack = [];

    /**
     * Trans table
     *
     * @var array|int[]
     */
    public array $transTable = [
        1 => self::PHPCODE,
        2 => self::COMMENTSTART,
        3 => self::COMMENTEND,
        4 => self::QUOTE,
        5 => self::SINGLEQUOTE,
        6 => self::PATTERN,
        7 => self::CODE,
        8 => self::SUBPATTERN,
        9 => self::PI,
    ];

    /**
     * @var mixed|null
     */
    private mixed $patterns = null;

    /**
     * File resource
     *
     * @var false|resource
     */
    private mixed $out;

    /**
     * The Lexer object
     *
     * @var Lexer
     */
    private Lexer $lex;

    /**
     * Input string
     *
     * @var string
     */
    private string $input = '';

    /**
     * Counter name
     *
     * @var string
     */
    private string $counter = 'counter';

    /**
     * Token name
     *
     * @var string
     */
    private string $token = 'token';

    /**
     * Value name
     *
     * @var string
     */
    private string $value = 'value';

    /**
     * Line name
     *
     * @var string
     */
    private string $line = 'line';

    /**
     * Math flag
     *
     * @var bool
     */
    private bool $matchlongest = false;

    /**
     * RegexLexer object
     *
     * @var RegexLexer
     */
    private RegexLexer $_regexLexer;

    /**
     * RegexParser object
     *
     * @var RegexParser
     */
    private RegexParser $_regexParser;

    /**
     * Pattern index
     *
     * @var int
     */
    private int $_patternIndex = 0;

    /**
     * Rule index
     *
     * @var int
     */
    private int $_outRuleIndex = 1;

    /**
     * Flag for case-insensitive
     *
     * @var bool
     */
    private bool $caseinsensitive = false;

    /**
     * Pattern flags
     *
     * @var string
     */
    private string $patternFlags = '';

    /**
     * Unicode flag
     *
     * @var bool
     */
    private bool $unicode = false;

    /**
     * placeholder for the left hand side in a reduce operation.
     *
     * For a parser with a rule like this:
     * <pre>
     * rule(A) ::= B. { A = 1; }
     * </pre>
     *
     * The parser will translate to something like:
     *
     * <code>
     * function yy_r0(){$this->_retvalue = 1;}
     * </code>
     *
     * @var array|string|null
     */
    private array|string|null $_retvalue = null;

    /**
     * Turn parser tracing on by giving a stream to which to write the trace
     * and a prompt to preface each trace message.  Tracing is turned off
     * by making either argument NULL
     *
     * Inputs:
     *
     * - A stream resource to which trace output should be written.
     *   If NULL, then tracing is turned off.
     * - A prefix string written at the beginning of every
     *   line of trace output.  If NULL, then tracing is
     *   turned off.
     *
     * Outputs:
     *
     * - None.
     *
     * @param resource $TraceFILE
     * @param mixed    $zTracePrompt
     */
    public static function Trace($TraceFILE, mixed $zTracePrompt): void
    {
        if (!$TraceFILE) {
            $zTracePrompt = 0;
        } elseif (!$zTracePrompt) {
            $TraceFILE = 0;
        }

        self::$yyTraceFILE   = $TraceFILE;
        self::$yyTracePrompt = $zTracePrompt;
    }

    /**
     * Output debug information to output (php://output stream)
     */
    public static function PrintTrace(): void
    {
        self::$yyTraceFILE   = fopen('php://output', 'w');
        self::$yyTracePrompt = '';
    }

    /**
     * The following function deletes the value associated with a
     * symbol.  The symbol can be either a terminal or non-terminal.
     *
     * @param int   $yymajor  the symbol code
     * @param mixed $yypminor the symbol's value
     */
    public static function yy_destructor(int $yymajor, mixed $yypminor): void
    {
        switch ($yymajor) {
            /* Here is inserted the actions which take place when a
            ** terminal or non-terminal is destroyed.  This can happen
            ** when the symbol is popped from the stack during a
            ** reduce or during error processing or when a parser is
            ** being destroyed before it is finished parsing.
            **
            ** Note: during a reduce, the only symbols destroyed are those
            ** which appear on the RHS of the rule, but which are not used
            ** inside the C code.
            */
            default:
                break;   /* If no destructor action specified: do nothing */
        }
    }

    /**
     * Constructor for Parser
     *
     * @param string $outfile
     * @param Lexer  $lex
     *
     * @throws Exception
     */
    public function __construct(string $outfile, Lexer $lex)
    {
        $this->out = fopen($outfile, 'wb');

        if (!$this->out) {
            throw new Exception('unable to open lexer output file "' . $outfile . '"');
        }

        $this->lex          = $lex;
        $this->_regexLexer  = new RegexLexer('');
        $this->_regexParser = new RegexParser($this->_regexLexer);
    }

    /**
     * Deallocate and destroy a parser.  Destructors are all called for
     * all stack elements before shutting the parser down.
     */
    public function __destruct()
    {
        while ($this->yyidx >= 0) {
            $this->yy_pop_parser_stack();
        }

        if (is_resource(self::$yyTraceFILE)) {
            fclose(self::$yyTraceFILE);
        }
    }

    /**
     * This function returns the symbolic name associated with a token
     * value.
     *
     * @param int $tokenType
     *
     * @return string
     */
    public function tokenName(int $tokenType): string
    {
        if ($tokenType === 0) {
            return 'End of Input';
        }

        if ($tokenType > 0 && $tokenType < count(self::$yyTokenName)) {
            return self::$yyTokenName[$tokenType];
        }

        return 'Unknown';
    }

    /**
     * Pop the parser's stack once.
     *
     * If there is a destructor routine associated with the token which
     * is popped from the stack, then call it.
     *
     * Return the major token number for the symbol popped.
     *
     * @return int|null
     */
    public function yy_pop_parser_stack(): ?int
    {
        if (!count($this->yystack)) {
            return null;
        }

        $yytos = array_pop($this->yystack);
        if (self::$yyTraceFILE && $this->yyidx >= 0) {
            fwrite(self::$yyTraceFILE,
                self::$yyTracePrompt . 'Popping ' . self::$yyTokenName[$yytos->major] .
                "\n");
        }

        $yymajor = $yytos->major;
        //self::yy_destructor($yymajor, $yytos->minor);
        $this->yyidx--;

        return $yymajor;
    }

    /**
     * Based on the current state and parser stack, get a list of all
     * possible lookahead tokens
     *
     * @param int $token
     *
     * @return array
     */
    public function yy_get_expected_tokens(int $token): array
    {
        $state    = $this->yystack[$this->yyidx]->stateno;
        $expected = self::$yyExpectedTokens[$state];
        if (in_array($token, self::$yyExpectedTokens[$state], true)) {
            return $expected;
        }
        $stack = $this->yystack;
        $yyidx = $this->yyidx;
        do {
            $yyact = $this->yy_find_shift_action($token);
            if ($yyact >= self::YYNSTATE && $yyact < self::YYNSTATE + self::YYNRULE) {
                // reduce action
                $done = 0;
                do {
                    if ($done++ == 100) {
                        $this->yyidx   = $yyidx;
                        $this->yystack = $stack;
                        // too much recursion prevents proper detection
                        // so give up
                        return array_unique($expected);
                    }
                    $yyruleno    = $yyact - self::YYNSTATE;
                    $this->yyidx -= self::$yyRuleInfo[$yyruleno]['rhs'];
                    $nextstate   = $this->yy_find_reduce_action(
                        $this->yystack[$this->yyidx]->stateno,
                        self::$yyRuleInfo[$yyruleno]['lhs']);
                    if (isset(self::$yyExpectedTokens[$nextstate])) {
                        $expected += self::$yyExpectedTokens[$nextstate];
                        if (in_array($token,
                            self::$yyExpectedTokens[$nextstate], true)) {
                            $this->yyidx   = $yyidx;
                            $this->yystack = $stack;

                            return array_unique($expected);
                        }
                    }
                    if ($nextstate < self::YYNSTATE) {
                        // we need to shift a non-terminal
                        $this->yyidx++;
                        $x                           = new StackEntry();
                        $x->stateno                  = $nextstate;
                        $x->major                    = self::$yyRuleInfo[$yyruleno]['lhs'];
                        $this->yystack[$this->yyidx] = $x;
                        continue 2;
                    } elseif ($nextstate == self::YYNSTATE + self::YYNRULE + 1) {
                        $this->yyidx   = $yyidx;
                        $this->yystack = $stack;
                        // the last token was just ignored, we can't accept
                        // by ignoring input, this is in essence ignoring a
                        // syntax error!
                        return array_unique($expected);
                    } elseif ($nextstate === self::YY_NO_ACTION) {
                        $this->yyidx   = $yyidx;
                        $this->yystack = $stack;

                        // input accepted, but not shifted (I guess)
                        return $expected;
                    } else {
                        $yyact = $nextstate;
                    }
                } while (true);
            }
            break;
        } while (true);

        return array_unique($expected);
    }

    /**
     * Based on the parser state and current parser stack, determine whether
     * the lookahead token is possible.
     *
     * The parser will convert the token value to an error token if not.  This
     * catches some unusual edge cases where the parser would fail.
     *
     * @param int $token
     *
     * @return bool
     */
    public function yy_is_expected_token(int $token): bool
    {
        if ($token === 0) {
            return true; // 0 is not part of this
        }
        $state = $this->yystack[$this->yyidx]->stateno;
        if (in_array($token, self::$yyExpectedTokens[$state], true)) {
            return true;
        }
        $stack = $this->yystack;
        $yyidx = $this->yyidx;
        do {
            $yyact = $this->yy_find_shift_action($token);
            if ($yyact >= self::YYNSTATE && $yyact < self::YYNSTATE + self::YYNRULE) {
                // reduce action
                $done = 0;
                do {
                    if ($done++ == 100) {
                        $this->yyidx   = $yyidx;
                        $this->yystack = $stack;
                        // too much recursion prevents proper detection
                        // so give up
                        return true;
                    }
                    $yyruleno    = $yyact - self::YYNSTATE;
                    $this->yyidx -= self::$yyRuleInfo[$yyruleno]['rhs'];
                    $nextstate   = $this->yy_find_reduce_action(
                        $this->yystack[$this->yyidx]->stateno,
                        self::$yyRuleInfo[$yyruleno]['lhs']);
                    if (isset(self::$yyExpectedTokens[$nextstate]) &&
                        in_array($token, self::$yyExpectedTokens[$nextstate], true)) {
                        $this->yyidx   = $yyidx;
                        $this->yystack = $stack;

                        return true;
                    }
                    if ($nextstate < self::YYNSTATE) {
                        // we need to shift a non-terminal
                        $this->yyidx++;
                        $x                           = new StackEntry();
                        $x->stateno                  = $nextstate;
                        $x->major                    = self::$yyRuleInfo[$yyruleno]['lhs'];
                        $this->yystack[$this->yyidx] = $x;
                        continue 2;
                    } elseif ($nextstate == self::YYNSTATE + self::YYNRULE + 1) {
                        $this->yyidx   = $yyidx;
                        $this->yystack = $stack;
                        if (!$token) {
                            // end of input: this is valid
                            return true;
                        }
                        // the last token was just ignored, we can't accept
                        // by ignoring input, this is in essence ignoring a
                        // syntax error!
                        return false;
                    } elseif ($nextstate === self::YY_NO_ACTION) {
                        $this->yyidx   = $yyidx;
                        $this->yystack = $stack;

                        // input accepted, but not shifted (I guess)
                        return true;
                    } else {
                        $yyact = $nextstate;
                    }
                } while (true);
            }
            break;
        } while (true);
        $this->yyidx   = $yyidx;
        $this->yystack = $stack;

        return true;
    }

    /**
     * Find the appropriate action for a parser given the terminal
     * look-ahead token iLookAhead.
     *
     * If the look-ahead token is YYNOCODE, then check to see if the action is
     * independent of the look-ahead.  If it is, return the action, otherwise
     * return YY_NO_ACTION.
     *
     * @param int $iLookAhead The look-ahead token
     *
     * @return int
     */
    public function yy_find_shift_action(int $iLookAhead): int
    {
        $stateno = $this->yystack[$this->yyidx]->stateno;

        /* if ($this->yyidx < 0) return self::YY_NO_ACTION;  */
        if (!isset(self::$yy_shift_ofst[$stateno])) {
            // no shift actions
            return self::$yy_default[$stateno];
        }
        $i = self::$yy_shift_ofst[$stateno];
        if ($i === self::YY_SHIFT_USE_DFLT) {
            return self::$yy_default[$stateno];
        }
        if ($iLookAhead == self::YYNOCODE) {
            return self::YY_NO_ACTION;
        }
        $i += $iLookAhead;
        if ($i < 0 || $i >= self::YY_SZ_ACTTAB ||
            self::$yy_lookahead[$i] != $iLookAhead) {
            if (count(self::$yyFallback) && $iLookAhead < count(self::$yyFallback)
                && ($iFallback = self::$yyFallback[$iLookAhead]) != 0) {
                if (self::$yyTraceFILE) {
                    fwrite(self::$yyTraceFILE, self::$yyTracePrompt . "FALLBACK " .
                        self::$yyTokenName[$iLookAhead] . " => " .
                        self::$yyTokenName[$iFallback] . "\n");
                }

                return $this->yy_find_shift_action($iFallback);
            }

            return self::$yy_default[$stateno];
        } else {
            return self::$yy_action[$i];
        }
    }

    /**
     * Find the appropriate action for a parser given the non-terminal
     * look-ahead token $iLookAhead.
     *
     * If the look-ahead token is self::YYNOCODE, then check to see if the action is
     * independent of the look-ahead.  If it is, return the action, otherwise
     * return self::YY_NO_ACTION.
     *
     * @param int $stateno    Current state number
     * @param int $iLookAhead The look-ahead token
     *
     * @return int
     */
    public function yy_find_reduce_action(int $stateno, int $iLookAhead): int
    {
        if (!isset(self::$yy_reduce_ofst[$stateno])) {
            return self::$yy_default[$stateno];
        }

        $i = self::$yy_reduce_ofst[$stateno];
        if ($i == self::YY_REDUCE_USE_DFLT) {
            return self::$yy_default[$stateno];
        }

        if ($iLookAhead == self::YYNOCODE) {
            return self::YY_NO_ACTION;
        }

        $i += $iLookAhead;
        if ($i < 0 || $i >= self::YY_SZ_ACTTAB ||
            self::$yy_lookahead[$i] != $iLookAhead) {
            return self::$yy_default[$stateno];
        } else {
            return self::$yy_action[$i];
        }
    }

    /**
     * Perform a shift action.
     *
     * @param int   $yyNewState The new state to shift in
     * @param int   $yyMajor    The major token to shift in
     * @param mixed $yypMinor   the minor token to shift in
     */
    public function yy_shift(int $yyNewState, int $yyMajor, mixed $yypMinor): void
    {
        $this->yyidx++;
        if ($this->yyidx >= self::YYSTACKDEPTH) {
            $this->yyidx--;
            if (self::$yyTraceFILE) {
                fprintf(self::$yyTraceFILE, "%sStack Overflow!\n", self::$yyTracePrompt);
            }
            while ($this->yyidx >= 0) {
                $this->yy_pop_parser_stack();
            }

            /* Here code is inserted which will execute if the parser
            ** stack ever overflows */

            return;
        }

        $yytos           = new StackEntry();
        $yytos->stateno  = $yyNewState;
        $yytos->major    = $yyMajor;
        $yytos->minor    = $yypMinor;
        $this->yystack[] = $yytos;

        if (self::$yyTraceFILE && $this->yyidx > 0) {
            fprintf(self::$yyTraceFILE, "%sShift %d\n", self::$yyTracePrompt,
                $yyNewState);
            fprintf(self::$yyTraceFILE, "%sStack:", self::$yyTracePrompt);
            for ($i = 1; $i <= $this->yyidx; $i++) {
                fprintf(self::$yyTraceFILE, " %s",
                    self::$yyTokenName[$this->yystack[$i]->major]);
            }
            fwrite(self::$yyTraceFILE, "\n");
        }
    }

    /**
     * Returns the index
     *
     * @return int
     */
    protected function getIndex(): int
    {
        return $this->yyidx + 0;
    }

    /**
     * Output the error message.
     *
     * @param string $msg
     *
     * @return void
     */
    protected function error(string $msg): void
    {
        echo 'Error on line ' . $this->lex->line . ': ', $msg;
    }

    /**
     * Output the rules.
     *
     * @param array $rules
     * @param mixed $statename
     *
     * @return void
     */
    protected function outputRules(array $rules, mixed $statename): void
    {
        if (!$statename) {
            $statename = $this->_outRuleIndex;
        }

        $funcName = 'lex' . $this->_outRuleIndex;
        fwrite($this->out, Code::getSimpleMethod($funcName, []));

        if ($this->matchlongest) {
            $ruleMap = [];
            foreach ($rules as $i => $rule) {
                $ruleMap[$i] = $i;
            }
            $this->doLongestMatch($rules, $statename, $this->_outRuleIndex);
        } else {
            $ruleMap     = [];
            $actualindex = 1;
            $i           = 0;
            foreach ($rules as $rule) {
                $ruleMap[$i++] = $actualindex;
                $actualindex   += $rule['subpatterns'] + 1;
            }
            $this->doFirstMatch($rules, $statename, $this->_outRuleIndex);
        }
        fwrite($this->out, '
    } // end function

');
        if (is_string($statename)) {
            fwrite($this->out, Code::getConstant($statename, $this->_outRuleIndex));
        }
        foreach ($rules as $i => $rule) {
            $PHP = Code::getMethod(
                'lex_r' . $this->_outRuleIndex . '_' . $ruleMap[$i],
                ['mixed $subPatterns'],
                $rule['code']
            );
            fwrite($this->out, $PHP);
        }
        $this->_outRuleIndex++; // for next set of rules
    }

    protected function doFirstMatch(array $rules, string $statename, int $ruleindex): void
    {
        $patterns    = [];
        $pattern     = '/';
        $ruleMap     = [];
        $tokenindex  = [];
        $actualindex = 1;
        $i           = 0;
        foreach ($rules as $rule) {
            $ruleMap[$i++]            = $actualindex;
            $tokenindex[$actualindex] = $rule['subpatterns'];
            $actualindex              += $rule['subpatterns'] + 1;
            //$patterns[]               = '\G(' . $rule['pattern'] . ')';
            $patterns[]               = '\G(' . Code::quotePatter($rule['pattern']) . ')';
        }
        // Re-index tokencount from zero.
        $tokencount = array_values($tokenindex);
        $tokenindex = var_export($tokenindex, true);
        $tokenindex = explode("\n", $tokenindex);
        // indent for prettiness
        $tokenindex = implode("\n            ", $tokenindex);
        $pattern    .= implode('|', $patterns);
        $pattern    .= '/' . $this->patternFlags;
        fwrite($this->out, '
        $tokenMap = ' . $tokenindex . ';
        if (' . $this->counter . ' >= strlen(' . $this->input . ')) {
            return false; // end of input
        }
        ');
        fwrite($this->out, '$yy_global_pattern = \'' .
            $pattern . '\';' . "\n");
        fwrite($this->out, '
        do {
            if (preg_match($yy_global_pattern,' . $this->input . ', $yymatches, 0, ' .
            $this->counter .
            ')) {
                $yysubmatches = $yymatches;
                $yymatches = array_filter($yymatches, \'strlen\'); // remove empty sub-patterns
                if (!count($yymatches)) {
                    throw new Exception(\'Error: lexing failed because a rule matched\' .
                        \' an empty string.  Input "\' . substr(' . $this->input . ',
                        ' . $this->counter . ', 5) . \'... state ' . $statename . '\');
                }
                next($yymatches); // skip global match
                ' . $this->token . ' = key($yymatches); // token number
                if ($tokenMap[' . $this->token . ']) {
                    // extract sub-patterns for passing to lex function
                    $yysubmatches = array_slice($yysubmatches, ' . $this->token . ' + 1,
                        $tokenMap[' . $this->token . ']);
                } else {
                    $yysubmatches = array();
                }
                ' . $this->value . ' = current($yymatches); // token value
                $r = $this->{\'lex_r' . $ruleindex . '_\' . ' . $this->token . '}($yysubmatches);
                if ($r === null) {
                    ' . $this->counter . ' += strlen(' . $this->value . ');
                    ' . $this->line . ' += substr_count(' . $this->value . ', "\n");
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->run();
                } elseif ($r === false) {
                    ' . $this->counter . ' += strlen(' . $this->value . ');
                    ' . $this->line . ' += substr_count(' . $this->value . ', "\n");
                    if (' . $this->counter . ' >= strlen(' . $this->input . ')) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                } else {');
        fwrite($this->out, '
                    $yy_yymore_patterns = [' . "\n");
        $extra = 0;
        for ($i = 0; count($patterns); $i++) {
            unset($patterns[$i]);
            $extra += $tokencount[0];
            array_shift($tokencount);
            fwrite($this->out, '        ' . $ruleMap[$i] . ' => [' . $extra . ', "' .
                implode('|', $patterns) . "\"],\n");
        }
        fwrite($this->out, '    ];' . "\n");
        fwrite($this->out, '
                    // yymore is needed
                    do {
                        if (!strlen($yy_yymore_patterns[' . $this->token . '][1])) {
                            throw new Exception(\'cannot do yymore for the last token\');
                        }
                        $yysubmatches = array();
                        if (preg_match(\'/\' . $yy_yymore_patterns[' . $this->token . '][1] . \'/' . $this->patternFlags . '\',
                              ' . $this->input . ', $yymatches, 0, ' . $this->counter . ')) {
                            $yysubmatches = $yymatches;
                            $yymatches = array_filter($yymatches, \'strlen\'); // remove empty sub-patterns
                            next($yymatches); // skip global match
                            ' . $this->token . ' += key($yymatches) + $yy_yymore_patterns[' . $this->token . '][0]; // token number
                            ' . $this->value . ' = current($yymatches); // token value
                            ' . $this->line . ' = substr_count(' . $this->value . ', "\n");
                            if ($tokenMap[' . $this->token . ']) {
                                // extract sub-patterns for passing to lex function
                                $yysubmatches = array_slice($yysubmatches, ' . $this->token . ' + 1,
                                    $tokenMap[' . $this->token . ']);
                            } else {
                                $yysubmatches = [];
                            }
                        }
                        $r = $this->{\'lex_r' . $ruleindex . '_\' . ' . $this->token . '}($yysubmatches);
                    } while ($r !== null && !is_bool($r));
                    if ($r === true) {
                        // we have changed state
                        // process this token in the new state
                        return $this->run();
                    } elseif ($r === false) {
                        ' . $this->counter . ' += strlen(' . $this->value . ');
                        ' . $this->line . ' += substr_count(' . $this->value . ', "\n");
                        if (' . $this->counter . ' >= strlen(' . $this->input . ')) {
                            return false; // end of input
                        }
                        // skip this token
                        continue;
                    } else {
                        // accept
                        ' . $this->counter . ' += strlen(' . $this->value . ');
                        ' . $this->line . ' += substr_count(' . $this->value . ', "\n");
                        return true;
                    }
                }
            } else {
                throw new Exception(\'Unexpected input at line\' . ' . $this->line . ' .
                    \': \' . ' . $this->input . '[' . $this->counter . ']);
            }
            break;
        } while (true);
');
    }

    /**
     * Returns the string case-insensitive
     *
     * @param string $string
     *
     * @return string
     */
    protected function makeCaseInsensitive(string $string): string
    {
        return preg_replace_callback('/[a-z]/i', function ($m) {
            var_dump($m);

            return $m;
        }, strtolower($string));

        //return preg_replace('/[a-z]/ie', "'[\\0'.strtoupper('\\0').']'", strtolower($string));
    }

    /**
     * @param array  $rules
     * @param string $statename
     * @param int    $ruleindex
     *
     * @return void
     */
    protected function doLongestMatch(array $rules, string $statename, int $ruleindex): void
    {
        fwrite($this->out, '
        if (' . $this->counter . ' >= strlen(' . $this->input . ')) {
            return false; // end of input
        }
        do {
            $rules = [');
        foreach ($rules as $rule) {
            fwrite($this->out, '
                \'/\G' . $rule['pattern'] . '/' . $this->patternFlags . ' \',');
        }
        fwrite($this->out, '
            ];
            $match = false;
            foreach ($rules as $index => $rule) {
                if (preg_match($rule, substr(' . $this->input . ', ' .
            $this->counter . '), $yymatches)) {
                    if ($match) {
                        if (strlen($yymatches[0]) > strlen($match[0][0])) {
                            $match = [$yymatches, $index]; // matches, token
                        }
                    } else {
                        $match = [$yymatches, $index];
                    }
                }
            }
            if (!$match) {
                throw new Exception(\'Unexpected input at line \' . ' . $this->line . ' .
                    \': \' . ' . $this->input . '[' . $this->counter . ']);
            }
            ' . $this->token . ' = $match[1];
            ' . $this->value . ' = $match[0][0];
            $yysubmatches = $match[0];
            array_shift($yysubmatches);
            if (!$yysubmatches) {
                $yysubmatches = [];
            }
            $r = $this->{\'lex_r' . $ruleindex . '_\' . ' . $this->token . '}($yysubmatches);
            if ($r === null) {
                ' . $this->counter . ' += strlen(' . $this->value . ');
                ' . $this->line . ' += substr_count(' . $this->value . ', "\n");
                // accept this token
                return true;
            } elseif ($r === true) {
                // we have changed state
                // process this token in the new state
                return $this->run();
            } elseif ($r === false) {
                ' . $this->counter . ' += strlen(' . $this->value . ');
                ' . $this->line . ' += substr_count(' . $this->value . ', "\n");
                if (' . $this->counter . ' >= strlen(' . $this->input . ')) {
                    return false; // end of input
                }
                // skip this token
                continue;
            } else {');
        fwrite($this->out, '
                $yy_yymore_patterns = array_slice($rules, $this->token, true);
                // yymore is needed
                do {
                    if (!isset($yy_yymore_patterns[' . $this->token . '])) {
                        throw new Exception(\'cannot do yymore for the last token\');
                    }
                    $match = false;
                    foreach ($yy_yymore_patterns[' . $this->token . '] as $index => $rule) {
                        if (preg_match(\'/\' . $rule . \'/' . $this->patternFlags . '\',
                                ' . $this->input . ', $yymatches, 0, ' . $this->counter . ')) {
                            $yymatches = array_filter($yymatches, \'strlen\'); // remove empty sub-patterns
                            if ($match) {
                                if (strlen($yymatches[0]) > strlen($match[0][0])) {
                                    $match = [$yymatches, $index]; // matches, token
                                }
                            } else {
                                $match = [$yymatches, $index];
                            }
                        }
                    }
                    if (!$match) {
                        throw new Exception(\'Unexpected input at line \' . ' . $this->line . ' .
                            \': \' . ' . $this->input . '[' . $this->counter . ']);
                    }
                    ' . $this->token . ' = $match[1];
                    ' . $this->value . ' = $match[0][0];
                    $yysubmatches = $match[0];
                    array_shift($yysubmatches);
                    if (!$yysubmatches) {
                        $yysubmatches = array();
                    }
                    ' . $this->line . ' = substr_count(' . $this->value . ', "\n");
                    $r = $this->{\'lex_r' . $ruleindex . '_\' . ' . $this->token . '}();
                } while ($r !== null || !$r);
                if ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->run();
                } else {
                    // accept
                    ' . $this->counter . ' += strlen(' . $this->value . ');
                    ' . $this->line . ' += substr_count(' . $this->value . ', "\n");
                    return true;
                }
            }
        } while (true);
');
    }

    /**
     * Validate the patter.
     *
     * @param string $pattern
     * @param bool   $update
     *
     * @return Parser\Token
     * @throws Exception
     */
    protected function _validatePattern(string $pattern, bool $update = false): Parser\Token
    {
        $this->_regexLexer->reset($pattern, $this->lex->line);
        $this->_regexParser->reset($this->_patternIndex, $update);

        try {
            while ($this->_regexLexer->run()) {
                $this->_regexParser->doParse(
                    $this->_regexLexer->token, $this->_regexLexer->value);
            }
            $this->_regexParser->doParse(0, 0);
        } catch (Exception $e) {
            $this->error($e->getMessage());
            throw new Exception('Invalid pattern "' . $pattern . '"');
        }

        return $this->_regexParser->result;
    }

    /**
     * @return void
     */
    protected function yy_r1(): void
    {
        fwrite($this->out, $this->getCoreMethods());

        foreach ($this->yystack[$this->getIndex()]->minor as $rule) {
            $this->outputRules($rule['rules'], $rule['statename']);
            if ($rule['code']) {
                fwrite($this->out, $rule['code']);
            }
        }
    }

    /**
     * @return void
     */
    protected function yy_r2(): void
    {
        fwrite($this->out, $this->getCoreMethods());

        if (strlen($this->yystack[$this->getIndex() + -1]->minor)) {
            fwrite($this->out, $this->yystack[$this->getIndex() + -1]->minor);
        }

        foreach ($this->yystack[$this->getIndex()]->minor as $rule) {
            $this->outputRules($rule['rules'], $rule['statename']);
            if ($rule['code']) {
                fwrite($this->out, $rule['code']);
            }
        }
    }

    /**
     * @return void
     */
    protected function yy_r3(): void
    {
        if (strlen($this->yystack[$this->getIndex() + -2]->minor)) {
            fwrite($this->out, $this->yystack[$this->getIndex() + -2]->minor);
        }

        fwrite($this->out, $this->getCoreMethods());

        foreach ($this->yystack[$this->getIndex()]->minor as $rule) {
            $this->outputRules($rule['rules'], $rule['statename']);
            if ($rule['code']) {
                fwrite($this->out, $rule['code']);
            }
        }
    }

    /**
     * @return void
     */
    protected function yy_r4(): void
    {
        if (strlen($this->yystack[$this->getIndex() + -3]->minor)) {
            fwrite($this->out, $this->yystack[$this->getIndex() + -3]->minor);
        }

        fwrite($this->out, $this->getCoreMethods());

        if (strlen($this->yystack[$this->getIndex() + -1]->minor)) {
            fwrite($this->out, $this->yystack[$this->getIndex() + -1]->minor);
        }

        foreach ($this->yystack[$this->getIndex()]->minor as $rule) {
            $this->outputRules($rule['rules'], $rule['statename']);
            if ($rule['code']) {
                fwrite($this->out, $rule['code']);
            }
        }
    }

    /**
     * @return void
     */
    protected function yy_r5(): void
    {
        $this->_retvalue     = $this->yystack[$this->getIndex() + -1]->minor;
        $this->patterns      = $this->yystack[$this->getIndex() + -1]->minor['patterns'];
        $this->_patternIndex = 1;
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function yy_r6(): void
    {
        $expected = [
            'counter' => true,
            'input'   => true,
            'token'   => true,
            'value'   => true,
            'line'    => true,
        ];

        foreach ($this->yystack[$this->getIndex() + -1]->minor as $pi) {
            if (isset($expected[$pi['pi']])) {
                unset($expected[$pi['pi']]);
                continue;
            }
            if (count($expected)) {
                throw new Exception('Processing Instructions "' .
                    implode(', ', array_keys($expected)) . '" must be defined');
            }
        }

        $expected = [
            'caseinsensitive' => true,
            'counter'         => true,
            'input'           => true,
            'token'           => true,
            'value'           => true,
            'line'            => true,
            'matchlongest'    => true,
            'unicode'         => true,
        ];

        foreach ($this->yystack[$this->getIndex() + -1]->minor as $pi) {
            if (isset($expected[$pi['pi']])) {
                switch ($pi['pi']) {
                    case 'caseinsensitive':
                        $this->caseinsensitive = true;
                        break;
                    case 'matchlongest':
                        $this->matchlongest = true;
                        break;
                    case 'unicode':
                        $this->unicode = true;
                        break;

                    default:
                        $this->{$pi['pi']} = $pi['definition'];
                        break;
                }

                continue;
            }
            $this->error('Unknown processing instruction %' . $pi['pi'] .
                ', should be one of "' . implode(', ', array_keys($expected)) . '"');
        }
        $this->patternFlags  = ($this->caseinsensitive ? 'i' : '') . ($this->unicode ? 'u' : '');
        $this->_retvalue     = [
            'patterns' => $this->yystack[$this->getIndex()]->minor,
            'pis'      => $this->yystack[$this->getIndex() + -1]->minor
        ];
        $this->_patternIndex = 1;
    }

    /**
     * @return void
     */
    protected function yy_r7(): void
    {
        $this->_retvalue = [
            [
                'pi'         => $this->yystack[$this->getIndex() + -1]->minor,
                'definition' => $this->yystack[$this->getIndex()]->minor
            ]
        ];
    }

    /**
     * @return void
     */
    protected function yy_r9(): void
    {
        $this->_retvalue   = $this->yystack[$this->yyidx + -2]->minor;
        $this->_retvalue[] = [
            'pi'         => $this->yystack[$this->yyidx + -1]->minor,
            'definition' => $this->yystack[$this->yyidx + 0]->minor
        ];
    }

    /**
     * @return void
     */
    protected function yy_r11(): void
    {
        $this->_retvalue = [
            $this->yystack[$this->getIndex() + -1]->minor => $this->yystack[$this->getIndex()]->minor
        ];

        // reset internal indicator of where we are in a pattern
        $this->_patternIndex = 0;
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function yy_r12(): void
    {
        $this->_retvalue = $this->yystack[$this->getIndex() + -2]->minor;

        if (isset($this->_retvalue[$this->yystack[$this->getIndex() + -1]->minor])) {
            throw new Exception('Pattern "' . $this->yystack[$this->getIndex() + -1]->minor . '" is already defined as "' .
                $this->_retvalue[$this->yystack[$this->getIndex() + -1]->minor] . '", cannot redefine as "' . $this->yystack[$this->getIndex()]->minor->string . '"');
        }

        $this->_retvalue[$this->yystack[$this->getIndex() + -1]->minor] = $this->yystack[$this->getIndex()]->minor;

        // reset internal indicator of where we are in a pattern declaration
        $this->_patternIndex = 0;
    }

    /**
     * @return void
     */
    protected function yy_r13(): void
    {
        $this->_retvalue = [
            [
                'rules' => $this->yystack[$this->getIndex() + -1]->minor,
                'code'  => '', 'statename' => ''
            ]
        ];
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function yy_r14(): void
    {
        if ($this->yystack[$this->getIndex() + -3]->minor != 'statename') {
            throw new Exception('Error: only %statename processing instruction ' .
                'is allowed in rule sections (found ' . $this->yystack[$this->getIndex() + -3]->minor . ').');
        }

        $this->_retvalue = [
            [
                'rules'     => $this->yystack[$this->getIndex() + -1]->minor, 'code' => '',
                'statename' => $this->yystack[$this->getIndex() + -2]->minor
            ]
        ];
    }

    /**
     * @return void
     */
    protected function yy_r15(): void
    {
        $this->_retvalue = [
            [
                'rules' => $this->yystack[$this->getIndex() + -2]->minor,
                'code'  => $this->yystack[$this->getIndex()]->minor, 'statename' => ''
            ]
        ];
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function yy_r16(): void
    {
        if ($this->yystack[$this->getIndex() + -4]->minor != 'statename') {
            throw new Exception('Error: only %statename processing instruction ' .
                'is allowed in rule sections (found ' . $this->yystack[$this->getIndex() + -4]->minor . ').');
        }

        $this->_retvalue     = [
            [
                'rules'     => $this->yystack[$this->getIndex() + -2]->minor,
                'code'      => $this->yystack[$this->getIndex()]->minor,
                'statename' => $this->yystack[$this->getIndex() + -3]->minor
            ]
        ];
        $this->_patternIndex = 1;
    }

    /**
     * @return void
     */
    protected function yy_r17(): void
    {
        $this->_retvalue     = $this->yystack[$this->getIndex() + -2]->minor;
        $this->_retvalue[]   = [
            'rules'     => $this->yystack[$this->getIndex() + -1]->minor,
            'code'      => '',
            'statename' => ''
        ];
        $this->_patternIndex = 1;
    }

    /**
     * @throws Exception
     */
    protected function yy_r18(): void
    {
        if ($this->yystack[$this->getIndex() + -3]->minor != 'statename') {
            throw new Exception('Error: only %statename processing instruction ' .
                'is allowed in rule sections (found ' . $this->yystack[$this->getIndex() + -3]->minor . ').');
        }

        $this->_retvalue   = $this->yystack[$this->getIndex() + -4]->minor;
        $this->_retvalue[] = [
            'rules'     => $this->yystack[$this->getIndex() + -1]->minor,
            'code'      => '',
            'statename' => $this->yystack[$this->getIndex() + -2]->minor
        ];
    }

    /**
     * @return void
     */
    protected function yy_r19(): void
    {
        $this->_retvalue   = $this->yystack[$this->getIndex() + -3]->minor;
        $this->_retvalue[] = [
            'rules'     => $this->yystack[$this->getIndex() + -2]->minor,
            'code'      => $this->yystack[$this->getIndex() + 0]->minor,
            'statename' => ''
        ];
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function yy_r20(): void
    {
        if ($this->yystack[$this->getIndex() + -4]->minor != 'statename') {
            throw new Exception('Error: only %statename processing instruction ' .
                'is allowed in rule sections (found ' . $this->yystack[$this->getIndex() + -4]->minor . ').');
        }

        $this->_retvalue   = $this->yystack[$this->getIndex() + -5]->minor;
        $this->_retvalue[] = [
            'rules'     => $this->yystack[$this->getIndex() + -2]->minor,
            'code'      => $this->yystack[$this->getIndex()]->minor,
            'statename' => $this->yystack[$this->getIndex() + -3]->minor
        ];
    }

    /**
     * @return void
     */
    protected function yy_r21(): void
    {
        $this->_retvalue     = $this->yystack[$this->getIndex() + -1]->minor;
        $this->_patternIndex = 1;
    }

    /**
     * @return void
     */
    protected function yy_r22(): void
    {
        $name                                         = $this->yystack[$this->getIndex() + -1]->minor[1];
        $this->yystack[$this->getIndex() + -1]->minor = $this->yystack[$this->getIndex() + -1]->minor[0];
        $this->yystack[$this->getIndex() + -1]->minor = $this->_validatePattern($this->yystack[$this->getIndex() + -1]->minor);
        $this->_patternIndex                          += $this->yystack[$this->getIndex() + -1]->minor['subpatterns'] + 1;

        if (@preg_match('/' . str_replace('/', '\\/', $this->yystack[$this->getIndex() + -1]->minor['pattern']) . '/', '')) {
            $this->error('Rule "' . $name . '" can match the empty string, this will break yying');
        }

        $this->_retvalue = [
            [
                'pattern'     => str_replace('/', '\\/', $this->yystack[$this->getIndex() + -1]->minor->string),
                'code'        => $this->yystack[$this->getIndex()]->minor,
                'subpatterns' => $this->yystack[$this->getIndex() + -1]->minor['subpatterns']
            ]
        ];
    }

    /**
     * @return void
     */
    protected function yy_r23(): void
    {
        $this->_retvalue                              = $this->yystack[$this->getIndex() + -2]->minor;
        $name                                         = $this->yystack[$this->getIndex() + -1]->minor[1];
        $this->yystack[$this->getIndex() + -1]->minor = $this->yystack[$this->getIndex() + -1]->minor[0];
        $this->yystack[$this->getIndex() + -1]->minor = $this->_validatePattern($this->yystack[$this->getIndex() + -1]->minor);
        $this->_patternIndex                          += $this->yystack[$this->getIndex() + -1]->minor['subpatterns'] + 1;

        if (@preg_match('/' . str_replace('/', '\\/', $this->yystack[$this->getIndex() + -1]->minor['pattern']) . '/', '')) {
            $this->error('Rule "' . $name . '" can match the empty string, this will break yying');
        }

        $this->_retvalue[] = [
            'pattern'     => str_replace('/', '\\/', $this->yystack[$this->getIndex() + -1]->minor->string),
            'code'        => $this->yystack[$this->getIndex()]->minor,
            'subpatterns' => $this->yystack[$this->getIndex() + -1]->minor['subpatterns']
        ];
    }

    /**
     * @return void
     */
    protected function yy_r24(): void
    {
        $this->_retvalue = [
            preg_quote($this->yystack[$this->getIndex()]->minor, '/'),
            $this->yystack[$this->getIndex()]->minor
        ];
    }

    /**
     * @return void
     */
    protected function yy_r25(): void
    {
        $this->_retvalue = [
            $this->makeCaseInsensitive(preg_quote($this->yystack[$this->getIndex()]->minor, '/')),
            $this->yystack[$this->getIndex()]->minor
        ];
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function yy_r26(): void
    {
        if (!isset($this->patterns[$this->yystack[$this->getIndex()]->minor])) {
            $this->error('Undefined pattern "' . $this->yystack[$this->getIndex()]->minor . '" used in rules');
            throw new Exception('Undefined pattern "' . $this->yystack[$this->getIndex()]->minor . '" used in rules');
        }

        $this->_retvalue = [
            $this->patterns[$this->yystack[$this->getIndex()]->minor],
            $this->yystack[$this->getIndex()]->minor
        ];
    }

    /**
     * @return void
     */
    protected function yy_r27(): void
    {
        $this->_retvalue = [
            $this->yystack[$this->getIndex() + -1]->minor[0] . preg_quote($this->yystack[$this->getIndex() + 0]->minor, '/'),
            $this->yystack[$this->getIndex() + -1]->minor[1] . ' ' . $this->yystack[$this->getIndex() + 0]->minor
        ];
    }

    /**
     * @return void
     */
    protected function yy_r28(): void
    {
        $this->_retvalue = [
            $this->yystack[$this->getIndex() + -1]->minor[0] . $this->makeCaseInsensitive(preg_quote($this->yystack[$this->getIndex()]->minor, '/')),
            $this->yystack[$this->getIndex() + -1]->minor[1] . ' ' . $this->yystack[$this->getIndex()]->minor
        ];
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function yy_r29(): void
    {
        if (!isset($this->patterns[$this->yystack[$this->getIndex()]->minor])) {
            $this->error('Undefined pattern "' . $this->yystack[$this->getIndex()]->minor . '" used in rules');
            throw new Exception('Undefined pattern "' . $this->yystack[$this->getIndex()]->minor . '" used in rules');
        }

        $this->_retvalue = [
            $this->yystack[$this->getIndex() + -1]->minor[0] . $this->patterns[$this->yystack[$this->getIndex()]->minor],
            $this->yystack[$this->getIndex() + -1]->minor[1] . ' ' . $this->yystack[$this->getIndex()]->minor
        ];
    }

    /**
     * @return void
     */
    protected function yy_r30(): void
    {
        $this->_retvalue = preg_quote($this->yystack[$this->getIndex()]->minor, '/');
    }

    /**
     * @return void
     */
    protected function yy_r31(): void
    {
        $this->_retvalue = $this->makeCaseInsensitive(preg_quote($this->yystack[$this->getIndex()]->minor, '/'));
    }

    /**
     * @return void
     */
    protected function yy_r32(): void
    {
        // increment internal sub-pattern counter
        // adjust back-references in pattern based on previous pattern
        $test                = $this->_validatePattern($this->yystack[$this->getIndex()]->minor, true);
        $this->_patternIndex += $test['subpatterns'];
        $this->_retvalue     = $test['pattern'];
    }

    /**
     * @return void
     */
    protected function yy_r33(): void
    {
        $this->_retvalue = $this->yystack[$this->getIndex() + -1]->minor . preg_quote($this->yystack[$this->getIndex()]->minor, '/');
    }

    /**
     * @return void
     */
    protected function yy_r34(): void
    {
        $this->_retvalue = $this->yystack[$this->getIndex() + -1]->minor
            . $this->makeCaseInsensitive(preg_quote($this->yystack[$this->getIndex()]->minor, '/'));
    }

    /**
     * @return void
     */
    protected function yy_r35(): void
    {
        // increment internal sub-pattern counter
        // adjust back-references in pattern based on previous pattern
        $test                = $this->_validatePattern($this->yystack[$this->getIndex()]->minor, true);
        $this->_patternIndex += $test['subpatterns'];
        $this->_retvalue     = $this->yystack[$this->getIndex() + -1]->minor . $test['pattern'];
    }

    /**
     * Perform a reduce action and the shift that must immediately
     * follow the reduce.
     *
     * For a rule such as:
     *
     * <pre>
     * A ::= B blah C. { dosomething(); }
     * </pre>
     *
     * This function will first call the action, if any, ("dosomething();" in our
     * example), and then it will pop three states from the stack,
     * one for each entry on the right-hand side of the expression
     * (B, blah, and C in our example rule), and then push the result of the action
     * back on to the stack with the resulting state reduced to (as described in the .out
     * file)
     *
     * @param int $yyruleno Number of the rule by which to reduce
     */
    public function yy_reduce(int $yyruleno): void
    {
        //int $yygoto;                     /* The next state */
        //int $yyact;                      /* The next action */
        //mixed $yygotominor;        /* The LHS of the rule reduced */
        //StackEntry $yymsp;            /* The top of the parser's stack */
        //int $yysize;                     /* Amount to pop the stack */
        $yymsp = $this->yystack[$this->yyidx];
        if (self::$yyTraceFILE && $yyruleno >= 0
            && $yyruleno < count(self::$yyRuleName)) {
            fprintf(self::$yyTraceFILE, "%sReduce (%d) [%s].\n",
                self::$yyTracePrompt, $yyruleno,
                self::$yyRuleName[$yyruleno]);
        }

        $this->_retvalue = $yy_lefthand_side = null;
        if (array_key_exists($yyruleno, self::$yyReduceMap)) {
            // call the action
            $this->_retvalue = null;
            $this->{'yy_r' . self::$yyReduceMap[$yyruleno]}();
            $yy_lefthand_side = $this->_retvalue;
        }

        $yygoto      = self::$yyRuleInfo[$yyruleno]['lhs'];
        $yysize      = self::$yyRuleInfo[$yyruleno]['rhs'];
        $this->yyidx -= $yysize;

        for ($i = $yysize; $i; $i--) {
            // pop all of the right-hand side parameters
            array_pop($this->yystack);
        }

        $yyact = $this->yy_find_reduce_action($this->yystack[$this->yyidx]->stateno, $yygoto);
        if ($yyact < self::YYNSTATE) {
            /* If we are not debugging and the reduce action popped at least
            ** one element off the stack, then we can push the new element back
            ** onto the stack here, and skip the stack overflow test in yy_shift().
            ** That gives a significant speed improvement. */
            if (!self::$yyTraceFILE && $yysize) {
                $this->yyidx++;
                $x                           = new StackEntry();
                $x->stateno                  = $yyact;
                $x->major                    = $yygoto;
                $x->minor                    = $yy_lefthand_side;
                $this->yystack[$this->yyidx] = $x;
            } else {
                $this->yy_shift($yyact, $yygoto, $yy_lefthand_side);
            }
        } elseif ($yyact == self::YYNSTATE + self::YYNRULE + 1) {
            $this->yy_accept();
        }
    }

    /**
     * The following code executes when the parse fails
     *
     * Code from %parse_fail is inserted here
     */
    public function yy_parse_failed(): void
    {
        if (self::$yyTraceFILE) {
            fprintf(self::$yyTraceFILE, "%sFail!\n", self::$yyTracePrompt);
        }

        while ($this->yyidx >= 0) {
            $this->yy_pop_parser_stack();
        }
        /* Here code is inserted which will be executed whenever the
        ** parser fails */
    }

    /**
     * The following code executes when a syntax error first occurs.
     *
     * %syntax_error code is inserted here
     *
     * @param int   $yymajor The major type of the error token
     * @param mixed $TOKEN   The minor type of the error token
     *
     * @throws Exception
     */
    public function yy_syntax_error(int $yymajor, mixed $TOKEN): void
    {
        echo "Syntax Error on line " . $this->lex->line . ": token '" .
            $this->lex->value . "' while parsing rule:";

        foreach ($this->yystack as $entry) {
            echo $this->tokenName($entry->major) . ' ';
        }

        $expect = [];
        foreach ($this->yy_get_expected_tokens($yymajor) as $token) {
            $expect[] = self::$yyTokenName[$token];
        }

        throw new Exception('Unexpected ' . $this->tokenName($yymajor) . '(' . $TOKEN
            . '), expected one of: ' . implode(',', $expect));
    }

    /**
     * The following is executed when the parser accepts
     *
     * %parse_accept code is inserted here
     */
    public function yy_accept(): void
    {
        if (self::$yyTraceFILE) {
            fprintf(self::$yyTraceFILE, "%sAccept!\n", self::$yyTracePrompt);
        }

        while ($this->yyidx >= 0) {
            $stack = $this->yy_pop_parser_stack();
        }
        /* Here code is inserted which will be executed whenever the
        ** parser accepts */
    }

    /**
     * The main parser program.
     *
     * The first argument is the major token number.  The second is
     * the token value string as scanned from the input.
     *
     * @param int   $yymajor      the token number
     * @param mixed $yytokenvalue the token value
     */
    public function doParse(int $yymajor, mixed $yytokenvalue): void
    {
//        $yyact;            /* The parser action. */
//        $yyendofinput;     /* True if we are at the end of input */
        $yyerrorhit = 0;   /* True if yymajor has invoked an error */

        /* (re)initialize the parser, if necessary */
        if ($this->yyidx === null || $this->yyidx < 0) {
            /* if ($yymajor == 0) return; // not sure why this was here... */
            $this->yyidx     = 0;
            $this->yyerrcnt  = -1;
            $x               = new StackEntry();
            $x->stateno      = 0;
            $x->major        = 0;
            $this->yystack   = [];
            $this->yystack[] = $x;
        }
        $yyendofinput = ($yymajor == 0);

        if (self::$yyTraceFILE) {
            fprintf(self::$yyTraceFILE, "%sInput %s\n",
                self::$yyTracePrompt, self::$yyTokenName[$yymajor]);
        }

        do {
            $yyact = $this->yy_find_shift_action($yymajor);
            if ($yymajor < self::YYERRORSYMBOL &&
                !$this->yy_is_expected_token($yymajor)) {
                // force a syntax error
                $yyact = self::YY_ERROR_ACTION;
            }
            if ($yyact < self::YYNSTATE) {
                $this->yy_shift($yyact, $yymajor, $yytokenvalue);
                $this->yyerrcnt--;
                if ($yyendofinput && $this->yyidx >= 0) {
                    $yymajor = 0;
                } else {
                    $yymajor = self::YYNOCODE;
                }
            } elseif ($yyact < self::YYNSTATE + self::YYNRULE) {
                $this->yy_reduce($yyact - self::YYNSTATE);
            } elseif ($yyact == self::YY_ERROR_ACTION) {
                if (self::$yyTraceFILE) {
                    fprintf(self::$yyTraceFILE, "%sSyntax Error!\n",
                        self::$yyTracePrompt);
                }
                if (self::YYERRORSYMBOL) {
                    /* A syntax error has occurred.
                    ** The response to an error depends upon whether or not the
                    ** grammar defines an error token "ERROR".
                    **
                    ** This is what we do if the grammar does define ERROR:
                    **
                    **  * Call the %syntax_error function.
                    **
                    **  * Begin popping the stack until we enter a state where
                    **    it is legal to shift the error symbol, then shift
                    **    the error symbol.
                    **
                    **  * Set the error count to three.
                    **
                    **  * Begin accepting and shifting new tokens.  No new error
                    **    processing will occur until three tokens have been
                    **    shifted successfully.
                    **
                    */
                    if ($this->yyerrcnt < 0) {
                        $this->yy_syntax_error($yymajor, $yytokenvalue);
                    }
                    $yymx = $this->yystack[$this->yyidx]->major;
                    if ($yymx == self::YYERRORSYMBOL || $yyerrorhit) {
                        if (self::$yyTraceFILE) {
                            fprintf(self::$yyTraceFILE, "%sDiscard input token %s\n",
                                self::$yyTracePrompt, self::$yyTokenName[$yymajor]);
                        }
                        //self::yy_destructor($yymajor, $yytokenvalue);
                        $yymajor = self::YYNOCODE;
                    } else {
                        while ($this->yyidx >= 0 &&
                            $yymx != self::YYERRORSYMBOL &&
                            ($yyact = $this->yy_find_shift_action(self::YYERRORSYMBOL)) >= self::YYNSTATE
                        ) {
                            $this->yy_pop_parser_stack();
                        }
                        if ($this->yyidx < 0 || $yymajor == 0) {
                            //self::yy_destructor($yymajor, $yytokenvalue);
                            $this->yy_parse_failed();
                            $yymajor = self::YYNOCODE;
                        } elseif ($yymx != self::YYERRORSYMBOL) {
                            $u2 = 0;
                            $this->yy_shift($yyact, self::YYERRORSYMBOL, $u2);
                        }
                    }
                    $this->yyerrcnt = 3;
                    $yyerrorhit     = 1;
                } else {
                    /* YYERRORSYMBOL is not defined */
                    /* This is what we do if the grammar does not define ERROR:
                    **
                    **  * Report an error message, and throw away the input token.
                    **
                    **  * If the input token is $, then fail the parse.
                    **
                    ** As before, subsequent error messages are suppressed until
                    ** three input tokens have been successfully shifted.
                    */
                    if ($this->yyerrcnt <= 0) {
                        $this->yy_syntax_error($yymajor, $yytokenvalue);
                    }
                    $this->yyerrcnt = 3;
                    //self::yy_destructor($yymajor, $yytokenvalue);
                    if ($yyendofinput) {
                        $this->yy_parse_failed();
                    }
                    $yymajor = self::YYNOCODE;
                }
            } else {
                $this->yy_accept();
                $yymajor = self::YYNOCODE;
            }
        } while ($yymajor != self::YYNOCODE && $this->yyidx >= 0);
    }

    /**
     * Returns the code for the core methods.
     *
     * @return string
     */
    private function getCoreMethods(): string
    {
        return <<<CODE
    private int \$_yy_state = 1;
    private array \$_yy_stack = [];

    /**
     * Run the lexer.
     * 
     * @return mixed
     */
    public function run(): mixed
    {
        return \$this->{'lex' . \$this->_yy_state}();
    }

    /**
     * Push the state.
     * 
     * @param int \$state
     *
     * @return void
     */
    public function pushState(int \$state): void
    {
        array_push(\$this->_yy_stack, \$this->_yy_state);
        \$this->_yy_state = \$state;
    }

    /**
     * Pop the state.
     * 
     * @return void
     */
    public function popState(): void
    {
        \$this->_yy_state = array_pop(\$this->_yy_stack);
    }

    /**
     * Start the lexer from state.
     *
     * @param int \$state
     *
     * @return void
     */
    public function begin(int \$state): void
    {
        \$this->_yy_state = \$state;
    }

CODE;
    }
}
