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

namespace Phalcon\Parser\Annotations;

use Phalcon\Parser\Annotations\Parser\ParserStatus;
use Phalcon\Parser\Enum;
use Phalcon\Parser\Parser\StackEntry;

/**
 * Annotation parser.
 *
 * @package Phalcon\Parser\Annotations
 */
class Parser
{
    /**
     * Parser status object
     *
     * @var ParserStatus
     */
    protected ParserStatus $status;

    /**
     * Helper object
     *
     * @var Helper
     */
    protected Helper $helper;

    public mixed $retvalue = null;

    /**
     * Parser constructor.
     *
     * @param ParserStatus $status
     */
    public function __construct(ParserStatus $status)
    {
        $this->status = $status;
        $this->helper = new Helper();
    }

    public int $yyidx    = -1;                    /* Index of top element in stack */
    public int $yyerrcnt = 0;                 /* Shifts left before out of the error */
    // phannotARG_SDECL                /* A place to hold %extra_argument */
    public array $yystack = [
        /* of YYSTACKDEPTH elements */
    ];  /* The parser's stack */

    public mixed   $yyTraceFILE   = null;
    public ?string $yyTracePrompt = null;


    /* Next is all token values, in a form suitable for use by makeheaders.
    ** This section will be null unless lemon is run with the -m switch.
    */
    /*
    ** These constants (all generated automatically by the parser generator)
    ** specify the various kinds of tokens (terminals) that the parser
    ** understands.
    **
    ** Each symbol here is a terminal symbol in the grammar.
    */
    const PHANNOT_COMMA             = 1;
    const PHANNOT_AT                = 2;
    const PHANNOT_IDENTIFIER        = 3;
    const PHANNOT_PARENTHESES_OPEN  = 4;
    const PHANNOT_PARENTHESES_CLOSE = 5;
    const PHANNOT_STRING            = 6;
    const PHANNOT_EQUALS            = 7;
    const PHANNOT_COLON             = 8;
    const PHANNOT_INTEGER           = 9;
    const PHANNOT_DOUBLE            = 10;
    const PHANNOT_NULL              = 11;
    const PHANNOT_FALSE             = 12;
    const PHANNOT_TRUE              = 13;
    const PHANNOT_BRACKET_OPEN      = 14;
    const PHANNOT_BRACKET_CLOSE     = 15;
    const PHANNOT_SBRACKET_OPEN     = 16;
    const PHANNOT_SBRACKET_CLOSE    = 17;
    /* The next thing included is series of defines which control
    ** various aspects of the generated parser.
    **    YYCODETYPE         is the data type used for storing terminal
    **                       and nonterminal numbers.  "unsigned char" is
    **                       used if there are fewer than 250 terminals
    **                       and nonterminals.  "int" is used otherwise.
    **    YYNOCODE           is a number of type YYCODETYPE which corresponds
    **                       to no legal terminal or nonterminal number.  This
    **                       number is used to fill in empty slots of the hash
    **                       table.
    **    YYFALLBACK         If defined, this indicates that one or more tokens
    **                       have fall-back values which should be used if the
    **                       original value of the token will not parse.
    **    YYACTIONTYPE       is the data type used for storing terminal
    **                       and nonterminal numbers.  "unsigned char" is
    **                       used if there are fewer than 250 rules and
    **                       states combined.  "int" is used otherwise.
    **    phannotTOKENTYPE     is the data type used for minor tokens given
    **                       directly to the parser from the tokenizer.
    **    YYMINORTYPE        is the data type used for all minor tokens.
    **                       This is typically a union of many types, one of
    **                       which is phannotTOKENTYPE.  The entry in the union
    **                       for base tokens is called "yy0".
    **    YYSTACKDEPTH       is the maximum depth of the parser's stack.
    **    phannotARG_SDECL     A static variable declaration for the %extra_argument
    **    phannotARG_PDECL     A parameter declaration for the %extra_argument
    **    phannotARG_STORE     Code to store %extra_argument into yypParser
    **    phannotARG_FETCH     Code to extract %extra_argument from yypParser
    **    YYNSTATE           the combined number of states.
    **    YYNRULE            the number of rules in the grammar
    **    YYERRORSYMBOL      is the code number of the error symbol.  If not
    **                       defined, then do no error processing.
    */
    const YYNOCODE = 28;
#define phannotTOKENTYPE void*
    const YYSTACKDEPTH  = 100;
    const YYNSTATE      = 40;
    const YYNRULE       = 25;
    const YYERRORSYMBOL = 18;

    /* since we cant use expressions to initialize these as class
     * constants, we do so during parser init. */
    public mixed $YY_NO_ACTION     = null;
    public mixed $YY_ACCEPT_ACTION = null;
    public mixed $YY_ERROR_ACTION  = null;

    /* Next are that tables used to determine what action to take based on the
    ** current state and lookahead token.  These tables are used to implement
    ** functions that take a state number and lookahead value and return an
    ** action integer.
    **
    ** Suppose the action integer is N.  Then the action is determined as
    ** follows
    **
    **   0 <= N < YYNSTATE                  Shift N.  That is, push the lookahead
    **                                      token onto the stack and goto state N.
    **
    **   YYNSTATE <= N < YYNSTATE+YYNRULE   Reduce by rule N-YYNSTATE.
    **
    **   N == YYNSTATE+YYNRULE              A syntax error has occurred.
    **
    **   N == YYNSTATE+YYNRULE+1            The parser accepts its input.
    **
    **   N == YYNSTATE+YYNRULE+2            No such action.  Denotes unused
    **                                      slots in the yy_action[] table.
    **
    ** The action table is constructed as a single large table named yy_action[].
    ** Given state S and lookahead X, the action is computed as
    **
    **      yy_action[ yy_shift_ofst[S] + X ]
    **
    ** If the index value yy_shift_ofst[S]+X is out of range or if the value
    ** yy_lookahead[yy_shift_ofst[S]+X] is not equal to X or if yy_shift_ofst[S]
    ** is equal to YY_SHIFT_USE_DFLT, it means that the action is not in the table
    ** and that yy_default[S] should be used instead.
    **
    ** The formula above is for computing the action when the lookahead is
    ** a terminal symbol.  If the lookahead is a non-terminal (as occurs after
    ** a reduce action) then the yy_reduce_ofst[] array is used in place of
    ** the yy_shift_ofst[] array and YY_REDUCE_USE_DFLT is used in place of
    ** YY_SHIFT_USE_DFLT.
    **
    ** The following are the tables generated in this section:
    **
    **  yy_action[]        A single table containing all actions.
    **  yy_lookahead[]     A table containing the lookahead for each entry in
    **                     yy_action.  Used to detect hash collisions.
    **  yy_shift_ofst[]    For each state, the offset into yy_action for
    **                     shifting terminals.
    **  yy_reduce_ofst[]   For each state, the offset into yy_action for
    **                     shifting non-terminals after a reduce.
    **  yy_default[]       Default action for each state.
    */
    public static array $yy_action    = [
        /*     0 */
        15, 13, 23, 38, 11, 22, 24, 26, 28, 29,
        /*    10 */
        30, 31, 2, 15, 3, 15, 13, 23, 18, 11,
        /*    20 */
        34, 24, 26, 28, 29, 30, 31, 2, 16, 3,
        /*    30 */
        15, 25, 23, 1, 27, 36, 24, 26, 28, 29,
        /*    40 */
        30, 31, 2, 54, 3, 23, 10, 33, 21, 24,
        /*    50 */
        54, 54, 23, 12, 33, 21, 24, 23, 14, 33,
        /*    60 */
        21, 24, 66, 17, 9, 39, 4, 23, 4, 20,
        /*    70 */
        21, 24, 23, 4, 54, 37, 24, 19, 5, 8,
        /*    80 */
        32, 6, 7, 54, 35,
    ];
    public static array $yy_lookahead = [
        /*     0 */
        2, 3, 22, 5, 6, 25, 26, 9, 10, 11,
        /*    10 */
        12, 13, 14, 2, 16, 2, 3, 22, 22, 6,
        /*    20 */
        25, 26, 9, 10, 11, 12, 13, 14, 3, 16,
        /*    30 */
        2, 3, 22, 4, 6, 25, 26, 9, 10, 11,
        /*    40 */
        12, 13, 14, 27, 16, 22, 23, 24, 25, 26,
        /*    50 */
        27, 27, 22, 23, 24, 25, 26, 22, 23, 24,
        /*    60 */
        25, 26, 19, 20, 21, 22, 1, 22, 1, 24,
        /*    70 */
        25, 26, 22, 1, 27, 25, 26, 5, 7, 8,
        /*    80 */
        15, 7, 8, 27, 17,
    ];
    const YY_SHIFT_USE_DFLT = -3;
    const YY_SHIFT_MAX      = 16;
    public static array $yy_shift_ofst = [
        /*     0 */
        11, -2, 13, 13, 13, 28, 28, 28, 28, 11,
        /*    10 */
        72, 71, 65, 74, 67, 25, 29,
    ];
    const YY_REDUCE_USE_DFLT = -21;
    const YY_REDUCE_MAX      = 9;
    public static array $yy_reduce_ofst = [
        /*     0 */
        43, 23, 30, 35, 45, -20, -5, 10, 50, -4,
    ];
    public static array $yy_default     = [
        /*     0 */
        65, 65, 65, 65, 65, 65, 65, 65, 65, 41,
        /*    10 */
        65, 58, 65, 56, 65, 65, 46, 40, 42, 44,
        /*    20 */
        47, 49, 50, 54, 55, 56, 57, 58, 59, 60,
        /*    30 */
        61, 62, 63, 48, 52, 64, 53, 51, 45, 43,
    ];

    /* The next table maps tokens into fallback tokens.  If a construct
    ** like the following:
    **
    **      %fallback ID X Y Z.
    **
    ** appears in the grammer, then ID becomes a fallback token for X, Y,
    ** and Z.  Whenever one of the tokens X, Y, or Z is input to the parser
    ** but it does not parse, the type of the token is changed to ID and
    ** the parse is retried before an error is thrown.
    */
    public static array $yyFallback = [
    ];

    /*
    ** Turn parser tracing on by giving a stream to which to write the trace
    ** and a prompt to preface each trace message.  Tracing is turned off
    ** by making either argument NULL
    **
    ** Inputs:
    ** <ul>
    ** <li> A FILE* to which trace output should be written.
    **      If NULL, then tracing is turned off.
    ** <li> A prefix string written at the beginning of every
    **      line of trace output.  If NULL, then tracing is
    **      turned off.
    ** </ul>
    **
    ** Outputs:
    ** None.
    */
    public function phannotTrace(mixed $TraceFILE, ?string $zTracePrompt): void
    {
        $this->yyTraceFILE   = $TraceFILE;
        $this->yyTracePrompt = $zTracePrompt;
        if ($this->yyTraceFILE === null) $this->yyTracePrompt = null;
        else if ($this->yyTracePrompt === null) $this->yyTraceFILE = null;
    }

    /* For tracing shifts, the names of all terminals and nonterminals
    ** are required.  The following table supplies these names */
    public static array $yyTokenName = [
        '$', 'COMMA', 'AT', 'IDENTIFIER',
        'PARENTHESES_OPEN', 'PARENTHESES_CLOSE', 'STRING', 'EQUALS',
        'COLON', 'INTEGER', 'DOUBLE', 'NULL',
        'FALSE', 'TRUE', 'BRACKET_OPEN', 'BRACKET_CLOSE',
        'SBRACKET_OPEN', 'SBRACKET_CLOSE', 'error', 'program',
        'annotation_language', 'annotation_list', 'annotation', 'argument_list',
        'argument_item', 'expr', 'array',
    ];

    /* For tracing reduce actions, the names of all rules are required.
    */
    public static array $yyRuleName = [
        /*   0 */
        "program ::= annotation_language",
        /*   1 */
        "annotation_language ::= annotation_list",
        /*   2 */
        "annotation_list ::= annotation_list annotation",
        /*   3 */
        "annotation_list ::= annotation",
        /*   4 */
        "annotation ::= AT IDENTIFIER PARENTHESES_OPEN argument_list PARENTHESES_CLOSE",
        /*   5 */
        "annotation ::= AT IDENTIFIER PARENTHESES_OPEN PARENTHESES_CLOSE",
        /*   6 */
        "annotation ::= AT IDENTIFIER",
        /*   7 */
        "argument_list ::= argument_list COMMA argument_item",
        /*   8 */
        "argument_list ::= argument_item",
        /*   9 */
        "argument_item ::= expr",
        /*  10 */
        "argument_item ::= STRING EQUALS expr",
        /*  11 */
        "argument_item ::= STRING COLON expr",
        /*  12 */
        "argument_item ::= IDENTIFIER EQUALS expr",
        /*  13 */
        "argument_item ::= IDENTIFIER COLON expr",
        /*  14 */
        "expr ::= annotation",
        /*  15 */
        "expr ::= array",
        /*  16 */
        "expr ::= IDENTIFIER",
        /*  17 */
        "expr ::= INTEGER",
        /*  18 */
        "expr ::= STRING",
        /*  19 */
        "expr ::= DOUBLE",
        /*  20 */
        "expr ::= NULL",
        /*  21 */
        "expr ::= FALSE",
        /*  22 */
        "expr ::= TRUE",
        /*  23 */
        "array ::= BRACKET_OPEN argument_list BRACKET_CLOSE",
        /*  24 */
        "array ::= SBRACKET_OPEN argument_list SBRACKET_CLOSE",
    ];

    /*
    ** This function returns the symbolic name associated with a token
    ** value.
    */
    public function tokenName(int $tokenType): string
    {
        if (isset(self::$yyTokenName[$tokenType]))
            return self::$yyTokenName[$tokenType];

        return "Unknown";
    }

    /* The following function deletes the value associated with a
    ** symbol.  The symbol can be either a terminal or nonterminal.
    ** "yymajor" is the symbol code, and "yypminor" is a pointer to
    ** the value.
    */
    private function yy_destructor(mixed $yymajor, mixed $yypminor): void
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
            case 20:
#line 31 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.lemon"
                {
                    if (isset($yypminor->yy0)) $yypminor->yy0 = null;
                }
#line 299 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.php"
                break;
            case 21:
            case 22:
            case 23:
            case 24:
            case 25:
#line 39 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.lemon"
                {
                    //zval_ptr_dtor(&(yypminor->yy0));
                }
#line 310 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.php"
                break;
            default:
                break;   /* If no destructor action specified: do nothing */
        }
    }

    /*
    ** Pop the parser's stack once.
    **
    ** If there is a destructor routine associated with the token which
    ** is popped from the stack, then call it.
    **
    ** Return the major token number for the symbol popped.
    */
    private function yy_pop_parser_stack(): int
    {
        if ($this->yyidx < 0) return 0;
        $yytos = $this->yystack[$this->yyidx];
        if ($this->yyTraceFILE) {
            fprintf($this->yyTraceFILE, "%sPopping %s\n",
                $this->yyTracePrompt,
                self::$yyTokenName[$yytos->major]);
        }
        $this->yy_destructor($yytos->major, $yytos->minor);
        unset($this->yystack[$this->yyidx]);
        $this->yyidx--;

        return $yytos->major;
    }

    /*
    ** Deallocate and destroy a parser.  Destructors are all called for
    ** all stack elements before shutting the parser down.
    **
    ** Inputs:
    ** <ul>
    ** <li>  A pointer to the parser.  This should be a pointer
    **       obtained from phannotAlloc.
    ** <li>  A pointer to a function used to reclaim memory obtained
    **       from malloc.
    ** </ul>
    */
    public function __destruct()
    {
        while ($this->yyidx >= 0)
            $this->yy_pop_parser_stack();
    }

    /*
    ** Find the appropriate action for a parser given the terminal
    ** look-ahead token iLookAhead.
    **
    ** If the look-ahead token is YYNOCODE, then check to see if the action is
    ** independent of the look-ahead.  If it is, return the action, otherwise
    ** return YY_NO_ACTION.
    */
    private function yy_find_shift_action(
        $iLookAhead     /* The look-ahead token */
    ): mixed
    {
        $i       = 0;
        $stateno = $this->yystack[$this->yyidx]->stateno;

        if ($stateno > self::YY_SHIFT_MAX ||
            ($i = self::$yy_shift_ofst[$stateno]) == self::YY_SHIFT_USE_DFLT) {
            return self::$yy_default[$stateno];
        }
        if ($iLookAhead == self::YYNOCODE) {
            return $this->YY_NO_ACTION;
        }
        $i += $iLookAhead;
        if ($i < 0 || $i >= count(self::$yy_action) || self::$yy_lookahead[$i] != $iLookAhead) {
            if ($iLookAhead > 0) {
                if (isset(self::$yyFallback[$iLookAhead]) &&
                    ($iFallback = self::$yyFallback[$iLookAhead]) != 0) {
                    if ($this->yyTraceFILE) {
                        fprintf($this->yyTraceFILE, "%sFALLBACK %s => %s\n",
                            $this->yyTracePrompt, self::$yyTokenName[$iLookAhead],
                            self::$yyTokenName[$iFallback]);
                    }

                    return $this->yy_find_shift_action($iFallback);
                }
                if (defined(__CLASS__ . '::YYWILDCARD')) {
                    $j = $i - $iLookAhead + self::YYWILDCARD;
                    if ($j >= 0 && $j < count(self::$yy_action) && self::$yy_lookahead[$j] == self::YYWILDCARD) {
                        if ($this->yyTraceFILE) {
                            fprintf($this->yyTraceFILE, "%sWILDCARD %s => %s\n",
                                $this->yyTracePrompt, self::$yyTokenName[$iLookAhead],
                                self::$yyTokenName[self::YYWILDCARD]);
                        }

                        return self::$yy_action[$j];
                    }
                }
            }

            return self::$yy_default[$stateno];
        }

        return self::$yy_action[$i];
    }

    /*
    ** Find the appropriate action for a parser given the non-terminal
    ** look-ahead token iLookAhead.
    **
    ** If the look-ahead token is YYNOCODE, then check to see if the action is
    ** independent of the look-ahead.  If it is, return the action, otherwise
    ** return YY_NO_ACTION.
    */
    private function yy_find_reduce_action(
        int   $stateno,              /* Current state number */
        mixed $iLookAhead     /* The look-ahead token */
    ): mixed
    {
        $i = 0;

        if ($stateno > self::YY_REDUCE_MAX ||
            ($i = self::$yy_reduce_ofst[$stateno]) == self::YY_REDUCE_USE_DFLT) {
            return self::$yy_default[$stateno];
        }
        if ($iLookAhead == self::YYNOCODE) {
            return $this->YY_NO_ACTION;
        }
        $i += $iLookAhead;
        if ($i < 0 || $i >= count(self::$yy_action) || self::$yy_lookahead[$i] != $iLookAhead) {
            return self::$yy_default[$stateno];
        } else {
            return self::$yy_action[$i];
        }
    }

    /*
    ** Perform a shift action.
    */
    private function yy_shift(
        mixed $yyNewState,               /* The new state to shift in */
        mixed $yyMajor,                  /* The major token to shift in */
        mixed $yypMinor         /* Pointer ot the minor token to shift in */
    ): void
    {
        $this->yyidx++;
        if (isset($this->yystack[$this->yyidx])) {
            $yytos = $this->yystack[$this->yyidx];
        } else {
            $yytos                       = new StackEntry();
            $this->yystack[$this->yyidx] = $yytos;
        }
        $yytos->stateno = $yyNewState;
        $yytos->major   = $yyMajor;
        $yytos->minor   = $yypMinor;
        if ($this->yyTraceFILE) {
            fprintf($this->yyTraceFILE, "%sShift %d\n", $this->yyTracePrompt, $yyNewState);
            fprintf($this->yyTraceFILE, "%sStack:", $this->yyTracePrompt);
            for ($i = 1; $i <= $this->yyidx; $i++) {
                $ent = $this->yystack[$i];
                fprintf($this->yyTraceFILE, " %s", self::$yyTokenName[$ent->major]);
            }
            fprintf($this->yyTraceFILE, "\n");
        }
    }

    private function __overflow_dead_code(): void
    {
        /* if the stack can overflow (it can't in the PHP implementation)
         * Then the following code would be emitted */
    }

    /* The following table contains information about every rule that
    ** is used during the reduce.
    ** Rather than pollute memory with a large number of arrays,
    ** we store both data points in the same array, indexing by
    ** rule number * 2.
    static const struct {
      YYCODETYPE lhs;         // Symbol on the left-hand side of the rule
      unsigned char nrhs;     // Number of right-hand side symbols in the rule
    } yyRuleInfo[] = {
    */
    public static array $yyRuleInfo = [
        19, 1,
        20, 1,
        21, 2,
        21, 1,
        22, 5,
        22, 4,
        22, 2,
        23, 3,
        23, 1,
        24, 1,
        24, 3,
        24, 3,
        24, 3,
        24, 3,
        25, 1,
        25, 1,
        25, 1,
        25, 1,
        25, 1,
        25, 1,
        25, 1,
        25, 1,
        25, 1,
        26, 3,
        26, 3,
    ];

    /*
    ** Perform a reduce action and the shift that must immediately
    ** follow the reduce.
    */
    private function yy_reduce(
        int $yyruleno                 /* Number of the rule by which to reduce */
    ): void
    {
        $yygoto      = 0;                     /* The next state */
        $yyact       = 0;                      /* The next action */
        $yygotominor = null;        /* The LHS of the rule reduced */
        $yymsp       = null;            /* The top of the parser's stack */
        $yysize      = 0;                     /* Amount to pop the stack */

        $yymsp = $this->yystack[$this->yyidx];
        if ($this->yyTraceFILE && isset(self::$yyRuleName[$yyruleno])) {
            fprintf($this->yyTraceFILE, "%sReduce [%s].\n", $this->yyTracePrompt,
                self::$yyRuleName[$yyruleno]);
        }

        switch ($yyruleno) {
            /* Beginning here are the reduction cases.  A typical example
            ** follows:
            **   case 0:
            **  #line <lineno> <grammarfile>
            **     { ... }           // User supplied code
            **  #line <lineno> <thisfile>
            **     break;
            */
            case 0:
#line 27 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.lemon"
                {
                    $this->retvalue = $this->yystack[$this->yyidx + 0]->minor;
                }
#line 545 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.php"
                break;
            case 1:
            case 14:
            case 15:
#line 35 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.lemon"
                {
                    $yygotominor = $this->yystack[$this->yyidx + 0]->minor;
                }
#line 554 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.php"
                break;
            case 2:
#line 43 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.lemon"
                {
                    $this->helper->list($yygotominor, $this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor);
                }
#line 561 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.php"
                break;
            case 3:
            case 8:
#line 47 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.lemon"
                {
                    $this->helper->list($yygotominor, NULL, $this->yystack[$this->yyidx + 0]->minor);
                }
#line 569 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.php"
                break;
            case 4:
#line 55 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.lemon"
                {
                    $this->helper->annotation($yygotominor, $this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor, $this->status);
                }
#line 576 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.php"
                break;
            case 5:
#line 59 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.lemon"
                {
                    $this->helper->annotation($yygotominor, $this->yystack[$this->yyidx + -2]->minor, NULL, $this->status);
                }
#line 583 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.php"
                break;
            case 6:
#line 63 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.lemon"
                {
                    $this->helper->annotation($yygotominor, $this->yystack[$this->yyidx + 0]->minor, NULL, $this->status);
                }
#line 590 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.php"
                break;
            case 7:
#line 71 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.lemon"
                {
                    $this->helper->list($yygotominor, $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);
                }
#line 597 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.php"
                break;
            case 9:
#line 83 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.lemon"
                {
                    $this->helper->namedItem($yygotominor, NULL, $this->yystack[$this->yyidx + 0]->minor);
                }
#line 604 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.php"
                break;
            case 10:
            case 11:
            case 12:
            case 13:
#line 87 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.lemon"
                {
                    $this->helper->namedItem($yygotominor, $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);
                }
#line 614 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.php"
                break;
            case 16:
#line 115 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.lemon"
                {
                    $this->helper->literal($yygotominor, Enum::PHANNOT_T_IDENTIFIER, $this->yystack[$this->yyidx + 0]->minor);
                }
#line 621 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.php"
                break;
            case 17:
#line 119 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.lemon"
                {
                    $this->helper->literal($yygotominor, Enum::PHANNOT_T_INTEGER, $this->yystack[$this->yyidx + 0]->minor);
                }
#line 628 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.php"
                break;
            case 18:
#line 123 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.lemon"
                {
                    $this->helper->literal($yygotominor, Enum::PHANNOT_T_STRING, $this->yystack[$this->yyidx + 0]->minor);
                }
#line 635 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.php"
                break;
            case 19:
#line 127 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.lemon"
                {
                    $this->helper->literal($yygotominor, Enum::PHANNOT_T_DOUBLE, $this->yystack[$this->yyidx + 0]->minor);
                }
#line 642 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.php"
                break;
            case 20:
#line 131 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.lemon"
                {
                    $this->helper->literal($yygotominor, Enum::PHANNOT_T_NULL, NULL);
                }
#line 649 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.php"
                break;
            case 21:
#line 135 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.lemon"
                {
                    $this->helper->literal($yygotominor, Enum::PHANNOT_T_FALSE, NULL);
                }
#line 656 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.php"
                break;
            case 22:
#line 139 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.lemon"
                {
                    $this->helper->literal($yygotominor, Enum::PHANNOT_T_TRUE, NULL);
                }
#line 663 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.php"
                break;
            case 23:
            case 24:
#line 143 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.lemon"
                {
                    $this->helper->arrays($yygotominor, $this->yystack[$this->yyidx + -1]->minor);
                }
#line 671 "/home/phptux/Frameworks/Phalcon/phalcon5-php/resources/annotations/parser.php"
                break;
        };
        $yygoto = self::$yyRuleInfo[2 * $yyruleno];
        $yysize = self::$yyRuleInfo[(2 * $yyruleno) + 1];

        $state_for_reduce = $this->yystack[$this->yyidx - $yysize]->stateno;

        $this->yyidx -= $yysize;
        $yyact       = $this->yy_find_reduce_action($state_for_reduce, $yygoto);
        if ($yyact < self::YYNSTATE) {
            $this->yy_shift($yyact, $yygoto, $yygotominor);
        } else if ($yyact == self::YYNSTATE + self::YYNRULE + 1) {
            $this->yy_accept();
        }
    }

    /*
    ** The following code executes when the parse fails
    */
    private function yy_parse_failed(): void
    {
        if ($this->yyTraceFILE) {
            fprintf($this->yyTraceFILE, "%sFail!\n", $this->yyTracePrompt);
        }
        while ($this->yyidx >= 0) $this->yy_pop_parser_stack();
        /* Here code is inserted which will be executed whenever the
        ** parser fails */
    }

    /*
    ** The following code executes when a syntax error first occurs.
    */
    private function yy_syntax_error
    (
        $yymajor,                   /* The major type of the error token */
        $yyminor            /* The minor type of the error token */
    ): void
    {
        if ($this->status->getScannerState()->getStartLength() !== null) {
            $status    = $this->status;
            $tokenName = null;
            $activeToken = $this->status->getToken();

            if ($activeToken) {
                $tokenName = Enum::getTokenName($activeToken->getOpcode());
            }

            if (null === $tokenName) {
                $tokenName = 'UNKNOWN';
            }

            if ($status->getScannerState()->getStartLength() > 0) {
                if ($activeToken->getValue()) {
                    $error = sprintf(
                        'Syntax error, unexpected token %s(%s), near to "%s" in %s on line %d',
                        $tokenName,
                        $activeToken->getValue(),
                        $status->getScannerState()->getStartLength(),
                        $status->getScannerState()->getActiveFile(),
                        $status->getScannerState()->getActiveLine()
                    );
                    $status->setSyntaxError($error);
                } else {
                    $error = sprintf(
                        'Syntax error, unexpected token %s, near to "%s" in %s on line %d',
                        $tokenName,
                        $status->getScannerState()->getStartLength(),
                        $status->getScannerState()->getActiveFile(),
                        $status->getScannerState()->getActiveLine()
                    );
                    $status->setSyntaxError($error);
                }

            } else {
                if ($activeToken->getOpcode() !== Enum::PHANNOT_T_IGNORE) {
                    if ($activeToken->getValue()) {
                        $error = sprintf(
                            'Syntax error, unexpected token %s(%s), at the end of docblock in %s on line %d',
                            $tokenName,
                            $activeToken->getValue(),
                            $status->getScannerState()->getActiveFile(),
                            $status->getScannerState()->getActiveLine()
                        );
                        $status->setSyntaxError($error);
                    } else {
                        $error = sprintf(
                            'Syntax error, unexpected token %s, at the end of docblock in %s on line %d',
                            $tokenName,
                            $status->getScannerState()->getActiveFile(),
                            $status->getScannerState()->getActiveLine()
                        );
                        $status->setSyntaxError($error);
                    }
                }

            }
        } else {
            $error = sprintf(
                'Syntax error, unexpected EOF, at the end of docblock in %s on line %d',
                $this->status->getScannerState()->getActiveFile(),
                $this->status->getScannerState()->getActiveLine()
            );
            $this->status->setSyntaxError($error);
        }

        $this->status->setStatus(ParserStatus::PHANNOT_PARSING_FAILED);
    }

    /*
    ** The following is executed when the parser accepts
    */
    private function yy_accept(): void
    {
        if ($this->yyTraceFILE) {
            fprintf($this->yyTraceFILE, "%sAccept!\n", $this->yyTracePrompt);
        }
        while ($this->yyidx >= 0) $this->yy_pop_parser_stack();
        /* Here code is inserted which will be executed whenever the
        ** parser accepts */

        $this->status->setStatus(ParserStatus::PHANNOT_PARSING_OK);
    }

    /* The main parser program.
    ** The first argument is a pointer to a structure obtained from
    ** "phannotAlloc" which describes the current state of the parser.
    ** The second argument is the major token number.  The third is
    ** the minor token.  The fourth optional argument is whatever the
    ** user wants (and specified in the grammar) and is available for
    ** use by the action routines.
    **
    ** Inputs:
    ** <ul>
    ** <li> A pointer to the parser (an opaque structure.)
    ** <li> The major token number.
    ** <li> The minor token number.
    ** <li> An option argument of a grammar-specified type.
    ** </ul>
    **
    ** Outputs:
    ** None.
    */
    public function phannot(
        int     $yymajor,                 /* The major token code number */
        ?string $yyminor = null           /* The value for the token */
    ): void
    {
        $yyact        = 0;            /* The parser action. */
        $yyendofinput = 0;     /* True if we are at the end of input */
        $yyerrorhit   = 0;   /* True if yymajor has invoked an error */

        /* (re)initialize the parser, if necessary */
        if ($this->yyidx < 0) {
            $this->yyidx    = 0;
            $this->yyerrcnt = -1;
            $ent            = new StackEntry();
            $ent->stateno   = 0;
            $ent->major     = 0;
            $this->yystack  = [0 => $ent];

            $this->YY_NO_ACTION     = self::YYNSTATE + self::YYNRULE + 2;
            $this->YY_ACCEPT_ACTION = self::YYNSTATE + self::YYNRULE + 1;
            $this->YY_ERROR_ACTION  = self::YYNSTATE + self::YYNRULE;
        }
        $yyendofinput = ($yymajor == 0);

        if ($this->yyTraceFILE) {
            fprintf($this->yyTraceFILE, "%sInput %s\n", $this->yyTracePrompt,
                self::$yyTokenName[$yymajor]);
        }

        do {
            $yyact = $this->yy_find_shift_action($yymajor);
            if ($yyact < self::YYNSTATE) {
                $this->yy_shift($yyact, $yymajor, $yyminor);
                $this->yyerrcnt--;
                if ($yyendofinput && $this->yyidx >= 0) {
                    $yymajor = 0;
                } else {
                    $yymajor = self::YYNOCODE;
                }
            } else if ($yyact < self::YYNSTATE + self::YYNRULE) {
                $this->yy_reduce($yyact - self::YYNSTATE);
            } else if ($yyact == $this->YY_ERROR_ACTION) {
                if ($this->yyTraceFILE) {
                    fprintf($this->yyTraceFILE, "%sSyntax Error!\n", $this->yyTracePrompt);
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
                        $this->yy_syntax_error($yymajor, $yyminor);
                    }
                    $yymx = $this->yystack[$this->yyidx]->major;
                    if ($yymx == self::YYERRORSYMBOL || $yyerrorhit) {
                        if ($this->yyTraceFILE) {
                            fprintf($this->yyTraceFILE, "%sDiscard input token %s\n",
                                $this->yyTracePrompt, self::$yyTokenName[$yymajor]);
                        }
                        $this->yy_destructor($yymajor, $yyminor);
                        $yymajor = self::YYNOCODE;
                    } else {
                        while (
                            $this->yyidx >= 0 &&
                            $yymx != self::YYERRORSYMBOL &&
                            ($yyact = $this->yy_find_reduce_action(
                                $this->yystack[$this->yyidx]->stateno,
                                self::YYERRORSYMBOL)) >= self::YYNSTATE
                        ) {
                            $this->yy_pop_parser_stack();
                        }
                        if ($this->yyidx < 0 || $yymajor == 0) {
                            $this->yy_destructor($yymajor, $yyminor);
                            $this->yy_parse_failed();
                            $yymajor = self::YYNOCODE;
                        } else if ($yymx != self::YYERRORSYMBOL) {
                            $this->yy_shift($yyact, self::YYERRORSYMBOL, 0);
                        }
                    }
                    $this->yyerrcnt = 3;
                    $yyerrorhit     = 1;
                } else {  /* YYERRORSYMBOL is not defined */
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
                        $this->yy_syntax_error($yymajor, $yyminor);
                    }
                    $this->yyerrcnt = 3;
                    $this->yy_destructor($yymajor, $yyminor);
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

}
