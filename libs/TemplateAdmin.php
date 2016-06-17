<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class TemplateAdmin extends Template
{
    /**
     * {@inheritdoc}
     */
    public function registerCustomPlugins()
    {
        $this->addFilter("output", "trimwhitespace");
        $this->addFilter("output", "canonical_url");
        $this->addFilter("output", "css_includes");
        $this->addFilter("output", "js_includes");
        if (php_sapi_name() != 'cli') {
            $this->addFilter("output", "backend_analytics");
        }
    }
}
