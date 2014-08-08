<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Framework\Assetic;

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
     * Default configuration path.
     *
     * @var string
     */
    protected $configPath;

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

        // Get current instance theme path
        $this->themePath = SITE_PATH . 'themes' . DS .
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
        return $this->env == 'dev';
    }

    /**
     * Returns the current configuration.
     *
     * @return array The current configuration.
     */
    public function getConfiguration()
    {
        return $this->config;
    }

    /**
     * Parses the list of assets.
     *
     * @param array $assets The list of assets.
     */
    public function initAssets($assets)
    {
        $this->assets = array();
        foreach ($assets as $asset) {
            $this->assets[] = $this->parseAssetSrc($asset);
        }
    }

    /**
     * Initializes the AssetFactory.
     */
    public function initFactory()
    {
        // Factory setup
        $this->af = new AssetFactory($this->config['root']);
        $this->af->setAssetManager($this->am);
        $this->af->setFilterManager($this->fm);
        $this->af->setDefaultOutput(
            $this->config['output'] . '/*.' . $this->extension
        );
        $this->af->setDebug($this->debug);
        // $factory->addWorker(new CacheBustingWorker(CacheBustingWorker::STRATEGY_MODIFICATION));
    }

    /**
     * Writes all the assets.
     */
    public function writeAssets()
    {
        $srcs = array();

        // Prepare the assets writer
        $this->writer = new AssetWriter($this->config['root']);

        // Create all-in-one asset
        $assets = $this->af->createAsset($this->assets);
        if ($this->debug()) {
            foreach ($assets as $asset) {
                $cached = new AssetCache(
                    $asset,
                    new FilesystemCache($this->config['build_path'])
                );

                $this->writer->writeAsset($cached);

                // Save all asset source paths
                $srcs[] = $this->createAssetSrc($asset->getTargetPath());
            }
        } else {
            $cached = new AssetCache(
                $assets,
                new FileSystemCache($this->config['build_path'])
            );

            $this->writer->writeAsset($cached);

            // Save all-in-one source path
            $srcs[] = $this->createAssetSrc($assets->getTargetPath());
        }

        return $srcs;
    }

    /**
     * Parses the source path for an asset.
     * @param string $src The asset source path.
     *
     * @return string The real asset source path.
     */
    private function parseAssetSrc($src)
    {
        if (strpos($src, '@') === 0) {
            $theme = substr($src, 1, strpos($src, '/') - 1);
            $asset = substr($src, strpos($src, '/'));

            switch ($theme) {
                case 'Common':
                    $src = SITE_PATH . $this->config['folders']['common'] . $asset;
                    break;
                case 'Theme':
                    $src = SITE_PATH . $this->config['folders']['themes']
                        . $this->themePath . $asset;
                    break;
                default:
                    $theme = $this->parseThemeName($theme);
                    $src   = SITE_PATH . $this->config['folders']['themes']
                        . DS .$theme . $asset;
            }
        }

        return $src;
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
        if (array_key_exists('site_url', $this->config)
            && $this->config['site_url'] !== false
        ) {
            $src = $this->config['site_url'] . $src;
        }

        return $src;
    }

    /**
     * Parses the theme name and returns the theme folder name.
     *
     * @param string $theme The theme name.
     *
     * @return string The theme folder name.
     */
    private function parseThemeName($theme)
    {
        if (strpos($theme, 'Theme') !== false) {
            $theme = substr($theme, 0, strpos($theme, 'Theme'));
        }

        $theme = strtolower($theme);

        return $theme;
    }

    /**
     * Creates a new FilterManager from the given filters configuration.
     *
     * @param array $filters Array of filters configuration.
     */
    abstract public function initFilters($filters);
}
