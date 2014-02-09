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
            $finder->name('*.json');
            $finder->files()->in($schema['source']['path']);
            $finder->files()->in($schema['source']['path']);
            $files = $finder;
        } else if (is_file($schema['source']['path'])) {
            $files = array(new File($schema['source']['path']));
        }

        $total = count($files);
        $current = 1;
        foreach ($files as $file) {
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
                // var_dump('assoc');
                $builded = $this->itemToFlat($item, $schema);
            } else if (is_array($item)
                && array_keys($item) === range(0, count($item) - 1)
            ) {
                // Non-Associative array (1..* items per file)
                // var_dump('non-assoc');
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
                    if ((!isset($schema['filters'])
                        || (isset($schema['filters'])
                        && $this->isParseable($schema['filters'], $value)))
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
                        // Remove invalided items according to filter
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
            // var_dump('key: ' . $name);

            if (!is_array($value)) {
                // var_dump('non-array - copy');
                if (count($parsed) == 0) {
                    // var_dump('new array');
                    $parsed[] = array($name => $value);
                } else {
                    // var_dump('fill array');
                    foreach ($parsed as $key => $p) {
                        $parsed[$key][$name] = $value;
                    }
                }
            } else {
                if (array_keys($value) !== range(0, count($value) - 1)) {
                    // var_dump('assoc - extract fields');
                    // Associative array (Sub-fields - Extract)
                    foreach ($value as $i => $v) {
                        foreach ($parsed as $j => $p) {
                            $parsed[$j][$i] = $v;
                        }
                    }
                } else {
                    // var_dump('non-assoc - clone & join');
                    // Non-associative array (Sub-items - Clone & Join)
                    $joined = array();
                    foreach ($value as $k => $v) {
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
