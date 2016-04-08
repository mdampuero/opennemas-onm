<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Instance;

use Framework\ORM\Entity\Client;

class Instance
{
    /**
     * The instance id.
     *
     * @var integer
     */
    public $id = null;

    /**
     * The instance internal name.
     *
     * @var string
     */
    public $internal_name = '';

    /**
     * The instance name (human readable).
     *
     * @var string
     */
    public $name = '';

    /**
     * The array of allowed domains to access the instance.
     *
     * @var array
     */
    public $domains = array();

    /**
     * The index of the main domain in the array of domains.
     *
     * @var integer
     */
    public $main_domain = 0;

    /**
     * The date when main domain expires.
     *
     * @var \Datetime
     */
    public $domain_expire = null;

    /**
     * Contact email of user that owns the instance.
     *
     * @var string
     */
    public $contact_mail = '';

    /**
     * The creation date.
     *
     * @var \Datetime
     */
    public $created = null;

    /**
     * The date when the last user logged in.
     *
     * @var \Datetime
     */
    public $last_login = null;

    /**
     * Flag to indicate that the instance is enabled or disabled.
     *
     * @var boolean
     */
    public $activated = 0;

    /**
     * The activated modules of the current instance.
     *
     * @var array
     */
    public $activated_modules = array();

    /**
     * The requested changes in modules of the current instance.
     *
     * @var array
     */
    public $changes_in_modules = array();

    /**
     * The array of settings.
     *
     * @var array
     */
    public $settings = array();

    /**
     * Number of contents of the instance.
     *
     * @var integer
     */
    public $contents = 0;

    /**
     * The number of advertisements.
     *
     * @var integer
     */
    public $advertisements = 0;

    /**
     * The number of albums.
     *
     * @var integer
     */
    public $albums = 0;

    /**
     * The number of articles.
     *
     * @var integer
     */
    public $articles = 0;

    /**
     * The number of advertisements.
     *
     * @var integer
     */
    public $attachments = 0;

    /**
     * The number of articles.
     *
     * @var integer
     */
    public $letters = 0;

    /**
     * The number of opinions.
     *
     * @var integer
     */
    public $opinions = 0;

    /**
     * The number of photos.
     *
     * @var integer
     */
    public $photos = 0;

    /**
     * The number of polls.
     *
     * @var integer
     */
    public $polls = 0;

    /**
     * The number of static_pages.
     *
     * @var integer
     */
    public $static_pages = 0;

    /**
     * The number of videos.
     *
     * @var integer
     */
    public $videos = 0;

    /**
     * The number of widgets.
     *
     * @var integer
     */
    public $widgets = 0;


    /**
     * Size in Mb of the instance.
     *
     * @var float
     */
    public $media_size = 0;

    /**
     * Rank of the instance in Alexa.
     *
     * @var integer
     */
    public $alexa = 100000000;

    /**
     * Number of page views.
     *
     * @var integer
     */
    public $page_views = 0;

    /**
     * Number of backend users.
     *
     * @var integer
     */
    public $users = 0;

    /**
     * Number of sent emails.
     *
     * @var integer
     */
    public $emails = 0;

    /**
     * Support plan type
     *
     * @var string
     */
    public $support_plan = '';

    /**
     * Unserializes the instance metas on wake up.
     */
    public function __wakeup()
    {
        if (property_exists($this, 'metas')) {
            return;
        }

        foreach ($this->metas as $value) {
            $data = @unserialize($value);

            if ($data) {
                $value = $data;
            }
        }
    }

    /**
     * Initializes all the application values for the instance.
     */
    public function boot()
    {
        if (!is_array($this->settings)) {
            $this->settings = unserialize($this->settings);
        }

        if (!array_key_exists('MEDIA_URL', $this->settings)
            || empty($this->settings['MEDIA_URL'])
        ) {
            $this->settings['MEDIA_URL'] = '/media/';
        }

        foreach ($this->settings as $key => $value) {
            define($key, str_replace('es.openhost.theme.', '', $value));
        }

        $this->initInternalConstants();

        if ($this->internal_name !== 'manager') {
            $this->initTheme();
        }
    }

    /**
     * Initializes all the internal application constants.
     */
    public function initInternalConstants()
    {
        define('INSTANCE_UNIQUE_NAME', $this->internal_name);

        $mainDomain = $this->getMainDomain();
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
     * Loads the theme configuration.
     */
    public function initTheme()
    {
        $this->theme = include_once TEMPLATE_USER_PATH . '/init.php';
    }

    /**
     * Returns the instance Client object.
     *
     * @return Client The client.
     */
    public function getClient()
    {
        if (!array_key_exists('client', $this->metas)) {
            return null;
        }

        if (is_array($this->metas['client'])) {
            return $this->metas['client'];
        }

        return $this->metas['client'];
    }

    /**
     * Returns the database name.
     *
     * @return string The database name.
     */
    public function getDatabaseName()
    {
        if (array_key_exists('BD_DATABASE', $this->settings)) {
            return $this->settings['BD_DATABASE'];
        }

        return null;
    }

    /**
     * Returns the instance main domain.
     *
     * @return string The instance main domain.
     */
    public function getMainDomain()
    {
        if ($this->main_domain && $this->main_domain > 0) {
            $domain = $this->domains[$this->main_domain - 1];
        } elseif (is_array($this->domains) && !empty($this->domains)) {
            $domain = $this->domains[0];
        } else {
            $domain = null;
        }

        return $domain;
    }
}
