<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ManagerWebService\Controller;

use Onm\Framework\Controller\Controller;

class TemplateController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     */
    public function init()
    {
        $this->view = new \TemplateManager(TEMPLATE_MANAGER);
    }

    /**
     * Renders a template.
     *
     * @return Response The response object.
     */
    public function renderAction($template)
    {
        $template = str_replace(':', '/', $template);
        return $this->render($template);
    }
}
