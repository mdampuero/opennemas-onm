<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace BackendWebService\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

class WidgetsController extends ContentController
{
    /**
     * Returns a list of contents in JSON format.
     *
     * @param  Request      $request     The request with the search parameters.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response in JSON format.
     */
    public function listAction(Request $request, $contentType = null)
    {
        $oql = $request->query->get('oql', '');
        $em  = $this->get('widget_repository');

        list($criteria, $order, $epp, $page) =
            $this->get('core.helper.oql')->getFiltersFromOql($oql);

        $results = $em->findBy($criteria, $order, $epp, $page);
        $results = \Onm\StringUtils::convertToUtf8($results);
        $total   = $em->countBy($criteria);

        return new JsonResponse([ 'results' => $results, 'total' => $total ]);
    }

    /**
     * Returns the parameters form for widgets of the given uuid.
     *
     * @param string $uuid The widget uuid.
     *
     * @return Response The response object.
     */
    public function getFormAction($uuid)
    {
        $this->get('widget_repository')->loadWidget($uuid);

        $uuid = 'Widget' . $uuid;
        if (!class_exists($uuid)) {
            return new Response('', 400);
        }

        $widget = new $uuid(null);

        if (empty($widget->getForm())) {
            return new Response('', 400);
        }

        return new Response($widget->getForm());
    }
}
