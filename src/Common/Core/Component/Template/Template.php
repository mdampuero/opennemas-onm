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
     * The template file to use when rendering.
     *
     * @var string
     */
    protected $file = null;

    /**
     * The list of filters.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * The active instance.
     *
     * @var Instance
     */
    protected $instance;

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

        $this->setTemplateVars();
        $this->setupCompiles($theme);
        $this->setupPlugins($theme);

        $this->addTheme($theme);

        if (!empty($theme->parameters)
            && array_key_exists('layouts', $theme->parameters)
        ) {
            $this->setupLayouts($theme);
        }
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
        $paths = $theme->multirepo
            ? [ 'src/tpl', 'vendor/baseline/src/tpl' ]
            : [ 'tpl' ];

        foreach ($paths as $path) {
            $this->addTemplateDir($theme->realpath . '/' . $path);
        }

        if (!empty($theme->text_domain)) {
            $path = $theme->realpath . '/locale';

            $this->container->get('core.locale')
                ->addTextDomain($theme->text_domain, $path);
        }
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
                $this->arrayFlatten(func_get_args())
            ),
            function ($item) {
                return !empty($item);
            }
        );

        if (!empty($params) && $this->locale) {
            $params[] = $this->container->get('core.locale')->getRequestLocale();
        }

        if ($this->hasValue('token')) {
            $params[] = $this->getValue('token');
        }

        $params[] = $this->container->get('core.globals')->getDevice();

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
     */
    public function getThemeSkinName()
    {
        return $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('theme_skin', 'default');
    }

    /**
     * Returns the theme file name for the variant selected
     *
     * @return string the file name of the variant
     */
    public function getThemeSkinProperty($propertyName)
    {
        return $this->theme->getSkinProperty(
            $this->container->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('theme_skin', 'default'),
            $propertyName
        );
    }

    /**
     * Returns the theme font selected
     *
     * @return string the file name of the variant
     */
    public function getFonts()
    {
        return $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('theme_font', 'default');
    }

    /**
     * Returns the theme secondary font selected
     *
     * @return string the file name of the variant
     */
    public function getSecondaryFonts()
    {
        return $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('theme_font_secondary', 'default');
    }

    /**
     * Returns the theme secondary font selected
     *
     * @return string the file name of the variant
     */
    public function getThemeOptions()
    {
        $options = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('theme_options', []);

        if (empty($options)) {
            $options = $this->container->get('core.theme')->getSkinProperty(
                $this->container->get('orm.manager')
                    ->getDataSet('Settings', 'instance')
                    ->get('theme_skin', 'default'),
                'options'
            );

            $options = array_map(function ($option) {
                $option = $option['default'];
                return $option;
            }, $options);
        }

        return $options;
    }

    /**
     * Returns a value assigned to template.
     *
     * @param string $name The value name.
     *
     * @return mixed The value if it was assigned to template or null if it was
     *               not assigned to template.
     */
    public function getValue($name)
    {
        return $this->getTemplateVars($name) ?? null;
    }

    /**
     * Checks if a value is already assigned to template.
     *
     * @param string $name The value name.
     *
     * @return boolean True if the value is assigned to template. False if the
     *                 value is not assigned or it is empty.
     */
    public function hasValue($name)
    {
        return !empty($this->getValue($name));
    }

    /**
     * Assigns parameters to template and returns the generated HTML.
     *
     * @param string $template The template name.
     * @param array  $params   The list of parameters.
     *
     * @return string The generated HTML.
     */
    public function render($template = null, $params = [])
    {
        $cacheId = null;

        if (array_key_exists('cache_id', $params)) {
            $cacheId = $params['cache_id'];
            unset($params['cache_id']);
        }

        if (!empty($params)) {
            $this->assign($params);
        }

        return $this->fetch($template, $cacheId);
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
     * Changes the template file to use when redering.
     *
     * @param string $file The template file.
     *
     * @return Template The current template service.
     */
    public function setFile($file)
    {
        if (!empty($file)) {
            $this->file = $file;
        }

        return $this;
    }

    /**
     * Changes the value of the locale flag.
     *
     * @param boolean $locale The locale flag value.
     *
     * @return Template The current template.
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Assigns a value to template.
     *
     * @param string $name  The value name.
     * @param string $value The value to assign.
     */
    public function setValue($name, $value)
    {
        $this->assign($name, $value);
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
     * Sets some template paths.
     */
    protected function setTemplateVars()
    {
        // Keep this to ignore notice
        $this->error_reporting = E_ALL & ~E_NOTICE;

        $this->assign([
            'app'       => $this->container->get('core.globals'),
            '_template' => $this
        ]);
    }

    /**
     * Configures the smarty cache path.
     *
     * @param Instance $instance The current instance.
     *
     * @codeCoverageIgnore
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
            $cm = $this->container->get('core.template.cache');
            $cm->setPath($path);
            $cm->write();
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
     *
     * @codeCoverageIgnore
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
     * Configures the path for the layout manager.
     *
     * @param Theme $theme The current theme.
     */
    protected function setupLayouts($theme)
    {
        $path = $theme->multirepo ? 'src/layouts' : 'layouts';

        $this->container->get('core.template.layout')
            ->setPath($theme->realpath . '/' . $path);
    }

    /**
     * Configures the smarty plugins path.
     *
     * @param Theme $theme The current theme.
     */
    protected function setupPlugins($theme)
    {
        $path = $theme->realpath . '/plugins';

        $this->addPluginsDir($path);
        $this->addPluginsDir(SITE_LIBS_PATH . '/smarty-onm-plugins/');

        foreach ($this->filters as $section => $filters) {
            foreach ($filters as $filter) {
                $this->loadFilter($section, $filter);
            }
        }
    }

    /**
     * Flatten a multi-dimensional array to a single-level array,
     * sorting the entries in order to depth first.
     *
     * @param array $carry Array to initialize, will be the subject if the only argument passed.
     * @param mixed $subject Optional Array or value that has data in potential multi-dimensions.
     *
     * @return array
     */
    private function arrayFlatten(?array $carry = [], $subject = null): array
    {
        return array_reduce((array) ($subject ?? $carry), function (array $carry, $item) {
            return is_array($item)
                ? $this->arrayFlatten($carry, $item)
                : array_merge($carry, (array) $item);
        }, $subject !== null ? (array) $carry : []); // Don't merge onto the $subject...
    }
}
