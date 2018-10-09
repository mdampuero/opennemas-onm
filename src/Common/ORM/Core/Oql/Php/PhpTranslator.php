<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Core\Oql\Php;

use Common\ORM\Core\Metadata;
use Common\ORM\Core\Oql\Tokenizer;

/**
 * The PhpTranslator class translates the internal representation of an OQL
 * query to a string ready to use in PHP eval() function.
 */
class PhpTranslator
{
    /**
     * List of actions to consume tokens.
     */
    protected $consume = [
        'C_AND'          => 'consumeOperator',
        'C_OR'           => 'consumeOperator',
        'G_OPARENTHESIS' => 'consumeOperator',
        'G_CPARENTHESIS' => 'consumeOperator',
        'M_ORDER_BY'     => 'consumeOrderBy',
        'M_LIMIT'        => 'consumePrefixOperator',
        'O_EQUALS'       => 'consumeInfixOperator',
        'O_GREAT'        => 'consumeInfixOperator',
        'O_GREAT_EQUALS' => 'consumeInfixOperator',
        'O_IN'           => 'consumeInfixOperator',
        'O_IS'           => 'consumeInfixOperator',
        'O_LESS'         => 'consumeInfixOperator',
        'O_LESS_EQUALS'  => 'consumeInfixOperator',
        'O_LIKE'         => 'consumeInfixOperator',
        'O_NOT_EQUALS'   => 'consumeInfixOperator',
        'O_NOT_IN'       => 'consumeInfixOperator',
        'O_NOT_IS'       => 'consumeInfixOperator',
        'O_NOT_LIKE'     => 'consumeInfixOperator',
        'O_NOT_REGEXP'   => 'consumeInfixOperator',
        'O_REGEXP'       => 'consumeInfixOperator',
    ];

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
    protected $ignorable = [ 'M_LIMIT',  'M_OFFSET', 'M_ORDER_BY' ];

    /**
     * List of operators.
     *
     * @var array
     */
    protected $translations = [
        'C_AND'          => ' && ',
        'COMMA'          => ',',
        'C_OR'           => ' || ',
        'G_OBRACKET'     => '(',
        'G_CBRACKET'     => ')',
        'G_OPARENTHESIS' => '(',
        'G_CPARENTHESIS' => ')',
        'M_ASC'          => 'asc',
        'M_DESC'         => 'desc',
        'M_ORDER_BY'     => 'orderBy',
        'M_LIMIT'        => 'limit',
        'M_OFFSET'       => 'offset',
        'O_EQUALS'       => 'isEquals',
        'O_GREAT'        => 'isGreat',
        'O_GREAT_EQUALS' => 'isGreatEquals',
        'O_IN'           => 'isInArray',
        'O_IS'           => 'isEquals',
        'O_LESS'         => 'isLess',
        'O_LESS_EQUALS'  => 'isLessEquals',
        'O_LIKE'         => 'isLike',
        'O_NOT_EQUALS'   => 'isNotEquals',
        'O_NOT_IN'       => 'isNotInArray',
        'O_NOT_IS'       => 'isNotEquals',
        'O_NOT_LIKE'     => 'isNotLike',
        'O_NOT_REGEXP'   => 'notMatch',
        'O_REGEXP'       => 'match',
    ];

    /**
     * Initializes the PHPTranslator.
     *
     * @param Metadata $metadata The entity metadata.
     */
    public function __construct(Metadata $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * Translates an OQL query and returns the parameters to build a PHP query.
     *
     * @param string  $oql     The OQL query.
     * @param boolean $exclude Whether to exclude some tokens.
     *
     * @return array A list with filters, sorting criteria, size and offset
     *               to search and filter an array of entities.
     */
    public function translate($oql = '', $ignoreMode = false)
    {
        $this->ignoreMode = $ignoreMode;
        $this->params     = [];
        $this->operators  = [];

        if (empty($oql)) {
            return [ '', [], 0, 0 ];
        }

        $tokenizer = new Tokenizer();
        $tokens    = $tokenizer->tokenize($oql);

        $i = 0;
        while ($i < count($tokens) && !$this->isIgnorable($tokens[$i][1])) {
            $token = $tokens[$i++];

            // Field or value
            if (!$this->isArray($token[1]) && !$this->isTranslatable($token[1])) {
                $this->params[] = [
                    'value' => $this->translateParameter($token[0], $token[1]),
                    'type'  => $token[1]
                ];
            }

            // Operator, connector or grouper
            if (!$this->isArray($token[1]) && $this->isTranslatable($token[1])) {
                $this->operators[] = $token[1];
            }

            // Build array from tokens
            if ($this->isArray($token[1])) {
                $value = [];

                while ($tokens[$i][1] !== 'G_CBRACKET') {
                    if ($tokens[$i][1] !== 'COMMA') {
                        $value[] = $tokens[$i][0];
                    }

                    $i++;
                }

                // Ignore closing bracket
                $i++;

                $this->params[] = [ 'value' => $value, 'type' => 'T_ARRAY' ];
            }
        }

        while (count($this->operators) > 0 || count($this->params) > 0) {
            $map[] = $this->consume();
        }

        return [
            $this->getFilter($map),
            $this->getOrder($map),
            $this->getSize($map),
            $this->getOffset($map),
        ];
    }

    /**
     * Consumes tokens from the lists of tokens.
     *
     * @return mixed The result of consume tokens.
     */
    protected function consume()
    {
        $operator = $this->operators[0];
        $method   = $this->consume[$operator];

        return $this->{$method}();
    }

    /**
     * Consumes and infix operator.
     *
     * @return array The consumed operator and its parameters.
     */
    protected function consumeInfixOperator()
    {
        $operator = array_shift($this->operators);
        $key      = $this->translateOperator($operator);

        // Consume 2 parameters
        for ($i = 0; $i < 2; $i++) {
            $param    = array_shift($this->params);
            $params[] = $param['value'];
        }

        return [ $key => $params ];
    }

    /**
     * Consumes an operator from the list of operators.
     *
     * @return array The consumed operator.
     */
    protected function consumeOperator()
    {
        $operator = array_shift($this->operators);
        $key      = $this->translateOperator($operator);

        return [ $key => [] ];
    }

    /**
     * Consumes the order by operator.
     *
     * @return array The consumed order by operator and its parameters.
     */
    protected function consumeOrderBy()
    {
        // Consume order
        $operator = array_shift($this->operators);
        $key      = $this->translateOperator($operator);

        // Consume field and direction
        $params[] = array_shift($this->params)['value'];
        $params[] = $this->translateOperator(array_shift($this->operators));

        while (!empty($this->operators) && $this->operators[0] === 'COMMA') {
            // Ignore comma
            $operator = array_shift($this->operators);

            // Consume field and direction
            $params[] = array_shift($this->params)['value'];
            $params[] = $this->translateOperator(array_shift($this->operators));
        }

        return [ $key => $params ];
    }

    /**
     * Consumes an operator and the next parameter.
     *
     * @return array The consumed operator and the parameter.
     */
    protected function consumePrefixOperator()
    {
        $operator = array_shift($this->operators);
        $params[] = array_shift($this->params)['value'];
        $key      = $this->translateOperator($operator);

        return [ $key => $params ];
    }

    /**
     * Returns a PHP expression to evaluate in eval() function.
     *
     * @param array $map The array of tokens.
     *
     * @return string The PHP expression to evaluate.
     */
    protected function getFilter($map)
    {
        $str = '';
        foreach ($map as $key => $value) {
            $key    = array_keys($value)[0];
            $filter = $key;

            // Not a connector or grouper
            if (!in_array($key, [ '(', ' && ', ' || ' , ')' ])) {
                $parameters = $this->parseParameters($value[$key]);

                $filter = '$this->evaluate($entity, \'' . $key . '\', '
                    . $parameters . ')';
            }

            // Ignore order by, limit and offset
            if (in_array($key, [ 'limit', 'offset', 'orderBy' ])) {
                $filter = '';
            }

            $str .= $filter;
        }

        if (empty($str)) {
            return $str;
        }

        return 'return ' . $str . ';';
    }

    /**
     * Returns the number of items to ignore in the search.
     *
     * @param array $map The array of tokens.
     *
     * @return integer The start offset.
     */
    protected function getOffset($map)
    {
        foreach ($map as $value) {
            if (array_keys($value)[0] === 'offset') {
                return (int) $value['offset'][0];
            }
        }

        return 0;
    }

    /**
     * Returns sorting criteria.
     *
     * @param array $map The array of tokens.
     *
     * @return array The sorting criteria.
     */
    protected function getOrder($map)
    {
        foreach ($map as $value) {
            if (array_keys($value)[0] === 'orderBy') {
                return $value['orderBy'];
            }
        }

        return [];
    }

    /**
     * Returns the number of items to search.
     *
     * @param array $map The array of tokens.
     *
     * @return integer The number of items.
     */
    protected function getSize($map)
    {
        foreach ($map as $value) {
            if (array_keys($value)[0] === 'limit') {
                return (int) $value['limit'][0];
            }
        }

        return 0;
    }

    /**
     * Checks if the string is the opening bracket from an array.
     *
     * @param string $type The token type.
     *
     * @return boolean True if the string is an array. False otherwise.
     */
    protected function isArray($type)
    {
        return $type === 'G_OBRACKET';
    }

    /**
     * Checks if the string is a valid field basing on the entity metadata.
     *
     * @param string $str  The string to check.
     * @param string $type The token type.
     *
     * @return boolean True if the string is a field. False otherwise.
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
     * @return boolean True if the token is ignorable. False otherwise.
     */
    protected function isIgnorable($token)
    {
        return $this->ignoreMode && in_array($token, $this->ignorable);
    }

    /**
     * Checks if the string is a translatable token.
     *
     * @param string $str The string to check.
     *
     * @return boolean True if the string is translatable. False otherwise.
     */
    protected function isTranslatable($str)
    {
        return in_array($str, array_keys($this->translations));
    }

    /**
    * Parses the list of parameters and returns the equivalent PHP string to
    * use in eval() function.
     *
     * @param array $parameters The list of parameters.
     *
     * @return string The list of parameters as string.
     */
    protected function parseParameters($parameters)
    {
        $str = '';

        foreach ($parameters as $parameter) {
            $p = $parameter;

            if (is_string($p)) {
                $p = '\'' . trim(trim($p, '"'), "'") . '\'';
            }

            if (is_array($parameter)) {
                $p = '[' . $this->parseParameters($parameter) . ']';
            }

            $str .= $p . ', ';
        }

        return trim($str, ', ');
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
        return $this->translations[$type];
    }

    /**
     * Translates a parameter.
     *
     * @param string  $str          The parameter to translate.
     * @param string  $type         The parameter type.
     * @param boolean $previousLike Whether the previous translation was a
     *                              like/not like operator.
     *
     * @return string The translated string.
     */
    protected function translateParameter($str, $type)
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

        return $str;
    }
}
