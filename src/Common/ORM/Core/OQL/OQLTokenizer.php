<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Core\OQL;

use Common\ORM\Core\Exception\InvalidTokenException;
use Common\ORM\Core\Exception\InvalidQueryException;

/**
 * The OQLTokenizer class parses and splits OQL queries into tokens.
 */
class OQLTokenizer
{
    /**
     * Array of valid tokens.
     *
     * @var array
     */
    protected $tokens = [
        'COMMA'          => '/\s*,\s*/',
        'C_AND'          => '/\s*and\s*/',
        'C_OR'           => '/\s*or\s*/',
        'G_CPARENTHESIS' => '/\)/',
        'G_CBRACKET'     => '/\]/',
        'G_OBRACKET'     => '/\[/',
        'G_OPARENTHESIS' => '/\(/',
        'M_ASC'          => '/\s*asc\s*/',
        'M_DESC'         => '/\s*desc\s*/',
        'M_LIMIT'        => '/\s*limit\s*/',
        'M_OFFSET'       => '/\s*offset\s*/',
        'M_ORDER'        => '/\s*order by\s*/',
        'O_GREAT_EQUALS' => '/\s*>=\s*/',
        'O_LESS_EQUALS'  => '/\s*<=\s*/',
        'O_NOT_EQUALS'   => '/\s*!=\s*/',
        'O_NOT_IN'       => '/\s*!in\s*/',
        'O_NOT_LIKE'     => '/\s*!~\s*/',
        'O_NOT_REGEXP'   => '/\s*!in\s*/',
        'O_EQUALS'       => '/\s*=\s*/',
        'O_GREAT'        => '/\s*>\s*/',
        'O_IN'           => '/\s*in\s*/',
        'O_IS'           => '/\s*is\s*/',
        'O_NOT_IS'       => '/\s*!is\s*/',
        'O_LESS'         => '/\s*<\s*/',
        'O_LIKE'         => '/\s*~\s*/',
        'O_REGEXP'       => '/\s*\^\s*/',
        'T_BOOL'         => '/true|false/',
        'T_NULL'         => '/null/',
        'T_FLOAT'        => '/-?[0-9]+\.[0-9]+/',
        'T_INTEGER'      => '/-?[0-9]+/',
        'T_STRING'       => '/\'[^\']*\'|\"[^\"]*\"/',
        'T_FIELD'        => '/[a-zA-Z0-9\_\.]+/',
    ];

    /**
     * Array of valid token combinations.
     *
     * @var array
     */
    protected $sentences = [
        'T_CONNECTOR' => '/C_AND|C_OR/',
        'T_OPERATOR'  => '/O_GREAT_EQUALS|O_LESS_EQUALS|O_EQUALS|O_GREAT|O_IN|O_IS|O_LESS|O_LIKE|O_NOT_EQUALS|O_NOT_IN|O_NOT_IS|O_NOT_LIKE|O_NOT_REGEXP|O_REGEXP/',
        'T_LITERAL'   => '/T_ARRAY|T_BOOL|T_FLOAT|T_INTEGER|T_NULL|T_STRING/',
        'T_ARRAY'     => '/G_OBRACKET\s*T_LITERAL\s*(COMMA\s*T_LITERAL\s*)*\s*G_CBRACKET/',
        'S_LIMIT'     => '/M_LIMIT T_INTEGER/',
        'S_OFFSET'    => '/M_OFFSET T_INTEGER/',
        'S_ORDER'     => '/M_ORDER\s*T_FIELD\s*(M_ASC|M_DESC)(\s*COMMA\s*T_FIELD\s*(M_ASC|M_DESC))*/',
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

        // Translate tokens to internal OQL representation
        $translated = [];
        foreach ($tokens as &$token) {
            $translated[] = $this->translateToken($token);
        }

        $this->checkOQL($translated);

        $map = [];
        for ($i = 0; $i < count($tokens); $i++) {
            $map[] = [ $tokens[$i], $translated[$i] ];
        }

        return $map;
    }

    /**
     * Checks if an OQL query is well-formed basing on the given tokens.
     *
     * @param array $tokens The OQL tokens.
     */
    protected function checkOQL($tokens)
    {
        while ((count($tokens) > 1 || $tokens[0] !== 'OQL')) {
            $sentence = $this->sentenize($tokens);

            // Can't build sentence from tokens
            if ($sentence === $tokens) {
                throw new InvalidQueryException($tokens[0]);
            }

            $tokens = $sentence;
        }
    }

    /**
     * Returns the tokens of the query.
     *
     * @param string $query The query to tokenize.
     *
     * @return array The list of tokens.
     *
     * @throws InvalidTokenException If one or more invalid tokens are found in
     *                               the query.
     */
    protected function getTokens($query)
    {
        $end    = strlen($query);
        $tokens = [];

        while ($query !== '' && $end > 0) {
            $token = substr($query, 0, $end--);

            if ($this->isToken($token)) {
                $query = substr($query, strlen($token));
                $end   = strlen($query);

                $tokens[] = trim($token);
            }
        }

        if (!empty($query)) {
            throw new InvalidTokenException($query);
        }

        return $tokens;
    }

    /**
     * Checks if a string is a valid sentence.
     *
     * @param string $str The string to check.
     *
     * @return boolean True if the string is a valid sentence. Otherwise,
     *                 returns false.
     */
    protected function isSentence($str)
    {
        return $this->translateSentence($str) !== false;
    }

    /**
     * Checks if a string is a valid token.
     *
     * @param string $str The string to check.
     *
     * @return boolean True if the string is a valid token. Otherwise, returns
     *                 false.
     */
    protected function isToken($str)
    {
        return $this->translateToken($str) !== false;
    }

    /**
     * Translates a string basing on the source.
     *
     * @param string $str    The string to translate.
     * @param array  $source The translation source.
     *
     * @return mixed The translated string if the token can be translated.
     *               Otherwise, returns false.
     */
    protected function match($str, $source)
    {
        foreach ($source as $replacement => $pattern) {
            preg_match_all($pattern, $str, $matches);

            if (count($matches[0]) === 1 && $matches[0][0] === $str) {
                return $replacement;
            }
        }

        return false;
    }

    /**
     * Returns the internal representation of an OQL sentence built with the
     * given tokens.
     *
     * @param string $tokens The list of tokens.
     *
     * @return array The sentence.
     */
    protected function sentenize($tokens)
    {
        $end        = count($tokens);
        $translated = [];

        while (!empty($tokens)) {
            $token      = implode(' ', array_slice($tokens, 0, $end--));
            $isSentence = $this->isSentence($token);

            // Translate token if it is a sentence
            if ($isSentence) {
                $token = $this->translateSentence($token);
            }

            // Remove from list if sentence or end of the list of tokens
            if ($isSentence || $end === 0) {
                $translated[] = $token;
                $tokens       = array_slice($tokens, $end + 1);
                $end          = count($tokens);
            }
        }

        return $translated;
    }

    /**
     * Translates a OQL sentence to the internal representation.
     *
     * @param string $token The sentence to translate.
     *
     * @return string The translated sentence.
     */
    protected function translateSentence($sentence)
    {
        return $this->match($sentence, $this->sentences);
    }

    /**
     * Translates a token to the internal representation.
     *
     * @param string $token The token to translate.
     *
     * @return string The translated token.
     */
    protected function translateToken($token)
    {
        return $this->match($token, $this->tokens);
    }
}
