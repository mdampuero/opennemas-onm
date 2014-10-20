<?php
/**
 * Handles the common actions for misc purposes
 *
 * @package Backend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;
use Onm\StringUtils;

/**
 * Handles the common actions for misc purposes
 *
 * @package Backend_Controllers
 **/
class UtilsController extends Controller
{
    /**
     * Returns the cleaned and normalized tags for a given string
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function calculateTagsAction(Request $request)
    {
        $tags = $request->query->filter('data', '', FILTER_SANITIZE_STRING);

        $tags = StringUtils::getTags($tags);
        $tags = str_replace(', ', ',', $tags);

        return new Response($tags, 200);
    }
}
