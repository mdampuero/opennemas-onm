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

use Common\ORM\Core\Exception\InvalidTokenException;
use Common\ORM\Core\Metadata;

/**
 * The OQLTranslator class translates the internal representation of an OQL
 * query to conditions, parameters and types ready to use by SQL queries.
 */
class OQLTranslator
{
    /**
     * List of operators.
     *
     * @var array
     */
    protected $translations = [
        'C_AND'          => 'and',
        'COMMA'          => ',',
        'C_OR'           => 'or',
        'G_OBRACKET'     => '(',
        'G_CBRACKET'     => ')',
        'G_OPARENTHESIS' => '(',
        'G_CPARENTHESIS' => ')',
        'M_ASC'          => 'asc',
        'M_DESC'         => 'desc',
        'M_ORDER'        => 'order by',
        'M_LIMIT'        => 'limit',
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
     * Initializes the OQLTranslator.
     *
     * @param Metadata $metadata The entity metadata.
     */
    public function __construct(Metadata $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * List of parameters.
     *
     * @var array
     */
    public function translate($oql)
    {
        if (empty($oql)) {
            return [ [], [], [] ];
        }

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
     * Checks if the string is a valid field basing on the entity metadata.
     *
     * @param string $str  The string to check.
     * @param string $type The token type.
     *
     * @return boolean True if the string is a field.
     */
    protected function isField($str, $type)
    {
        return $type === 'T_FIELD'
            && array_key_exists($str, $this->metadata->properties);
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
     * Checks if the string is a translatable token.
     *
     * @param string $str The string to check.
     *
     * @return boolean True if the string is translatable.
     */
    protected function isTranslatable($str)
    {
        return in_array($str, array_keys($this->translations));
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
        if ($this->isTranslatable($type)) {
            return [ $this->translations[$type], null, null ];
        }

        if ($this->isParameter($type) && !$previousLike) {
            // Remove quotes for strings
            if ($type === 'T_STRING') {
                $str = str_replace([ '\'', '\"' ], '', $str);
            }

            return [ '?', $str, $this->params[$type] ];
        }

        if ($this->isField($str, $type)
            || ($this->isParameter($type) && $previousLike)
        ) {
            return [ $str, null, null ];
        }

        throw new InvalidTokenException($str, $this->metadata->name);
    }
}
