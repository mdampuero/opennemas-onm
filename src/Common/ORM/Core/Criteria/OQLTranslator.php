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
        'O_NOT_LIKE'     => '!~',
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
        $sql    = [];
        foreach ($tokens as $token) {
            $isOperator  = $this->isOperator($token[1]);
            $isParameter = $this->isParameter($token[1]);

            if (!$isOperator && !$isParameter) {
                $sql[] = $token[0];
            }

            if ($isOperator) {
                $sql[] = $this->operators[$token[1]];
            }

            if ($isParameter) {
                $params[] = $token[0];
                $types[]  = $this->params[$token[1]];
                $sql[]    = '?';
            }
        }

        return [ implode(' ', $sql), $params, $types ];
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
}
