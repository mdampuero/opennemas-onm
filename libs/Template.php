<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Onm\FilesManager as fm;
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
     * @param Extension $theme The current theme.
     */
    public function __construct($container)
    {
        parent::__construct();

        $this->container = $container;

        $this->registerCustomPlugins();
        $this->setTemplateVars();

        // Fran: I have to comment this line cause templating.globals is no
        // longer available. We need to know if this don't have any drawback in
        // current template files.
        // $this->assign('app', getService('templating.globals'));
    }

    /**
     * Sets the active theme.
     *
     * @param Extension $theme The current theme.
     */
    public function addActiveTheme($theme)
    {
        $this->setupCompiles($theme);
        $this->setupCache();
        $this->setupPlugins($theme);

        $this->addTheme($theme);
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
     * Configures the smarty cache path.
     */
    public function setupCache()
    {
        $basePath = $this->container->getParameter('core.paths.cache') . '/'
            . $this->container->get('instance')->internal_name;

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
    public function setupCompiles($theme)
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
    public function setupPlugins($theme)
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

    /**
     * Sets some template paths
     */
    public function setTemplateVars()
    {
        $theme = $this->container->get('theme');

        if (!empty($theme)) {
            $theme = str_replace('es.openhost.theme.', '', $theme->uuid);
        }

        $this->error_reporting = E_ALL & ~E_NOTICE;

        // Template variables
        $baseUrl = SITE_URL.'/themes/'.$theme.'/';
        $baseUrl = str_replace('http:', '', $baseUrl);

        $this->image_dir = $baseUrl.'images/';
        $this->caching   = false;

        $this->assign(
            'params',
            array(
                'IMAGE_DIR' => $this->image_dir,
                'THEME'     => $theme,
            )
        );
    }

    public function addFilter($filterSection, $filterName)
    {
        if (in_array($filterSection, array('pre', 'post', 'output'))) {
            $this->filters [$filterSection][]= $filterName;
        }
    }

    public function generateCacheId($seccion, $subseccion = null, $resource = null)
    {
        $cacheId = '';

        if (!empty($subseccion)) {
            $cacheId = (preg_replace('/[^a-zA-Z0-9\s]+/', '', $subseccion).'|'.$resource);
        } elseif (!empty($seccion)) {
            $cacheId = (preg_replace('/[^a-zA-Z0-9\s]+/', '', $seccion).'|'.$resource);
        } else {
            $cacheId = ('home|'.$resource);
        }
        $cacheId = preg_replace('@-@', '', $cacheId);

        return $cacheId;
    }

    public function setConfig($section)
    {
        // Load configuration for the given $section
        $this->configLoad('cache.conf', $section);
        $config = $this->getConfigVars();

        // If configuration says cache is enabled forward this to smarty object
        if (array_key_exists('caching', $config) && $config['caching'] == true) {
            // retain current cache lifetime for each specific display call
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
     */
    public function registerCustomPlugins()
    {
        $this->addFilter("output", "ads_generator");
        $this->addFilter("output", "canonical_url");
        $this->addFilter("output", "comscore");
        $this->addFilter("output", "css_includes");
        $this->addFilter("output", "generate_fb_admin_tag");
        $this->addFilter("output", "generate_fb_pages_tag");
        $this->addFilter("output", "google_analytics");
        $this->addFilter("output", "js_includes");
        $this->addFilter("output", "ojd");
        $this->addFilter("output", "piwik");
        $this->addFilter("output", "ads_scripts");
        $this->addFilter("output", "meta_amphtml");
    }
}
