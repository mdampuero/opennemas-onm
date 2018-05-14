<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Template;

use Symfony\Component\Filesystem\Filesystem;

/**
 * The Template class extends Smarty to add theme support.
 */
class Template extends \Smarty
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * The list of filters.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * The path to image directory.
     *
     * @var string
     *
     * TODO: Make this variable protected
     */
    public $image_dir = null;

    /**
     * The active instance.
     *
     * @var Instance
     */
    protected $instance = null;

    /**
     * Whether to include locale in the cache id.
     *
     * @var boolean
     */
    protected $locale = true;

    /**
     * The active theme.
     *
     * @var Theme
     *
     * TODO: Make this variable protected
     */
    public $theme;

    /**
     * Initializes the Template class
     *
     * @param ServiceContainer $container The service container.
     * @param array            $filters   The list of filters.
     */
    public function __construct($container, $filters)
    {
        parent::__construct();
        $this->setTemplateDir([]);

        // Make compile and cache files writable by group
        $this->_file_perms = 0664;
        $this->_dir_perms  = 0771;

        $this->container = $container;

        $this->assign('container', $container);
        $this->registerFilters($filters);
    }

    /**
     * Sets the active theme.
     *
     * @param Theme $theme The current theme.
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
        $this->instance = $instance;
        $this->setupCache($instance);
    }

    /**
     * Sets the path to template basing on the theme.
     *
     * @param Theme $theme The theme to add.
     */
    public function addTheme($theme)
    {
        $wm = $this->container->get('widget_repository');

        $path = $theme->realpath . '/tpl';
        $this->addTemplateDir($path);

        $path = $theme->realpath . '/tpl/widgets';
        $wm->addPath($path);

        if (!empty($theme->text_domain)) {
            $path = $theme->realpath . '/locale';

            $this->container->get('core.locale')
                ->addTextDomain($theme->text_domain, $path);
        }
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
     * Returns the cache id basing on all the function params passed
     *
     * @return string The cache id.
     */
    public function getCacheId()
    {
        $params = array_filter(
            array_map(
                function ($item) {
                    return preg_replace('/[^a-zA-Z0-9\s]+/', '', $item);
                },
                func_get_args()
            ),
            function ($item) {
                return !empty($item);
            }
        );

        if (!empty($params) && $this->locale) {
            $params[] = $this->container->get('core.locale')->getRequestLocale();
        }

        if (!empty($this->getTemplateVars())
            && array_key_exists('token', $this->getTemplateVars())
        ) {
            $params[] = $this->getTemplateVars()['token'];
        }

        return implode('|', $params);
    }

    /**
     * Returns the service container.
     *
     * @return ServiceContainer The service container.
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Returns the path to image folder for the active theme.
     *
     * @return string The path to image folder for the active theme.
     */
    public function getImageDir()
    {
        if (empty($this->container->get('request_stack')->getCurrentRequest())) {
            return false;
        }

        return $this->container->get('request_stack')->getCurrentRequest()
            ->getSchemeAndHttpHost() . $this->theme->path . 'images/';
    }

    /**
     * Returns the current instance.
     *
     * @return Instance The current instance.
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * Returns the active theme.
     *
     * @return Theme The active theme.
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Returns the theme name for the variant selected
     *
     * @return string the name of the variant
     **/
    public function getThemeSkinName()
    {
        return $this->theme->getCurrentSkinName(
            $this->container->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('theme_skin', 'default')
        );
    }

    /**
     * Returns the theme file name for the variant selected
     *
     * @return string the file name of the variant
     **/
    public function getThemeSkinProperty($propertyName)
    {
        return $this->theme->getCurrentSkinProperty(
            $this->container->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('theme_skin', 'default'),
            $propertyName
        );
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
            $this->setCaching(\Smarty::CACHING_LIFETIME_SAVED);

            if (!array_key_exists('cache_lifetime', $config)
                || empty($config['cache_lifetime'])
            ) {
                $config['cache_lifetime'] = 86400;
            }

            $this->setCacheLifetime($config['cache_lifetime']);
        }
    }

    /**
     * Registers the required smarty filters.
     *
     * @param array $filters The list of filters.
     */
    protected function registerFilters($filters)
    {
        if (empty($filters)) {
            return;
        }

        $ignoreCli = [];

        if (array_key_exists('ignore_cli', $filters)) {
            $ignoreCli = $filters['ignore_cli'];
            unset($filters['ignore_cli']);
        }

        foreach ($filters as $section => $f) {
            if (php_sapi_name() === 'cli') {
                $f = array_diff($f, $ignoreCli);
            }

            foreach ($f as $filter) {
                $this->addFilter($section, $filter);
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

        // Keep this to ignore notice
        $this->error_reporting = E_ALL & ~E_NOTICE;

        if (!empty($this->theme)) {
            $this->image_dir = substr(SITE_URL, 0, -1) . $this->theme->path . 'images/';
        }

        $this->assign([
            'app'       => $this->container->get('core.globals'),
            '_template' => $this,
            'params'    => [ 'IMAGE_DIR' => $this->image_dir ]
        ]);
    }

    /**
     * Changes the value of the locale flag.
     *
     * @param boolean $locale The locale flag value.
     *
     * @return Template The current template.
     */
    protected function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
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

        // Copy default cache configuration if it doesnt exists
        if (!file_exists($path . '/cache.conf')) {
            $cm = $this->container->get('template_cache_config_manager');
            $cm->setConfigDir($path);
            $cm->saveDefault();
        }

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
     * @param Theme $theme The current theme.
     */
    protected function setupCompiles($theme)
    {
        $basePath = $this->container->getParameter('core.paths.cache.common');

        $fs   = new Filesystem();
        $path = $basePath . '/smarty/compile-'
            . str_replace('es.openhost.theme.', '', $theme->uuid);

        if (!file_exists($path)) {
            $fs->mkdir($path, 0775);
        }

        $this->setCompileDir($path);
    }

    /**
     * Configures the smarty plugins path.
     *
     * @param Theme $theme The current theme.
     */
    protected function setupPlugins($theme)
    {
        $path = $this->container->getParameter('core.paths.themes') . '/'
            . str_replace('es.openhost.theme.', '', $theme->uuid) . '/plugins';

        $this->addPluginsDir($path);
        $this->addPluginsDir(SITE_LIBS_PATH . '/smarty-onm-plugins/');

        // Load filters
        foreach ($this->filters as $filterSectionName => $filters) {
            foreach ($filters as $filterName) {
                $this->loadFilter($filterSectionName, $filterName);
            }
        }
    }
}
