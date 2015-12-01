<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\ORM\Loader;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class Loader
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * The list of paths to load items from.
     *
     * @var array
     */
    protected $paths;

    /**
     * The list of loaded items.
     *
     * @var array
     */
    protected $loaded;

    /**
     * Initializes the Loader.
     *
     * @param ServiceContainer $cache The service container.
     * @param string           $paths The path to folders to load from.
     *
     * @throws InvalidArgumentException If the path is not valid.
     */
    public function __construct($container, $paths)
    {
        if (empty($paths)) {
            throw new \InvalidArgumentException(
                _('Unable to load plugins. No folder specified.')
            );
        }

        $this->container = $container;
        $this->paths     = $paths;

        $this->load();
    }

    /**
     * Returns the list of loaded items.
     *
     * @return array The list of loaded items.
     */
    public function get()
    {
        return $this->loaded;
    }

    /**
     * Loads all plugins in the paths.
     */
    public function load()
    {
        $finder = new Finder();

        foreach ($this->paths as $path) {
            $path = $this->container->getParameter('kernel.root_dir')
                . DS . '..' . DS . $path . DS;

            $finder->files()->in($path)->name('*.yml');

            foreach ($finder as $file) {
                if (file_exists($file)) {
                    $this->loadItem($file);
                }
            }
        }
    }

    /**
     * Load a plugin from configuration file.
     *
     * @param string $path The path to plugin configuration file.
     */
    public function loadItem($path)
    {
        $config = Yaml::parse($path);

        $path           = str_replace('/config.yml', '', $path);
        $config['path'] = substr($path, strpos($path, '/themes'));

        try {
            if (array_key_exists('type', $config)) {
                $loader = $this->container
                    ->get('orm.loader.' . $config['type']);

                $this->loaded[] = $loader->load($config);
            }
        } catch (\Exception $e) {
        }
    }
}
