<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\ORM\Core;

use Doctrine\DBAL\Schema\Schema as DbalSchema;
use Framework\Component\Data\DataObject;
use Framework\ORM\Core\Validation\Validable;

class Metadata extends DataObject implements Validable
{
    /**
     * {@inheritdoc}
     */
    public function getClassName()
    {
        return 'Metadata';
    }

    /**
     * Returns a schema for doctrine DBAL basing on the current schema.
     */
    public function getDbalSchema()
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
}
