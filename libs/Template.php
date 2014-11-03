<?php
use FilesManager as fm;

/**
 * Template class
 *
 * @package Onm
 * @author  Fran Dieguez <fran@openhost.es>
 **/
class Template extends Smarty
{
    // Private properties
    public $theme               = null;
    public $themeName           = null;
    public $locale_dir	        = null;
    public $css_dir	            = null;
    public $image_dir           = null;
    public $js_dir              = null;
    public $common_asset_dir    = null;
    public $js_includes         = array( 'head' => array() );
    public $css_includes        = array( 'head' => array(), 'footer' => array() );
    public $metatags            = array();
    public $templateBaseDir;
    public $allow_php_tag;
    public $container           = null;
    public $filters             = array();
    public $baseCachePath       = '';

    public $relative_path = null;
    static public $registry = array();

    /**
     * Initializes the Template class
     *
     * @param string $theme the theme to use
     * @param array  $filters the list of filters to load
     *
     * @return void
     **/
    public function __construct($theme, $filters = array())
    {
        // Call the parent constructor
        parent::__construct();
        $this->themeName = $theme;

        $this->setBaseCachePath();

        $this->setBasePaths($theme);

        $this->setPluginLoadPaths();

        $this->registerCustomPlugins();

        $this->setTemplateVars($theme);

        // Load filters
        foreach ($this->filters as $filterSectionName => $filters) {
            foreach ($filters as $filterName) {
                $this->loadFilter($filterSectionName, $filterName);
            }
        }
    }

    /**
     * Sets the template base path
     *
     * @param string $theme the theme to use
     *
     * @return void
     **/
    public function setBasePaths($theme)
    {
        // Parent variables
        $this->templateBaseDir = realpath(SITE_PATH.'/themes/'.$theme);
        $this->setTemplateDir(realpath($this->templateBaseDir.'/tpl/'));

        $instanceManager = getService('instance_manager');
        $baseTheme = '';
        if (property_exists($instanceManager, 'current_instance') && isset($instanceManager->current_instance->theme)) {
            $baseTheme = $instanceManager->current_instance->theme->getParentTheme();

            if (!empty($baseTheme)) {
                $this->addTemplateDir(realpath(SITE_PATH."/themes/{$baseTheme}/tpl"));
            }
        }
        $this->setupCachePath($baseTheme);

        $this->addTemplateDir(realpath(SITE_PATH.'/themes/base/tpl'));
    }

    /**
     * Sets the cache base path
     *
     * @return void
     **/
    public function setBaseCachePath()
    {
        $this->baseCachePath = CACHE_PATH;
    }

    /**
     * Sets the cache environment path and copy cache configurations
     *
     * @return void
     **/
    public function setupCachePath($themeName)
    {
        if (!file_exists($this->baseCachePath.'/smarty')) {
            mkdir($this->baseCachePath.'/smarty', 0775, true);
        }

        $cachePath = $this->baseCachePath.'/smarty/config/';
        $cacheFilePath = $cachePath.'cache.conf';
        $templateConfigPath = $this->templateBaseDir.'/config';

        // If config dir exists copy it to cache directory to make instance aware.
        if (!is_file($cacheFilePath)
            && is_dir($templateConfigPath)
        ) {
            fm::recursiveCopy(
                $templateConfigPath,
                $cachePath
            );
        }

        $this->setConfigDir(realpath($this->baseCachePath.'/smarty/config'));

        $directory = COMMON_CACHE_PATH.'/smarty/compile-'.$this->themeName;
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        $this->compile_dir = realpath($directory).'/';

        $directory = $this->baseCachePath.'/smarty/cache';
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        $this->cache_dir = realpath($directory).'/';
    }

    /**
     * Sets the path where plugins are loaded from
     *
     * @return void
     **/
    public function setPluginLoadPaths()
    {
        $this->addPluginsDir($this->templateBaseDir.'/plugins/');
        $this->addPluginsDir(SITE_LIBS_PATH.'/smarty-onm-plugins/');
    }

    /**
     * Sets some template paths
     *
     * @param string $theme the theme to use
     *
     * @return void
     **/
    public function setTemplateVars($theme)
    {
        $this->error_reporting = E_ALL & ~E_NOTICE;

        $this->theme = $theme;
        $this->assign('THEME', $theme);

        // Template variables
        $baseUrl = SITE_URL.'/themes/'.$theme.'/';
        $this->locale_dir       = $baseUrl.'locale/';
        $this->css_dir          = $baseUrl.'css/';
        $this->image_dir        = $baseUrl.'images/';
        $this->js_dir           = $baseUrl.'js/';
        $this->common_asset_dir = SITE_URL.'assets/';

        $this->caching          = false;
        $this->allow_php_tag    = true;

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


    public function saveConfig($data, $configFile)
    {
        $filename = $this->config_dir . $configFile;
        if (file_exists($filename)) {
            $fp = fopen($filename, 'w');
            foreach ($data as $sectionName => $vars) {
                fwrite($fp, '[' . $sectionName . ']' . "\n");
                foreach ($vars as $k => $v) {
                    fwrite($fp, $k . '=' . $v . "\n");
                }
            }
            fclose($fp);
            clearstatcache();
        }
    }

    public function readConfig($filename)
    {
        $vars = parse_ini_file($this->config_dir . $filename, true);
        return $vars;
    }

    public function readKeyConfig($filename, $key, $iniSection = 'default')
    {
        $vars = parse_ini_file($this->config_dir . $filename, true);
        if (isset($vars[$iniSection][$key])) {
            return $vars[$iniSection][$key];
        } elseif (($iniSection!='default') && (isset($vars['default'][$key]))) {
            return $vars['default'][$key];
        }

        return null;
    }

    /**
     * Try load a section of a config file, otherwise use default section
     * Default section must exists
     *
     * @param string $configFile This value will be concat with $this->config_dir
     * @param string $section Load this section if it's possible
     * @param string $defaultSection If $section don't exists then use $defaultSection
     *
     * @return void
     */
    public function loadConfigOrDefault($configFile, $section, $defaultSection = 'default')
    {
        $configFile = $this->config_dir . $configFile;
        if ($this->existsConfigSection($configFile, $section)) {
            $this->configLoad($configFile, $section);
        } else {
            $this->configLoad($configFile, $defaultSection);
        }
    }

    /**
     * Check if a section exist into a file configuration
     *
     * @param string $configFile Absolute path to configuration dir
     * @param string $section
     *
     * @return boolean
     */
    public function existsConfigSection($configFile, $section)
    {
        $content = file_get_contents($configFile);
        return preg_match('/\[' . $section . '\]/', $content);
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
        $this->addFilter("output", "js_includes");
        $this->addFilter("output", "css_includes");
        $this->addFilter("output", "canonical_url");
        $this->addFilter("output", "generate_fb_admin_tag");
        $this->addFilter("output", "ads_generator");
        $this->addFilter("output", "trimwhitespace");
    }
}
