<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Core\Schema;

use Doctrine\DBAL\Schema\Schema as DoctrineSchema;
use Common\ORM\Core\Exception\InvalidSchemaException;

/**
 * The Dumper class translates the ORM configuration to a Schema.
 */
class Dumper
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
        'default'             => [ 'boolean', 'integer', 'string' ],
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
     * The array of schemas.
     *
     * @var array
     */
    protected $schemas = [];

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
     * Initializes the Dumper.
     *
     * @param array $schemas   The list of schemas.
     * @param array $metadatas The list of entity metadata.
     */
    public function __construct($schemas = [], $metadata = [])
    {
        if (empty($schemas)) {
            return;
        }

        $this->configure($schemas, $metadata);
    }

    /**
     * Configures the dumper.
     *
     * @param array $schemas  The list of schemas.
     * @param array $metadata The list of entity metadata.
     */
    public function configure($schemas, $metadata)
    {
        $this->schemas = $schemas;

        $entities = [];
        foreach ($schemas as $schema) {
            $entities = array_unique(array_merge($entities, $schema->entities));
        }

        $this->metadata = array_filter($metadata, function ($a) use ($entities) {
            return in_array($a->name, $entities);
        });
    }

    /**
     * Returns the current database schema.
     *
     * @param string $database The database name.
     *
     * @return DoctrineSchema The database schema.
     */
    public function discover($conn, $database)
    {
        $conn->selectDatabase($database);

        return $conn->getSchemaManager()->createSchema();
    }

    /**
     * Returns a database schema from a schema name.
     *
     * @param string $name The schema name.
     *
     * @return Schema The database schema.
     */
    public function dump($name)
    {
        if (!array_key_exists($name, $this->schemas)) {
            throw new \InvalidArgumentException(
                sprintf(_("Unable to dump schema '%s'"), $name)
            );
        }

        $schema = new DoctrineSchema();

        foreach ($this->schemas[$name]->entities as $entity) {
            $metadata = $this->metadata[$entity];

            if (!array_key_exists('database', $metadata->mapping)) {
                throw new InvalidSchemaException(_('No mapping information'));
            }

            $mapping = $metadata->mapping['database'];

            $this->validate($mapping);

            $table = $schema->createTable($mapping['table']);

            // Add column definitions
            foreach ($mapping['columns'] as $field => $value) {
                $options = [];

                if (array_key_exists('options', $value)) {
                    $options = $value['options'];
                }

                // Use datetime to prevent changes when no changes required
                if ($value['type'] === 'datetimetz') {
                    $value['type'] = 'datetime';
                }

                $table->addColumn($field, $value['type'], $options);
            }

            // Add index definitions
            foreach ($mapping['index'] as $field => $value) {
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
     * @param array $data The schema data.
     *
     * @throws InvalidSchemaException If the schema is not valid.
     */
    public function validate($data)
    {
        if (!array_key_exists('table', $data) || empty($data['table'])) {
            throw new InvalidSchemaException(_('Empty table name'));
        }

        if (!preg_match('/[a-z0-9_]+/', $data['table'], $matches)
            || $matches[0] !== $data['table']
        ) {
            throw new InvalidSchemaException(
                sprintf(_("Invalid table name '%s'"), $data['table'])
            );
        }

        $this->validateTable($data['table'], $data);
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
     * @return void
     *
     * @throws InvalidSchemaException If the table is not valid.
     */
    protected function validateTable($name, $config)
    {
        if (!array_key_exists('columns', $config)
            || empty($config['columns'])
        ) {
            throw new InvalidSchemaException(
                sprintf(_("No fields found for table '%s'"), $name)
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
