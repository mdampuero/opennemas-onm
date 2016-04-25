<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Core\Loader;

use Common\ORM\Core\Connection;
use Common\ORM\Core\Metadata;
use Common\ORM\Core\Schema\Schema;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

/**
 * Loads ORM configuration files.
 */
class Loader
{
    /**
     * The default data.
     *
     * @var array
     */
    protected $default;

    /**
     * The current environment.
     *
     * @var string
     */
    protected $env;

    /**
     * Path to the ORM configuration.
     *
     * @var string
     */
    protected $path;

   /**
     * Initializes the Loader.
     *
     * @param string $path    The path to load from.
     * @param string $env     The current environment.
     * @param string $default The default data for the items to load.
     *
     * @throws InvalidArgumentException If the path is not valid.
     */
    public function __construct($path, $env, $default = [])
    {
        $this->default = $default;
        $this->env     = $env;
        $this->path    = $path;
    }

    /**
     * Loads ORM-related items from configuration files.
     */
    public function load()
    {
        $finder = new Finder();
        $loaded = [];

        $finder->files()->in($this->path)->name('*.yml');

        // Load items
        foreach ($finder as $file) {
            $item = $this->loadItem($file->getRealPath());

            if (!empty($item)) {
                $type = \underscore($item->getClassName());
                $loaded[$type][$item->name] = $item;
            }
        }

        // Merge items
        foreach ($loaded as $type => $items) {
            foreach ($items as $item) {
                $parents = $item->parent ? $item->parent : [];

                if (!is_array($parents)) {
                    $parents = [ $parents ];
                }

                foreach ($parents as $parent) {
                    $this->mergeItems($item, $items[$parent]);
                }
            }

        }

        return $loaded;
    }

    /**
     * Load a plugin from configuration file.
     *
     * @param string $path The path to plugin configuration file.
     */
    public function loadItem($path)
    {
        $data = Yaml::parse(file_get_contents($path));

        if (empty($data)) {
            return false;
        }

        $method = 'load' . ucfirst(array_keys($data)[0]);

        if (!method_exists($this, $method)) {
            return false;
        }

        return $this->{$method}($data);
    }

    /**
     * Returns a new database connection from data.
     *
     * @param array $data The data to load.
     *
     * @return Connection The loaded database connection.
     */
    public function loadConnection($data)
    {
        if (array_key_exists('connection', $this->default)) {
            $data['connection'] =
                array_merge($this->default['connection'], $data['connection']);
        }

        return new Connection($data['connection'], $this->env);
    }

    /**
     * Returns a new entity from data.
     *
     * @param array $data The data to load.
     *
     * @return Entity The loaded entity.
     */
    public function loadEntity($data)
    {
        if (array_key_exists('entity', $this->default)) {
            $data['entity'] =
                array_merge($this->default['entity'], $data['entity']);
        }

        return new Metadata($data['entity']);
    }

    /**
     * Returns a new entity from data.
     *
     * @param array $data The data to load.
     *
     * @return Entity The loaded entity.
     */
    public function loadSchema($data)
    {
        if (array_key_exists('schema', $this->default)) {
            $data['schema'] =
                array_merge($this->default['schema'], $data['schema']);
        }

        return new Schema($data['schema']);
    }


    /**
     * Merges a item with their parents.
     *
     * @param mixed $item   The item.
     * @param mixed $parent The item parent.
     */
    protected function mergeItems($item, $parent)
    {
        $keys = array_merge(
            array_keys($item->getData()),
            array_keys($parent->getData())
        );

        foreach ($keys as $key) {
            $item->{$key} = $this->mergeValues(
                $key,
                $parent->{$key},
                $item->{$key}
            );
        }
    }

    /**
     * Merges two values.
     *
     * @param mixed $a The first value.
     * @param mixed $b The second value.
     *
     * @return type Description
     */
    protected function mergeValues($key, $a, $b)
    {
        if (empty($a)) {
            return $b;
        }

        if (empty($b)) {
            return $a;
        }

        if (in_array($key, [ 'enum', 'properties', 'required' ])) {
            $a = !empty($a) ? $a : [];
            $b = !empty($b) ? $b : [];

            return array_merge($a, $b);
        }

        if ($key !== 'mapping') {
            return empty($b) ? $a : $b;
        }

        $mapping = [];
        $keys    = array_merge(array_keys($a), array_keys($b));

        foreach ($keys as $key) {
            $x = array_key_exists($key, $a) ? $a[$key] : [];
            $y = array_key_exists($key, $b) ? $b[$key] : [];

            $mapping[$key] = !empty($y) ? $y : $x;

            if ($key !== 'table') {
                $mapping[$key] = array_merge($x, $y);
            }
        }

        return $mapping;
    }
}
