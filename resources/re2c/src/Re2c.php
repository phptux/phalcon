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

/**
 * The basic home class for the lexer generator.
 *
 * A lexer scans text and organizes it into tokens for usage by a parser.
 *
 * Sample Usage:
 *
 * ```php
 * $lex = new \Azirax\Re2c\Re2c('/path/to/lexerfile.plex');
 * ```
 *
 * A file named "/path/to/lexerfile.php" will be created.
 *
 * File format consists of a PHP file containing specially
 * formatted comments like so:
 *
 * ```php
 * /*!lex2php
 * {@*}
 * ```
 *
 * All lexer definition files must contain at least two lex2php comment blocks:
 *
 * - 1 regex declaration block
 * - 1 or more rule declaration blocks
 *
 * The first lex2php comment is the regex declaration block and must contain
 * several processor instruction as well as defining a name for all
 * regular expressions.  Processor instructions start with
 * a "%" symbol and must be:
 *
 * - %counter
 * - %input
 * - %token
 * - %value
 * - %line
 *
 * Token and counter should define the class variables used to define lexer input
 * and the index into the input.  token and value should be used to define the class
 * variables used to store the token number and its textual value.  Finally, line
 * should be used to define the class variable used to define the current line number
 * of scanning.
 *
 * For example:
 *
 * ```
 * /*!lex2php
 * %counter {$this->N}
 * %input {$this->data}
 * %token {$this->token}
 * %value {$this->value}
 * %line {%this->linenumber}
 * {@*}
 * ```
 *
 * Patterns consist of an identifier containing an letters or an underscore, and
 * a descriptive match pattern.
 *
 * Descriptive match patterns may either be regular expressions (regexes) or
 * quoted literal strings.  Here are some examples:
 *
 * ```
 * pattern = "quoted literal"
 * ANOTHER = /[a-zA-Z_]+/
 * COMPLEX = @<([a-zA-Z_]+)( +(([a-zA-Z_]+)=((["\'])([^\6]*)\6))+){0,1}>[^<]*</\1>@
 * ```
 *
 * Quoted strings must escape the \ and " characters with \" and \\.
 *
 * Regex patterns must be in Perl-compatible regular expression format (preg).
 * special characters (like \t \n or \x3H) can only be used in regexes, all
 * \ will be escaped in literal strings.
 *
 * Sub-patterns may be defined and back-references (like \1) may be used.  Any sub-
 * patterns detected will be passed to the token handler in the variable
 * $yysubmatches.
 *
 * In addition, lookahead expressions, and once-only expressions are allowed.
 * Lookbehind expressions are impossible (scanning always occurs from the
 * current position forward), and recursion (?R) can't work and is not allowed.
 *
 * ```
 * /*!lex2php
 * %counter {$this->N}
 * %input {$this->data}
 * %token {$this->token}
 * %value {$this->value}
 * %line {%this->linenumber}
 * alpha = /[a-zA-Z]/
 * alphaplus = /[a-zA-Z]+/
 * number = /[0-9]/
 * numerals = /[0-9]+/
 * whitespace = /[ \t\n]+/
 * blah = "$\""
 * blahblah = /a\$/
 * GAMEEND = @(?:1\-0|0\-1|1/2\-1/2)@
 * PAWNMOVE = /P?[a-h]([2-7]|[18]\=(Q|R|B|N))|P?[a-h]x[a-h]([2-7]|[18]\=(Q|R|B|N))/
 * {@*}
 * ```
 *
 * All regexes must be delimited.  Any legal preg delimiter can be used (as in @ or / in
 * the example above)
 *
 * Rule lex2php blocks each define a lexer state.  You can optionally name the state
 * with the %statename processor instruction.  State names can be used to transfer to
 * a new lexer state with the yybegin() method
 *
 * ```
 * /*!lexphp
 * %statename INITIAL
 * blah {
 *     $this->yybegin(self::INBLAH);
 *     // note - $this->yybegin(2) would also work
 * }
 * {@*}
 * /*!lex2php
 * %statename INBLAH
 * ANYTHING {
 *     $this->yybegin(self::INITIAL);
 *     // note - $this->yybegin(1) would also work
 * }
 * {@*}
 * ```
 *
 * You can maintain a parser state stack simply by using `pushState()` and
 * `popState()` instead of `begin()`:
 *
 * ```
 * /*!lexphp
 * %statename INITIAL
 * blah {
 *     $this->pushState(self::INBLAH);
 * }
 * {@*}
 * /*!lex2php
 * %statename INBLAH
 * ANYTHING {
 *     $this->popState();
 *     // now INBLAH doesn't care where it was called from
 * }
 * {@*}
 * ```
 *
 * Code blocks can choose to skip the current token and cycle to the next token by
 * returning "false"
 *
 * ```
 * /*!lex2php
 * WHITESPACE {
 *     return false;
 * }
 * {@*}
 * ```
 *
 * If you wish to re-process the current token in a new state, simply return true.
 * If you forget to change lexer state, this will cause an unterminated loop,
 * so be careful!
 *
 * ```
 * /*!lex2php
 * "(" {
 *     $this->pushState(self::INPARAMS);
 *     return true;
 * }
 * {@*}
 * ```
 *
 * Lastly, if you wish to cycle to the next matching rule, return any value other than
 * true, false or null:
 *
 * ```
 * /*!lex2php
 * "{@" ALPHA {
 *     if ($this->value == '{@internal') {
 *         return 'more';
 *     }
 *     ...
 * }
 * "{@internal" {
 *     ...
 * }
 * {@*}
 * ```
 *
 * Note that this procedure is exceptionally inefficient, and it would be far better
 * to take advantage of Azirax\Re2c's top-down precedence and instead code:
 *
 * ```
 * /*!lex2php
 * "{@internal" {
 *     ...
 * }
 * "{@" ALPHA {
 *     ...
 * }
 * {@*}
 * ```
 *
 * @package    Azirax\Re2c
 * @author     Rene Dziuba <php.tux@web.de>
 * @copyright  Copyright (c) 2023 The Authors
 * @license    <http://opensource.org/licenses/bsd-license.php> New BSD License
 */
class Re2c
{
    /**
     * Plex file lexer.
     *
     * @var Lexer|null
     */
    protected ?Lexer $lex = null;

    /**
     * Plex file parser.
     *
     * @var Parser|null
     */
    protected ?Parser $parser = null;

    /**
     * Path to the output PHP file.
     *
     * @var string|null
     */
    protected ?string $outfile = null;

    /**
     * Debug flag. When set, Parser trace information is generated.
     *
     * @var bool
     */
    public bool $debug = true;

    /**
     * Create a lexer generator and optionally generate a lexer file.
     *
     * @param string $lexerFile Optional plex file.
     * @param string $outfile   Optional output file.
     */
    public function __construct(string $lexerFile = '', string $outfile = '')
    {
        if ($lexerFile) {
            $this->create($lexerFile, $outfile);
        }
    }

    /**
     * Create a lexer file from its skeleton plex file.
     *
     * @param string $lexerFile Path to the plex file.
     * @param string $outfile   Optional path to output file.
     *                          Default is lexer-file with extension of ".php".
     */
    protected function create(string $lexerFile, string $outfile = ''): void
    {
        $this->lex = new Lexer(file_get_contents($lexerFile));
        $info      = pathinfo($lexerFile);

        if ($outfile) {
            $this->outfile = $outfile;
        } else {
            $this->outfile = $info['dirname'] . DIRECTORY_SEPARATOR .
                substr($info['basename'], 0,
                    strlen($info['basename']) - strlen($info['extension'])) . 'php';
        }

        $this->parser = new Parser($this->outfile, $this->lex);

        if ($this->debug) {
            $this->parser->PrintTrace();
        }

        while ($this->lex->advance()) {
            $this->parser->doParse($this->lex->token, $this->lex->value);
        }

        $this->parser->doParse(0, 0);
    }
}
