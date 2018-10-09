<?php

namespace Common\ORM\Core\Oql;

class Fixer
{
    /**
     * The OQL to fix.
     *
     * @var string
     */
    protected $oql = '';

    /**
     * Initializes the OQL query to fix.
     *
     * @param string $oql The OQL query to fix.
     *
     * @return Fixer
     */
    public function fix($oql)
    {
        $this->oql = '';

        if (!empty($oql)) {
            $this->oql = $oql;
        }

        return $this;
    }

    /**
     * Returns the current OQL query.
     *
     * @return string The current OQL query.
     */
    public function getOql()
    {
        return $this->oql;
    }

    /**
     * Adds a filter condition to the current OQL query.
     *
     * @param string $condition The filter condition to add.
     *
     * @return Fixer
     */
    public function addCondition($condition)
    {
        if (empty($this->oql)) {
            $this->oql .= $condition;
            return $this;
        }

        // Surround current OQL by parenthesis
        if (!preg_match('/^\s*(order|limit)/', $this->oql)) {
            $conditions = preg_split('/\s*(order|limit|offset)\s*/', $this->oql);
            $conditions = trim($conditions[0]);

            $this->oql = $condition . ' and '
                . trim(str_replace($conditions, "($conditions)", $this->oql));

            return $this;
        }

        $this->oql = $condition . ' ' . $this->oql;

        return $this;
    }

    /**
     * Adds a limit constraint to the current OQL query.
     *
     * @param integer $limit The maximum number of items.
     *
     * @return Fixer
     */
    public function addLimit($limit)
    {
        if (!preg_match('/limit\s*\d+/', $this->oql)) {
            $this->oql = trim($this->oql . ' limit ' . $limit);
            return $this;
        }

        $this->oql = preg_replace('/limit\s*\d+/', "limit $limit", $this->oql);

        return $this;
    }

    /**
     * Adds a offset constraint to the current OQL query.
     *
     * @param integer $offset The maximum number of items.
     *
     * @return Fixer
     */
    public function addOffset($offset)
    {
        if (!preg_match('/offset\s*\d+/', $this->oql)) {
            $this->oql = trim($this->oql . ' offset ' . $offset);
            return $this;
        }

        $this->oql = preg_replace('/offset\s*\d+/', "offset $offset", $this->oql);

        return $this;
    }

    /**
     * Adds a order constraint to the current OQL query.
     *
     * @param string $field     The field to order by.
     * @param string $direction The order by direction.
     *
     * @return Fixer
     */
    public function addOrder($field, $direction)
    {
        if (!preg_match('/order\s*by.*/', $this->oql)) {
            $this->oql = trim($this->oql . " order by $field $direction");
            return $this;
        }

        // Replace existing direction
        if (preg_match("/order by.*\s+$field\s+(asc|desc)/", $this->oql)) {
            $this->oql = preg_replace(
                "/order by.*\s+$field\s+(asc|desc)/",
                "order by $field $direction",
                $this->oql
            );

            return $this;
        }

        $this->oql = preg_replace("/(order by(\s+\w+\s+(asc|desc)+)+)/", "$1, $field $direction", $this->oql);

        return $this;
    }
}
