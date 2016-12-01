<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Core\Oql;

use Common\ORM\Core\Exception\InvalidTokenException;
use Common\ORM\Core\Exception\InvalidQueryException;

/**
 * The Tokenizer class parses and splits OQL queries into tokens.
 */
class Tokenizer
{
    /**
     * List of patterns to check by step when tokenizing.
     *
     * @var array
     */
    protected $steps = [
        [ 'T_STRING' ],
        [
            'COMMA', 'C_AND', 'C_OR', 'G_CPARENTHESIS', 'G_CBRACKET',
            'G_OBRACKET', 'G_OPARENTHESIS', 'M_ASC', 'M_DESC', 'M_LIMIT',
            'M_OFFSET', 'M_ORDER_BY', 'O_GREAT_EQUALS', 'O_LESS_EQUALS',
            'O_NOT_EQUALS', 'O_NOT_IN', 'O_NOT_LIKE', 'O_NOT_REGEXP',
            'O_EQUALS', 'O_GREAT', 'O_IN', 'O_IS', 'O_NOT_IS', 'O_LESS',
            'O_LIKE', 'O_REGEXP',
        ],
        [
            'T_BOOL', 'T_DATETIME', 'T_NULL', 'T_FLOAT', 'T_INTEGER', 'T_FIELD',
        ]
    ];

    /**
     * Array of valid tokens.
     *
     * @var array
     */
    protected $tokens = [
        'COMMA'          => '/\s*,\s*/',
        'C_AND'          => '/\s+and\s+/',
        'C_OR'           => '/\s+or\s+/',
        'G_CPARENTHESIS' => '/\)/',
        'G_CBRACKET'     => '/\]/',
        'G_OBRACKET'     => '/\[/',
        'G_OPARENTHESIS' => '/\(/',
        'M_ASC'          => '/\s*asc\s*/',
        'M_DESC'         => '/\s*desc\s*/',
        'M_LIMIT'        => '/\s*limit\s+/',
        'M_OFFSET'       => '/\s*offset\s+/',
        'M_ORDER_BY'     => '/\s*order by\s+/',
        'O_GREAT_EQUALS' => '/\s*>=\s*/',
        'O_LESS_EQUALS'  => '/\s*<=\s*/',
        'O_NOT_EQUALS'   => '/\s*!=\s*/',
        'O_NOT_IN'       => '/\s+!in\s+/',
        'O_NOT_LIKE'     => '/\s*!~\s*/',
        'O_NOT_REGEXP'   => '/\s+!regexp\s+/',
        'O_EQUALS'       => '/\s*=\s*/',
        'O_GREAT'        => '/\s*>\s*/',
        'O_IN'           => '/\s+in\s+/',
        'O_IS'           => '/\s+is\s+/',
        'O_NOT_IS'       => '/\s+!is\s+/',
        'O_LESS'         => '/\s*<\s*/',
        'O_LIKE'         => '/\s*~\s*/',
        'O_REGEXP'       => '/\s+regexp\s+/',
        'T_BOOL'         => '/true|false/',
        'T_DATETIME'     => '/\'\d{4}\-\d{2}\-\d{2} \d{2}\:\d{2}\:\d{2}\'|\"\d{4}\-\d{2}\-\d{2} \d{2}\:\d{2}\:\d{2}\"/',
        'T_NULL'         => '/null/',
        'T_FLOAT'        => '/-?[0-9]+\.[0-9]+/',
        'T_INTEGER'      => '/-?[0-9]+/',
        'T_STRING'       => '/\'[^\']*\'|\"[^\"]*\"/',
        'T_FIELD'        => '/[a-z][a-zA-Z0-9\_\.]+/',
    ];

    /**
     * Array of valid token combinations.
     *
     * @var array
     */
    protected $sentences = [
        'T_CONNECTOR' => '/C_AND|C_OR/',
        'T_OPERATOR'  => '/O_GREAT_EQUALS|O_LESS_EQUALS|O_EQUALS|O_GREAT|O_IN|O_IS|O_LESS|O_LIKE|O_NOT_EQUALS|O_NOT_IN|O_NOT_IS|O_NOT_LIKE|O_NOT_REGEXP|O_REGEXP/',
        'S_LIMIT'     => '/M_LIMIT T_INTEGER/',
        'S_OFFSET'    => '/M_OFFSET T_INTEGER/',
        'T_LITERAL'   => '/T_ARRAY|T_BOOL|T_DATETIME|T_FLOAT|T_INTEGER|T_NULL|T_STRING/',
        'T_ARRAY'     => '/G_OBRACKET\s*T_LITERAL\s*(COMMA\s*T_LITERAL\s*)*\s*G_CBRACKET/',
        'S_ORDER'     => '/M_ORDER_BY\s*T_FIELD\s*(M_ASC|M_DESC)(\s*COMMA\s*T_FIELD\s*(M_ASC|M_DESC))*/',
        'S_MODIFIER'  => '/S_ORDER(\s*S_LIMIT\s*(S_OFFSET)?)?|(S_ORDER)?\s*S_LIMIT(\s*S_OFFSET)?/',
        'S_CONDITION' => '/T_FIELD\s*T_OPERATOR\s*(T_FIELD|T_LITERAL)|T_LITERAL\s*T_OPERATOR\s*T_FIELD|G_OPARENTHESIS\s*S_CONDITION\s*G_CPARENTHESIS|S_CONDITION(\s*T_CONNECTOR\s*S_CONDITION)+/',
        'OQL'         => '/G_OPARENTHESIS\s*OQL\s*G_CPARENTHESIS|OQL(\s*T_CONNECTOR\s*OQL)+|S_CONDITION(\s*S_MODIFIER|OQL)*|OQL\s*S_MODIFIER|OQL\s*OQL|^S_MODIFIER$/',
    ];

    /**
     * Translates an OQL query to a MySQL query.
     *
     * @param string $query The OQL query.
     *
     * @return string The MySQL query.
     */
    public function tokenize($query)
    {
        $tokens = $this->getTokens($query);

        // Get internal OQL tokens
        $translated = array_map(function ($a) {
            return $a[1];
        }, $tokens);

        $this->checkOQL($translated);

        return $tokens;
    }

    /**
     * Checks if an OQL query is well-formed basing on the given tokens.
     *
     * @param array $tokens The OQL tokens.
     */
    protected function checkOQL($tokens)
    {
        $query      = implode(' ', $tokens);
        $translated = '';

        while ($query !== 'OQL' && $query !== $translated) {
            $translated = $query;

            foreach ($this->sentences as $key => $sentence) {
                $translated = preg_replace($sentence, $key, $translated);
            }

            if ($query === $translated) {
                throw new InvalidQueryException($query);
            }

            $query      = $translated;
            $translated = '';
        }
    }

    /**
    * Returns the list of tokens and OQL internal representation from an OQL
    * query.
     *
     * @param string $query The query to tokenize.
     *
     * @return array The list of tokens and the OQL internal representation.
     *
     * @throws InvalidTokenException If one or more invalid tokens are found in
     *                               the query.
     */
    protected function getTokens($query)
    {
        $matrix = [];
        $tokens = [ $query ];
        foreach ($this->steps as $step) {
            foreach ($tokens as $token) {
                $replacement = $this->replaceTokens($token, $matrix, $step);

                // Replace token by replacement
                $pattern = '/' . preg_quote($token) . '/';
                $query   = preg_replace($pattern, $replacement, $query, 1);
            }

            // Split query in tokens to ignore replacements
            $tokens = preg_split('/' . implode('|', $step) . '/', $query);
            $tokens = array_filter($tokens, function ($a) {
                return trim($a) !== "";
            });
        }

        // Split internal query representation into tokens
        $tokens = explode(' ', preg_replace('/\s+/', ' ', trim($query)));

        // Build OQL map
        $map = [];
        foreach ($tokens as $token) {
            if (!array_key_exists($token, $matrix)) {
                throw new InvalidTokenException($token);
            }

            $replacement = array_shift($matrix[$token]);
            $map[]       = [ trim($replacement), $token ];
        }

        return $map;
    }

    /**
     * Replaces tokens into a query.
     *
     * @param string $query  The query to replace into.
     * @param array  $matrix The list of replaced strings.
     * @param array  $tokens The tokens to try to replace.
     *
     * @return string The replacement query.
     */
    protected function replaceTokens($query, &$matrix, $tokens)
    {
        foreach ($tokens as $replacement) {
            $pattern = $this->tokens[$replacement];

            if (preg_match_all($pattern, $query, $matches)) {
                if (!array_key_exists($replacement, $matrix)) {
                    $matrix[$replacement] = [];
                }

                $matrix[$replacement] =
                    array_merge($matrix[$replacement], $matches[0]);

                $query = str_replace($matches[0], " $replacement ", $query);
            }
        }

        return $query;
    }
}
