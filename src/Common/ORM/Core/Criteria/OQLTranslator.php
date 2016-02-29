<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Core\Criteria;

class OQLTranslator
{
    /**
     * List of operators.
     *
     * @var array
     */
    protected $operators = [
        'C_AND'          => 'and',
        'COMMA'          => ',',
        'C_OR'           => 'or',
        'G_OBRACKET'     => '(',
        'G_CBRACKET'     => ')',
        'G_OPARENTHESIS' => '(',
        'G_CPARENTHESIS' => ')',
        'O_EQUALS'       => '=',
        'O_GREAT'        => '>',
        'O_GREAT_EQUALS' => '>=',
        'O_IN'           => 'in',
        'O_LESS'         => '<',
        'O_LESS_EQUALS'  => '<=',
        'O_LIKE'         => 'like ',
        'O_NOT_EQUALS'   => '!=',
        'O_NOT_IN'       => 'not in',
        'O_NOT_LIKE'     => 'not like',
        'O_NOT_REGEX'    => 'not regex',
        'O_REGEX'        => 'regex ',
    ];

    /**
     * List of parameters.
     *
     * @var array
     */
    protected $params = [
        'T_BOOL'    => \PDO::PARAM_BOOL,
        'T_FLOAT'   => \PDO::PARAM_STR,
        'T_INTEGER' => \PDO::PARAM_INT,
        'T_NULL'    => \PDO::PARAM_NULL,
        'T_STRING'  => \PDO::PARAM_STR,
    ];

    /**
     * List of parameters.
     *
     * @var array
     */
    public function translate($oql)
    {
        $tokenizer = new OQLTokenizer();
        $tokens    = $tokenizer->tokenize($oql);

        $params = [];
        $types  = [];
        $sqls   = [];
        $isLike = false;
        foreach ($tokens as $token) {
            list($sql, $param, $type) =
                $this->translateToken($token[0], $token[1], $isLike);

            if (!empty($sql)) {
                $sqls[] = $sql;
            }

            if (!empty($param)) {
                $params[] = $param;
            }

            if (!empty($type)) {
                $types[] = $type;
            }

            $isLike = $token[1] === 'O_LIKE' || $token[1] === 'O_NOT_LIKE';
        }

        return [ implode(' ', $sqls), $params, $types ];
    }

    /**
     * Checks if the string is an operator.
     *
     * @param string $str The string to check.
     *
     * @return boolean True if the string is an operator.
     */
    protected function isOperator($str)
    {
        return in_array($str, array_keys($this->operators));
    }

    /**
     * Checks if the string is an operator.
     *
     * @param string $str The string to check.
     *
     * @return boolean True if the string is an operator.
     */
    protected function isParameter($str)
    {
        return in_array($str, array_keys($this->params));
    }

    /**
     * Translates a token.
     *
     * @param string  $str          The token to translate.
     * @param string  $type         The token type.
     * @param boolean $previousLike Whether the previous translation was a
     *                              like/not like operator.
     *
     * @return array An array with the translation, the parameter value and the
     *               parameter type..
     */
    protected function translateToken($str, $type, $previousLike)
    {
        if (!$this->isOperator($type)
            && (!$this->isParameter($type)
            || $this->isParameter($type) && $previousLike)
        ) {
            return [ $str, null, null ];
        }

        if ($this->isOperator($type)) {
            return [ $this->operators[$type], null, null ];
        }

        return [ '?', $str, $this->params[$type] ];
    }
}
