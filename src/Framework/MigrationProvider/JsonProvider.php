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
namespace Framework\MigrationProvider;

use Symfony\Component\Finder\Finder;

use Onm\DatabaseConnection;
use Onm\Settings as s;
use Onm\StringUtils;

class JsonProvider extends MigrationProvider
{
    /**
     * Array of paths.
     *
     * @var array
     */
    protected $files;

    /**
     * Constructs a new Migration provider.
     *
     * @param Logger $logger
     * @param array  $settings
     * @param array  $translations Array of translations.
     * @param array  $stats
     * @param array  $debug
     */
    public function __construct(
        $logger,
        $settings,
        &$translations,
        &$stats,
        $debug = false
    ) {
        parent::__construct($logger, $settings, $translations, $stats, $debug);

        $this->prepareDatabase();
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

        // Read files
        $finder = new Finder();
        $finder->name('*.json');
        $finder->files()->in($schema['source']['path']);

        $total = count($finder);
        $current = 1;

        foreach ($finder as $file) {
            // Read item from file
            $item = json_decode(file_get_contents($file->getPathName()), true);

            // Builded item
            $builded = array();

            $imported = $item;
            foreach (explode('.', $schema['source']['id']) as $key) {
                $imported = $imported[$key];
            }

            if (!is_null($item) && (!isset($schema['filters'])
                || (isset($schema['filters'])
                && $this->isParseable($schema['filters'], $item)))
                && !$this->elementIsImported(
                    $imported,
                    $schema['translation']['name']
                )
            ) {
                foreach ($schema['fields'] as $field => $values) {
                    if (in_array('constant', $values['type'])) {
                        $builded[$field] = $values['value'];
                    } else {
                        $value = $item;
                        foreach (explode('.', $values['field']) as $f) {
                            $value = $value[$f];
                        }
                        $builded[$field] = $value;
                    }
                }

                $data[] = $builded;

            } else if ($this->elementIsImported(
                $imported,
                $schema['translation']['name']
            )) {
                $this->stats[$name]['already_imported']++;
            }
        }

        // var_dump($data);die();

        return $data;
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

    private function prepareDatabase()
    {
        $sql = "ALTER TABLE  `translation_ids` CHANGE  `pk_content_old` "
            . " `pk_content_old` VARCHAR( 255 ) NOT NULL";
        $rss = $this->targetConnection->Execute($sql);
    }
}
