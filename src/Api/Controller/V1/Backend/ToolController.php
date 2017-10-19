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

    /**
     * Returns the information translated to the locale.
     *
     * @param Request $request The resquest object.
     *
     * @return JsonResponse The response object.
     */
    public function translateAction(Request $request)
    {
        $from       = $request->get('from');
        $to         = $request->get('to');
        $translator = $request->get('translator');
        $data       = $request->get('data');

        if (empty($from) || empty($to) || empty($translator) || empty($data)) {
            return new JsonResponse('Invalid request', 400);
        }

        $settings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('translators');

        $translator = array_filter(
            $settings,
            function ($a) use ($from, $to, $translator) {
                return $a['from'] === $from
                    && $a['to'] === $to
                    && $a['translator'] === $translator;
            }
        );

        if (empty($translator)) {
            return new JsonResponse('No translators', 404);
        }

        $translator = $this->get('core.factory.translator')->get(
            $translator[0]['translator'],
            $translator[0]['from'],
            $translator[0]['to'],
            $translator[0]['config']
        );

        $data = array_map(function ($a) use ($translator) {
            $value = $translator->translate(htmlentities($a));

            return mb_convert_encoding(html_entity_decode($value), 'UTF-8');
        }, $data);

        return new JsonResponse($data);
    }
}
