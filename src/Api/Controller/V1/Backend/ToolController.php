<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1\Backend;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Common\Core\Controller\Controller;

/**
 * Lists and displays files for content.
 */
class ToolController extends Controller
{
    /**
     * Returns a list slug list for the strings passed in the request
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function slugAction(Request $request)
    {
        $slug = $request->query->filter('slug', '', FILTER_SANITIZE_STRING);
        $slug = \Onm\StringUtils::generateSlug($slug);

        return new JsonResponse([ 'slug' => $slug ]);
    }
}
