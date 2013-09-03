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

    public $relative_path = null;
    static public $registry = array();

    public function __construct($theme, $filters = array())
    {
        // Call the parent constructor
        parent::__construct();

        if (!file_exists(CACHE_PATH.DS.'smarty')) {
            mkdir(CACHE_PATH.DS.'smarty', 0775);
        }
        global $sc;
        $baseTheme = '';
        if (isset($sc->getParameter('instance')->theme)) {
            $baseTheme = $sc->getParameter('instance')->theme->getParentTheme();
        }
        // Parent variables
        $this->templateBaseDir = SITE_PATH.DS.'themes'.DS.$theme.DS;
        $this->setTemplateDir(realpath($this->templateBaseDir.'tpl').DS);
        if (!empty($baseTheme)) {
            $this->addTemplateDir(SITE_PATH.DS.'themes'.DS.$baseTheme.DS.'tpl');
        }
        $this->addTemplateDir(SITE_PATH.DS.'themes'.DS.'base'.DS.'tpl');

        $cachePath = CACHE_PATH.DS.'smarty'.DS.'config'.DS;
        $cacheFilePath = $cachePath.'cache.conf';
        $templateConfigPath = $this->templateBaseDir.'config';

        // If config dir exists copy it to cache directory to make instance aware.
        if (!is_file($cacheFilePath)
            && is_dir($templateConfigPath)
        ) {
            fm::recursiveCopy(
                $templateConfigPath,
                $cachePath
            );
        }

        $this->config_dir = realpath(CACHE_PATH.DS.'smarty'.DS.'config').'/';

        // Create cache and compile dirs if not exists to make template instance aware
        foreach (array('cache', 'compile') as $key => $value) {
            $directory = CACHE_PATH.DS.'smarty'.DS.$value;
            if (!is_dir($directory)) {
                mkdir($directory, 0755);
            }
            $this->{$value."_dir"} = realpath($directory).'/';
        }

        $this->error_reporting = E_ALL & ~E_NOTICE;

        $this->theme = $theme;
        $this->assign('THEME', $theme);

        // Add global plugins path
        $this->addPluginsDir(realpath($this->templateBaseDir.'plugins/').'/');
        $this->addPluginsDir(realpath(SMARTY_DIR.DS.'../'.DS.'onm-plugins/'));
        $this->caching          = false;
        $this->allow_php_tag    = true;


        // Template variables
        $baseUrl = SITE_URL.SS.'themes'.SS.$theme.SS;
        $this->locale_dir       = $baseUrl.'locale'.SS;
        $this->css_dir          = $baseUrl.'css'.SS;
        $this->image_dir        = $baseUrl.'images'.SS;
        $this->js_dir           = $baseUrl.'js'.SS;
        $this->common_asset_dir = SITE_URL.SS.'assets'.SS;

        $this->loadFilter("output", "js_includes");
        $this->loadFilter("output", "css_includes");

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

        $this->theme = $theme;
        $this->assign('THEME', $theme);

    }


    public function setFilters($filters = array())
    {
        if (count($filters) > 0) {
            $this->filters = $filters;
            $this->autoload_filters = $filters;
        }
        return $this;
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
        } else {
            //$this->setCaching(0);
        }
    }
}
