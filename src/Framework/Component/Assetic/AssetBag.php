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
        $this->config = $config;

        $this->sitePath  = SITE_PATH;
        $this->themePath = $this->sitePath . 'themes' . DS .
            $this->parseThemeName($instance->settings['TEMPLATE_USER']);
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
        $script = $this->parsePath($path);

        if (!in_array($script, $this->scripts)) {
            $this->scripts[$script] = $filters;
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
        $style = $this->parsePath($path);

        if (!in_array($style, $this->styles)) {
            $this->styles[$style] = $filters;
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
        return strtolower(preg_replace('/bundle/i', '', $bundle));
    }

    /**
     * Parses the source path for an asset.
     *
     * @param string $src The asset source path.
     *
     * @return string The real asset source path.
     */
    private function parsePath($src)
    {
        if (strpos($src, '@') === false) {
            return $src;
        }

        $theme = substr($src, 1, strpos($src, '/') - 1);
        $asset = substr($src, strpos($src, '/'));

        if ($theme === 'Theme') {
            return $this->themePath . $asset;
        }

        if ($theme === 'Common') {
            return $this->sitePath . $this->config['folders']['common']
                    . $asset;
        }

        $index = 'themes';
        $theme = $this->parseThemeName($theme);

        if (strpos($theme, 'bundle') !== false) {
            $index = 'bundles';
            $theme = $this->parseBundleName($theme);
        }

        return $this->sitePath . $this->config['folders'][$index] . DS
            . $theme . $asset;
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
        $theme = preg_replace('/[a-z]{2,63}\.[a-z0-9\-]+\.theme\./', '', $theme);

        return strtolower(preg_replace('/theme/i', '', $theme));
    }
}
