<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\ORM\Core\Loader;

use Framework\ORM\Core\Validation;
use Framework\ORM\Core\Connection;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class Loader
{
    /**
     * The base path.
     *
     * @var string
     */
    protected $basePath;

    /**
     * The list of paths to load items from.
     *
     * @var array
     */
    protected $paths;

    /**
     * Initializes the Loader.
     *
     * @param string $basePath The service container.
     * @param string $paths The path to folders to load from.
     *
     * @throws InvalidArgumentException If the path is not valid.
     */
    public function __construct($basePath, $paths)
    {
        if (empty($paths)) {
            throw new \InvalidArgumentException(
                _('Unable to load plugins. No folder specified.')
            );
        }

        $this->basePath = $basePath;
        $this->paths    = $paths;
    }

    /**
     * Loads ORM-related items from configuration files.
     */
    public function load()
    {
        $finder = new Finder();
        $loaded = [];

        foreach ($this->paths as $path) {
            $path = $this->basePath . DS . $path . DS;

            $finder->files()->in($path)->name('*.yml');

            foreach ($finder as $file) {
                $loaded[] = $this->loadItem($file->getRealPath());
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

        if (method_exists($this, $method)) {
            return $this->{$method}($data);
        }
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
        return new Validation($data);
    }

    /**
     * Returns a new database connection from data.
     *
     * @param array $data The data to load.
     *
     * @return Connection The loaded database connection.
     */
    public function loadDatabase($data)
    {
        return new Connection($data);
    }
}
