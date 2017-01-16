<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1\Frontend;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Common\Core\Controller\Controller;

/**
 * Lists and displays advertisements.
 */
class AdvertisementController extends Controller
{
    /**
     * List advertisements for requested positions.
     *
     * @return JsonResponse The list of advertisements.
     */
    public function listAction(Request $request)
    {
        $places         = explode(',', $request->query->get('places'));
        $category       = (int) $request->query->get('category');
        $advertisements = \Advertisement::findForPositionIdsAndCategoryPlain($places, $category);

        $advertisements = array_map(function ($element) {
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

            return $this->normalize($element);
        }, $advertisements);

        $advertisements = array_filter($advertisements, function ($element) {
            return !is_null($element);
        });

        return new JsonResponse($advertisements);
    }

    /**
     * Returns a HTML/JS advertisement.
     *
     * @param integer $id The advertisement id.
     *
     * @return Response The advertisement.
     */
    public function showAction($id)
    {
        $em = $this->get('entity_repository');
        $ad = $em->find('Advertisement', $id);

        if (!in_array($ad->with_script, [ 0, 1 ])) {
            throw new \Exception();
        }

        if ($ad->with_script != 0) {
            return new Response($ad->script);
        }

        $img = $em->find('Photo', $ad->img);

        if (strtolower($img->type_img) == 'swf') {
            return new Response($this->renderFlash($ad, $img));
        }

        return new Response($this->renderImage($ad, $img));
    }

    /**
     * Returns the HTML code for a flash-based advertisement.
     *
     * @param Advertisement $ad  The advertisement object.
     * @param Photo         $img The flash object.
     *
     * @return string The HTML code for a flash-based advertisement.
     */
    protected function renderFlash($ad, $img)
    {
        $tr = [
            '[width]'  => $img->width,
            '[height]' => $img->height,
            '[url]'    => SITE_URL . 'media/' . INSTANCE_UNIQUE_NAME . '/images'
                . $img->path_file . $img->name,
        ];

        $html = '<div style="width:[width]px; height:[height]px; margin: 0 auto;">
            <div style="position: relative; width: [width]px; height: [height]px;">
                <div style="left:0px;top:0px;cursor:pointer;background-color:#FFF;
                    filter:alpha(opacity=0);opacity:0;position:absolute;z-index:100;width:
                    [width]px;height:[height]px;"
                    onclick="javascript:window.open(\'[url]\', \'_blank\');return false;">
                </div>
                <object width="[width]" height="[height]" >
                    <param name="wmode" value="transparent" />
                    <param name="movie" value="[url]" />
                    <param name="width" value="[width]" />
                    <param name="height" value="[height]" />
                    <embed src="[url]" width="[width]" height="[url]"
                    SCALE="exactfit" wmode="transparent"></embed>
                </object>
            </div>
        </div>';

        return strtr($html, $tr);
    }

    /**
     * Returns the HTML code for an image-based advertisement.
     *
     * @param Advertisement $ad  The advertisement object.
     * @param Photo         $img The image object.
     *
     * @return string The HTML code for an image-based advertisement.
     */
    protected function renderImage($ad, $img)
    {
        $tr = [
            '[category]' => $img->category_name,
            '[width]'    => $img->width,
            '[height]'   => $img->height,
            '[url]'      => SITE_URL . 'media/' . INSTANCE_UNIQUE_NAME
                . '/images' . $img->path_file . $img->name,
        ];

        $html = '<a target="_blank" href="[url]" rel="nofollow">'
            . '<img alt="[category]" src="[url]" width="[width]" height="[height]" />'
            .'</a>';

        return strtr($html, $tr);
    }

    /**
     * Returns a normalized advertisement.
     *
     * @param Advertisement $element The advertisement object.
     *
     * @return StdClass The normalized advertisement.
     */
    protected function normalize($element)
    {
        $object = new \stdClass();
        // $object->content_status = (int) $element->content_status;
        $object->id           = (int) $element->pk_content;
        $object->type         = ((($element->type_advertisement + 50) % 100) == 0) ?
            'interstitial' : 'normal'; // Types: normal, interstitial
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

        return $object;
    }
}
