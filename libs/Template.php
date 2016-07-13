<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Symfony\Component\Filesystem\Filesystem;

class Template extends Smarty
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    // Private properties
    public $theme            = null;
    public $image_dir        = null;
    public $js_includes      = ['header' => array(), 'footer' => []];
    public $css_includes     = ['header' => array(), 'footer' => []];
    public $metatags         = [];
    public $filters          = [];
    public $baseCachePath    = '';
    public $templateBaseDir;

    /**
     * Initializes the Template class
     *
     * @param ServiceContainer $container The service container.
     * @param array            $plugins   The list of plugins.
     */
    public function __construct($container, $plugins)
    {
        parent::__construct();

        $this->container = $container;

        $this->assign('container', $container);
        $this->registerPlugins($plugins);
    }

    /**
     * Sets the active theme.
     *
     * @param Extension $theme The current theme.
     */
    public function addActiveTheme($theme)
    {
        $this->theme = $theme;

        $this->setTemplateVars($theme);
        $this->setupCompiles($theme);
        $this->setupPlugins($theme);

        $this->addTheme($theme);
    }

    /**
     * Adds a filter for a section.
     *
     * @param string $section The section name.
     * @param string $name    The filter name.
     */
    public function addFilter($section, $name)
    {
        if (in_array($section, [ 'pre', 'post', 'output' ])) {
            $this->filters[$section][] = $name;
        }
    }

    /**
     * Configures the cache basing on the instance.
     *
     * @param Instance $instance The current instance.
     */
    public function addInstance($instance)
    {
        $this->setupCache($instance);
    }

    /**
     * Sets the path to template basing on the theme.
     *
     * @param Extension $theme The theme to add.
     */
    public function addTheme($theme)
    {
        $basePath = $this->container->getParameter('core.paths.themes');
        $wm       = $this->container->get('widget_repository');

        $path = str_replace('/themes', '', $basePath) . $theme->path . '/tpl';
        $this->addTemplateDir($path);

        $path = str_replace('/themes', '', $basePath) . $theme->path
            . '/tpl/widgets';

        $wm->addPath($path);
    }

    /**
     * Returns the cache id basing on the section, subsection and resource
     * names.
     *
     * @param string $section    The section name.
     * @param string $subsection The section name.
     * @param string $resource   The resource name.
     *
     * @return string The cache id.
     */
    public function generateCacheId($section, $subsection = null, $resource = null)
    {
        $cacheId = 'home|' . $resource;

        if (!empty($subsection)) {
            $cacheId = preg_replace('/[^a-zA-Z0-9\s]+/', '', $subsection) . '|' . $resource;
        } elseif (!empty($section)) {
            $cacheId = preg_replace('/[^a-zA-Z0-9\s]+/', '', $section) . '|' . $resource;
        }

        $cacheId = preg_replace('@-@', '', $cacheId);

        return $cacheId;
    }

    /**
     * Configures the Smarty cache for the section.
     *
     * @param string $section The section.
     */
    public function setConfig($section)
    {
        // Load configuration for the given $section
        $this->configLoad('cache.conf', $section);
        $config = $this->getConfigVars();

        // If configuration says cache is enabled forward this to smarty object
        if (array_key_exists('caching', $config) && $config['caching'] == true) {
            // Retain current cache lifetime for each specific display call
            $this->setCaching(SMARTY::CACHING_LIFETIME_SAVED);

            if (!array_key_exists('cache_lifetime', $config)
                || empty($config['cache_lifetime'])
            ) {
                $config['cache_lifetime'] = 86400;
            }

            $this->setCacheLifetime($config['cache_lifetime']);
        }
    }

    /**
     * Registers the required smarty plugins.
     *
     * @param array $plugins The list of plugins.
     */
    protected function registerPlugins($plugins)
    {
        if (empty($plugins)) {
            return;
        }

        foreach ($plugins as $section => $p) {
            foreach ($p as $plugin) {
                $this->addFilter($section, $plugin);
            }
        }
    }

    /**
     * Sets some template paths
     *
     * @param string $theme The current theme.
     */
    protected function setTemplateVars($theme)
    {
        if (!empty($theme)) {
            $theme = str_replace('es.openhost.theme.', '', $theme->uuid);
        }

        $this->error_reporting = E_ALL & ~E_NOTICE;

        // Template variables
        $baseUrl = SITE_URL . '/themes/'.$theme.'/';
        $baseUrl = str_replace('http:', '', $baseUrl);

        $this->image_dir = $baseUrl . 'images/';
        $this->caching   = false;

        $this->assign('params', [
            'IMAGE_DIR' => $this->image_dir,
            'THEME'     => $theme,
        ]);
    }

    /**
     * Configures the smarty cache path.
     *
     * @param Instance $instance The current instance.
     */
    protected function setupCache($instance)
    {
        if (empty($instance)) {
            return;
        }

        $basePath = $this->container->getParameter('core.paths.cache') . '/'
            . $instance->internal_name;

        $fs   = new Filesystem();
        $path = $basePath . '/smarty/config';

        if (!file_exists($path)) {
            $fs->mkdir($path, 0775);
        }

        // Copy default cache configuration
        $cm = $this->container->get('template_cache_config_manager');
        $cm->setConfigDir($path);
        $cm->saveDefault();

        $this->setConfigDir($path);

        $path = $basePath . '/smarty/cache';

        if (!file_exists($path)) {
            $fs->mkdir($path, 0775);
        }

        $this->setCacheDir($path);
    }

    /**
     * Configures the smarty compiles path.
     *
     * @param Extension $theme The current theme.
     */
    protected function setupCompiles($theme)
    {
        $basePath = $this->container->getParameter('core.paths.cache.common');

        $fs    = new Filesystem();
        $path  = $basePath . '/smarty/compile-'
            . str_replace('es.openhost.theme.', '', $theme->uuid);

        if (!file_exists($path)) {
            $fs->mkdir($path, 0775);
        }

        $this->setCompileDir($path);
    }

    /**
     * Configures the smarty plugins path.
     *
     * @param Extension $theme The current theme.
     */
    protected function setupPlugins($theme)
    {
        $path = $this->container->getParameter('core.paths.themes') . '/'
            . str_replace('es.openhost.theme.', '', $theme->uuid) . '/plugins';

        $this->addPluginsDir($path);
        $this->addPluginsDir(SITE_LIBS_PATH.'/smarty-onm-plugins/');

        // Load filters
        foreach ($this->filters as $filterSectionName => $filters) {
            foreach ($filters as $filterName) {
                $this->loadFilter($filterSectionName, $filterName);
            }
        }
    }
}
