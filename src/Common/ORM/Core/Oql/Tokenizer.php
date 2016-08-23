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
        'M_OFFSET'       => '/\s*offset\s*/',
        'M_ORDER'        => '/\s*order\s*/',
        'M_BY'           => '/\s*by\s*/',
        'O_GREAT_EQUALS' => '/\s*>=\s*/',
        'O_LESS_EQUALS'  => '/\s*<=\s*/',
        'O_NOT_EQUALS'   => '/\s*!=\s*/',
        'O_NOT_IN'       => '/\s*!in\s*/',
        'O_NOT_LIKE'     => '/\s*!~\s*/',
        'O_NOT_REGEXP'   => '/\s*!regexp\s*/',
        'O_EQUALS'       => '/\s*=\s*/',
        'O_GREAT'        => '/\s*>\s*/',
        'O_IN'           => '/\s*in\s*/',
        'O_IS'           => '/\s*is\s*/',
        'O_NOT_IS'       => '/\s*!is\s*/',
        'O_LESS'         => '/\s*<\s*/',
        'O_LIKE'         => '/\s*~\s*/',
        'O_REGEXP'       => '/\s*regexp\s*/',
        'T_BOOL'         => '/true|false/',
        'T_DATETIME'     => '/\'\d{4}\-\d{2}\-\d{2} \d{2}\:\d{2}\:\d{2}\'|\"\d{4}\-\d{2}\-\d{2} \d{2}\:\d{2}\:\d{2}\"/',
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
        'S_LIMIT'     => '/M_LIMIT T_INTEGER/',
        'S_OFFSET'    => '/M_OFFSET T_INTEGER/',
        'T_LITERAL'   => '/T_ARRAY|T_BOOL|T_DATETIME|T_FLOAT|T_INTEGER|T_NULL|T_STRING/',
        'T_ARRAY'     => '/G_OBRACKET\s*T_LITERAL\s*(COMMA\s*T_LITERAL\s*)*\s*G_CBRACKET/',
        'S_ORDER'     => '/M_ORDER\s*M_BY\s*T_FIELD\s*(M_ASC|M_DESC)(\s*COMMA\s*T_FIELD\s*(M_ASC|M_DESC))*/',
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
        $query      = implode(' ', $tokens);
        $translated = '';
        $i = 0;

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
        $tokens = [];
        $i      = 0;
        $prev   = '';
        $index  = 0;
        $token  = '';

        while ($index < strlen($query)) {
            if ($this->isToken($token . $query[$index]) || !$this->isToken($token)) {
                $token = $token . $query[$index];
            } elseif ($this->isToken($token)) {
                $tokens[] = $token;
                $token    = $query[$index];
            }

            $index++;
        }

        if ($this->isToken($token)) {
            $tokens[] = $token;

            return $tokens;
        }


        throw new InvalidTokenException($token);
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
