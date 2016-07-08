<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class TemplateManager extends Template
{
    /**
     * Registers the required smarty plugins.
     */
    public function registerCustomPlugins()
    {
        $this->addFilter("output", "js_includes");
        $this->addFilter("output", "css_includes");
        $this->addFilter("output", "canonical_url");
    }
}
