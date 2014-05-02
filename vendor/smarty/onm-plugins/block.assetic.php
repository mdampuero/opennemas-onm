<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
use Assetic\AssetManager;
use Assetic\FilterManager;
use Assetic\Filter;
use Assetic\Factory\AssetFactory;
use Assetic\Factory\Worker\CacheBustingWorker;
use Assetic\AssetWriter;
use Assetic\Asset\AssetCache;
use Assetic\Cache\FilesystemCache;
use Symfony\Component\Yaml\Parser;

/**
 * [smarty_block_assetic description]
 *
 * @param  array                    $params   Array of parameters.
 * @param  string                   $content  Current HTML to return.
 * @param  Smarty_Internal_Template $template Current template
 * @param  boolean                  $repeat   Current extension call number.
 * @return string                             Result HTML.
 */
function smarty_block_assetic($params, $content, $template, &$repeat)
{
    // In debug mode, we have to be able to loop a certain number of times, so we use a static counter
    static $count;
    static $assetsUrls;

    $realpath = realpath($params['config_path']);
    $root     = mb_substr($realpath, 0, mb_strlen($realpath) - mb_strlen($params['config_path']));

    // Find the configuration directory
    $baseConfigPath = APP_PATH . 'config';

    // Load configuration
    $yaml   = new Parser();
    $config = $yaml->parse(file_get_contents($baseConfigPath . '/config.yml'));
    $config = $config['assetic'];
    $params['build_path'] = 'compile';

    // Opening tag (first call only)
    if ($repeat) {
        $am = new AssetManager();
        $fm = initFilterManager($config['filters']);

        // Factory setup
        $factory = new AssetFactory($root);
        $factory->setAssetManager($am);
        $factory->setFilterManager($fm);
        $factory->setDefaultOutput('assetic/*.' . $params['output']);
        $factory->setDebug($params['debug']);
        $factory->addWorker(new CacheBustingWorker());

        // Prepare the assets writer
        $writer = new AssetWriter($params['build_path']);

        // Read bundles and dependencies config files
        if (file_exists($baseConfigPath . '/bundles.json')) {
            $bundles = json_decode(file_get_contents($baseConfigPath . '/bundles.json'));
        }

        if (file_exists($baseConfigPath . '/dependencies.json')) {
            $dependencies = json_decode(file_get_contents($baseConfigPath . '/dependencies.json'));
        }

        // Parse filters to use
        $filters = array();
        if (isset($params['filters'])) {
            $filters = explode(',', $params['filters']);
        }

        // Parse required assets
        $requiredAssets = explode(',', $params['assets']);
        foreach ($requiredAssets as &$asset) {
            $asset = trim($asset);
        }

        // If a bundle name is provided
        if (isset($params['bundle'])) {
            $asset = $factory->createAsset(
                $bundles->$params['output']->$params['bundle'],
                $filters
            );

            $cache = new AssetCache(
                $asset,
                new FilesystemCache($params['build_path'])
            );

            $writer->writeAsset($cache);

        } elseif (isset($params['assets'])) { // If individual assets are provided
            $assets = array();
            // Include only the references first
            foreach ($requiredAssets as $a) {
                // If the asset is found in the dependencies file, let's create it
                // If it is not found in the assets but is needed by another asset and found in the references, don't worry, it will be automatically created
                if (isset($dependencies->$params['output']->assets->$a)) {
                    // Create the reference assets if they don't exist
                    foreach ($dependencies->$params['output']->assets->$a as $ref) {
                        try {
                            $am->get($ref);
                        } catch (InvalidArgumentException $e) {
                            $path = $dependencies->$params['output']->references->$ref;

                            $assetTmp = $factory->createAsset($path);
                            $am->set($ref, $assetTmp);
                            $assets[] = '@'.$ref;
                        }
                    }
                }
            }

            // Now, include assets
            foreach ($requiredAssets as $a) {
                // Add the asset to the list if not already present, as a reference or as a simple asset
                $ref = null;
                if (isset($dependencies->$params['output'])) {
                    foreach ($dependencies->$params['output']->references as $name => $file) {
                        if ($file == $a) {
                            $ref = $name;
                            break;
                        }
                    }
                }


                if (array_search($a, $assets) === false
                    && ($ref === null || array_search('@' . $ref, $assets) === false)
                ) {
                    $assets[] = $a;
                }
            }



            // Create the asset
            $asset = $factory->createAsset(
                $assets,
                $filters
            );

            $cache = new AssetCache(
                $asset,
                new FilesystemCache($params['build_path'])
            );

            $writer->writeAsset($cache);
        }

        // If debug mode is active, we want to include assets separately
        if (array_key_exists('debug', $params) && $params['debug'] == 'true') {
            $assetsUrls = array();
            foreach ($asset as $a) {

                $cache = new AssetCache(
                    $a,
                    new FilesystemCache($params['build_path'])
                );

                $writer->writeAsset($cache);
                $assetsUrls[] = $a->getTargetPath();
            }
            // It's easier to fetch the array backwards, so we reverse it to insert assets in the right order
            $assetsUrls = array_reverse($assetsUrls);

            $count = count($assetsUrls);

            if (isset($config->site_url)) {
                $template->assign($params['asset_url'], $config->site_url.'/'.$params['build_path'].'/'.$assetsUrls[$count-1]);
            } else {
                $template->assign($params['asset_url'], '/'.$params['build_path'].'/'.$assetsUrls[$count-1]);
            }


        } else { // Production mode, include an all-in-one asset
            if (isset($config->site_url)) {
                $template->assign($params['asset_url'], $config->site_url.'/'.$params['build_path'].'/'.$asset->getTargetPath());
            } else {
                $template->assign($params['asset_url'], '/'.$params['build_path'].'/'.$asset->getTargetPath());
            }
        }

    } else { // Closing tag

        if (isset($content)) {
            // If debug mode is active, we want to include assets separately
            if ($params['debug']) {
                $count--;
                if ($count > 0) {
                    if (isset($config->site_url)) {
                        $template->assign($params['asset_url'], $config->site_url.'/'.$params['build_path'].'/'.$assetsUrls[$count-1]);
                    } else {
                        $template->assign($params['asset_url'], '/'.$params['build_path'].'/'.$assetsUrls[$count-1]);
                    }
                }
                $repeat = $count > 0;
            }

            return $content;
        }
    }

}

/**
 * Filters configuration.
 *
 * TODO: Add support to multiple filters.
 *
 * @param  array         $config Array of filters.
 * @return FilterManager
 */
function initFilterManager($filters)
{
    $fm = new FilterManager();

    // $cssEmbedFilter = new Filter\CssEmbedFilter($root . $config->cssembed_path, $config->java_path);
    // $cssEmbedFilter->setRoot($root);
    // $fm->set('yui_js', new Filter\Yui\JsCompressorFilter('/usr/share/yui-compressor/yui-compressor.jar', '/home/opennemas/manager/java-executor.sh'));
    // $fm->set('yui_css', new Filter\Yui\CssCompressorFilter($root . $config->yuicompressor_path, $config->java_path));
    // $fm->set('less', new Filter\LessphpFilter());
    // $fm->set('sass', new Filter\Sass\SassFilter());
    // $fm->set('cssembed', $cssEmbedFilter);
    // $fm->set('cssabsolute', new Filter\CssAbsoluteFilter($config->site_url));
    // $fm->set('closure_api', new Filter\GoogleClosure\CompilerApiFilter());
    // $fm->set('closure_jar', new Filter\GoogleClosure\CompilerJarFilter($root . $config->closurejar_path, $config->java_path));

    $fm->set('uglifycss', new Filter\UglifyJsFilter($filters['uglifycss']['bin'], $filters['uglifycss']['node']));
    $fm->set('uglifyjs', new Filter\UglifyJsFilter($filters['uglifyjs']['bin'], $filters['uglifyjs']['node']));
    $fm->set('uglifyjs2', new Filter\UglifyJs2Filter($filters['uglifyjs2']['bin'], $filters['uglifyjs2']['node']));
    $fm->set('cssrewrite', new Filter\CssRewriteFilter());

    return $fm;
}
