<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Core\Oql\Sql;

use Common\ORM\Core\Exception\InvalidTokenException;
use Common\ORM\Core\Metadata;
use Common\ORM\Core\Oql\Tokenizer;

/**
 * The SqlTranslator class translates the internal representation of an OQL
 * query to conditions, parameters and types ready to use by SQL queries.
 */
class SqlTranslator
{
    /**
     * Flag to enable/disable ignore mode.
     *
     * @var boolean
     */
    protected $ignoreMode = false;

    /**
     * List of operators that can be ignored.
     *
     * @var array
     */
    protected $ignorable = [ 'M_BY', 'M_LIMIT',  'M_OFFSET', 'M_ORDER_BY' ];

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
        'M_ORDER_BY'     => 'order by',
        'M_LIMIT'        => 'limit',
        'M_OFFSET'       => 'offset',
        'O_EQUALS'       => '=',
        'O_GREAT'        => '>',
        'O_GREAT_EQUALS' => '>=',
        'O_IN'           => 'in',
        'O_IS'           => 'is',
        'O_LESS'         => '<',
        'O_LESS_EQUALS'  => '<=',
        'O_LIKE'         => 'like ',
        'O_NOT_EQUALS'   => '!=',
        'O_NOT_IN'       => 'not in',
        'O_NOT_IS'       => 'is not',
        'O_NOT_LIKE'     => 'not like',
        'O_NOT_REGEXP'   => 'not regexp',
        'O_REGEXP'       => 'regexp',
        'T_NULL'         => 'null'
    ];

    /**
     * List of parameters.
     *
     * @var array
     */
    protected $pdoParams = [
        'T_BOOL'     => \PDO::PARAM_BOOL,
        'T_FLOAT'    => \PDO::PARAM_STR,
        'T_INTEGER'  => \PDO::PARAM_INT,
        'T_STRING'   => \PDO::PARAM_STR,
        'T_DATETIME' => \PDO::PARAM_STR,
    ];

    /**
     * Initializes the SqlTranslator.
     *
     * @param Metadata $metadata The entity metadata.
     */
    public function __construct(Metadata $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * Translates an OQL query and returns the parameters to build a SQL query.
     *
     * @param string  $oql     The OQL query.
     * @param boolean $exclude Whether to exclude some tokens.
     *
     * @return array A list with tables, conditions, parameters and types to use
     *               to build a SQL query.
     */
    public function translate($oql = '', $ignoreMode = false)
    {
        $this->ignoreMode = $ignoreMode;
        $this->tables     = [ $this->metadata->getTable() ];
        $this->params     = [];
        $this->types      = [];
        $this->sqls       = [];

        $isLike = false;

        if (empty($oql)) {
            return [ $this->tables, '', [], [] ];
        }

        $tokenizer = new Tokenizer();
        $tokens    = $tokenizer->tokenize($oql);

        $i = 0;
        while ($i < count($tokens) && !$this->isIgnorable($tokens[$i][1])) {
            $token = $tokens[$i++];

            list($sql, $param, $type) =
                $this->translateToken($token[0], $token[1], $isLike);

            if (!empty($sql)) {
                $this->sqls[] = $sql;
            }

            if (!is_null($param)) {
                $this->params[] = $param;
            }

            if (!empty($type)) {
                $this->types[] = $type;
            }

            $isLike = $token[1] === 'O_LIKE' || $token[1] === 'O_NOT_LIKE';
        }

        return [
            array_unique($this->tables),
            implode(' ', $this->sqls),
            $this->params,
            $this->types
        ];
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
        if ($type !== 'T_FIELD') {
            return false;
        }

        if (array_key_exists($str, $this->metadata->properties)) {
            return true;
        }

        if ($this->metadata->hasMetas()) {
            return true;
        }

        return false;
    }

    /**
     * Checks if the token has to be ignored when ignore mode is enabled.
     *
     * @param string $token The token to check.
     *
     * @return boolean True if the token is ignorable.
     */
    protected function isIgnorable($token)
    {
        return $this->ignoreMode && in_array($token, $this->ignorable);
    }

    /**
     * Checks if the string is a parameter.
     *
     * @param string $str The string to check.
     *
     * @return boolean True if the string is a parameter.
     */
    protected function isParameter($str)
    {
        return in_array($str, array_keys($this->pdoParams));
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
     * Translates a field.
     *
     * @param string $str The field to translate.
     *
     * @return null|array An array with the translation.
     */
    protected function translateField($str)
    {
        // Recognized columns
        $columns = array_keys($this->metadata->mapping['database']['columns']);

        if (in_array($str, $columns)) {
            return [ $str, null, null ];
        }

        // Search by field in relations table
        if ($this->metadata->hasRelations()
            && in_array($str, $this->metadata->getRelationColumns())
        ) {
            $this->translateFieldInRelation($str);
            return null;
        }

        // Search by field in meta table
        if ($this->metadata->hasMetas()) {
            $this->translateFieldInMeta($str);
        }
    }

    /**
     * Translates a field when it refers to a meta key.
     *
     * @param string $str The field name.
     */
    protected function translateFieldInMeta($str)
    {
        // Seach by meta_key
        $this->tables[] = $this->metadata->getMetaTable();

        // Push join condition
        $keys = $this->metadata->getMetaKeys();
        foreach ($keys as $tableId => $metaId) {
            $this->sqls[] = sprintf(
                '%s.%s = %s.%s',
                $this->metadata->mapping['database']['table'],
                $tableId,
                $this->metadata->getMetaTable(),
                $metaId
            );
        }

        // Push meta-based filters
        $this->sqls[]   = "and meta_key = ? and meta_value";
        $this->params[] = $str;
        $this->types[]  = \PDO::PARAM_STR;
    }

    /**
     * Translates a field when it refers to a column in a relation table.
     *
     * @param string $str The field name.
     */
    protected function translateFieldInRelation($str)
    {
        $relations = $this->metadata->getRelations();
        foreach ($relations as $relation) {
            $this->tables[] = $relation['table'];

            foreach ($relation['ids'] as $tableId => $relationId) {
                $this->sqls[] = sprintf(
                    "%s.%s = %s.%s",
                    $this->metadata->mapping['database']['table'],
                    $tableId,
                    $relation['table'],
                    $relationId
                );
            }
        }

        // Push meta-based filters
        $this->sqls[] = "and $str";
    }

    /**
     * Translates an operator.
     *
     * @param string $str The parameter to translate.
     *
     * @return array An array with the translation, the parameter value and the
     *               parameter type.
     */
    protected function translateOperator($type)
    {
        return [ $this->translations[$type], null, null ];
    }

    /**
     * Translates a parameter.
     *
     * @param string  $str          The parameter to translate.
     * @param string  $type         The parameter type.
     * @param boolean $previousLike Whether the previous translation was a
     *                              like/not like operator.
     *
     * @return array An array with the translation, the parameter value and the
     *               parameter type.
     */
    protected function translateParameter($str, $type, $previousLike)
    {
        // Remove quotes for strings and datetimes
        if (($type === 'T_STRING' || $type === 'T_DATETIME')
            && ($str[0] === '"' || $str[0] === "'")
        ) {
            $str = str_replace($str[0], '', $str);
        }

        // Change datetime to UTC
        if ($type === 'T_DATETIME') {
            $date = new \DateTime($str);
            $date->setTimezone(new \DateTimeZone('UTC'));

            $str = $date->format('Y-m-d H:i:s');
        }

        // Surround with %
        if ($previousLike) {
            $str = "%$str%";
        }

        return [ '?', $str, $this->pdoParams[$type] ];
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
     *               parameter type.
     */
    protected function translateToken($str, $type, $previousLike)
    {
        if ($this->isTranslatable($type)) {
            return $this->translateOperator($type);
        }

        if ($this->isParameter($type)) {
            return $this->translateParameter($str, $type, $previousLike);
        }

        if ($this->isField($str, $type)) {
            return $this->translateField($str);
        }

        throw new InvalidTokenException($str, $this->metadata->name);
    }
}
