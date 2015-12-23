<?php
/**
 * This file is part of the Onm package.
 *
 * (c) OpenHost S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Component\Assetic;

use Assetic\Asset\AssetCache;
use Assetic\AssetManager as BaseAssetManager;
use Assetic\AssetWriter;
use Assetic\Cache\FilesystemCache;
use Assetic\Factory\AssetFactory;
use Symfony\Component\Yaml\Parser;

/**
* Base class for custom assets manager.
*/
abstract class AssetManager
{
    /**
     * The assets factory.
     *
     * @var AssetFactory
     */
    protected $af;

    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * The current environment.
     *
     * @var string
     */
    protected $env;

    /**
     * The array of filters
     *
     * @var array
     */
    protected $filters = [];

    /**
     * The filter manager.
     *
     * @var FilterManager
     */
    protected $fm;

    /**
     * Default output path.
     *
     * @var string
     */
    protected $outputPath;

    /**
     * Current theme path.
     *
     * @var string
     */
    protected $themePath;

    /**
     * Real site path.
     *
     * @var string
     */
    protected $sitePath;

    /**
     * Parses the assetic configuration and initializes the manager.
     *
     * @param string $environment The current environment.
     * @param string $config      The assetic configuration.
     */
    public function __construct($container, $config)
    {
        $this->container = $container;
        $this->env       = $container->get('kernel')->getEnvironment();
        $this->config    = $config;

        $this->sitePath = realpath(SITE_PATH) . DS;

        // Get current instance theme path
        $this->themePath = $this->sitePath . 'themes' . DS .
            $container->get('instance_manager')->current_instance
            ->settings['TEMPLATE_USER'];

        $this->am = new BaseAssetManager();
    }

    /**
     * Check if the asset manager has to run in debug mode.
     *
     * @return boolean True, if asset manager has to run in debug mode.
     *                 Otherwise, return false.
     */
    public function debug()
    {
        if ($this->config['asset_compilation_in_dev']) {
            return false;
        }

        return $this->env == 'dev';
    }

    /**
     * Writes all the assets.
     *
     * @param array The array of assets.
     */
    public function writeAssets($assets)
    {
        $factory = $this->getAssetFactory();
        if (empty($assets)) {
            return [];
        }

        $parsed = [];

        // Prepare the assets writer
        $this->writer = new AssetWriter($this->config['root']);

        foreach ($assets as $path => $filters) {
            $name = substr($path, strrpos($path, '/') + 1);
            $name = substr($name, 0, strrpos($name, '.'));

            $fm = $this->getFilterManager($filters);

            $factory->setFilterManager($fm);

            $asset = $factory->createAsset($path, $filters);

            // Get hash to append to filename
            $hash = substr(
                $asset->getTargetPath(),
                strrpos($asset->getTargetPath(), '/') + 1
            );

            $hash = substr($hash, 0, strrpos($hash, '.'));

            // Create and set target path
            $target = $this->config['output_path'] . '/' . $name . '-'
                . $hash . '.' . $this->extension;
            $asset->setTargetPath($target);

            $cached = new AssetCache(
                $asset,
                new FileSystemCache($this->config['build_path'])
            );

            $this->writer->writeAsset($cached);

            $parsed[] = '/' . $cached->getTargetPath();
        }

        if ($this->debug()) {
            $srcs = $parsed;
        } else {
            // Create all-in-one asset
            $parsed = array_map(function ($a) {
                return substr($a, 1);
            }, $parsed);

            $assets = $factory->createAsset($parsed);

            $cached = new AssetCache(
                $assets,
                new FileSystemCache($this->config['build_path'])
            );

            $this->writer->writeAsset($cached);

            // Save all-in-one source path
            $srcs[] = $this->createAssetSrc($cached->getTargetPath());
        }

        return $srcs;
    }

    /**
     * Initializes the AssetFactory.
     *
     * @return AssetFactory The asset factory.
     */
    protected function getAssetFactory()
    {
        // Factory setup
        $af = new AssetFactory($this->config['root']);
        $af->setAssetManager($this->am);
        $af->setDefaultOutput(
            $this->config['output_path'] . '/*.' . $this->extension
        );
        $af->setDebug($this->debug());

        return $af;
    }

    /**
     * Creates a target asset name basing on the default target path.
     *
     * @param string $defaultTarget Default target name.
     *
     * @return string Created asset name.
     */
    private function createAssetSrc($src)
    {
        $request = $this->container->get('request');

        $port = $request->getPort();
        if ($request->headers->get('X-Forwarded-port')) {
            $port = $request->headers->get('X-Forwarded-port');
        }

        if ($port != 80 && $port != 443) {
            $port = ':' . $port;
        } else {
            $port = '';
        }


        $src = DS . substr($src, 0, strrpos($src, '.') + 1) . DEPLOYED_AT . '.'
            . $this->extension;

        if ($this->config['use_asset_servers']) {
            if (strpos($this->config['asset_domain'], '%d') !== false) {
                // Site URL with pattern
                $sum = 0;
                $max = strlen($src);
                for ($i = 0; $i < $max; $i++) {
                    $sum += ord($src[$i]);
                }

                $server = $sum % $this->config['asset_servers'];
                $src = sprintf($this->config['asset_domain'], $server) . $port
                    . $src;
            } else {
                // Static site URL
                $src = $this->config['asset_domain'] . $port . $src;
            }
        }

        return $src;
    }

    /**
     * Initializes the filter manager with the current filters.
     *
     * @param array $filters The array of filters.
     *
     * @return FilterManager The filter manager.
     */
    abstract protected function getFilterManager($filters);
}
