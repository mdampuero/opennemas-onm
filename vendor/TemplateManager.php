<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
class TemplateManager extends Template
{

    public function __construct($theme, $filters = array())
    {
        // Call the parent constructor
        parent::__construct($theme, $filters);

        // Parent variables
        $this->templateBaseDir = SITE_PATH.DS.'themes'.DS.'manager'.DS;

        foreach (array('cache', 'compile') as $key => $value) {
            $directory = COMMON_CACHE_PATH.DS.'smarty'.DS.'manager'.DS.$value;

            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            $this->{$value."_dir"} = realpath($directory).'/';
        }

        $this->template_dir = $this->templateBaseDir.'tpl/';

        $this->config_dir    = $this->templateBaseDir.'config/';
        $this->addPluginsDir($this->templateBaseDir.'plugins/');
        $this->caching       = false;

        // Template variables
        $baseUrl = SITE_URL.'themes'.SS.'admin'.SS;

        $this->locale_dir = $baseUrl.'locale/';
        $this->css_dir    = $baseUrl.'css/';
        $this->image_dir  = $baseUrl.'images/';
        $this->js_dir     = $baseUrl.'js/';

        $this->assign(
            'params',
            array(
                'LOCALE_DIR'       =>    $this->locale_dir,
                'CSS_DIR'          =>    $this->css_dir,
                'IMAGE_DIR'        =>    $this->image_dir,
                'JS_DIR'           =>    $this->js_dir,
                'COMMON_ASSET_DIR' => $this->common_asset_dir,
            )
        );

        $this->theme = $theme;
        $this->assign('THEME', $theme);
    }
}
