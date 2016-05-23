<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Diego Blanco Estévez <diego@openhost.es>
 *
 */
namespace Framework\Migrator\Provider;

use Onm\DatabaseConnection;

class DatabaseProvider extends MigrationProvider
{
    /**
     * Origin database connection.
     *
     * @var Onm\DatabaseConnection
     */
    protected $originConnection;

    /**
     * Configures the current migrator.
     */
    public function configure()
    {
        parent::configure();

        // Initialize origin connection
        $this->originConnection = new DatabaseConnection(
            getContainerParameter('database')
        );
        $this->originConnection->selectDatabase(
            $this->settings['migration']['source']
        );
    }

    /**
     * Gets all source fields from database for each entity.
     *
     * @param  string $name   Schema name.
     * @param  array  $schema Database schema.
     * @return array          Array of fields used to create new entities.
     */
    public function getSource($name, $schema)
    {
        $data = array();
        $this->stats[$name]['already_imported'] = 0;

        $translations = '';
        if (array_key_exists(
            $schema['translation']['name'],
            $this->translations
        )) {
            foreach (array_keys($this->translations[$schema['translation']['name']]) as $oldId) {
                $translations .= $oldId . ', ';
            }
        }

        $sql = 'SELECT ' . $schema['source']['table'] . '.'
            . $schema['source']['id'] . ' FROM ' . $schema['source']['table']
            . ' WHERE ' ;

        if ($translations != '') {
            $sql .= $schema['source']['table'] . '.'
                . $schema['source']['id']
                . ' NOT IN (' . rtrim($translations, ', ') . ')';
        } else {
            $sql .= '1';
        }

        // Add logical comparisons to 'WHERE' chunk
        if (isset($schema['pre-conditions'])
            && count($schema['pre-conditions']) > 0
        ) {
            $sql .= ' AND ' . $this->parseCondition($schema['pre-conditions']);
        }

        $sql .= ' ORDER BY ' . $schema['source']['table'] . '.'
            . $schema['source']['id'];

        $request = $this->originConnection->Prepare($sql);
        $rs      = $this->originConnection->Execute($request);
        $ids     = $rs->getArray();

        $total = count($ids);
        $current = 1;
        foreach ($ids as $id) {
            if ($this->debug) {
                $this->output->writeln(
                    '   Processing item ' . $current++ . ' of ' . $total . '...'
                );
            }

            if (!$this->elementIsImported(
                $id[$schema['source']['id']],
                $schema['translation']['name']
            )) {
                // Build sql statement 'SELECT' chunk
                $sql = 'SELECT ';
                foreach ($schema['fields'] as $key => $field) {
                    if (isset($field['type']) &&
                            in_array('constant', $field['type'])) {
                        $sql .= '\'' . $field['value'] . '\'' . ' AS ' . $key
                            . ', ';
                    } elseif (isset($field['type']) &&
                            in_array('subselect', $field['type'])) {
                        $params = $field['params']['subselect'];

                        $sql .= '(SELECT group_concat(' . $params['table'] . '.'
                            . $params['field'] . ') FROM ' . $params['table']
                            . ' WHERE ' . $params['table'] . '.' . $params['id']
                            . '=' . $id[$schema['source']['id']];

                        // Add logical comparisons to 'WHERE' chunk
                        if (isset($params['conditions'])
                                && count($params['conditions']) > 0) {
                            $sql .= ' AND '
                                . $this->parseCondition($params['conditions']);
                        }

                        $sql .= ')' . ' AS ' . $key . ', ';
                    } else {
                        $sql .= $field['table'] . '.' . $field['field']
                            . ' AS ' . $key . ', ';
                    }
                }

                $sql = rtrim($sql, ', ');

                // Build sql statement 'FROM' chunk
                $sql .= ' FROM ';
                foreach ($schema['tables'] as $key => $table) {
                    $sql .= $table['table'];

                    if (isset($table['alias'])) {
                        $sql .= ' AS ' . $table['alias'];
                    }

                    $sql .=  ', ';
                }

                $sql = rtrim($sql, ', ');

                // Build sql statement 'WHERE' chuck
                $sql.= ' WHERE ('
                        . (isset($schema['source']['alias']) ?
                            $schema['source']['alias'] :
                            $schema['source']['table']) . '.'
                        . $schema['source']['id'] . '='
                        . $id[$schema['source']['id']] . ')';

                if (isset($schema['relations'])
                        && count($schema['relations']) > 0) {
                    foreach ($schema['relations'] as $key => $relation) {
                        if ($key < count($schema['relations'])) {
                            $sql .= ' AND (';
                        }
                        $sql .= $relation['table1'] . '.' . $relation['id1'] .
                            '=' . $relation['table2'] . '.' . $relation['id2']
                            . ')';
                    }
                }

                // Add logical comparisons to 'WHERE' chunk
                if (isset($schema['post-conditions'])
                    && count($schema['post-conditions']) > 0
                ) {
                    $sql .= ' AND '
                        . $this->parseCondition($schema['post-conditions']);
                }

                // Execute sql and save in array
                $request = $this->originConnection->Prepare($sql);
                $rs      = $this->originConnection->Execute($request);
                $results = $rs->getArray();

                if (count($results) > 0) {
                    foreach ($results as $result) {
                        if (isset($schema['collections'])) {
                            foreach ($schema['collections'] as $key => $value) {
                                $result[$key] = array();

                                foreach ($value as $field) {
                                    $result[$key][] = $result[$field];
                                    unset($result[$field]);
                                }
                            }
                        }

                        $data[] = $result;
                    }
                }
            } else {
                $this->stats[$name]['already_imported']++;
            }
        }

        return $data;
    }

    /**
     * Parses the given condition and returns the equivalent SQL.
     *
     * @param  array $condition Condition to parse.
     * @return string           SQL string.
     */
    private function parseCondition($condition)
    {
        $sql = '';
        if (array_keys($condition) !== range(0, count($condition) - 1)) {
            // Associative array
            $sql .= $condition['table'] . '.' . $condition['field'].' ';
            // Check operators
            if (array_key_exists('operator', $condition)) {
                if (in_array($condition['operator'], ['IN', 'NOT IN'])) {
                    $sql .= $condition['operator'] . ' ('. $condition['value'] . ')';
                } else {
                    $sql .= $condition['operator'] . ' \'' . $condition['value'] . '\'';
                }
            } else {
                $sql .= ' LIKE \'%' . $condition['value'] . '%\'';
            }
        } else {
            // Non-associative array
            $sql .= '(';
            foreach ($condition as $key => $value) {
                $sql .= $this->parseCondition($value);
                if ($key < count($condition) - 1) {
                    if (array_keys($value) !== range(0, count($value) - 1)) {
                        $sql .= ' OR ';
                    } else {
                        $sql .= ' AND ';
                    }
                } else {
                    $sql .= ')';
                }
            }
        }

        return $sql;
    }
}
