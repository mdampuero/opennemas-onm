<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\ORM\Core\Schema;

use Framework\ORM\Core\Metadata;
use Framework\ORM\Core\Exception\InvalidSchemaException;

class SchemaDumper
{
    /**
     * The list of allowed option value types.
     *
     * @var array
     */
    protected $options = [
        'autoincrement'       => [ 'boolean' ],
        'comment'             => [ 'integer', 'string' ],
        'customSchemaOptions' => [ 'array' ],
        'default'             => [ 'integer', 'string' ],
        'fixed'               => [ 'boolean' ],
        'length'              => [ 'integer' ],
        'notnull'             => [ 'boolean' ],
        'precision'           => [ 'integer' ],
        'scale'               => [ 'integer' ],
        'unsigned'            => [ 'boolean' ]
    ];

    /**
     * The required fields in configuration.
     *
     * @var array
     */
    protected $required = [
        'parameters' => [ 'array' ]
    ];

    /**
     * The list of allowed types.
     *
     * @var array
     */
    protected $types = [
        'array',
        'bigint',
        'binary',
        'blob',
        'boolean',
        'date',
        'dateinterval',
        'datetime',
        'datetimetz',
        'decimal',
        'float',
        'guid',
        'integer',
        'json_array',
        'object',
        'simple_array',
        'smallint',
        'string',
        'text',
    ];

    /**
     * Returns a schema for doctrine DBAL basing on the current schema.
     */
    public function dump(Metadata $metadata)
    {
        $schema = new DbalSchema();

        foreach ($this->data['parameters'] as $table => $definition) {
            $table = $schema->createTable($table);

            // Add column definitions
            foreach ($definition['columns'] as $field => $value) {
                $options = [];

                if (array_key_exists('options', $value)) {
                    $options = $value['options'];
                }

                $table->addColumn($field, $value['type'], $options);
            }

            // Add index definitions
            foreach ($definition['index'] as $field => $value) {
                if (!array_key_exists('name', $value)) {
                    $value['name'] = null;
                }

                if (array_key_exists('primary', $value)
                    && !empty($value['primary'])
                ) {
                    $table->setPrimaryKey($value['columns'], $value['name']);
                } elseif (array_key_exists('unique', $value)
                    && !empty($value['unique'])
                ) {
                    $table->addUniqueIndex($value['columns'], $value['name']);
                } else {
                    $table->addIndex($value['columns'], $value['name']);
                }
            }
        }

        return $schema;
    }

    /**
     * Validates the schema.
     *
     * @param Schema The configuration to validate.
     *
     * @throws InvalidSchemaException If the schema is not valid.
     */
    public function validate(Entity $schema)
    {
        parent::validate($schema);

        $data = $schema->getData();

        foreach ($data['parameters'] as $name => $table) {
            if (!preg_match('/[a-z0-9_]+/', $name)) {
                throw new InvalidSchemaException(
                    sprintf(_("Invalid table name '%s'"), $name)
                );
            }

            $this->validateTable($name, $table);
        }
    }

    /**
     * Validates a field from a table.
     *
     * @param array $table  The table name.
     * @param array $field  The field name.
     * @param array $config The field definition.
     *
     * @throws InvalidSchemaException If the field is invalid.
     */
    protected function validateField($table, $field, $config)
    {
        if (!array_key_exists('type', $config)
            || empty($config['type'])
        ) {
            throw new InvalidSchemaException(
                sprintf(
                    _("Invalid field '%s' in table '%s'"),
                    $field,
                    $table
                )
            );
        }

        $this->validateType($table, $field, $config['type']);

        if (array_key_exists('options', $config)
            && !empty($config['options'])
        ) {
            $this->validateOptions($table, $field, $config['options']);
        }
    }


    /**
     * Validates a list of fields from a table.
     *
     * @param array $table  The table name.
     * @param array $fields The fields to validate.
     */
    protected function validateFields($table, $fields)
    {
        foreach ($fields as $name => $field) {
            $this->validateField($table, $name, $field);
        }
    }

    /**
     * Validates an index from a table.
     *
     * @param array $table   The table name.
     * @param array $index   The index.
     * @param array $columns The list of columns.
     * @param array $config  The index definition.
     *
     * @throws InvalidSchemaException If the index is not valid.
     */
    protected function validateIndex($table, $columns, $index)
    {
        if (!array_key_exists('name', $index) || empty($index['name'])) {
            throw new InvalidSchemaException(
                sprintf(_("Invalid index name in table '%s'"), $table)
            );
        }

        if (!empty(array_diff($index['columns'], $columns))) {
            throw new InvalidSchemaException(
                sprintf(
                    _("Invalid columns in index '%s' name in table '%s'"),
                    $index['name'],
                    $table
                )
            );
        }

        if ((array_key_exists('primary', $index)
                && !is_bool($index['primary']))
            || (array_key_exists('unique', $index)
                && !is_bool($index['unique']))
        ) {
            throw new InvalidSchemaException(
                sprintf(
                    _("Invalid flags in index '%s' name in table '%s'"),
                    $index['name'],
                    $table
                )
            );
        }
    }

    /**
     * Validates a list of indexes from a table.
     *
     * @param array $table   The table name.
     * @param array $columns The list of columns.
     * @param array $fields  The indexes to validate.
     *
     * @throws InvalidSchemaException If one or more indexes are not valid.
     */
    protected function validateIndexes($table, $columns, $indexes)
    {
        $names = [];
        foreach ($indexes as $index) {
            $names[] = $index['name'];
            $this->validateIndex($table, $columns, $index);
        }

        if (count($indexes) !== count(array_unique($names))) {
            throw new InvalidSchemaException(
                sprintf(_("Duplicated index in table '%s'"), $table)
            );
        }
    }

    /**
     * Validates a field option from a table.
     *
     * @param array $table  The table name.
     * @param array $field  The field name.
     * @param array $option The field option.
     *
     * @throws InvalidSchemaException If the option is not valid.
     */
    protected function validateOption($table, $field, $option, $value)
    {
        foreach ($this->options[$option] as $type) {
            if (is_null($value)
                || ($type === 'string' && is_string($value))
                || ($type === 'integer' && is_numeric($value))
                || ($type === 'boolean' && is_bool($value))
            ) {
                return;
            }
        }

        throw new InvalidSchemaException(
            sprintf(
                _("Invalid option '%s' for field '%s' in table '%s"),
                $option,
                $field,
                $table
            )
        );
    }

    /**
     * Validates a list of field options from a table.
     *
     * @param array $table   The table name.
     * @param array $field   The field name.
     * @param array $options The field options.
     *
     * @throws InvalidSchemaException If the field is not valid.
     */
    protected function validateOptions($table, $field, $options)
    {
        foreach ($options as $name => $value) {
            if (!array_key_exists($name, $this->options)) {
                throw new InvalidSchemaException(
                    sprintf(
                        _("Invalid option %s for field %s in table '%s'"),
                        $name,
                        $field,
                        $table
                    )
                );
            }

            $this->validateOption($table, $field, $name, $value);
        }
    }

    /**
     * Validates a table configuration.
     *
     * @param array $name   The table name.
     * @param array $config The table definition to validate.
     *
     * @return boolean True if the table is valid. Otherwise, returns false.
     *
     * @throws InvalidSchemaException If the table is not valid.
     */
    protected function validateTable($name, $config)
    {
        if (!array_key_exists('columns', $config)
            || empty($config['columns'])
        ) {
            throw new InvalidSchemaException(
                sprintf(_("No fields found for table '%s'"), $config)
            );
        }

        if (!array_key_exists('index', $config) || empty($config['index'])) {
            throw new InvalidSchemaException(
                sprintf(_("No index found for table '%s'"), $name)
            );
        }

        $this->validateFields($name, $config['columns']);

        $this->validateIndexes(
            $name,
            array_keys($config['columns']),
            $config['index']
        );
    }

    /**
     * Validates a field type from a table.
     *
     * @param array $table The table name.
     * @param array $field The field name.
     * @param array $type  The field type definition.
     *
     * @throws InvalidSchemaException If the field is not valid.
     */
    protected function validateType($table, $field, $type)
    {
        if (!in_array($type, $this->types)) {
            throw new InvalidSchemaException(
                sprintf(
                    _("Invalid type for field %s in table '%s'"),
                    $field,
                    $table
                )
            );
        }
    }
}
