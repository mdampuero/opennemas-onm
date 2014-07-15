<?php
/**
 * Defines the Instance class
 *
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  Onm
 */
namespace Onm\Instance;

/**
 * Handles the instance operations
 *
 * @package Onm
 **/
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
    public $alexa = 0;

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
     * Array of deltas for counters.
     *
     * @var array
     */
    public $deltas = array(
        'contents'       => 0,
        'articles'       => 0,
        'opinions'       => 0,
        'advertisements' => 0,
        'attachments'    => 0,
        'albums'         => 0,
        'photos'         => 0,
        'videos'         => 0,
        'widgets'        => 0,
        'static_pages'   => 0,
        'letters'        => 0,
        'media_size'     => 0,
        'alexa'          => 0,
        'page_views'     => 0,
        'users'          => 0,
        'emails'         => 0
    );

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
            define($key, $value);
        }

        $this->initInternalConstants();

        if ($this->internal_name !== 'onm_manager') {
            $this->initTheme();
        }
    }

    /**
     * Initializes all the internal application constants.
     */
    public function initInternalConstants()
    {
        define('INSTANCE_UNIQUE_NAME', $this->internal_name);

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
     * Returns the database name.
     *
     * @return string The database name.
     */
    public function getDatabaseName()
    {
        return $this->settings['BD_DATABASE'];
    }
}
