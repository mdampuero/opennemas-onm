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
    /**
     * Initializes the Template class
     *
     * @param string $theme the theme to use
     * @param array  $filters the list of filters to load
     *
     * @return void
     **/
    public function __construct($theme = 'manager', $filters = array())
    {
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
}
