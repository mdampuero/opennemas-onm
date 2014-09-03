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
    /**
     * Initializes the Template class
     *
     * @param string $theme the theme to use
     * @param array  $filters the list of filters to load
     *
     * @return void
     **/
    public function __construct($theme = 'admin', $filters = array())
    {
        $this->addFilter("output", "trimwhitespace");

        parent::__construct($theme, $filters = array());
    }

    /**
     * Sets the cache base path
     *
     * @return void
     **/
    public function setBaseCachePath()
    {
        $this->baseCachePath = COMMON_CACHE_PATH;
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
    }
}
