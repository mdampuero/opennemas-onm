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

use Common\ORM\Entity\Instance;

/**
 * Loads the opennemas core.
 */
class Loader
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
     * Configures the core basing on the instance.
     *
     * @param Instance $instance The instance.
     */
    public function configureInstance($instance)
    {
        // Change database for `instance` database connection
        if ($this->container->has('orm.manager')) {
            $this->container->get('orm.manager')->getConnection('instance')
                ->selectDatabase($instance->getDatabaseName());
        }

        // Change namespace for `instance` cache connection
        if ($this->container->has('cache.manager')) {
            $this->container->get('cache.connection.instance')
                ->setNamespace($instance->internal_name);
        }
    }

    /**
     * Configures the core basing on the theme.
     *
     * @param Extension $theme The theme.
     */
    public function configureTheme($theme)
    {
        $template = $this->container->get('core.template');
        $parents  = $this->getParents($theme);

        $template->addActiveTheme($this->theme);

        foreach ($parents as $uuid) {
            $t = $this->container->get('orm.manager')
                ->getRepository('theme', 'file')
                ->findOneBy(sprintf('uuid = "%s"', $uuid));

            if (!empty($t)) {
                $template->addTheme($t);
            }
        }

        if (empty($theme->parameters)) {
            return;
        }

        foreach ($theme->parameters as $key => $values) {
            if (method_exists($this, 'load' . $key)) {
                $this->{'load' . $key}($values);
            }
        }
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
     * Returns the theme for the current instance.
     *
     * @return Theme The theme for the  current instance.
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Initializes all the application values for the instance.
     */
    public function init()
    {
        if (empty($this->instance)) {
            return;
        }

        if (!array_key_exists('MEDIA_URL', $this->instance->settings)
            || empty($this->instance->settings['MEDIA_URL'])
        ) {
            $this->instance->settings['MEDIA_URL'] = '/media/';
        }

        $this->initInternalConstants();

        if ($this->instance->internal_name !== 'manager') {
            $this->loadThemeFromUuid($this->instance->settings['TEMPLATE_USER']);
        }
    }

    /**
     * Returns an instance by internal name.
     *
     * @param string $internalName The instance internal name.
     */
    public function loadInstanceFromInternalName($internalName)
    {
        if ($internalName === 'manager') {
            $this->loadManagerInstance();
            return;
        }

        $oql = 'internal_name = "%s"';

        $this->instance = $this->loadInstanceFromOql(sprintf($oql, $internalName));

        $this->configureInstance($this->instance);

        return $this->instance;
    }

    /**
     * Loads an instance basing on the current host and the requested URI.
     *
     * @param string $host The current host.
     * @param string $uri  The requested URI.
     *
     * @return Instance The instance.
     */
    public function loadInstanceFromUri($host, $uri)
    {
        if (preg_match("@^\/(manager|_profiler|_wdt|framework)@", $uri)) {
            $this->loadManagerInstance();
            return $this->instance;
        }

        if ($this->container->has('cache.manager')) {
            $this->instance = $this->container->get('cache.manager')
                ->getConnection('manager')->get($host);

            if (!empty($this->instance)) {
                $this->configureInstance($this->instance);

                return $this->instance;
            }
        }

        $oql = 'domains regexp "^%s($|,)|,\s*%s\s*,|(^|,)\s*%s$"';

        $this->loadInstanceFromOql(sprintf($oql, $host, $host, $host));

        if ($this->container->has('cache.manager')) {
            $this->container->get('cache.manager')->getConnection('manager')
                ->set($host, $this->instance);
        }

        $this->configureInstance($this->instance);

        return $this->instance;
    }

    /**
     * Loads a theme basing on a theme UUID.
     *
     * @param string $uuid The theme UUID.
     */
    public function loadThemeFromUuid($uuid)
    {
        // TODO: Remove when using UUID format in production
        $uuid = 'es.openhost.theme.'
            . str_replace('es.openhost.theme.', '', $uuid);

        $oql = sprintf('uuid = "%s"', $uuid);

        return $this->loadThemeFromOql($oql);
    }

    /**
     * Returns the list of parents of the current theme.
     *
     * @param Extension $theme The theme.
     *
     * @return array The list of parents.
     */
    protected function getParents($theme)
    {
        $uuids   = [];
        $parents = [];

        if (empty($theme)
            || empty($theme->parameters)
            || !array_key_exists('parent', $theme->parameters)
        ) {
            return $parents;
        }

        foreach ($theme->parameters['parent'] as $parent) {
            $uuids[]   = $parent;
            $parents[] = $parent;
        }

        foreach ($parents as $uuid) {
            $parent = $this->container->get('orm.manager')
                ->getRepository('theme', 'file')
                ->findOneBy(sprintf('uuid = "%s"', $uuid));

            $uuids = array_merge($uuids, $this->getParents($parent));
        }

        return array_unique($uuids);
    }

    /**
     * Initializes all the internal application constants.
     */
    protected function initInternalConstants()
    {
        foreach ($this->instance->settings as $key => $value) {
            define($key, str_replace('es.openhost.theme.', '', $value));
        }

        define('INSTANCE_UNIQUE_NAME', $this->instance->internal_name);

        $mainDomain = $this->instance->getMainDomain();
        if (!is_null($mainDomain)) {
            define('INSTANCE_MAIN_DOMAIN', 'http://'.$mainDomain);
        }
        define('CACHE_PREFIX', INSTANCE_UNIQUE_NAME);

        $cachepath = APPLICATION_PATH . DS . 'tmp' . DS . 'instances' . DS . INSTANCE_UNIQUE_NAME;
        if (!file_exists($cachepath)) {
            mkdir($cachepath, 0755, true);
        }
        define('CACHE_PATH', realpath($cachepath));

        /**
         * Media paths and urls configurations
         **/
        //TODO: All the MEDIA_* should be ported to use this constant
        define('INSTANCE_MEDIA', MEDIA_URL.INSTANCE_UNIQUE_NAME.DS);
        define('INSTANCE_MEDIA_PATH', SITE_PATH.DS."media".DS.INSTANCE_UNIQUE_NAME.DS);

        // External server or a local dir
        define('MEDIA_DIR', INSTANCE_UNIQUE_NAME);
        // Full path to the instance media files
        define('MEDIA_DIR_URL', MEDIA_URL.MEDIA_DIR.'/');

        // local path to write media (/path/to/media)
        define('MEDIA_PATH', SITE_PATH."media".DS.INSTANCE_UNIQUE_NAME);

        define('MEDIA_IMG_PATH_URL', MEDIA_URL.MEDIA_DIR.'/'.IMG_DIR);
        define('MEDIA_IMG_ABSOLUTE_URL', SITE_URL."media".'/'.MEDIA_DIR.'/'.IMG_DIR);
        // TODO: A Eliminar
        // TODO: delete from application
        define('MEDIA_IMG_PATH', MEDIA_PATH.DS.IMG_DIR);
        // TODO: delete from application
        define('MEDIA_IMG_PATH_WEB', MEDIA_URL.MEDIA_DIR.'/'.IMG_DIR);

        define('KIOSKO_DIR', 'kiosko' . DS);

        // Template settings
        define('TEMPLATE_USER_PATH', SITE_PATH.DS."themes".DS.TEMPLATE_USER.DS);
        define('TEMPLATE_USER_URL', "/themes".'/'.TEMPLATE_USER.'/');
    }

    /**
     * Loads an instance basing on an QOL query.
     *
     * @param string $oql The OQL query.
     */
    protected function loadInstanceFromOql($oql)
    {
        $this->instance = $this->container->get('orm.manager')
            ->getRepository('Instance')
            ->findOneBy($oql);

        return $this->instance;
    }

    /**
     * Adds advertisement positions defined by theme to the advertisement
     * manager.
     *
     * @param array $positions The list of positions.
     */
    protected function loadAdvertisements($positions)
    {
        $this->container->get('core.manager.advertisement')
            ->addPositions($positions);
    }

    /**
     * Adds layouts defined by theme to the layout manager.
     *
     * @param array $positions The list of positions.
     */
    protected function loadLayouts($layouts)
    {
        $this->container->get('core.manager.layout')->addLayouts($layouts);
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

    /**
     * Loads the manager instance.
     */
    protected function loadManagerInstance()
    {
        $this->instance = new Instance([
            'activated'     => true,
            'internal_name' => 'manager',
            'settings'      => [
                'BD_DATABASE'   => 'onm-instances',
                'TEMPLATE_USER' => 'manager'
            ]
        ]);
    }

    /**
     * Loads a theme basing on an QOL query.
     *
     * @param string $oql The OQL query.
     */
    protected function loadThemeFromOql($oql)
    {
        $this->theme = $this->container->get('orm.manager')
            ->getRepository('theme', 'file')
            ->findOneBy($oql);

        $this->configureTheme($this->theme);

        return $this->theme;
    }
}
