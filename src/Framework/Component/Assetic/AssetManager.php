<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
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
        if (!empty($container->get('core.instance'))) {
            $this->themePath = $this->sitePath . 'themes' . DS .
                $container->get('core.instance')->settings['TEMPLATE_USER'];
        }

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
     * @param array  $assets  The array of assets.
     * @param array  $filters The array of filters per file.
     * @param string $name    The name of the output file.
     *
     * @codeCoverageIgnore
     */
    public function writeAssets($assets, $assetFilters, $name = 'default')
    {
        if (empty($assets)) {
            return [];
        }

        if (!$this->debug()) {
            // Check all-in-one asset if prod environment
            $target     = $this->getTargetPath($assets, $name, true);
            $targetPath = $this->config['root'] . $target;

            if (file_exists($targetPath)) {
                return [ $this->createAssetSrc($target) ];
            }
        }

        $cache   = new FileSystemCache($this->config['cache_path']);
        $factory = $this->getAssetFactory();
        $parsed  = [];
        $writer  = new AssetWriter($this->config['root']);

        // Apply filters to each file
        foreach ($assets as $path) {
            $filters    = $assetFilters[$path];
            $target     = $this->getTargetPath($path);
            $targetPath = $this->config['root'] . $target;

            if ($this->debug()
                || (!$this->debug() && !file_exists($targetPath))
            ) {
                $filters = $this->getFilters($path, $filters);
                $fm      = $this->getFilterManager($filters);

                $factory->setFilterManager($fm);

                $asset = $factory->createAsset($path, $filters);
                $asset->setTargetPath($target);

                $cached = new AssetCache($asset, $cache);
                $writer->writeAsset($cached);
            }

            $parsed[] = $this->createAssetSrc($target);
        }

        if ($this->debug() || empty($name)) {
            return $parsed;
        }

        $parsed = array_map(function ($a) {
            // Remove starting '/'
            if ($a[0] !== '/') {
                return $a;
            }

            return substr($a, 1);
        }, $parsed);

        // Apply CSS rewrite when unifiying
        $target  = $this->getTargetPath($assets, $name, true);
        $filters = $this->getFilters($target, [ 'cssrewrite' ]);
        $fm      = $this->getFilterManager($filters);

        $factory->setFilterManager($fm);

        $assets = $factory
            ->createAsset($parsed, $filters, [ 'output' => $target ]);
        $assets->setTargetPath($target);

        $cached = new AssetCache($assets, $cache);
        $writer->writeAsset($cached);

        return [ $this->createAssetSrc($target) ];
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
            $this->config['build_path'] . '/*.' . $this->extension
        );
        $af->setDebug($this->debug());

        return $af;
    }

    /**
     * Returns a target path from real asset paths.
     *
     * @param mixed   $asset The real path to asset.
     * @param string  $name  The target filename.
     * @param boolean $dist  Whether to use the dist path instead of output
     *                       path.
     *
     * @return string The target path for given assets.
     */
    protected function getTargetPath($asset, $name = 'default', $dist = false)
    {
        $src  = '';
        $path = $this->config['build_path'];

        if ($dist) {
            $path = $this->config['output_path'];
        }

        // Get original filename when no output provided
        if (is_string($asset)) {
            $asset = DS . str_replace(SITE_PATH, '', $asset);
            $src   = $asset;
        }

        // If array, remove path and implode for md5
        if (is_array($asset)) {
            $asset = array_map(function ($a) {
                return DS . str_replace(SITE_PATH, '', $a);
            }, $asset);

            $asset = implode(',', $asset);
            $src   = $name . '.' . $this->extension;
        }

        if (!empty($src)) {
            $src = basename($src);
            $src = substr($src, 0, strrpos($src, '.'));
        }

        return $path . DS . $src . '.'
            . substr(md5($asset), 0, 8) . '.' . DEPLOYED_AT . '.xzy.'
            . $this->extension;
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
        $request = $this->container->get('request_stack')->getCurrentRequest();

        $port = '';
        if (!empty($request) && $request->headers->get('X-Forwarded-port')) {
            $port = $request->headers->get('X-Forwarded-port');
        }

        $port = ($port != 80 && $port != 443) ? ':' . $port : '';

        $src = DS . $src;

        if ($this->config['use_asset_servers']) {
            if (strpos($this->config['asset_domain'], '%d') === false) {
                // Static site URL
                return $this->config['asset_domain'] . $port . $src;
            }

            // Site URL with pattern
            $sum = 0;
            $max = strlen($src);
            for ($i = 0; $i < $max; $i++) {
                $sum += ord($src[$i]);
            }

            $server = $sum % $this->config['asset_servers'];

            return sprintf($this->config['asset_domain'], $server) . $port
                . $src;
        }

        return $src;
    }

    /**
     * Initializes the filter manager with the current filters for an asset.
     *
     * @param array $filters The array of filters.
     *
     * @return FilterManager The filter manager.
     */
    abstract protected function getFilterManager($filters);

    /**
     * Returns the valid filters for the asset.
     *
     * @param string $asset   The asset path.
     * @param array  $filters The array of filters.
     *
     * @return array The valid filters.
     */
    abstract protected function getFilters($asset, $filters);
}
