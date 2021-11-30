<?php

namespace Frontend\Controller;

use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Common\Model\Entity\Url;

/**
 * Redirects unofficial URLs to real contents.
 */
class RedirectorController extends Controller
{
    /**
     * Handles the redirections for all the contents.
     *
     * @param Request $request The request object
     *
     * @return Response The response object
     */
    public function contentAction(Request $request)
    {
        return $this->get('core.redirector')->getResponseContent($request);
    }
}
