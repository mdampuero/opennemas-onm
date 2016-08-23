<?php

namespace Backend\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MenuSidebarController extends Controller
{
    /**
     * Updates the sidebar pinned status.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function setAction(Request $request)
    {
        $pinned = false;

        if ($request->get('pinned') == 'true') {
            $pinned = true;
        }

        $request->getSession()->set('sidebar_pinned', $pinned);

        return new JsonResponse();
    }

    /**
     * Returns the current sidebar pinned status.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function getAction(Request $request)
    {
        return !empty($request->getSession()->get('sidebar_pinned'));
    }
}
