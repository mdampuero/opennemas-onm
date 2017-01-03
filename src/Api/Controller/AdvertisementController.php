<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Api\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 **/
class AdvertisementController extends Controller
{
    /**
     *
     * Common code for all the actions
     *
     * @return JsonResponse the list of ads
     */
    public function endpointAction(Request $request)
    {
        $places = explode(',', $request->query->get('places'));
        $category = (int) $request->query->get('category');

        $advertisements = \Advertisement::findForPositionIdsAndCategoryPlain($places, $category);

        $advertisements = array_map(function($element) {
        	// Only image and html type ads
        	if (!in_array($element->with_script, [1, 2]) && $element->content_status != 0) {
        		return;
        	}

            $object = new \stdClass();

            if (!array_key_exists('restriction_devices', $element->params)
                || !empty($element->params['restriction_devices'])
            ) {
                $element->params['restriction_devices'] = [
                    'phone'   => 1,
                    'tablet'  => 1,
                    'desktop' => 1,
                ];
            }
            if (!array_key_exists('restriction_usergroups', $element->params)
                || !empty($element->params['restriction_usergroups'])
            ) {
                $element->params['restriction_usergroups'] = [];
            }

            // $object->content_status = (int) $element->content_status;
            $object->id           = (int) $element->pk_content;
            $object->type 		  = ((($element->type_advertisement + 50) % 100) == 0) ? 'interstitial' : 'normal'; // Types: normal, interstitial
            $object->position     = $element->type_advertisement;
            $object->show_during  = (int) $element->timeout;
            $object->format       = ($element->with_script == 1) ? 'html' : 'image';
            $object->html         = $element->script;
            $object->restrictions = [
                'devices'    => $element->params['restriction_devices'],
                'user_group' => $element->params['restriction_usergroups'],
                'time'       => [
                    'from'  => $element->starttime,
                    'until' => $element->endtime,
            	],
            ];
            $object->size = [
            	'width' => $element->params['width'],
            	'height' => $element->params['height'],
            ];
            $object->target_url   = ($object->format == 'image') ? $element->url: '';

            return $object;
        }, $advertisements);

        $advertisements = array_filter($advertisements, function($element) {
            return !is_null($element);
        });

        return new JsonResponse($advertisements);
    }
}
