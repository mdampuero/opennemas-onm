<?php
/**
 * Template class
 *
 * @author Tomás Vilariño <vifito@openhost.es>
 */
class Template extends Smarty
{
    // Private properties
    var $theme          = null;
    var $locale_dir	= null;
    var $css_dir	= null;
    var $image_dir      = null;
    var $js_dir         = null;
    var $js_includes    = array( 'head' => array() );
    var $css_includes   = array( 'head' => array() );
    var $metatags       = array();
    var $filters        = array( 'pre'    => array(),
                          'post'   => array(),
                          'output' => array(), );

    var $relative_path = null;
    static $registry = array();

    function __construct($theme, $filters=array())
    {
        // Call the parent constructor
        parent::__construct();

        // Set filters: $filters = array('pre' => array(), 'post' => array(), 'output' => array())
        $this->setFilters($filters);

        // Register db resource
        /*$this->register_resource("db",
                array("smarty_get_db_template", "smarty_get_db_timestamp",
                          "smarty_get_db_secure", "smarty_get_db_trusted"));*/

        // Parent variables
        $this->template_dir = SITE_PATH.DS.'themes'.DS.$theme.DS.'tpl/';
        $this->compile_dir  = SITE_PATH.DS.'themes'.DS.$theme.DS.'compile/';
        $this->config_dir   = SITE_PATH.DS.'themes'.DS.$theme.DS.'config/';
        $this->cache_dir    = SITE_PATH.DS.'themes'.DS.$theme.DS.'cache/';
        $this->plugins_dir[]= SITE_PATH.DS.'themes'.DS.$theme.DS.'plugins/';
        $this->caching      = false;
        $this->allow_php_tag = true;

        // Template variables
        $this->locale_dir   = SITE_URL.SS.'themes'.SS.$theme.SS.'locale/';
        $this->css_dir      = SITE_URL.SS.'themes'.SS.$theme.SS.'css/';
        $this->image_dir    = SITE_URL.SS.'themes'.SS.$theme.SS.'images/';
        $this->js_dir       = SITE_URL.SS.'themes'.SS.$theme.SS.'js/';

        $this->assign(  'params',
                        array('LOCALE_DIR' =>    $this->locale_dir,
                            'CSS_DIR'	 =>    $this->css_dir,
                            'IMAGE_DIR'  =>    $this->image_dir,
                            'JS_DIR'	 =>    $this->js_dir )
        );

        $this->theme = $theme;
        $this->assign('THEME', $theme);

    }

    function setFilters( $filters=array() )
    {
        $this->filters = $filters;
        $this->autoload_filters = $filters;
    }


    // TODO: documentation
    function addScript($js_path, $section='head')
    {
        $this->_addResource($js_path, $this->js_includes, $section);
    }

    function removeScript($js_path, $section='head')
    {
        $this->_removeResource($js_path, $this->js_includes, $section);
    }

    public function addStyle($css_path, $section='head')
    {
        $this->_addResource( $css_path, $this->css_includes, $section );
    }

    public function removeStyle($css_path, $section='head')
    {
        $this->_removeResource($css_path, $this->css_includes, $section);
    }

    /**
     * Add a resource to resources array (js_includes or css_includes)
     * @param Mixed $res,
     * @param Array $resources
     * @param String $section
    */
    private function _addResource($res, &$resources, $section)
    {
        $res = (is_array($res))? $res: array($res);

        if ( !isset($resources[$section]) ) {
            $resources[$section] = array();
        }

        foreach($res as $item) {
            if ( !in_array($item, $resources[$section] ) ) {
                $resources[$section][] = $item;
            }

            if ( in_array('@-'.$item, $resources[$section] ) ) {
                $resources[$section] = array_diff($resources[$section], array('@-'.$item) );
            }
        }
    }

    private function _removeResource($res, &$resources, $section)
    {
        $res = (is_array($res))? $res: array($res);

        if ( !isset($resources[$section]) ) {
            $resources[$section] = array();
        }

        foreach($res as $item) {
            if ( !in_array('@-'.$item, $resources[$section]) ) {
                $resources[$section][] = '@-'.$item;
            }

            if ( in_array($item, $resources[$section]) ) {
                $resources[$section] = array_diff($resources[$section], array($item) );
            }
        }
    }

    public function generateCacheId($seccion, $subseccion=null, $resource=null)
    {
        $cacheId = '';

        if (!empty($subseccion)) {
            $cacheId = ($subseccion.'|'.$resource);
        } elseif (!empty($seccion)) {
            $cacheId = ($seccion.'|'.$resource);
        } else {
            $cacheId = ('home|'.$resource);
        }
        return $cacheId;
    }

    function saveConfig($data, $configFile)
    {
        $filename = $this->config_dir . $configFile;
        if ( file_exists($filename) ) {
            $fp = fopen($filename, 'w');
            foreach($data as $sectionName => $vars) {
                fwrite($fp, '[' . $sectionName . ']' . "\n");
                foreach($vars as $k => $v) {
                    fwrite($fp, $k . '=' . $v . "\n");
                }
            }
            fclose($fp);
            clearstatcache();
        }
    }

    function readConfig($filename)
    {
        $vars = parse_ini_file($this->config_dir . $filename, true);
        return $vars;
    }

    function readKeyConfig($filename, $key, $iniSection='default')
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
    public function loadConfigOrDefault($configFile, $section, $defaultSection='default')
    {
        $configFile = $this->config_dir . $configFile;
        if ( $this->existsConfigSection($configFile, $section) ) {
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
        $this->configLoad('cache.conf', $section);
        $config = $this->getConfigVars();

        $this->caching = $config['caching'];
        $this->cache_lifetime = $config['cache_lifetime'];
    }

    public function setMeta($name, $content=null)
    {
        if (is_array($name) && is_null($content)) {

            $this->metatags = array_merge($this->metatags, $name);
        } else {

            $this->metatags[$name] = $content;
        }
    }

    public function getMeta($name=null)
    {
        if (is_null($name)) {
            return $this->metatags;
        }

        return (isset($this->metatags[$name]))? $this->metatags[$name]: null;
    }

    public function isHttpEquiv($name)
    {
        $valuesHttpEquiv = array('pragma', 'refresh', 'expires', 'content-type',
                                 'content-language', 'cache-control');
        $name = strtolower($name);

        return in_array($name, $valuesHttpEquiv);
    }

    public function get_template_vars($varname=null)
    {
        return $this->getTemplateVars($varname, $_ptr = null, $search_parents=true);
    }
}

class TemplateAdmin extends Template {

    function __construct($theme, $filters = array())
    {
        // Call the parent constructor
        parent::__construct($theme);

        $this->setFilters($filters);

        // Parent variables
        $baseDir = SITE_PATH.DS.ADMIN_DIR.DS.'themes'.DS.$theme.DS;
        $this->template_dir	= $baseDir.'tpl/';
        $this->compile_dir	= $baseDir.'compile/';
        $this->config_dir	= $baseDir.'config/';
        $this->cache_dir	= $baseDir.'cache/';
        $this->plugins_dir[]= $baseDir.'plugins/';
        $this->caching	= false;
        $this->allow_php_tag = true;

        // Template variables
        $this->locale_dir	= SITE_URL_ADMIN.SS.'themes'.SS.$theme.SS.'locale/';
        $this->css_dir	= SITE_URL_ADMIN.SS.'themes'.SS.$theme.SS.'css/';
        $this->image_dir	= SITE_URL_ADMIN.SS.'themes'.SS.$theme.SS.'images/';
        $this->js_dir	= SITE_URL_ADMIN.SS.'themes'.SS.$theme.SS.'js/';

        $this->assign('params',
                array(
                    'LOCALE_DIR' =>    $this->locale_dir,
                    'CSS_DIR'	 =>    $this->css_dir,
                    'IMAGE_DIR'  =>    $this->image_dir,
                    'JS_DIR'	 =>    $this->js_dir )
        );

        $this->theme = $theme;
        $this->assign('THEME', $theme);

    }

    function setUpLocale()
    {
        /* GetText configuration **********************************************/
        // I18N support information here
        $language = (isset($_REQUEST['lang']))? $_REQUEST['lang']: 'en';
        putenv("LANG=$language");
        setlocale(LC_ALL, $language);

        // Set the text domain as 'messages'
        $domain = 'messages';
        bindtextdomain($domain, $this->locale_dir);
        textdomain($domain);
        /**********************************************************************/
    }
}
