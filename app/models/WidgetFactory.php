<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Initializes the common properties class for Widgets.
 *
 * @package    Onm
 * @subpackage Model
 * @author     Fran Dieguez <fran@openhost.es>
 **/
class WidgetFactory
{
    public function __construct($useDB = true)
    {

        if ($useDB) {
            $this->cm = new ContentManager();
            $this->ccm = ContentCategoryManager::get_instance();
        }
        $this->tpl = new Template(TEMPLATE_USER);
        $this->tpl->caching = 0;
        $this->tpl->force_compile = true;

        // Assign a random number, usefull for diferenciate instances of
        // the same widget
        $this->tpl->assign('rnd_number', rand(5, 900));
    }
}
