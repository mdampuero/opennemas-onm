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

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;

use Onm\DatabaseConnection;
use Onm\Settings as s;
use Onm\StringUtils;

class JsonProvider extends MigrationProvider
{
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

        // Read files
        $files;
        if (is_dir($schema['source']['path'])) {
            $finder = new Finder();

            if (isset($schema['source']['name'])) {
                $finder->name($schema['source']['name']);
            } else {
                $finder->name('*.json');
            }

            $files = $finder->in($schema['source']['path']);
        } else if (is_file($schema['source']['path'])) {
            $files = array(new File($schema['source']['path']));
        }

        $current = 1;
        foreach ($files as $file) {
            if ($this->debug) {
                $this->output->writeln('   Processing item ' . $current++);
            }

            $builded = array();
            // Read item from file
            $item = json_decode(file_get_contents($file->getPathName()), true);

            if (array_key_exists('prefix', $schema['source'])) {
                foreach (explode('.', $schema['source']['prefix']) as $prefix) {
                    $item = $item[$prefix];
                }
            }

            if (is_array($item)
                && array_keys($item) !== range(0, count($item) - 1)
            ) {
                // Associative array (1 item per file)
                $builded = $this->itemToFlat($item, $schema);
            } else if (is_array($item)
                && array_keys($item) === range(0, count($item) - 1)
            ) {
                // Non-Associative array (1..* items per file)
                foreach ($item as $i) {
                    $builded =
                        array_merge($builded, $this->itemToFlat($i, $schema));
                }
            }

            // Ignore wrong joined items
            $required = array();
            foreach ($schema['fields'] as $key => $value) {
                if (array_key_exists('field', $value)
                    && array_key_exists('required', $value)
                    && $value['required']
                ) {
                    $required[] = $value['field'];
                }
            }

            // Apply filters
            foreach ($builded as $key => $value) {
                if ((!empty($required)
                    && !empty(array_intersect(array_keys($value), $required)))
                    || empty($required)
                ) {
                    // Has all required fields - Filter
                    if ((!isset($schema['pre-conditions'])
                        || (isset($schema['pre-conditions'])
                        && $this->isParseable(
                            $schema['pre-conditions'],
                            $value
                        )))
                        && $this->elementIsImported(
                            $value[$schema['source']['id']],
                            $schema['translation']['name']
                        ) === false
                    ) {
                        // Add constants
                        foreach ($schema['fields'] as $field => $values) {
                            if (in_array('constant', $values['type'])) {
                                $builded[$key][$field] = $values['value'];
                            }
                        }

                    } else {
                        // Remove invalided items according to pre-conditions
                        unset($builded[$key]);
                    }
                }
            }

            // Get fields according to schema
            foreach ($builded as $b) {
                $filtered = array();
                foreach ($schema['fields'] as $key => $value) {
                    if (array_key_exists('field', $value)
                        && array_key_exists($value['field'], $b)
                    ) {
                        $filtered[$key] = $b[$value['field']];
                    } else if (array_key_exists($key, $b)) {
                        $filtered[$key] = $b[$key];
                    } else {
                        $filtered[$key] = null;
                    }
                }

                // Save if it is not imported
                if (!$this->elementIsImported(
                    $filtered[$schema['translation']['field']],
                    $schema['translation']['name']
                )) {
                    $data[] = $filtered;
                } else {
                    $this->stats[$name]['already_imported']++;
                }
            }
        }

        return $data;
    }

    /**
     * Parses an item and returns an array with data to process.
     *
     * @param  array $key         Sliced key
     * @param  array $item        Item to parse.
     * @param  array $parsedIndex Array with parsed content keys.
     * @return array              Data to process.
     */
    private function itemToFlat($item, $schema)
    {
        $parsed = array();
        foreach ($item as $name => $value) {
            if (!array_key_exists('ignore', $schema)
                || (array_key_exists('ignore', $schema)
                && !in_array($name, $schema['ignore']))
            ) {
                if (!is_array($value)) {
                    // Non-array - Copy
                    if (count($parsed) == 0) {
                        // Parsed empty - New item
                        $parsed[] = array($name => $value);
                    } else {
                        // Append value to already parsed items
                        foreach ($parsed as $key => $p) {
                            $parsed[$key][$name] = $value;
                        }
                    }
                } else {
                    if (array_keys($value) !== range(0, count($value) - 1)) {
                        // Associative array (Sub-fields - Extract)
                        foreach ($value as $i => $v) {
                            foreach ($parsed as $j => $p) {
                                $parsed[$j][$i] = $v;
                            }
                        }
                    } else {
                        // Non-associative array (Sub-items - Clone & Join)
                        // Find field config in $schema
                        $keys = array_keys($schema['fields']);
                        $i = 0;
                        while ($i < count($keys)
                            && (
                                !array_key_exists(
                                    'field',
                                    $schema['fields'][$keys[$i]]
                                ) || (array_key_exists(
                                    'field',
                                    $schema['fields'][$keys[$i]]
                                ) && $schema['fields'][$keys[$i]]['field'] != $name)
                            )
                        ) {
                            $i++;
                        }

                        // Check if field has to be merged
                        if ($i < count($keys)
                            && in_array(
                                'merge',
                                $schema['fields'][$keys[$i]]['type']
                            )
                        ) {
                            // Append array to already parsed items
                            foreach ($parsed as $j => $p) {
                                $parsed[$j][$name] = $value;
                            }
                        } else {
                            // Join
                            $joined = array();
                            foreach ($value as $v) {
                                $toJoin = $this->itemToFlat($v, $schema);

                                // Join fields to elements in parsed
                                foreach ($toJoin as $f) {
                                    foreach ($parsed as $j => $v) {
                                        $joined[] = array_merge($parsed[$j], $f);
                                    }
                                }
                            }

                            $parsed = $joined;
                        }
                    }
                }
            }
        }

        return $parsed;
    }

    /**
     * Returns true if item satisfies the schema conditions.
     *
     * @param  array   $conditions Conditions to check.
     * @param  array   $item       Item to check.
     * @return boolean             True, if item satisfies schema conditions.
     *                             Otherwise, returns false.
     */
    private function isParseable($conditions, $item)
    {
        $parseable = true;
        foreach ($conditions as $condition) {
            $value = $item;
            foreach (explode('.', $condition['field']) as $field) {
                if (!array_key_exists($field, $value)) {
                    return false;
                }

                $value = $value[$field];
            }

            switch ($condition['operator']) {
                case '=':
                    $parseable = $parseable && ($value == $condition['value']);
                    break;
                case '!=':
                    $parseable = $parseable && ($value != $condition['value']);
                    break;
                case '>':
                    $parseable = $parseable && ($value > $condition['value']);
                    break;
                case '<':
                    $parseable = $parseable && ($value < $condition['value']);
                    break;
            }
        }

        return $parseable;
    }
}
