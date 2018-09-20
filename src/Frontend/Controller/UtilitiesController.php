<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

class UtilitiesController extends Controller
{
    /**
     * Integrates the sharrre jQuery plugin into ONM.
     *
     * @return Response The response object.
     */
    public function sharrreAction(Request $request)
    {
        $url  = $request->query->filter('url', '', FILTER_SANITIZE_STRING);
        $type = urlencode($request->query->filter('type', '', FILTER_SANITIZE_STRING));

        $json = [
            'count' => 0,
            'time'  => time(),
            'url'   => $url
        ];

        $content = str_replace('\\/', '/', json_encode($json));

        return new Response($content, 200, [
            'x-tags'       => 'sharre,' . $type . ',' . $url,
            'x-cache-for'  => '300s',
            'Content-Type' => 'application/json',
        ]);
    }
}
