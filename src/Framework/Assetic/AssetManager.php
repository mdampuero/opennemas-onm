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
     * @param array   $assets The list of assets.
     * @param booelan $append Whether to append or restart the array of assets.
     */
    public function initAssets($assets, $append = false)
    {
        if (!$append) {
            $this->assets = array();
        }

        foreach ($assets as $asset) {
            $asset = $this->parseAssetSrc($asset);

            $pos = strrpos($asset, '*');
            if ($pos == strlen($asset) - 1) {
                foreach (glob($asset) as $asset) {
                    if (!is_dir($asset)) {
                        $this->assets[] = str_replace($this->sitePath, '', $asset);
                    } else {
                        $this->initAssets(glob($asset . '/*'), true);
                    }
                }
            } else {
                $this->assets[] = $asset;
            }
        }
    }

    /**
     * Merge the current configuration with parameters from template.
     *
     * @param array $config The configuration parameters from template.
     */
    public function initConfiguration($config)
    {
        if (array_key_exists('src', $config)) {
            unset($config['src']);
        }

        if (array_key_exists('filters', $config)) {
            unset($config['filters']);
        }

        $this->config = array_merge($this->config, $config);
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
            $this->config['output_path'] . '/*.' . $this->extension
        );
        $this->af->setDebug($this->debug);
    }

    /**
     * Writes all the assets.
     */
    public function writeAssets()
    {
        $srcs = array();

        // Prepare the assets writer
        $this->writer = new AssetWriter($this->config['root']);

        if ($this->debug()) {
            foreach ($this->assets as &$asset) {
                $name = substr($asset, strrpos($asset, '/') + 1);
                $name = substr($name, 0, strrpos($name, '.'));

                $parsed = $this->af->createAsset($asset, $this->filters);

                // Get hash to append to filename
                $hash = substr(
                    $parsed->getTargetPath(),
                    strrpos($parsed->getTargetPath(), '/') + 1
                );
                $hash = substr($hash, 0, strrpos($hash, '.'));

                // Create and set target path
                $target = $this->config['output_path'] . '/' . $name . '.'
                    . $hash . '.' . $this->extension;
                $parsed->setTargetPath($target);

                $cached = new AssetCache(
                    $parsed,
                    new FileSystemCache($this->config['build_path'])
                );

                $this->writer->writeAsset($cached);

                $asset = '/' . $cached->getTargetPath();
            }

            $srcs = $this->assets;
        } else {
            // Create all-in-one asset
            $assets = $this->af->createAsset($this->assets, $this->filters);

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
                    $src = $this->config['folders']['common'] . $asset;
                    break;
                case 'Theme':
                    $src = $this->config['folders']['themes']
                        . $this->themePath . $asset;
                    break;
                default:
                    if (strpos($theme, 'Theme') !== false) {
                        $theme = $this->parseThemeName($theme);
                        $src   = $this->config['folders']['themes']
                            . DS . $theme . $asset;
                    } elseif (strpos($theme, 'Bundle') !== false) {
                        $theme = $this->parseBundleName($theme);
                        $src   = $this->config['folders']['bundles']
                            . DS . $theme . $asset;
                    }

            }

            if (!$this->debug()) {
                $src = $this->sitePath . $src;
            }
        }

        return $src;
    }

    /**
     * Parses the bundle name and returns the bundle folder name.
     *
     * @param string $bundle The bundle name.
     *
     * @return string The bundle folder name.
     */
    private function parseBundleName($bundle)
    {
        if (strpos($bundle, 'Bundle') !== false) {
            $bundle = substr($bundle, 0, strpos($bundle, 'Bundle'));
        }

        $bundle = strtolower($bundle);

        return $bundle;
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
