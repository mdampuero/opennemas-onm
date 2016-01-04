<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Component\Assetic;

use Symfony\Component\Finder\Finder;

class AssetBag
{
    /**
     * Array of literal styles.
     *
     * @var array
     */
    protected $literalStyles = [];

    /**
     * Array of literal scripts.
     *
     * @var array
     */
    protected $literalScripts = [];

    /**
     * The array of styles.
     *
     * @var array
     */
    protected $styles = [];

    /**
     * The array of scripts.
     *
     * @var array
     */
    protected $scripts = [];

    /**
     * Initializes the AssetBag
     *
     * @var array The assetic configuration.
     */
    public function __construct($config, $instance)
    {
        $this->config       = $config;
        $this->currentTheme = $instance->settings['TEMPLATE_USER'];
        $this->sitePath     = SITE_PATH;
    }

    /**
     * Adds a new literal script to the bag.
     *
     * @param string $script The script content.
     */
    public function addLiteralScript($script)
    {
        $this->literalScripts[] = $script;
    }

    /**
     * Adds a new literal style to the bag.
     *
     * @param string $script The styles content.
     */
    public function addLiteralStyle($styles)
    {
        $this->literalStyles[] = $styles;
    }

    /**
     * Adds a new script to the bag.
     *
     * @param string $path    The path to the file.
     * @param array  $filters The array of filters to apply.
     */
    public function addScript($path, $filters = [])
    {
        $scripts = $this->parsePath($path);

        if (!empty($scripts)) {
            foreach ($scripts as $script) {
                if (!array_key_exists($script, $this->scripts)) {
                    $this->scripts[$script] = $filters;
                }
            }
        }
    }

    /**
     * Adds a new style to the bag.
     *
     * @param string $path    The path to the file.
     * @param array  $filters The array of filters to apply.
     */
    public function addStyle($path, $filters = [])
    {
        $styles = $this->parsePath($path);

        if (!empty($styles)) {
            foreach ($styles as $style) {
                if (!array_key_exists($style, $this->styles)) {
                    $this->styles[$style] = $filters;
                }
            }
        }
    }

    /**
     * Return the list of literal scripts.
     *
     * @return array The array of literal scripts.
     */
    public function getLiteralScripts()
    {
        return $this->literalScripts;
    }

    /**
     * Return the list of literal styles.
     *
     * @return array The array of literal styles.
     */
    public function getLiteralStyles()
    {
        return $this->literalStyles;
    }

    /**
     * Return the list of scripts.
     *
     * @return array The array of scripts.
     */
    public function getScripts()
    {
        return $this->scripts;
    }

    /**
     * Return the list of styles.
     *
     * @return array The array of styles.
     */
    public function getStyles()
    {
        return $this->styles;
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
        return $this->sitePath . $this->config['folders']['bundles'] . DS
            . strtolower(preg_replace('/bundle/i', '', $bundle));
    }

    /**
     * Parses the source path for an asset.
     *
     * @param string $src The asset source path.
     *
     * @return array The list of real asset source paths.
     */
    private function parsePath($src)
    {
        if (strpos($src, '@') === false) {
            return [ $src ];
        }

        $theme = substr($src, 1, strpos($src, '/') - 1);
        $asset = substr($src, strpos($src, '/'));

        if (strpos($theme, 'Theme') !== false) {
            $path = $this->parseThemeName($theme) . $asset;
        }

        if ($theme === 'Common') {
            $path = $this->sitePath . $this->config['folders']['common']
                . $asset;
        }

        if (strpos($theme, 'Bundle') !== false) {
            $path = $this->parseBundleName($theme) . $asset;
        }

        if (strpos($asset, '*') !== false) {
            $path = str_replace('*', '', $path);

            if (!is_dir($path)) {
                return false;
            }

            $finder = new Finder();
            $finder->files()->in($path);

            $path = [];
            foreach ($finder as $file) {
                $path[] = $file->getRealPath();
            }

            return $path;
        }

        return [ $path ];
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
        if ($theme === 'Theme') {
            $theme = $this->currentTheme;
        }

        $theme = preg_replace('/[a-z]{2,63}\.[a-z0-9\-]+\.theme\./', '', $theme);
        $theme = strtolower(preg_replace('/theme/i', '', $theme));

        return $this->sitePath . $this->config['folders']['themes'] . DS
                . $theme;
    }
}
