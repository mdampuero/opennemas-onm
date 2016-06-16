<?php
use Onm\FilesManager as fm;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Template class
 *
 * @package Onm
 * @author  Fran Dieguez <fran@openhost.es>
 **/
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
    public $locale_dir       = null;
    public $css_dir          = null;
    public $image_dir        = null;
    public $js_dir           = null;
    public $common_asset_dir = null;
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
            $fs->mkdir($path, 0755);
        }

        // Copy default cache configuration
        $cm = $this->container->get('template_cache_config_manager');
        $cm->setConfigDir($path);
        $cm->saveDefault();

        $this->setConfigDir($path);

        $path = $basePath . '/smarty/cache';

        if (!file_exists($path)) {
            $fs->mkdir($path, 0755);
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

        $path  = $basePath . '/smarty/compile-'
            . str_replace('es.openhost.theme.', '', $theme->uuid);

        if (!file_exists($path)) {
            $fs->mkdir($path, 0755);
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
        $theme = str_replace('es.openhost.theme.', '', $theme->uuid);
        $this->error_reporting = E_ALL & ~E_NOTICE;

        // Template variables
        $baseUrl = SITE_URL.'/themes/'.$theme.'/';
        $baseUrl = str_replace('http:', '', $baseUrl);

        $this->locale_dir       = $baseUrl.'locale/';
        $this->css_dir          = $baseUrl.'css/';
        $this->image_dir        = $baseUrl.'images/';
        $this->js_dir           = $baseUrl.'js/';
        $this->common_asset_dir = SITE_URL.'assets/';
        $this->caching          = false;

        $this->assign(
            'params',
            array(
                'LOCALE_DIR'       => $this->locale_dir,
                'CSS_DIR'          => $this->css_dir,
                'IMAGE_DIR'        => $this->image_dir,
                'JS_DIR'           => $this->js_dir,
                'COMMON_ASSET_DIR' => $this->common_asset_dir,
                'THEME'            => $theme,
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
     * Registers the required smarty plugins
     *
     * @return void
     **/
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
