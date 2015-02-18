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

        $_SESSION['sidebar_pinned'] = $pinned;

        return new JsonResponse();
    }

    /**
     * Returns the current sidebar pinned status.
     *
     * @return JsonResponse The response object.
     */
    public function getAction()
    {
        if (array_key_exists('sidebar_pinned', $_SESSION)) {
            return new JsonResponse($_SESSION['sidebar_pinned']);
        }

        return new JsonResponse(0);
    }
}
