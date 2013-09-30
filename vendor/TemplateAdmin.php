<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/

class TemplateAdmin extends Template
{
    public function __construct($theme, $filters = array())
    {
        // Call the parent constructor
        parent::__construct($theme, $filters);

        $this->loadFilter("output", "trimwhitespace");

        foreach (array('cache', 'compile') as $key => $value) {
            $directory = COMMON_CACHE_PATH.DS.'smarty'.DS.'admin'.DS.$value;

            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            $this->{$value."_dir"} = realpath($directory).'/';
        }

        $this->template_dir = $this->templateBaseDir.'tpl/';
        $this->config_dir   = $this->templateBaseDir.'config/';
        $this->addPluginsDir($this->templateBaseDir.'plugins/');
    }
}
