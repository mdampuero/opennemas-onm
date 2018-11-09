<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Displays the maintenance mode page.
 */
class MaintenanceController extends Controller
{
    /**
     * Displays the maintenance mode page.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function defaultAction(Request $request)
    {
        $locale = $request->getPreferredLanguage();
        $theme  = $this->get('orm.manager')->getRepository('Theme')
            ->findOneBy('uuid = "es.openhost.theme.admin"');

        $this->get('core.locale')->setLocale($locale);

        $this->view = $this->get('core.template.admin');
        $this->view->addActiveTheme($theme);

        $output = $this->renderView('maintenance/index.tpl');

        return new Response($output, 503);
    }
}
