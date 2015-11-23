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
     * The list of paths to load plugins from.
     *
     * @var array
     */
    protected $paths;

    /**
     * The list of plugins.
     *
     * @var array
     */
    protected $plugins;

    /**
     * Initializes the plugin loader.
     *
     * @param string $cache The cache service.
     * @param string $paths The path to plugins folder.
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
     * Returns the list of plugins.
     *
     * @return array The list of plugins.
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * Loads all plugins in the paths.
     */
    public function load()
    {
        foreach ($this->paths as $path) {
            $path = $this->container->getParameter('kernel.root_dir')
                . DS . '..' . DS . $path;

            foreach (glob($path . '/*') as $dir) {
                $configPath = $dir . DS . 'config.yml';

                if (file_exists($configPath)) {
                    $this->loadPlugin($configPath);
                }
            }
        }
    }

    /**
     * Load a plugin from configuration file.
     *
     * @param string $path The path to plugin configuration file.
     */
    public function loadPlugin($path)
    {
        $config = Yaml::parse($path);

        if (!array_key_exists('type', $config)) {
            throw \Exception('InvalidPluginConfigurationException');
        }

        $loader = $this->container->get('orm.loader.' . $config['type']);

        $this->plugins[] = $loader->load($config);
    }
}
