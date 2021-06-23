<?php

namespace ManagerWebService\Controller;

use Common\Core\Annotation\Security;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Displays, saves, modifies and removes users.
 */
class SitemapController extends Controller
{

    /**
     * @api {get} /sitemap
     *
     * @apiSuccess {Array} results The list of sitemap settings
     *
     */
    public function showAction()
    {
        $st = getService('orm.manager')
            ->getDataSet('Settings', 'manager')->get('sitemap');

        if (empty($st)) {
            $response = new Response();
            $response->setContent(json_encode([]));

            return $response;
        }

        foreach ($st as $key => $value) {
            $st[$key] = (int) $value;
        }

        $response = new Response();
        $response->setContent(json_encode($st));

        return $response;
    }

    /**
     * Save sitemap settings
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     */
    public function saveAction(Request $request)
    {
        $settings['sitemap'] = $request->request->all();

        $msg = $this->get('core.messenger');

        if ($settings['sitemap']['perpage'] < 1 || $settings['sitemap']['perpage'] > 50000) {
            $msg->add(_('Items per page must be between 1 and 50000'), 'error');

            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        if ($settings['sitemap']['total'] < 100 || $settings['sitemap']['total'] > 1000) {
            $msg->add(_('Items total must be between 500 and 1000'), 'error');

            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        $this->get('orm.manager')
            ->getDataSet('Settings', 'manager')
            ->set($settings);

        $msg->add(_('Sitemap saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }
}
