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
        	if (!in_array($element->with_script, [0, 1]) && $element->content_status != 0) {
        		return;
        	}

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

            $object = $this->buildObject($element);

            return $object;
        }, $advertisements);

        $advertisements = array_filter($advertisements, function($element) {
            return !is_null($element);
        });

        return new JsonResponse($advertisements);
    }


    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function buildObject($element)
    {
        $object = new \stdClass();
        // $object->content_status = (int) $element->content_status;
        $object->id           = (int) $element->pk_content;
        $object->type         = ((($element->type_advertisement + 50) % 100) == 0) ? 'interstitial' : 'normal'; // Types: normal, interstitial
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
            'width'  => $element->params['width'],
            'height' => $element->params['height'],
        ];
        $object->target_url   = ($object->format == 'image') ? $element->url: '';


        if (is_array($element->params) && array_key_exists('width', $element->params) && !is_null($element->params['width'])
            && array_key_exists('height', $element->params) && !is_null($element->params['height'])
        ) {
            if (is_array($element->params['width'])
                && !empty($element->params['width'])
                && is_array($element->params['height'])
                && !empty($element->params['height'])
            ) {
                $width = $element->params['width'][0];
                $height = $element->params['height'][0];
            } else {
                $width = $element->params['width'];
                $height = $element->params['height'];
            }
        }

        if ($element->with_script == 0) {
            $photo = getService('entity_repository')->find('Photo', $element->img);
            $url = SITE_URL.'ads/'. date('YmdHis', strtotime($element->created))
                  .sprintf('%06d', $element->pk_advertisement).'.html';
            $mediaUrl = SITE_URL.'media/'.INSTANCE_UNIQUE_NAME.'/images'.$photo->path_file. $photo->name;
            if (isset($element->default_ad) && $element->default_ad == 1) {
                $url = $element->url;
            }
            // If the Ad is Flash/Image based try to get the width and height fixed
            if (isset($photo)) {
                if (($photo->width <= $width)
                    && ($photo->height <= $height)
                ) {
                    $width  = $photo->width;
                    $height = $photo->height;
                }
            }

            // TODO: controlar los banners swf especiales con div por encima
            if (strtolower($photo->type_img) == 'swf') {
                if (!$overlap && !$element->overlap) {
                    // Generate flash object with wmode window
                    $flashObject =
                        '<object width="'.$width.'" height="'.$height.'" >
                            <param name="wmode" value="window" />
                            <param name="movie" value="'.$mediaUrl. '" />
                            <param name="width" value="'.$width.'" />
                            <param name="height" value="'.$height.'" />
                            <embed src="'. $mediaUrl. '" width="'.$width.'" height="'.$height.'" '
                                .'SCALE="exactfit" wmode="window"></embed>
                        </object>';

                    $content =
                        '<a target="_blank" href="'.$url.'" rel="nofollow" '
                        .'style="display:block;cursor:pointer">'.$flashObject.'</a>';
                } else {
                    // Generate flash object with wmode transparent
                    $flashObject =
                        '<object width="'.$width.'" height="'.$height.'" >
                            <param name="wmode" value="transparent" />
                            <param name="movie" value="'.$mediaUrl. '" />
                            <param name="width" value="'.$width.'" />
                            <param name="height" value="'.$height.'" />
                            <embed src="'. $mediaUrl. '" width="'.$width.'" height="'.$height.'" '
                                .'SCALE="exactfit" wmode="transparent"></embed>
                        </object>';

                    // CHECK: dropped checking of IE
                    $content = '<div style="position: relative; width: '.$width.'px; height: '.$height.'px;">
                        <div style="left:0px;top:0px;cursor:pointer;background-color:#FFF;'
                            .'filter:alpha(opacity=0);opacity:0;position:absolute;z-index:100;width:'.
                            $width.'px;height:'.$height.'px;"
                            onclick="javascript:window.open(\''.$url.'\', \'_blank\');return false;">
                            </div>'.$flashObject.'</div>';
                }

                $content = '<div style="width:'.$width.'px; height:'.$height.'px; margin: 0 auto;">'.$content.'</div>';
            } else {
                // Image
                $imageObject = '<img alt="'.$photo->category_name.'" src="'. $mediaUrl.'" '
                                .'width="'.$width.'" height="'.$height.'" />';

                $content = '<a target="_blank" href="'.$url.'" rel="nofollow">'.$imageObject.'</a>';
            }

            $object->html = $content;
        }

        $object->html = preg_replace('@async@', '', $object->html);

        return $object;
    }
}
