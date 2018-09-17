<?php
/**
 * Handles the actions for advertisements
 *
 * @package Frontend_Controllers
 */
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for advertisements
 *
 * @package Frontend_Controllers
 */
class UtilitiesController extends Controller
{
    /**
     * Integrates the sharrre jQuery plugin into ONM
     *
     * @return JsonResponse the response object
     */
    public function sharrreAction(Request $request)
    {
        $content = [
            'url'   => $request->query->filter('url', '', FILTER_SANITIZE_STRING),
            'count' => 0,
            'time'  => time(),
        ];

        return new JsonResponse(
            $content,
            200,
            [
                'x-tags'       => 'sharre,' . $type . ',' . $url,
                'x-cache-for'  => '300s',
                'Content-Type' => 'application/json',
            ]
        );
    }
}
