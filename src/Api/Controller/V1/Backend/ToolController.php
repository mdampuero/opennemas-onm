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
 * The ToolController provides common actions to parse and transform values
 * and return them to the client.
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

    /**
     * Returns the information translated to the locale.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function translateStringAction(Request $request)
    {
        $from       = $request->get('from');
        $to         = $request->get('to');
        $translator = $request->get('translator');
        $data       = $request->get('data');

        if (empty($from) || empty($to) || is_null($translator) || empty($data)) {
            return new JsonResponse('Invalid request', 400);
        }

        $translator = (int) $translator;

        $settings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('translators');

        if (is_array($settings) && array_key_exists($translator, $settings)) {
            $translator = $settings[(int) $translator];
        }

        if (!is_array($translator)) {
            return new JsonResponse('No translators', 404);
        }

        $translator = $this->get('core.factory.translator')->get(
            $translator['translator'],
            $translator['from'],
            $translator['to'],
            $translator['config']
        );

        $data = array_map(function ($a) use ($translator) {
            $value = $translator->translate(htmlentities($a));

            return mb_convert_encoding(html_entity_decode($value), 'UTF-8');
        }, $data);

        return new JsonResponse($data);
    }
}
