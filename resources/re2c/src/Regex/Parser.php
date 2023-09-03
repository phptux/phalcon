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
use Azirax\Re2c\Parser\StackEntry;
use Azirax\Re2c\Parser\Token;

/**
 * PHP port of LEMON
 *
 * @package    Azirax\Re2c\Regex
 * @author     Rene Dziuba <php.tux@web.de>
 * @copyright  Copyright (c) 2023 The Authors
 * @license    <http://opensource.org/licenses/bsd-license.php> New BSD License
 */
class Parser
{
    public const OPENPAREN          = 1;
    public const OPENASSERTION      = 2;
    public const BAR                = 3;
    public const MULTIPLIER         = 4;
    public const MATCHSTART         = 5;
    public const MATCHEND           = 6;
    public const OPENCHARCLASS      = 7;
    public const CLOSECHARCLASS     = 8;
    public const NEGATE             = 9;
    public const TEXT               = 10;
    public const ESCAPEDBACKSLASH   = 11;
    public const HYPHEN             = 12;
    public const BACKREFERENCE      = 13;
    public const COULDBEBACKREF     = 14;
    public const CONTROLCHAR        = 15;
    public const FULLSTOP           = 16;
    public const INTERNALOPTIONS    = 17;
    public const CLOSEPAREN         = 18;
    public const COLON              = 19;
    public const POSITIVELOOKAHEAD  = 20;
    public const NEGATIVELOOKAHEAD  = 21;
    public const POSITIVELOOKBEHIND = 22;
    public const NEGATIVELOOKBEHIND = 23;
    public const PATTERNNAME        = 24;
    public const ONCEONLY           = 25;
    public const COMMENT            = 26;
    public const RECUR              = 27;
    public const YY_NO_ACTION       = 230;
    public const YY_ACCEPT_ACTION   = 229;
    public const YY_ERROR_ACTION    = 228;
    public const YY_SZ_ACTTAB       = 354;
    public const YY_SHIFT_USE_DFLT  = -1;
    public const YY_SHIFT_MAX       = 70;
    public const YY_REDUCE_USE_DFLT = -30;
    public const YY_REDUCE_MAX      = 19;
    public const YYNOCODE           = 45;
    public const YYSTACKDEPTH       = 100;
    public const YYNSTATE           = 135;
    public const YYNRULE            = 93;
    public const YYERRORSYMBOL      = 28;
    public const YYERRSYMDT         = 'yy0';
    public const YYFALLBACK         = 0;

    static public array $yy_action = [
        /*     0 */
        229, 45, 15, 23, 104, 106, 107, 109, 108, 118,
        /*    10 */
        119, 129, 128, 130, 36, 15, 23, 104, 106, 107,
        /*    20 */
        109, 108, 118, 119, 129, 128, 130, 39, 15, 23,
        /*    30 */
        104, 106, 107, 109, 108, 118, 119, 129, 128, 130,
        /*    40 */
        25, 15, 23, 104, 106, 107, 109, 108, 118, 119,
        /*    50 */
        129, 128, 130, 32, 15, 23, 104, 106, 107, 109,
        /*    60 */
        108, 118, 119, 129, 128, 130, 28, 15, 23, 104,
        /*    70 */
        106, 107, 109, 108, 118, 119, 129, 128, 130, 35,
        /*    80 */
        15, 23, 104, 106, 107, 109, 108, 118, 119, 129,
        /*    90 */
        128, 130, 92, 15, 23, 104, 106, 107, 109, 108,
        /*   100 */
        118, 119, 129, 128, 130, 38, 15, 23, 104, 106,
        /*   110 */
        107, 109, 108, 118, 119, 129, 128, 130, 40, 15,
        /*   120 */
        23, 104, 106, 107, 109, 108, 118, 119, 129, 128,
        /*   130 */
        130, 33, 15, 23, 104, 106, 107, 109, 108, 118,
        /*   140 */
        119, 129, 128, 130, 30, 15, 23, 104, 106, 107,
        /*   150 */
        109, 108, 118, 119, 129, 128, 130, 37, 15, 23,
        /*   160 */
        104, 106, 107, 109, 108, 118, 119, 129, 128, 130,
        /*   170 */
        34, 15, 23, 104, 106, 107, 109, 108, 118, 119,
        /*   180 */
        129, 128, 130, 16, 23, 104, 106, 107, 109, 108,
        /*   190 */
        118, 119, 129, 128, 130, 54, 24, 22, 72, 76,
        /*   200 */
        85, 84, 82, 81, 80, 97, 134, 125, 93, 12,
        /*   210 */
        12, 26, 83, 2, 5, 1, 11, 4, 10, 13,
        /*   220 */
        49, 50, 9, 17, 46, 98, 14, 12, 18, 113,
        /*   230 */
        124, 52, 43, 79, 44, 57, 42, 41, 9, 17,
        /*   240 */
        127, 12, 53, 91, 18, 126, 12, 52, 43, 120,
        /*   250 */
        44, 57, 42, 41, 9, 17, 47, 12, 31, 117,
        /*   260 */
        18, 88, 99, 52, 43, 75, 44, 57, 42, 41,
        /*   270 */
        9, 17, 51, 19, 67, 69, 18, 101, 87, 52,
        /*   280 */
        43, 12, 44, 57, 42, 41, 132, 64, 63, 103,
        /*   290 */
        62, 58, 66, 65, 59, 12, 60, 68, 90, 111,
        /*   300 */
        116, 122, 61, 100, 60, 68, 12, 111, 116, 122,
        /*   310 */
        71, 5, 1, 11, 4, 67, 69, 12, 101, 87,
        /*   320 */
        12, 102, 12, 12, 112, 6, 105, 131, 78, 7,
        /*   330 */
        8, 95, 77, 74, 70, 56, 123, 48, 133, 73,
        /*   340 */
        27, 114, 86, 55, 115, 89, 110, 121, 3, 94,
        /*   350 */
        21, 29, 96, 20,
    ];

    static public array $yy_lookahead = [
        /*     0 */
        29, 30, 31, 32, 33, 34, 35, 36, 37, 38,
        /*    10 */
        39, 40, 41, 42, 30, 31, 32, 33, 34, 35,
        /*    20 */
        36, 37, 38, 39, 40, 41, 42, 30, 31, 32,
        /*    30 */
        33, 34, 35, 36, 37, 38, 39, 40, 41, 42,
        /*    40 */
        30, 31, 32, 33, 34, 35, 36, 37, 38, 39,
        /*    50 */
        40, 41, 42, 30, 31, 32, 33, 34, 35, 36,
        /*    60 */
        37, 38, 39, 40, 41, 42, 30, 31, 32, 33,
        /*    70 */
        34, 35, 36, 37, 38, 39, 40, 41, 42, 30,
        /*    80 */
        31, 32, 33, 34, 35, 36, 37, 38, 39, 40,
        /*    90 */
        41, 42, 30, 31, 32, 33, 34, 35, 36, 37,
        /*   100 */
        38, 39, 40, 41, 42, 30, 31, 32, 33, 34,
        /*   110 */
        35, 36, 37, 38, 39, 40, 41, 42, 30, 31,
        /*   120 */
        32, 33, 34, 35, 36, 37, 38, 39, 40, 41,
        /*   130 */
        42, 30, 31, 32, 33, 34, 35, 36, 37, 38,
        /*   140 */
        39, 40, 41, 42, 30, 31, 32, 33, 34, 35,
        /*   150 */
        36, 37, 38, 39, 40, 41, 42, 30, 31, 32,
        /*   160 */
        33, 34, 35, 36, 37, 38, 39, 40, 41, 42,
        /*   170 */
        30, 31, 32, 33, 34, 35, 36, 37, 38, 39,
        /*   180 */
        40, 41, 42, 31, 32, 33, 34, 35, 36, 37,
        /*   190 */
        38, 39, 40, 41, 42, 1, 2, 32, 33, 34,
        /*   200 */
        35, 36, 37, 38, 39, 40, 41, 42, 18, 3,
        /*   210 */
        3, 17, 10, 19, 20, 21, 22, 23, 24, 25,
        /*   220 */
        26, 27, 1, 2, 18, 18, 5, 3, 7, 10,
        /*   230 */
        11, 10, 11, 4, 13, 14, 15, 16, 1, 2,
        /*   240 */
        10, 3, 18, 6, 7, 15, 3, 10, 11, 4,
        /*   250 */
        13, 14, 15, 16, 1, 2, 18, 3, 12, 6,
        /*   260 */
        7, 18, 4, 10, 11, 4, 13, 14, 15, 16,
        /*   270 */
        1, 2, 18, 9, 10, 11, 7, 13, 14, 10,
        /*   280 */
        11, 3, 13, 14, 15, 16, 4, 10, 11, 4,
        /*   290 */
        13, 14, 15, 16, 8, 3, 10, 11, 18, 13,
        /*   300 */
        14, 15, 8, 4, 10, 11, 3, 13, 14, 15,
        /*   310 */
        18, 20, 21, 22, 23, 10, 11, 3, 13, 14,
        /*   320 */
        3, 18, 3, 3, 18, 19, 10, 11, 4, 36,
        /*   330 */
        37, 4, 18, 4, 12, 18, 4, 18, 18, 4,
        /*   340 */
        12, 4, 4, 10, 4, 4, 4, 4, 18, 4,
        /*   350 */
        43, 12, 4, 43,
    ];

    static public array $yy_shift_ofst = [
        /*     0 */
        221, 221, 221, 221, 221, 221, 221, 221, 221, 221,
        /*    10 */
        221, 221, 221, 221, 269, 253, 237, 194, 264, 305,
        /*    20 */
        286, 294, 277, 277, 291, 320, 306, 316, 317, 219,
        /*    30 */
        224, 230, 238, 206, 207, 319, 243, 314, 303, 254,
        /*    40 */
        292, 345, 348, 261, 282, 278, 285, 324, 327, 280,
        /*    50 */
        190, 229, 245, 343, 333, 330, 342, 337, 329, 332,
        /*    60 */
        328, 340, 335, 338, 341, 299, 258, 339, 246, 322,
        /*    70 */
        202,
    ];

    static public array $yy_reduce_ofst = [
        /*     0 */
        -29, 127, 114, 101, 140, 88, 10, -3, 23, 36,
        /*    10 */
        49, 75, 62, -16, 152, 165, 165, 293, 310, 307,
    ];

    static public array $yyExpectedTokens = [
        /* 0 */
        [1, 2, 5, 7, 10, 11, 13, 14, 15, 16,],
        /* 1 */
        [1, 2, 5, 7, 10, 11, 13, 14, 15, 16,],
        /* 2 */
        [1, 2, 5, 7, 10, 11, 13, 14, 15, 16,],
        /* 3 */
        [1, 2, 5, 7, 10, 11, 13, 14, 15, 16,],
        /* 4 */
        [1, 2, 5, 7, 10, 11, 13, 14, 15, 16,],
        /* 5 */
        [1, 2, 5, 7, 10, 11, 13, 14, 15, 16,],
        /* 6 */
        [1, 2, 5, 7, 10, 11, 13, 14, 15, 16,],
        /* 7 */
        [1, 2, 5, 7, 10, 11, 13, 14, 15, 16,],
        /* 8 */
        [1, 2, 5, 7, 10, 11, 13, 14, 15, 16,],
        /* 9 */
        [1, 2, 5, 7, 10, 11, 13, 14, 15, 16,],
        /* 10 */
        [1, 2, 5, 7, 10, 11, 13, 14, 15, 16,],
        /* 11 */
        [1, 2, 5, 7, 10, 11, 13, 14, 15, 16,],
        /* 12 */
        [1, 2, 5, 7, 10, 11, 13, 14, 15, 16,],
        /* 13 */
        [1, 2, 5, 7, 10, 11, 13, 14, 15, 16,],
        /* 14 */
        [1, 2, 7, 10, 11, 13, 14, 15, 16,],
        /* 15 */
        [1, 2, 6, 7, 10, 11, 13, 14, 15, 16,],
        /* 16 */
        [1, 2, 6, 7, 10, 11, 13, 14, 15, 16,],
        /* 17 */
        [1, 2, 17, 19, 20, 21, 22, 23, 24, 25, 26, 27,],
        /* 18 */
        [9, 10, 11, 13, 14,],
        /* 19 */
        [10, 11, 13, 14,],
        /* 20 */
        [8, 10, 11, 13, 14, 15,],
        /* 21 */
        [8, 10, 11, 13, 14, 15,],
        /* 22 */
        [10, 11, 13, 14, 15, 16,],
        /* 23 */
        [10, 11, 13, 14, 15, 16,],
        /* 24 */
        [20, 21, 22, 23,],
        /* 25 */
        [3, 18,],
        /* 26 */
        [18, 19,],
        /* 27 */
        [10, 11,],
        /* 28 */
        [3, 18,],
        /* 29 */
        [10, 11,],
        /* 30 */
        [3, 18,],
        /* 31 */
        [10, 15,],
        /* 32 */
        [3, 18,],
        /* 33 */
        [3, 18,],
        /* 34 */
        [3, 18,],
        /* 35 */
        [3, 18,],
        /* 36 */
        [3, 18,],
        /* 37 */
        [3, 18,],
        /* 38 */
        [3, 18,],
        /* 39 */
        [3, 18,],
        /* 40 */
        [3, 18,],
        /* 41 */
        [4,],
        /* 42 */
        [4,],
        /* 43 */
        [4,],
        /* 44 */
        [4,],
        /* 45 */
        [3,],
        /* 46 */
        [4,],
        /* 47 */
        [4,],
        /* 48 */
        [4,],
        /* 49 */
        [18,],
        /* 50 */
        [18,],
        /* 51 */
        [4,],
        /* 52 */
        [4,],
        /* 53 */
        [4,],
        /* 54 */
        [10,],
        /* 55 */
        [18,],
        /* 56 */
        [4,],
        /* 57 */
        [4,],
        /* 58 */
        [4,],
        /* 59 */
        [4,],
        /* 60 */
        [12,],
        /* 61 */
        [4,],
        /* 62 */
        [4,],
        /* 63 */
        [4,],
        /* 64 */
        [4,],
        /* 65 */
        [4,],
        /* 66 */
        [4,],
        /* 67 */
        [12,],
        /* 68 */
        [12,],
        /* 69 */
        [12,],
        /* 70 */
        [10,],
        /* 71 */
        [],
        /* 72 */
        [],
        /* 73 */
        [],
        /* 74 */
        [],
        /* 75 */
        [],
        /* 76 */
        [],
        /* 77 */
        [],
        /* 78 */
        [],
        /* 79 */
        [],
        /* 80 */
        [],
        /* 81 */
        [],
        /* 82 */
        [],
        /* 83 */
        [],
        /* 84 */
        [],
        /* 85 */
        [],
        /* 86 */
        [],
        /* 87 */
        [],
        /* 88 */
        [],
        /* 89 */
        [],
        /* 90 */
        [],
        /* 91 */
        [],
        /* 92 */
        [],
        /* 93 */
        [],
        /* 94 */
        [],
        /* 95 */
        [],
        /* 96 */
        [],
        /* 97 */
        [],
        /* 98 */
        [],
        /* 99 */
        [],
        /* 100 */
        [],
        /* 101 */
        [],
        /* 102 */
        [],
        /* 103 */
        [],
        /* 104 */
        [],
        /* 105 */
        [],
        /* 106 */
        [],
        /* 107 */
        [],
        /* 108 */
        [],
        /* 109 */
        [],
        /* 110 */
        [],
        /* 111 */
        [],
        /* 112 */
        [],
        /* 113 */
        [],
        /* 114 */
        [],
        /* 115 */
        [],
        /* 116 */
        [],
        /* 117 */
        [],
        /* 118 */
        [],
        /* 119 */
        [],
        /* 120 */
        [],
        /* 121 */
        [],
        /* 122 */
        [],
        /* 123 */
        [],
        /* 124 */
        [],
        /* 125 */
        [],
        /* 126 */
        [],
        /* 127 */
        [],
        /* 128 */
        [],
        /* 129 */
        [],
        /* 130 */
        [],
        /* 131 */
        [],
        /* 132 */
        [],
        /* 133 */
        [],
        /* 134 */
        [],
    ];

    static public array $yy_default = [
        /*     0 */
        228, 228, 228, 228, 228, 228, 228, 228, 228, 228,
        /*    10 */
        228, 228, 228, 228, 228, 139, 137, 228, 228, 228,
        /*    20 */
        228, 228, 152, 141, 228, 228, 228, 228, 228, 228,
        /*    30 */
        228, 228, 228, 228, 228, 228, 228, 228, 228, 228,
        /*    40 */
        228, 185, 187, 189, 191, 135, 212, 215, 221, 228,
        /*    50 */
        228, 213, 183, 209, 228, 228, 223, 193, 205, 163,
        /*    60 */
        176, 164, 203, 201, 195, 197, 199, 167, 175, 168,
        /*    70 */
        228, 217, 153, 204, 206, 190, 154, 218, 216, 214,
        /*    80 */
        159, 158, 157, 169, 156, 155, 202, 173, 225, 196,
        /*    90 */
        226, 136, 140, 227, 186, 222, 188, 160, 220, 200,
        /*   100 */
        198, 172, 219, 211, 142, 180, 143, 144, 146, 145,
        /*   110 */
        224, 181, 207, 170, 194, 166, 182, 138, 147, 148,
        /*   120 */
        184, 210, 174, 165, 171, 162, 177, 178, 150, 149,
        /*   130 */
        151, 179, 192, 208, 161,
    ];

    /**
     * For tracing shifts, the names of all terminals and non-terminals
     * are required.  The following table supplies these names
     *
     * @var array
     */
    static public array $yyTokenName = [
        '$', 'OPENPAREN', 'OPENASSERTION', 'BAR',
        'MULTIPLIER', 'MATCHSTART', 'MATCHEND', 'OPENCHARCLASS',
        'CLOSECHARCLASS', 'NEGATE', 'TEXT', 'ESCAPEDBACKSLASH',
        'HYPHEN', 'BACKREFERENCE', 'COULDBEBACKREF', 'CONTROLCHAR',
        'FULLSTOP', 'INTERNALOPTIONS', 'CLOSEPAREN', 'COLON',
        'POSITIVELOOKAHEAD', 'NEGATIVELOOKAHEAD', 'POSITIVELOOKBEHIND', 'NEGATIVELOOKBEHIND',
        'PATTERNNAME', 'ONCEONLY', 'COMMENT', 'RECUR',
        'error', 'start', 'pattern', 'basic_pattern',
        'basic_text', 'character_class', 'assertion', 'grouping',
        'lookahead', 'lookbehind', 'subpattern', 'onceonly',
        'comment', 'recur', 'conditional', 'character_class_contents',
    ];

    /**
     * For tracing, reduce actions, the names of all rules are required.
     *
     * @var array
     */
    static public array $yyRuleName = [
        /*   0 */
        "start ::= pattern",
        /*   1 */
        "pattern ::= MATCHSTART basic_pattern MATCHEND",
        /*   2 */
        "pattern ::= MATCHSTART basic_pattern",
        /*   3 */
        "pattern ::= basic_pattern MATCHEND",
        /*   4 */
        "pattern ::= basic_pattern",
        /*   5 */
        "pattern ::= pattern BAR pattern",
        /*   6 */
        "basic_pattern ::= basic_text",
        /*   7 */
        "basic_pattern ::= character_class",
        /*   8 */
        "basic_pattern ::= assertion",
        /*   9 */
        "basic_pattern ::= grouping",
        /*  10 */
        "basic_pattern ::= lookahead",
        /*  11 */
        "basic_pattern ::= lookbehind",
        /*  12 */
        "basic_pattern ::= subpattern",
        /*  13 */
        "basic_pattern ::= onceonly",
        /*  14 */
        "basic_pattern ::= comment",
        /*  15 */
        "basic_pattern ::= recur",
        /*  16 */
        "basic_pattern ::= conditional",
        /*  17 */
        "basic_pattern ::= basic_pattern basic_text",
        /*  18 */
        "basic_pattern ::= basic_pattern character_class",
        /*  19 */
        "basic_pattern ::= basic_pattern assertion",
        /*  20 */
        "basic_pattern ::= basic_pattern grouping",
        /*  21 */
        "basic_pattern ::= basic_pattern lookahead",
        /*  22 */
        "basic_pattern ::= basic_pattern lookbehind",
        /*  23 */
        "basic_pattern ::= basic_pattern subpattern",
        /*  24 */
        "basic_pattern ::= basic_pattern onceonly",
        /*  25 */
        "basic_pattern ::= basic_pattern comment",
        /*  26 */
        "basic_pattern ::= basic_pattern recur",
        /*  27 */
        "basic_pattern ::= basic_pattern conditional",
        /*  28 */
        "character_class ::= OPENCHARCLASS character_class_contents CLOSECHARCLASS",
        /*  29 */
        "character_class ::= OPENCHARCLASS NEGATE character_class_contents CLOSECHARCLASS",
        /*  30 */
        "character_class ::= OPENCHARCLASS character_class_contents CLOSECHARCLASS MULTIPLIER",
        /*  31 */
        "character_class ::= OPENCHARCLASS NEGATE character_class_contents CLOSECHARCLASS MULTIPLIER",
        /*  32 */
        "character_class_contents ::= TEXT",
        /*  33 */
        "character_class_contents ::= ESCAPEDBACKSLASH",
        /*  34 */
        "character_class_contents ::= ESCAPEDBACKSLASH HYPHEN TEXT",
        /*  35 */
        "character_class_contents ::= TEXT HYPHEN TEXT",
        /*  36 */
        "character_class_contents ::= TEXT HYPHEN ESCAPEDBACKSLASH",
        /*  37 */
        "character_class_contents ::= BACKREFERENCE",
        /*  38 */
        "character_class_contents ::= COULDBEBACKREF",
        /*  39 */
        "character_class_contents ::= character_class_contents CONTROLCHAR",
        /*  40 */
        "character_class_contents ::= character_class_contents ESCAPEDBACKSLASH",
        /*  41 */
        "character_class_contents ::= character_class_contents TEXT",
        /*  42 */
        "character_class_contents ::= character_class_contents ESCAPEDBACKSLASH HYPHEN CONTROLCHAR",
        /*  43 */
        "character_class_contents ::= character_class_contents ESCAPEDBACKSLASH HYPHEN TEXT",
        /*  44 */
        "character_class_contents ::= character_class_contents TEXT HYPHEN ESCAPEDBACKSLASH",
        /*  45 */
        "character_class_contents ::= character_class_contents TEXT HYPHEN TEXT",
        /*  46 */
        "character_class_contents ::= character_class_contents BACKREFERENCE",
        /*  47 */
        "character_class_contents ::= character_class_contents COULDBEBACKREF",
        /*  48 */
        "basic_text ::= TEXT",
        /*  49 */
        "basic_text ::= TEXT MULTIPLIER",
        /*  50 */
        "basic_text ::= FULLSTOP",
        /*  51 */
        "basic_text ::= FULLSTOP MULTIPLIER",
        /*  52 */
        "basic_text ::= CONTROLCHAR",
        /*  53 */
        "basic_text ::= CONTROLCHAR MULTIPLIER",
        /*  54 */
        "basic_text ::= ESCAPEDBACKSLASH",
        /*  55 */
        "basic_text ::= ESCAPEDBACKSLASH MULTIPLIER",
        /*  56 */
        "basic_text ::= BACKREFERENCE",
        /*  57 */
        "basic_text ::= BACKREFERENCE MULTIPLIER",
        /*  58 */
        "basic_text ::= COULDBEBACKREF",
        /*  59 */
        "basic_text ::= COULDBEBACKREF MULTIPLIER",
        /*  60 */
        "basic_text ::= basic_text TEXT",
        /*  61 */
        "basic_text ::= basic_text TEXT MULTIPLIER",
        /*  62 */
        "basic_text ::= basic_text FULLSTOP",
        /*  63 */
        "basic_text ::= basic_text FULLSTOP MULTIPLIER",
        /*  64 */
        "basic_text ::= basic_text CONTROLCHAR",
        /*  65 */
        "basic_text ::= basic_text CONTROLCHAR MULTIPLIER",
        /*  66 */
        "basic_text ::= basic_text ESCAPEDBACKSLASH",
        /*  67 */
        "basic_text ::= basic_text ESCAPEDBACKSLASH MULTIPLIER",
        /*  68 */
        "basic_text ::= basic_text BACKREFERENCE",
        /*  69 */
        "basic_text ::= basic_text BACKREFERENCE MULTIPLIER",
        /*  70 */
        "basic_text ::= basic_text COULDBEBACKREF",
        /*  71 */
        "basic_text ::= basic_text COULDBEBACKREF MULTIPLIER",
        /*  72 */
        "assertion ::= OPENASSERTION INTERNALOPTIONS CLOSEPAREN",
        /*  73 */
        "assertion ::= OPENASSERTION INTERNALOPTIONS COLON pattern CLOSEPAREN",
        /*  74 */
        "grouping ::= OPENASSERTION COLON pattern CLOSEPAREN",
        /*  75 */
        "grouping ::= OPENASSERTION COLON pattern CLOSEPAREN MULTIPLIER",
        /*  76 */
        "conditional ::= OPENASSERTION OPENPAREN TEXT CLOSEPAREN pattern CLOSEPAREN MULTIPLIER",
        /*  77 */
        "conditional ::= OPENASSERTION OPENPAREN TEXT CLOSEPAREN pattern CLOSEPAREN",
        /*  78 */
        "conditional ::= OPENASSERTION lookahead pattern CLOSEPAREN",
        /*  79 */
        "conditional ::= OPENASSERTION lookahead pattern CLOSEPAREN MULTIPLIER",
        /*  80 */
        "conditional ::= OPENASSERTION lookbehind pattern CLOSEPAREN",
        /*  81 */
        "conditional ::= OPENASSERTION lookbehind pattern CLOSEPAREN MULTIPLIER",
        /*  82 */
        "lookahead ::= OPENASSERTION POSITIVELOOKAHEAD pattern CLOSEPAREN",
        /*  83 */
        "lookahead ::= OPENASSERTION NEGATIVELOOKAHEAD pattern CLOSEPAREN",
        /*  84 */
        "lookbehind ::= OPENASSERTION POSITIVELOOKBEHIND pattern CLOSEPAREN",
        /*  85 */
        "lookbehind ::= OPENASSERTION NEGATIVELOOKBEHIND pattern CLOSEPAREN",
        /*  86 */
        "subpattern ::= OPENASSERTION PATTERNNAME pattern CLOSEPAREN",
        /*  87 */
        "subpattern ::= OPENASSERTION PATTERNNAME pattern CLOSEPAREN MULTIPLIER",
        /*  88 */
        "subpattern ::= OPENPAREN pattern CLOSEPAREN",
        /*  89 */
        "subpattern ::= OPENPAREN pattern CLOSEPAREN MULTIPLIER",
        /*  90 */
        "onceonly ::= OPENASSERTION ONCEONLY pattern CLOSEPAREN",
        /*  91 */
        "comment ::= OPENASSERTION COMMENT CLOSEPAREN",
        /*  92 */
        "recur ::= OPENASSERTION RECUR CLOSEPAREN",
    ];

    /**
     * The following table contains information about every rule that
     * is used during the reduce.
     *
     * <pre>
     * array(
     *  array(
     *   int $lhs;         Symbol on the left-hand side of the rule
     *   int $nrhs;     Number of right-hand side symbols in the rule
     *  ),...
     * );
     * </pre>
     */
    static public array $yyRuleInfo = [
        ['lhs' => 29, 'rhs' => 1],
        ['lhs' => 30, 'rhs' => 3],
        ['lhs' => 30, 'rhs' => 2],
        ['lhs' => 30, 'rhs' => 2],
        ['lhs' => 30, 'rhs' => 1],
        ['lhs' => 30, 'rhs' => 3],
        ['lhs' => 31, 'rhs' => 1],
        ['lhs' => 31, 'rhs' => 1],
        ['lhs' => 31, 'rhs' => 1],
        ['lhs' => 31, 'rhs' => 1],
        ['lhs' => 31, 'rhs' => 1],
        ['lhs' => 31, 'rhs' => 1],
        ['lhs' => 31, 'rhs' => 1],
        ['lhs' => 31, 'rhs' => 1],
        ['lhs' => 31, 'rhs' => 1],
        ['lhs' => 31, 'rhs' => 1],
        ['lhs' => 31, 'rhs' => 1],
        ['lhs' => 31, 'rhs' => 2],
        ['lhs' => 31, 'rhs' => 2],
        ['lhs' => 31, 'rhs' => 2],
        ['lhs' => 31, 'rhs' => 2],
        ['lhs' => 31, 'rhs' => 2],
        ['lhs' => 31, 'rhs' => 2],
        ['lhs' => 31, 'rhs' => 2],
        ['lhs' => 31, 'rhs' => 2],
        ['lhs' => 31, 'rhs' => 2],
        ['lhs' => 31, 'rhs' => 2],
        ['lhs' => 31, 'rhs' => 2],
        ['lhs' => 33, 'rhs' => 3],
        ['lhs' => 33, 'rhs' => 4],
        ['lhs' => 33, 'rhs' => 4],
        ['lhs' => 33, 'rhs' => 5],
        ['lhs' => 43, 'rhs' => 1],
        ['lhs' => 43, 'rhs' => 1],
        ['lhs' => 43, 'rhs' => 3],
        ['lhs' => 43, 'rhs' => 3],
        ['lhs' => 43, 'rhs' => 3],
        ['lhs' => 43, 'rhs' => 1],
        ['lhs' => 43, 'rhs' => 1],
        ['lhs' => 43, 'rhs' => 2],
        ['lhs' => 43, 'rhs' => 2],
        ['lhs' => 43, 'rhs' => 2],
        ['lhs' => 43, 'rhs' => 4],
        ['lhs' => 43, 'rhs' => 4],
        ['lhs' => 43, 'rhs' => 4],
        ['lhs' => 43, 'rhs' => 4],
        ['lhs' => 43, 'rhs' => 2],
        ['lhs' => 43, 'rhs' => 2],
        ['lhs' => 32, 'rhs' => 1],
        ['lhs' => 32, 'rhs' => 2],
        ['lhs' => 32, 'rhs' => 1],
        ['lhs' => 32, 'rhs' => 2],
        ['lhs' => 32, 'rhs' => 1],
        ['lhs' => 32, 'rhs' => 2],
        ['lhs' => 32, 'rhs' => 1],
        ['lhs' => 32, 'rhs' => 2],
        ['lhs' => 32, 'rhs' => 1],
        ['lhs' => 32, 'rhs' => 2],
        ['lhs' => 32, 'rhs' => 1],
        ['lhs' => 32, 'rhs' => 2],
        ['lhs' => 32, 'rhs' => 2],
        ['lhs' => 32, 'rhs' => 3],
        ['lhs' => 32, 'rhs' => 2],
        ['lhs' => 32, 'rhs' => 3],
        ['lhs' => 32, 'rhs' => 2],
        ['lhs' => 32, 'rhs' => 3],
        ['lhs' => 32, 'rhs' => 2],
        ['lhs' => 32, 'rhs' => 3],
        ['lhs' => 32, 'rhs' => 2],
        ['lhs' => 32, 'rhs' => 3],
        ['lhs' => 32, 'rhs' => 2],
        ['lhs' => 32, 'rhs' => 3],
        ['lhs' => 34, 'rhs' => 3],
        ['lhs' => 34, 'rhs' => 5],
        ['lhs' => 35, 'rhs' => 4],
        ['lhs' => 35, 'rhs' => 5],
        ['lhs' => 42, 'rhs' => 7],
        ['lhs' => 42, 'rhs' => 6],
        ['lhs' => 42, 'rhs' => 4],
        ['lhs' => 42, 'rhs' => 5],
        ['lhs' => 42, 'rhs' => 4],
        ['lhs' => 42, 'rhs' => 5],
        ['lhs' => 36, 'rhs' => 4],
        ['lhs' => 36, 'rhs' => 4],
        ['lhs' => 37, 'rhs' => 4],
        ['lhs' => 37, 'rhs' => 4],
        ['lhs' => 38, 'rhs' => 4],
        ['lhs' => 38, 'rhs' => 5],
        ['lhs' => 38, 'rhs' => 3],
        ['lhs' => 38, 'rhs' => 4],
        ['lhs' => 39, 'rhs' => 4],
        ['lhs' => 40, 'rhs' => 3],
        ['lhs' => 41, 'rhs' => 3],
    ];

    /**
     * The following table contains a mapping of reduce action to method name
     * that handles the reduction.
     *
     * If a rule is not set, it has no handler.
     */
    static public array $yyReduceMap = [
        0  => 0,
        1  => 1,
        2  => 2,
        3  => 3,
        4  => 4,
        6  => 4,
        7  => 4,
        9  => 4,
        10 => 4,
        12 => 4,
        13 => 4,
        14 => 4,
        15 => 4,
        16 => 4,
        5  => 5,
        17 => 17,
        18 => 17,
        20 => 17,
        21 => 17,
        23 => 17,
        24 => 17,
        25 => 17,
        26 => 17,
        27 => 17,
        28 => 28,
        29 => 29,
        30 => 30,
        31 => 31,
        32 => 32,
        48 => 32,
        50 => 32,
        33 => 33,
        54 => 33,
        34 => 34,
        35 => 35,
        36 => 36,
        37 => 37,
        56 => 37,
        38 => 38,
        58 => 38,
        39 => 39,
        64 => 39,
        40 => 40,
        66 => 40,
        41 => 41,
        60 => 41,
        62 => 41,
        42 => 42,
        43 => 43,
        44 => 44,
        45 => 45,
        46 => 46,
        68 => 46,
        47 => 47,
        70 => 47,
        49 => 49,
        51 => 49,
        52 => 52,
        53 => 53,
        55 => 55,
        57 => 57,
        59 => 59,
        61 => 61,
        63 => 61,
        65 => 65,
        67 => 67,
        69 => 69,
        71 => 71,
        72 => 72,
        73 => 73,
        74 => 74,
        75 => 75,
        76 => 76,
        77 => 77,
        78 => 78,
        79 => 79,
        80 => 80,
        84 => 80,
        81 => 81,
        82 => 82,
        83 => 83,
        85 => 85,
        86 => 86,
        87 => 87,
        88 => 88,
        89 => 89,
        90 => 90,
        91 => 91,
        92 => 92,
    ];

    static public array $yyFallback = [];

    /**
     * @var resource|0
     */
    static public mixed $yyTraceFILE = 0;

    /**
     * String to prepend to debug output
     *
     * @var string|0
     */
    static public mixed $yyTracePrompt = 0;

    /**
     * Index of top element in stack
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
     * Lexer object
     *
     * @var Lexer
     */
    private Lexer $_lex;

    /**
     * Sub-patters
     *
     * @var int
     */
    private int $_subPatterns;

    /**
     * Flag for pattern update
     *
     * @var bool
     */
    private bool $_updatePattern = false;

    /**
     * Pattern index
     *
     * @var int
     */
    private int $_patternIndex;

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
     */
    private ?Token $_retvalue = null;

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
     *
     * @param mixed    $zTracePrompt
     */
    public static function Trace(mixed $TraceFILE, mixed $zTracePrompt): void
    {
        if (!$TraceFILE) {
            $zTracePrompt = null;
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
    static function yy_destructor(int $yymajor, mixed $yypminor): void
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
     * Current token
     *
     * @var Token
     */
    public Token $result;

    /**
     * Constructor for Parser
     *
     * @param Lexer $lex
     */
    public function __construct(Lexer $lex)
    {
        $this->result        = new Token('');
        $this->_lex          = $lex;
        $this->_subPatterns  = 0;
        $this->_patternIndex = 1;
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
     * Reset the parser.
     *
     * @param int  $patternIndex
     * @param bool $updatePattern
     *
     * @return void
     */
    public function reset(int $patternIndex, bool $updatePattern = false): void
    {
        $this->_updatePattern = $updatePattern;
        $this->_patternIndex  = $patternIndex;
        $this->_subPatterns   = 0;
        $this->result         = new Token('');
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
     * @return int
     */
    public function yy_pop_parser_stack(): mixed
    {
        if (!count($this->yystack)) {
            return null;
        }

        $yytos = array_pop($this->yystack);
        if (self::$yyTraceFILE && $this->yyidx >= 0) {
            fwrite(
                self::$yyTraceFILE,
                self::$yyTracePrompt . 'Popping ' . self::$yyTokenName[$yytos->major] . "\n"
            );
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
     */
    public function yy_find_shift_action(int $iLookAhead): mixed
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
     */
    public function yy_find_reduce_action(int $stateno, int $iLookAhead): mixed
    {
        /* $stateno = $this->yystack[$this->yyidx]->stateno; */

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
     * Perform a reduce action and the shift that must immediately
     * follow the reduction.
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
        //PHP_LexerGenerator_Regex_yyStackEntry $yymsp;            /* The top of the parser's stack */
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
            // pop all the right-hand side parameters
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
#line 6 "Parser.y"

        /* ?><?php */
        // we need to add auto-escaping of all stuff that needs it for result.
        // and then validate the original regex only
        echo "Syntax Error on line " . $this->_lex->line . ": token '" .
            $this->_lex->value . "' while parsing rule:\n";

        foreach ($this->yystack as $entry) {
            echo $this->tokenName($entry->major) . ' ';
        }

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
        // True if yymajor has invoked an error
        $yyerrorhit = 0;

        /* (re)initialize the parser, if necessary */
        if ($this->yyidx === null || $this->yyidx < 0) {
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
                        // $this->yy_destructor($yymajor, $yytokenvalue);
                        $yymajor = self::YYNOCODE;
                    } else {
                        while ($this->yyidx >= 0 &&
                            $yymx != self::YYERRORSYMBOL &&
                            ($yyact = $this->yy_find_shift_action(self::YYERRORSYMBOL)) >= self::YYNSTATE
                        ) {
                            $this->yy_pop_parser_stack();
                        }
                        if ($this->yyidx < 0 || $yymajor == 0) {
                            // $this->yy_destructor($yymajor, $yytokenvalue);
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
                    //$this->yy_destructor($yymajor, $yytokenvalue);
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
     * Returns the index
     *
     * @return int
     */
    protected function getIndex(): int
    {
        return $this->yyidx + 0;
    }

    protected function yy_r0(): void
    {
        $this->yystack[$this->getIndex()]->minor->string   = str_replace('"', '\\"', $this->yystack[$this->getIndex()]->minor->string);
        $x                                                 = $this->yystack[$this->getIndex()]->minor->metadata;
        $x['subpatterns']                                  = $this->_subPatterns;
        $this->yystack[$this->getIndex()]->minor->metadata = $x;
        $this->_subPatterns                                = 0;
        $this->result                                      = $this->yystack[$this->getIndex()]->minor;
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function yy_r1(): void
    {
        $message = sprintf(
            'Cannot include start match "%s" or end match "%s"',
            $this->yystack[$this->yyidx + -2]->minor,
            $this->yystack[$this->getIndex()]->minor
        );

        throw new Exception($message);
    }

    /**
     * @throws Exception
     */
    protected function yy_r2(): void
    {
        throw new Exception('Cannot include start match "B"');
    }

    /**
     * @throws Exception
     */
    protected function yy_r3(): void
    {
        throw new Exception('Cannot include end match "' . $this->yystack[$this->getIndex()]->minor . '"');
    }

    protected function yy_r4(): void
    {
        $this->_retvalue = $this->yystack[$this->getIndex()]->minor;
    }

    protected function yy_r5(): void
    {
        $this->_retvalue = new Token(
            $this->yystack[$this->getIndex() + -2]->minor->string . '|' . $this->yystack[$this->getIndex()]->minor->string,
            ['pattern' => $this->yystack[$this->getIndex() + -2]->minor['pattern'] . '|' . $this->yystack[$this->getIndex()]->minor['pattern']]
        );
    }

    protected function yy_r17(): void
    {
        $this->_retvalue = new Token(
            $this->yystack[$this->getIndex() + -1]->minor->string . $this->yystack[$this->getIndex()]->minor->string,
            ['pattern' => $this->yystack[$this->getIndex() + -1]->minor['pattern'] . $this->yystack[$this->getIndex()]->minor['pattern']]
        );
    }

    protected function yy_r28(): void
    {
        $this->_retvalue = new Token(
            '[' . $this->yystack[$this->getIndex() + -1]->minor->string . ']',
            ['pattern' => '[' . $this->yystack[$this->getIndex() + -1]->minor['pattern'] . ']']
        );
    }

    protected function yy_r29(): void
    {
        $this->_retvalue = new Token(
            '[^' . $this->yystack[$this->getIndex() + -1]->minor->string . ']',
            ['pattern' => '[^' . $this->yystack[$this->getIndex() + -1]->minor['pattern'] . ']']
        );
    }

    protected function yy_r30(): void
    {
        $this->_retvalue = new Token(
            '[' . $this->yystack[$this->getIndex() + -2]->minor->string . ']' . $this->yystack[$this->getIndex()]->minor,
            ['pattern' => '[' . $this->yystack[$this->getIndex() + -2]->minor['pattern'] . ']' . $this->yystack[$this->getIndex()]->minor]
        );
    }

    protected function yy_r31(): void
    {
        $this->_retvalue = new Token(
            '[^' . $this->yystack[$this->getIndex() + -2]->minor->string . ']' . $this->yystack[$this->getIndex()]->minor,
            ['pattern' => '[^' . $this->yystack[$this->getIndex() + -2]->minor['pattern'] . ']' . $this->yystack[$this->getIndex()]->minor]
        );
    }

    protected function yy_r32()
    {
        $this->_retvalue = new Token(
            $this->yystack[$this->getIndex()]->minor,
            ['pattern' => $this->yystack[$this->getIndex()]->minor]
        );
    }

    protected function yy_r33(): void
    {
        $this->_retvalue = new Token(
            '\\\\' . $this->yystack[$this->getIndex()]->minor,
            ['pattern' => $this->yystack[$this->getIndex()]->minor]
        );
    }

    protected function yy_r34(): void
    {
        $this->_retvalue = new Token(
            '\\\\' . $this->yystack[$this->getIndex() + -2]->minor . '-' . $this->yystack[$this->getIndex()]->minor,
            ['pattern' => $this->yystack[$this->getIndex() + -2]->minor . '-' . $this->yystack[$this->getIndex()]->minor]
        );
    }

    protected function yy_r35(): void
    {
        $this->_retvalue = new Token(
            $this->yystack[$this->getIndex() + -2]->minor . '-' . $this->yystack[$this->getIndex()]->minor,
            ['pattern' => $this->yystack[$this->getIndex() + -2]->minor . '-' . $this->yystack[$this->getIndex()]->minor]
        );
    }

    protected function yy_r36(): void
    {
        $this->_retvalue = new Token(
            $this->yystack[$this->getIndex() + -2]->minor . '-\\\\' . $this->yystack[$this->getIndex()]->minor,
            ['pattern' => $this->yystack[$this->getIndex() + -2]->minor . '-' . $this->yystack[$this->getIndex()]->minor]
        );
    }

    /**
     * @throws Exception
     */
    protected function yy_r37(): void
    {
        if (((int)substr($this->yystack[$this->getIndex()]->minor, 1)) > $this->_subPatterns) {
            throw new Exception(sprintf(
                'Back-reference refers to non-existent sub-pattern %s',
                substr($this->yystack[$this->getIndex()]->minor, 1)
            ));
        }

        $this->yystack[$this->getIndex()]->minor = substr($this->yystack[$this->getIndex()]->minor, 1);

        // adjust back-reference for containing ()
        $this->_retvalue = new Token(
            '\\\\' . ($this->yystack[$this->getIndex()]->minor . (string)$this->_patternIndex),
            ['pattern' => '\\' . ($this->_updatePattern ? ($this->yystack[$this->getIndex()]->minor . $this->_patternIndex) : $this->yystack[$this->getIndex()]->minor)]
        );
    }

    /**
     * @throws Exception
     */
    protected function yy_r38(): void
    {
        if (((int)substr($this->yystack[$this->getIndex()]->minor, 1)) > $this->_subPatterns) {
            throw new Exception(sprintf(
                '%s will be interpreted as an invalid back-reference, use "\\0%s for octal',
                $this->yystack[$this->getIndex()]->minor,
                substr($this->yystack[$this->getIndex()]->minor, 1)
            ));
        }
        $this->yystack[$this->getIndex()]->minor = substr($this->yystack[$this->getIndex()]->minor, 1);
        $this->_retvalue                         = new Token(
            '\\\\' . ($this->yystack[$this->getIndex()]->minor . $this->_patternIndex),
            ['pattern' => '\\' . ($this->_updatePattern ? ($this->yystack[$this->getIndex()]->minor . $this->_patternIndex) : $this->yystack[$this->getIndex()]->minor)]
        );
    }

    protected function yy_r39(): void
    {
        $this->_retvalue = new Token(
            $this->yystack[$this->getIndex() + -1]->minor->string . '\\' . $this->yystack[$this->getIndex()]->minor,
            ['pattern' => $this->yystack[$this->getIndex() + -1]->minor['pattern'] . $this->yystack[$this->getIndex()]->minor]
        );
    }

    protected function yy_r40(): void
    {
        $this->_retvalue = new Token(
            $this->yystack[$this->getIndex() + -1]->minor->string . '\\\\' . $this->yystack[$this->getIndex()]->minor,
            ['pattern' => $this->yystack[$this->getIndex() + -1]->minor['pattern'] . $this->yystack[$this->getIndex()]->minor]
        );
    }

    protected function yy_r41(): void
    {
        $this->_retvalue = new Token(
            $this->yystack[$this->getIndex() + -1]->minor->string . $this->yystack[$this->getIndex()]->minor,
            ['pattern' => $this->yystack[$this->getIndex() + -1]->minor['pattern'] . $this->yystack[$this->getIndex()]->minor]
        );
    }

    protected function yy_r42(): void
    {
        $this->_retvalue = new Token(
            $this->yystack[$this->getIndex() + -3]->minor->string . '\\\\' . $this->yystack[$this->getIndex() + -2]->minor . '-\\' . $this->yystack[$this->getIndex()]->minor,
            ['pattern' => $this->yystack[$this->getIndex() + -3]->minor['pattern'] . $this->yystack[$this->getIndex() + -2]->minor . '-' . $this->yystack[$this->getIndex()]->minor]
        );
    }

    protected function yy_r43(): void
    {
        $this->_retvalue = new Token(
            $this->yystack[$this->getIndex() + -3]->minor->string . '\\\\' . $this->yystack[$this->getIndex() + -2]->minor . '-' . $this->yystack[$this->getIndex()]->minor,
            ['pattern' => $this->yystack[$this->getIndex() + -3]->minor['pattern'] . $this->yystack[$this->getIndex() + -2]->minor . '-' . $this->yystack[$this->getIndex()]->minor]
        );
    }

    protected function yy_r44(): void
    {
        $this->_retvalue = new Token(
            $this->yystack[$this->getIndex() + -3]->minor->string . $this->yystack[$this->getIndex() + -2]->minor . '-\\\\' . $this->yystack[$this->getIndex()]->minor,
            ['pattern' => $this->yystack[$this->getIndex() + -3]->minor['pattern'] . $this->yystack[$this->getIndex() + -2]->minor . '-' . $this->yystack[$this->getIndex()]->minor]
        );
    }

    protected function yy_r45(): void
    {
        $this->_retvalue = new Token(
            $this->yystack[$this->getIndex() + -3]->minor->string . $this->yystack[$this->getIndex() + -2]->minor . '-' . $this->yystack[$this->getIndex()]->minor,
            ['pattern' => $this->yystack[$this->getIndex() + -3]->minor['pattern'] . $this->yystack[$this->getIndex() + -2]->minor . '-' . $this->yystack[$this->getIndex()]->minor]
        );
    }

    /**
     * @throws Exception
     */
    protected function yy_r46(): void
    {
        if (((int)substr($this->yystack[$this->getIndex()]->minor, 1)) > $this->_subPatterns) {
            throw new Exception(sprintf(
                'Back-reference refers to non-existent sub-pattern %s',
                substr($this->yystack[$this->getIndex()]->minor, 1)
            ));
        }

        $this->yystack[$this->getIndex()]->minor = substr($this->yystack[$this->getIndex()]->minor, 1);
        $this->_retvalue                         = new Token($this->yystack[$this->getIndex() + -1]->minor->string . '\\\\' . ($this->yystack[$this->getIndex()]->minor . $this->_patternIndex),
            ['pattern' => $this->yystack[$this->getIndex() + -1]->minor['pattern'] . '\\' . ($this->_updatePattern ? ($this->yystack[$this->getIndex()]->minor . $this->_patternIndex) : $this->yystack[$this->getIndex()]->minor)]
        );
    }

    /**
     * @throws Exception
     */
    protected function yy_r47(): void
    {
        if (((int)substr($this->yystack[$this->getIndex()]->minor, 1)) > $this->_subPatterns) {
            throw new Exception(sprintf(
                '%s will be interpreted as an invalid back-reference, use "\\0%s" for octal',
                $this->yystack[$this->getIndex()]->minor,
                substr($this->yystack[$this->getIndex()]->minor, 1)
            ));
        }

        $this->yystack[$this->getIndex()]->minor = substr($this->yystack[$this->getIndex() + 0]->minor, 1);
        $this->_retvalue                         = new Token(
            $this->yystack[$this->getIndex() + -1]->minor->string . '\\\\' . ($this->yystack[$this->getIndex()]->minor . $this->_patternIndex),
            ['pattern' => $this->yystack[$this->getIndex() + -1]->minor['pattern'] . '\\' . ($this->_updatePattern ? ($this->yystack[$this->getIndex()]->minor . $this->_patternIndex) : $this->yystack[$this->getIndex()]->minor)]
        );
    }

    protected function yy_r49(): void
    {
        $this->_retvalue = new Token($this->yystack[$this->getIndex() + -1]->minor . $this->yystack[$this->getIndex()]->minor,
            ['pattern' => $this->yystack[$this->getIndex() + -1]->minor . $this->yystack[$this->getIndex()]->minor]
        );
    }

    protected function yy_r52(): void
    {
        $this->_retvalue = new Token('\\' . $this->yystack[$this->getIndex()]->minor,
            ['pattern' => $this->yystack[$this->getIndex()]->minor]
        );
    }

    protected function yy_r53(): void
    {
        $this->_retvalue = new Token(
            '\\' . $this->yystack[$this->getIndex() + -1]->minor . $this->yystack[$this->getIndex()]->minor,
            ['pattern' => $this->yystack[$this->getIndex() + -1]->minor . $this->yystack[$this->getIndex()]->minor]
        );
    }

    protected function yy_r55(): void
    {
        $this->_retvalue = new Token('\\\\' . $this->yystack[$this->getIndex() + -1]->minor . $this->yystack[$this->getIndex()]->minor,
            ['pattern' => $this->yystack[$this->getIndex() + -1]->minor . $this->yystack[$this->getIndex()]->minor]
        );
    }

    /**
     * @throws Exception
     */
    protected function yy_r57(): void
    {
        if (((int)substr($this->yystack[$this->getIndex() + -1]->minor, 1)) > $this->_subPatterns) {
            throw new Exception(sprintf(
                'Back-reference refers to non-existent sub-pattern %s',
                substr($this->yystack[$this->getIndex() + -1]->minor, 1)
            ));
        }

        $this->yystack[$this->getIndex() + -1]->minor = substr($this->yystack[$this->getIndex() + -1]->minor, 1);

        // adjust back-reference for containing ()
        $this->_retvalue = new Token('\\\\' . ($this->yystack[$this->getIndex() + -1]->minor . $this->_patternIndex) . $this->yystack[$this->getIndex()]->minor,
            ['pattern' => '\\' . ($this->_updatePattern ? ($this->yystack[$this->getIndex() + -1]->minor . $this->_patternIndex) : $this->yystack[$this->getIndex() + -1]->minor) . $this->yystack[$this->getIndex()]->minor]
        );
    }

    protected function yy_r59(): void
    {
        if (((int)substr($this->yystack[$this->getIndex() + -1]->minor, 1)) > $this->_subPatterns) {
            throw new Exception(sprintf(
                '%s will be interpreted as an invalid back-reference, use "\\0%s for octal',
                $this->yystack[$this->getIndex() + -1]->minor,
                substr($this->yystack[$this->getIndex() + -1]->minor, 1)
            ));
        }

        $this->yystack[$this->getIndex() + -1]->minor = substr($this->yystack[$this->getIndex() + -1]->minor, 1);
        $this->_retvalue                              = new Token(
            '\\\\' . ($this->yystack[$this->getIndex() + -1]->minor . $this->_patternIndex) . $this->yystack[$this->getIndex()]->minor,
            ['pattern' => '\\' . ($this->_updatePattern ? ($this->yystack[$this->getIndex() + -1]->minor . $this->_patternIndex) : $this->yystack[$this->getIndex() + -1]->minor) . $this->yystack[$this->getIndex()]->minor]
        );
    }

    protected function yy_r61(): void
    {
        $this->_retvalue = new Token(
            $this->yystack[$this->getIndex() + -2]->minor->string . $this->yystack[$this->getIndex() + -1]->minor . $this->yystack[$this->getIndex()]->minor,
            ['pattern' => $this->yystack[$this->getIndex() + -2]->minor['pattern'] . $this->yystack[$this->getIndex() + -1]->minor . $this->yystack[$this->getIndex()]->minor]
        );
    }

    protected function yy_r65(): void
    {
        $this->_retvalue = new Token(
            $this->yystack[$this->getIndex() + -2]->minor->string . '\\' . $this->yystack[$this->getIndex() + -1]->minor . $this->yystack[$this->getIndex()]->minor,
            ['pattern' => $this->yystack[$this->getIndex() + -2]->minor['pattern'] . $this->yystack[$this->getIndex() + -1]->minor . $this->yystack[$this->getIndex()]->minor]
        );
    }

    protected function yy_r67(): void
    {
        $this->_retvalue = new Token(
            $this->yystack[$this->getIndex() + -2]->minor->string . '\\\\' . $this->yystack[$this->getIndex() + -1]->minor . $this->yystack[$this->getIndex()]->minor,
            ['pattern' => $this->yystack[$this->getIndex() + -2]->minor['pattern'] . $this->yystack[$this->getIndex() + -1]->minor . $this->yystack[$this->getIndex()]->minor]
        );
    }

    protected function yy_r69(): void
    {
        if (((int)substr($this->yystack[$this->getIndex() + -1]->minor, 1)) > $this->_subPatterns) {
            throw new Exception(sprintf(
                'Back-reference refers to non-existent sub-pattern %s',
                substr($this->yystack[$this->getIndex() + -1]->minor, 1)
            ));
        }

        $this->yystack[$this->getIndex() + -1]->minor = substr($this->yystack[$this->getIndex() + -1]->minor, 1);
        $this->_retvalue                              = new Token(
            $this->yystack[$this->getIndex() + -2]->minor->string . '\\\\' . ($this->yystack[$this->getIndex() + -1]->minor . $this->_patternIndex) . $this->yystack[$this->getIndex()]->minor,
            ['pattern' => $this->yystack[$this->getIndex() + -2]->minor['pattern'] . '\\' . ($this->_updatePattern ? ($this->yystack[$this->getIndex() + -1]->minor . $this->_patternIndex) : $this->yystack[$this->getIndex() + -1]->minor) . $this->yystack[$this->getIndex()]->minor]
        );
    }

    protected function yy_r71(): void
    {
        if (((int)substr($this->yystack[$this->getIndex() + -1]->minor, 1)) > $this->_subPatterns) {
            throw new Exception(sprintf(
                '%s will be interpreted as an invalid back-reference, use "\\0%s for octal',
                $this->yystack[$this->getIndex() + -1]->minor,
                substr($this->yystack[$this->getIndex() + -1]->minor, 1)
            ));
        }

        $this->yystack[$this->getIndex() + -1]->minor = substr($this->yystack[$this->getIndex() + -1]->minor, 1);
        $this->_retvalue                              = new Token(
            $this->yystack[$this->getIndex() + -2]->minor->string . '\\\\' . ($this->yystack[$this->getIndex() + -1]->minor . $this->_patternIndex) . $this->yystack[$this->getIndex()]->minor,
            ['pattern' => $this->yystack[$this->getIndex() + -2]->minor['pattern'] . '\\' . ($this->_updatePattern ? ($this->yystack[$this->getIndex() + -1]->minor . $this->_patternIndex) : $this->yystack[$this->getIndex() + -1]->minor) . $this->yystack[$this->getIndex()]->minor]
        );
    }

    /**
     * @throws Exception
     */
    protected function yy_r72(): void
    {
        throw new Exception(sprintf(
            'Error: cannot set preg options directly with "%s"',
            $this->yystack[$this->getIndex() + -2]->minor . $this->yystack[$this->getIndex() + -1]->minor . $this->yystack[$this->getIndex()]->minor
        ));
    }

    /**
     * @throws Exception
     */
    protected function yy_r73(): void
    {
        throw new Exception(sprintf(
            'Error: cannot set preg options directly with "%s"',
            $this->yystack[$this->getIndex() + -4]->minor . $this->yystack[$this->getIndex() + -3]->minor . $this->yystack[$this->getIndex() + -2]->minor . $this->yystack[$this->getIndex() + -1]->minor['pattern'] . $this->yystack[$this->getIndex()]->minor
        ));
    }

    protected function yy_r74(): void
    {
        $this->_retvalue = new Token(
            '(?:' . $this->yystack[$this->getIndex() + -1]->minor->string . ')',
            ['pattern' => '(?:' . $this->yystack[$this->getIndex() + -1]->minor['pattern'] . ')']
        );
    }

    protected function yy_r75(): void
    {
        $this->_retvalue = new Token(
            '(?:' . $this->yystack[$this->getIndex() + -2]->minor->string . ')' . $this->yystack[$this->getIndex()]->minor,
            ['pattern' => '(?:' . $this->yystack[$this->getIndex() + -2]->minor['pattern'] . ')' . $this->yystack[$this->getIndex()]->minor]
        );
    }

    /**
     * @throws Exception
     */
    protected function yy_r76(): void
    {
        if ($this->yystack[$this->getIndex() + -4]->minor != 'R') {
            if (!preg_match('/[1-9][0-9]*/', $this->yystack[$this->getIndex() + -4]->minor)) {
                throw new Exception('Invalid sub-pattern conditional: "(?(' . $this->yystack[$this->getIndex() + -4]->minor . ')"');
            }

            if ($this->yystack[$this->getIndex() + -4]->minor > $this->_subPatterns) {
                throw new Exception('sub-pattern conditional . "' . $this->yystack[$this->getIndex() + -4]->minor . '" refers to non-existent sub-pattern');
            }
        } else {
            throw new Exception('Recursive conditional (?(' . $this->yystack[$this->getIndex() + -4]->minor . ')" cannot work in this lexer');
        }

        $this->_retvalue = new Token(
            '(?(' . $this->yystack[$this->getIndex() + -4]->minor . ')' . $this->yystack[$this->getIndex() + -2]->minor->string . ')' . $this->yystack[$this->getIndex()]->minor,
            ['pattern' => '(?(' . $this->yystack[$this->getIndex() + -4]->minor . ')' . $this->yystack[$this->getIndex() + -2]->minor['pattern'] . ')' . $this->yystack[$this->getIndex()]->minor]
        );
    }

    /**
     * @throws Exception
     */
    protected function yy_r77(): void
    {
        if ($this->yystack[$this->getIndex() + -3]->minor != 'R') {
            if (!preg_match('/[1-9][0-9]*/', $this->yystack[$this->getIndex() + -3]->minor)) {
                throw new Exception('Invalid sub-pattern conditional: "(?(' . $this->yystack[$this->getIndex() + -3]->minor . ')"');
            }
            if ($this->yystack[$this->getIndex() + -3]->minor > $this->_subPatterns) {
                throw new Exception('sub-pattern conditional . "' . $this->yystack[$this->getIndex() + -3]->minor . '" refers to non-existent sub-pattern');
            }
        } else {
            throw new Exception('Recursive conditional (?(' . $this->yystack[$this->getIndex() + -3]->minor . ')" cannot work in this lexer');
        }

        $this->_retvalue = new Token(
            '(?(' . $this->yystack[$this->getIndex() + -3]->minor . ')' . $this->yystack[$this->getIndex() + -1]->minor->string . ')',
            ['pattern' => '(?(' . $this->yystack[$this->getIndex() + -3]->minor . ')' . $this->yystack[$this->getIndex() + -1]->minor['pattern'] . ')']
        );
    }

    protected function yy_r78(): void
    {
        $this->_retvalue = new Token(
            '(?' . $this->yystack[$this->getIndex() + -2]->minor->string . $this->yystack[$this->getIndex() + -1]->minor->string . ')',
            ['pattern' => '(?' . $this->yystack[$this->getIndex() + -2]->minor['pattern'] . $this->yystack[$this->getIndex() + -1]->minor['pattern'] . ')']
        );
    }

    protected function yy_r79(): void
    {
        $this->_retvalue = new Token('(?' . $this->yystack[$this->getIndex() + -3]->minor->string . $this->yystack[$this->getIndex() + -2]->minor->string . ')' . $this->yystack[$this->getIndex()]->minor,
            ['pattern' => '(?' . $this->yystack[$this->getIndex() + -3]->minor['pattern'] . $this->yystack[$this->getIndex() + -2]->minor['pattern'] . ')' . $this->yystack[$this->getIndex()]->minor]
        );
    }

    /**
     * @throws Exception
     */
    protected function yy_r80(): void
    {
        throw new Exception('Look-behind assertions cannot be used: "(?<=' .
            $this->yystack[$this->getIndex() + -1]->minor['pattern'] . ')');
    }

    /**
     * @throws Exception
     */
    protected function yy_r81(): void
    {
        throw new Exception('Look-behind assertions cannot be used: "(?<=' .
            $this->yystack[$this->getIndex() + -2]->minor['pattern'] . ')');
    }

    protected function yy_r82(): void
    {
        $this->_retvalue = new Token(
            '(?=' . $this->yystack[$this->getIndex() + -1]->minor->string . ')',
            ['pattern ' => '(?=' . $this->yystack[$this->getIndex() + -1]->minor['pattern'] . ')']
        );
    }

    protected function yy_r83(): void
    {
        $this->_retvalue = new Token(
            '(?!' . $this->yystack[$this->getIndex() + -1]->minor->string . ')',
            ['pattern' => '(?!' . $this->yystack[$this->getIndex() + -1]->minor['pattern'] . ')']
        );
    }

    /**
     * @throws Exception
     */
    protected function yy_r85(): void
    {
        throw new Exception('Look-behind assertions cannot be used: "(?<!' .
            $this->yystack[$this->getIndex() + -1]->minor['pattern'] . ')');
    }

    /**
     * @throws Exception
     */
    protected function yy_r86(): void
    {
        throw new Exception('Cannot use named sub-patterns: "(' .
            $this->yystack[$this->getIndex() + -2]->minor['pattern'] . ')');
    }

    /**
     * @throws Exception
     */
    protected function yy_r87(): void
    {
        throw new Exception('Cannot use named sub-patterns: "(' .
            $this->yystack[$this->getIndex() + -3]->minor['pattern'] . ')');
    }

    protected function yy_r88(): void
    {
        $this->_subPatterns++;
        $this->_retvalue = new Token(
            '(' . $this->yystack[$this->getIndex() + -1]->minor->string . ')',
            ['pattern' => '(' . $this->yystack[$this->getIndex() + -1]->minor['pattern'] . ')']
        );
    }

    protected function yy_r89(): void
    {
        $this->_subPatterns++;
        $this->_retvalue = new Token(
            '(' . $this->yystack[$this->getIndex() + -2]->minor->string . ')' . $this->yystack[$this->getIndex()]->minor,
            ['pattern' => '(' . $this->yystack[$this->getIndex() + -2]->minor['pattern'] . ')' . $this->yystack[$this->getIndex()]->minor]
        );
    }

    protected function yy_r90(): void
    {
        $this->_retvalue = new Token(
            '(?>' . $this->yystack[$this->getIndex() + -1]->minor->string . ')',
            ['pattern' => '(?>' . $this->yystack[$this->getIndex() + -1]->minor['pattern'] . ')']
        );
    }

    protected function yy_r91(): void
    {
        $this->_retvalue = new Token(
            '(' . $this->yystack[$this->getIndex() + -1]->minor->string . ')',
            ['pattern' => '(' . $this->yystack[$this->getIndex() + -1]->minor['pattern'] . ')']
        );
    }

    /**
     * @throws Exception
     */
    protected function yy_r92(): void
    {
        throw new Exception('(?R) cannot work in this lexer');
    }
}
