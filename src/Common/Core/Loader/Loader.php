<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Loader;

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
        $this->loadTheme();
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

        return $this->loadInstanceFromOql(sprintf($oql, $internalName));
    }

    /**
     * Loads an instance basing on an QOL query.
     *
     * @param string $oql The OQL query.
     */
    public function loadInstanceFromOql($oql)
    {
        $this->instance = $this->container->get('orm.manager')
            ->getRepository('Instance')
            ->findOneBy($oql);

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
                return $this->instance;
            }
        }

        $oql = 'domains ^ "^%s|,\s*%s\s*,|\s*%s$"';

        $this->loadInstanceFromOql(sprintf($oql, $host, $host, $host));

        if ($this->container->has('cache.manager')) {
            $this->container->get('cache.manager')->getConnection('manager')
                ->set($host, $this->instance);
        }

        return $this->instance;
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
     * Loads the theme for the instance.
     */
    protected function loadTheme()
    {
        if ($this->instance->internal_name === 'manager') {
            return;
        }

        $path = SITE_PATH . DS . 'themes' . DS . TEMPLATE_USER . DS . 'init.php';

        $this->theme = include_once($path);
    }
}
