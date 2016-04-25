<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Cache\Core\Loader;

use Common\Cache\Redis\Redis;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

/**
 * Loads cache configuration files.
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
     * Path to the cache configuration.
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
     */
    public function __construct($path, $env, $default = [])
    {
        $this->default = $default;
        $this->env     = $env;
        $this->path    = $path;
    }

    /**
     * Loads cache-related items from configuration files.
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
                $loaded[$item->name] = $item;
            }
        }

        return $loaded;
    }

    /**
     * Load an item from a configuration file.
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
     * Returns a Redis connection from data.
     *
     * @param array $data The data to load.
     *
     * @return Redis The Redis connection.
     */
    public function loadRedis($data)
    {
        if (array_key_exists('redis', $this->default)) {
            $data['redis'] =
                array_merge($this->default['redis'], $data['redis']);
        }

        return new Redis($data['redis']);
    }
}
