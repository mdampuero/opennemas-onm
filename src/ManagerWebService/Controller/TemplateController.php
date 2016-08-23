<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ManagerWebService\Controller;

use Onm\Framework\Controller\Controller;

class TemplateController extends Controller
{
    /**
     * Renders a template.
     *
     * @return Response The response object.
     */
    public function renderAction($template)
    {
        $template = str_replace(':', '/', $template);
        $template = preg_replace('/\.\d+\./', '.', $template);

        return $this->render($template);
    }
}
