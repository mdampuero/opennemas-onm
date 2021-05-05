<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Loader;

use Common\Core\Component\Exception\Instance\InstanceNotActivatedException;
use Common\Core\Component\Exception\Instance\InstanceNotFoundException;
use Common\Model\Entity\Instance;
use Common\Model\Entity\Theme;

/**
 * Loads the opennemas core.
 */
class CoreLoader
{
    /**
     * The current instance.
     *
     * @var Instance
     */
    protected $instance;

    /**
     * The theme for the current instance.
     *
     * @var Theme
     */
    protected $theme;

    /**
     * Initializes the Loader.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;

        // Force cache initialization
        if ($this->container->has('cache.manager')) {
            $this->container->get('cache.manager');
        }

        // Force database initialization
        if ($this->container->has('orm.manager')) {
            $this->container->get('orm.manager');
        }
    }

    /**
     * Configures the services basing on the instance.
     *
     * @param Instance $instance The instance.
     *
     * @return CoreLoader The current CoreLoader.
     */
    public function configureInstance(Instance $instance) : CoreLoader
    {
        $database  = $instance->getDatabaseName();
        $namespace = $instance->internal_name;

        $this->container->get('core.globals')->setInstance($instance);

        // Change database for `instance` database connection
        $this->container->get('orm.manager')->getConnection('instance')
            ->selectDatabase($database);

        // Change namespace for `instance` cache connection
        $this->container->get('cache.connection.instance')
            ->setNamespace($namespace);

        // TODO: Remove when everyone use new cache.manager service
        $this->container->get('cache')->setNamespace($namespace);

        // TODO: Remove when using new ORM for all models
        $this->container->get('dbal_connection')
            ->selectDatabase($database);

        // TODO: Remove when no MEDIA_URL constant usage
        if (!array_key_exists('MEDIA_URL', $instance->settings)
            || empty($instance->settings['MEDIA_URL'])
        ) {
            $instance->settings['MEDIA_URL'] = '/media/';
        }

        $this->container->get('orm.manager')->getDataSet('Settings', 'instance')->init();

        return $this;
    }

    /**
     * Configures the locale basing on the instance information.
     *
     * @param Instance The instance.
     *
     * @return CoreLoader The current CoreLoader.
     */
    public function configureLocale(Instance $instance) : CoreLoader
    {
        $name = $instance->internal_name === 'manager' ? 'manager' : 'instance';

        $config = $this->container->get('orm.manager')
            ->getDataSet('Settings', $name)
            ->get('locale');

        $this->container->get('core.locale')->configure($config);

        return $this;
    }

    /**
     * Configures the core basing on the theme and the list of parent themes.
     *
     * @param Extension $theme   The theme.
     * @param array     $parents The list of parents.
     *
     * @return CoreLoader The current CoreLoader.
     */
    public function configureTheme(Theme $theme, array $parents = []) : CoreLoader
    {
        $template = $this->container->get('core.template');

        $wl       = $this->container->get('core.loader.widget');

        $this->container->get('core.globals')->setTheme($theme);

        $template->addActiveTheme($theme);

        foreach ($parents as $parent) {
            $template->addTheme($parent);
        }

        // Load advertisements, layouts and menus for parents and current theme
        $themes = array_merge(array_reverse($parents), [ $theme ]);

        foreach ($themes as $t) {
            $wl->addTheme($t);

            if (empty($t->parameters)) {
                continue;
            }

            foreach ($t->parameters as $key => $values) {
                if (method_exists($this, 'load' . $key)) {
                    $this->{'load' . $key}($values, $t->uuid);
                }
            }
        }

        return $this;
    }

    /**
     * Reset de Template Dir

     * @return CoreLoader The current CoreLoader.
     */
    public function resetTemplateDir() : CoreLoader
    {
        $template = $this->container->get('core.template');
        $template->setTemplateDir([]);

        return $this;
    }

    /**
     * Initializes all the application values for the instance.
     *
     * @param Instance $instance The instance to init core with.
     *
     * @return CoreLoader The current CoreLoader.
     *
     * @codeCoverageIgnore
     */
    public function init(?Instance $instance = null) : CoreLoader
    {
        $instance = $instance ?? $this->instance;

        if (empty($instance)) {
            return $this;
        }

        foreach ($instance->settings as $key => $value) {
            define($key, str_replace('es.openhost.theme.', '', $value));
        }

        define('INSTANCE_UNIQUE_NAME', $instance->internal_name);
        define('CACHE_PREFIX', INSTANCE_UNIQUE_NAME);

        $cachepath = APPLICATION_PATH . DS . 'tmp' . DS . 'instances' . DS . INSTANCE_UNIQUE_NAME;
        if (!file_exists($cachepath)) {
            mkdir($cachepath, 0755, true);
        }

        define('CACHE_PATH', realpath($cachepath));

        /**
         * Media paths and urls configurations
         */
        //TODO: All the MEDIA_* should be ported to use this constant
        define('INSTANCE_MEDIA', MEDIA_URL . INSTANCE_UNIQUE_NAME . DS);

        // External server or a local dir
        define('MEDIA_DIR', INSTANCE_UNIQUE_NAME);

        if (file_exists($this->theme->realpath . '/.deploy.themes.php')) {
            include_once $this->theme->realpath . '/.deploy.themes.php';
        }

        if (!defined('THEMES_DEPLOYED_AT')) {
            define('THEMES_DEPLOYED_AT', '00000000000000');
        }

        return $this;
    }

    /**
     * Force CoreLoader to only load enabled instances.
     *
     * @return CoreLoader The current CoreLoader.
     *
     * @throws InstanceNotActivatedException When the loaded instance is not
     *                                       activated.
     */
    public function onlyEnabled() : CoreLoader
    {
        if (empty($this->instance)) {
            throw new InstanceNotFoundException();
        }

        if (!$this->instance->activated) {
            throw new InstanceNotActivatedException($this->instance->internal_name);
        }

        return $this;
    }

    /**
     * Loads the application core basing on the request hostname and URI, if
     * the URI is not empty, or basing on the instance name, if the URI is
     * empty.
     *
     * @param string $host The request hostname or the instance name.
     * @param string $uri  The request URI.
     *
     * @return CoreLoader The current CoreLoader.
     *
     * @throws InstanceNotFoundException When the instance can not be found.
     */
    public function load(string $host, ?string $uri = null) : CoreLoader
    {
        try {
            $this->instance = empty($uri)
                ? $this->getInstanceByName($host)
                : $this->getInstanceByDomain($host, $uri);
        } catch (\Exception $e) {
            throw new InstanceNotFoundException();
        }

        $this->container->get('core.loader.theme')
            ->loadThemeByUuid($this->instance->settings['TEMPLATE_USER'])
            ->loadThemeParents();

        $this->theme   = $this->container->get('core.loader.theme')->getTheme();
        $this->parents = $this->container->get('core.loader.theme')->getThemeParents();

        return $this->configureInstance($this->instance)
            ->configureTheme($this->theme, $this->parents)
            ->configureLocale($this->instance);
    }

    /**
     * Returns an instance basing on the request's host and URI.
     *
     * @param string $host The requested host.
     * @param string $uri  The requested URI.
     *
     * @return Instance The found instance.
     */
    protected function getInstanceByDomain(string $host, string $uri) : Instance
    {
        return $this->container->get('core.loader.instance')
            ->loadInstanceByDomain($host, $uri)
            ->getInstance();
    }

    /**
     * Returns an instance basing on the request's host and URI.
     *
     * @param string $name The instance name.
     *
     * @return Instance The found instance.
     */
    protected function getInstanceByName(string $name) : Instance
    {
        return $this->container->get('core.loader.instance')
            ->loadInstanceByName($name)
            ->getInstance();
    }

    /**
     * Adds advertisement positions defined by theme to the advertisement
     * manager.
     *
     * @param array  $positions The list of positions.
     * @param string $themeName The theme name.
     */
    protected function loadAdvertisements($positions, $themeName)
    {
        $this->container->get('core.helper.advertisement')
            ->addPositions($positions, $themeName);
    }

    /**
     * Adds layouts defined by theme to the layout manager.
     *
     * @param array  $positions The list of positions.
     * @param string $themeName The theme name.
     */
    protected function loadLayouts($layouts)
    {
        $this->container->get('core.template.layout')
            ->addLayouts($layouts);
    }

    /**
     * Adds menu positions defined by theme to the menu manager.
     *
     * @param array $menus The list of menu positions.
     */
    protected function loadMenus($menus)
    {
        $this->container->get('core.manager.menu')->addMenus($menus);
    }
}
