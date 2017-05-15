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
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
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
        $places   = explode(',', $request->query->get('places'));
        $category = $request->query->get('category');

        $advertisements = $this->getAdvertisements($places, $category);

        $advertisements = array_map(function ($element) {
            $date = date('Y-m-d H:i:s');

            // Only image and html type ads
            if (empty($element->content_status)
                || (!empty($element->endtime) && $element->endtime < $date)
            ) {
                return;
            }

            return $this->normalize($element);
        }, $advertisements);

        $advertisements = array_filter($advertisements, function ($element) {
            return !is_null($element);
        });

        $instance = $this->get('core.instance');
        $headers  = [
            'x-cache-for'  => '1d',
            'x-cacheable'  => true,
            'x-instance'   => $instance->internal_name,
            'x-tags'       => $this->getListTags($places, $advertisements, $instance)
        ];

        return new JsonResponse(array_values($advertisements), 200, $headers);
    }

    /**
     * Returns a HTML/JS advertisement.
     *
     * @param Request $request The request object.
     * @param integer $id      The advertisement id.
     *
     * @return Response The advertisement.
     */
    public function showAction(Request $request, $id)
    {
        $category  = $request->query->get('category', 'home');

        $ad = $this->getAdvertisement($id);

        if (empty($ad) || empty($ad->content_status)) {
            throw new ResourceNotFoundException();
        }

        $instance = $this->get('core.instance');
        $headers  = [
            'x-cache-for'  => '1d',
            'x-cacheable'  => true,
            'x-instance'   => $instance->internal_name,
            'x-tags'       => $this->getItemTags($ad, $instance),
        ];

        if ($ad->with_script == 3) {
            return new Response($this->renderDFP($ad, $category), 200, $headers);
        }

        if ($ad->with_script == 2) {
            return new Response($this->renderOpenX($ad, $category), 200, $headers);
        }

        if ($ad->with_script != 0) {
            return new Response($this->renderHtml($ad), 200, $headers);
        }

        $img = $this->get('entity_repository')->find('Photo', $ad->img);

        if (!empty($img) && strtolower($img->type_img) == 'swf') {
            return new Response($this->renderFlash($ad, $img), 200, $headers);
        }

        return new Response($this->renderImage($ad, $img), 200, $headers);
    }

    /**
     * Returns an advertisement by id.
     *
     * @param integer $id The advertisement id.
     *
     * @return Advertisement The advertisement.
     */
    protected function getAdvertisement($id)
    {
        if (in_array('ADS_MANAGER', $this->get('core.instance')->activated_modules)) {
            return $this->get('entity_repository')->find('Advertisement', $id);
        }

        // TODO: Improve this shit
        $advertisements = include APP_PATH . 'config/ads/onm_default_ads.php';

        foreach ($advertisements as $advertisement) {
            if ($advertisement->id == $id) {
                return $advertisement;
            }
        }

        return null;
    }

    /**
     * Returns the list of advertisements.
     *
     * @param array   $places   The list of places.
     * @param integer $category The category id.
     *
     * @return array The list of advertisements.
     */
    protected function getAdvertisements($places, $category)
    {
        $id = 0;

        if (!empty($category) && $category != 'home') {
            $category = $this->get('category_repository')
                ->findOneBy([ 'name' => [ [ 'value' => $category ] ] ]);

            if (empty($category)) {
                return [];
            }

            $id = $category->pk_content_category;
        }

        return \Advertisement::findForPositionIdsAndCategoryPlain($places, $id);
    }

    /**
     * Returns the custom code for Google DFP.
     *
     * @return string The custom code for Google DFP.
     */
    protected function getCustomCode()
    {
        $code = $this->get('setting_repository')->get('dfp_custom_code');

        if (empty($code)) {
            return '';
        }

        return base64_decode($code);
    }

    /**
     * Returns the current advertisement format.
     *
     * @param Advertisement $advertisement The advertisement object.
     *
     * @return string The current advertisement format.
     */
    protected function getFormat($advertisement)
    {
        if ((int) $advertisement->with_script === 0) {
            return 'image';
        }

        if ((int) $advertisement->with_script === 2) {
            return 'OpenX';
        }

        if ((int) $advertisement->with_script === 3
            || ((int) $advertisement->with_script === 1
                && preg_match('/googletag\.defineSlot/', $advertisement->script))
        ) {
            return 'DFP';
        }

        return 'html';
    }

    /**
     * Returns the list of tags basing on an advertisement.
     *
     * @param Advertisement $advertisement The advertisement object.
     * @param Instance      $instance      The current instance.
     *
     * @return string The list of tags.
     */
    protected function getItemTags($advertisement, $instance)
    {
        $tags = [
            'instance-' . $instance->internal_name,
            'extension-advertisement',
            'show'
        ];

        $tags[] = 'content-' . $advertisement->id;
        $tags[] = 'position-' . $advertisement->type_advertisement;

        return implode(',', $tags);
    }

    /**
     * Returns the list of tags basing on positions and advertisements.
     *
     * @param array    $positions      The list of positions.
     * @param array    $advertisements The list of advertisements.
     * @param Instance $instance       The current instance.
     *
     * @return string The list of tags.
     */
    protected function getListTags($positions, $advertisements, $instance)
    {
        $tags = [
            'instance-' . $instance->internal_name,
            'extension-advertisement',
            'list'
        ];

        foreach ($advertisements as $advertisement) {
            $tags[] = 'content-' . $advertisement->id;
        }

        foreach ($positions as $position) {
            $tags[] = 'position-' . $position;
        }

        return implode(',', $tags);
    }

    /**
     * Returns the list of sizes for Google DFP.
     *
     * @param array $sizes The list of sizes for the current add.
     *
     * @return string The list of sizes for Google DFP.
     */
    protected function getSizes($sizes)
    {
        $sizes = array_map(function ($a) {
            return "[ {$a['width']}, {$a['height']} ]";
        }, $sizes);

        return '[ ' . implode(', ', $sizes) . ' ]';
    }

    /**
     * Returns the targeting-related JS code for google DFP.
     *
     * @param string $category The current category.
     *
     * @return string The targeting-related JS code.
     */
    protected function getTargeting($category)
    {
        $options = $this->get('setting_repository')->get('dfp_options');

        $targetingCode = '';
        if (!is_array($options)
            || !array_key_exists('target', $options)
            || empty($options['target'])
        ) {
            return '';
        }

        return "googletag.pubads().setTargeting('{$options['target']}', ['{$category}']);";
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
        if (!array_key_exists('devices', $element->params)
            || empty($element->params['devices'])
        ) {
            $element->params['devices'] = [
                'phone'   => 1,
                'tablet'  => 1,
                'desktop' => 1,
            ];
        }

        if (!array_key_exists('user_groups', $element->params)
            || empty($element->params['user_groups'])
        ) {
            $element->params['user_groups'] = [];
        }

        $object = new \stdClass();

        $object->id          = (int) $element->pk_content;
        $object->type        = ((($element->type_advertisement + 50) % 100) == 0) ?
            'interstitial' : 'normal'; // Types: normal, interstitial
        $object->position    = array_map('intval', explode(',', $element->type_advertisement));
        $object->publicId    = date('YmdHis', strtotime($element->created)).
            sprintf('%06d', $element->pk_advertisement);
        $object->timeout     = (int) $element->timeout;
        $object->starttime   = $element->starttime;
        $object->endtime     = $element->endtime;
        $object->format      = $this->getFormat($element);
        $object->devices     = $element->params['devices'];
        $object->user_groups = $element->params['user_groups'];
        $object->sizes       = $this->normalizeSizes($element->params);

        $object->orientation = array_key_exists('orientation', $element->params) ?
            $element->params['orientation'] : 'top';

        $object->target_url = ($object->format == 'image') ? $element->url: '';

        return $object;
    }

    /**
     * Checks all parameters (old version) and returns the list of sizes.
     *
     * @param array $params The item parameters.
     *
     * @return array The list of sizes.
     */
    protected function normalizeSizes($params)
    {
        // New system, sizes with devices
        if (array_key_exists('sizes', $params)) {
            return $params['sizes'];
        }

        if (!array_key_exists('height', $params)
            || !array_key_exists('width', $params)) {
            return [];
        }

        $sizes  = [];
        $totalW = is_array($params['width']) ? count($params['width']) : 1;
        $totalH = is_array($params['height']) ? count($params['height']) : 1;
        $total  = max($totalH, $totalW);

        // Convert non-array values to array
        if (!is_array($params['height'])) {
            $params['height'] = array_fill(0, $total, $params['height']);
        }

        // Convert non-array values to array
        if (!is_array($params['width'])) {
            $params['width'] = array_fill(0, $total, $params['width']);
        }

        for ($i = 0; $i < $total; $i++) {
            $size = [
                'height' => $params['height'][$i],
                'width' =>  $params['width'][$i]
            ];

            if ($i < 3) {
                $size['device'] = $i === 0 ? 'desktop' :
                    ($i === 1 ? 'tablet' : 'phone');
            }

            $sizes[] = $size;
        }

        return $sizes;
    }

    /**
     * Returns the HTML code for a Google DFP advertisement.
     *
     * @param Advertisement $ad       The advertisement object.
     * @param string        $category The current category.
     *
     * @return string The HTML code for the Google DFP advertisement.
     */
    protected function renderDFP($ad, $category)
    {
        $params = [
            'id'        => $ad->id,
            'dfpId'     => $ad->params['googledfp_unit_id'],
            'sizes'     => $this->getSizes($this->normalizeSizes($ad->params)),
            'targeting' => $this->getTargeting($category),
            'custom'    => $this->getCustomCode()
        ];

        return $this->get('core.template.admin')
            ->fetch('advertisement/helpers/safeframe/dfp.tpl', $params);
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
        $publicId = date('YmdHis', strtotime($ad->created)) .
            sprintf('%06d', $ad->pk_advertisement);

        $params = [
            'width'  => $img->width,
            'height' => $img->height,
            'src'    => SITE_URL . 'media/' . INSTANCE_UNIQUE_NAME . '/images'
                . $img->path_file . $img->name,
            'url'    => $this->get('router')->generate('frontend_ad_redirect', [
                'id' => $publicId
            ])
        ];

        return $this->get('core.template.admin')
            ->fetch('advertisement/helpers/safeframe/flash.tpl', $params);
    }

    /**
     * Returns the HTML code for a HTML/JS advertisement.
     *
     * @param Advertisement $ad The advertisement object.
     *
     * @return string The HTML code for the HTML/JS advertisement.
     */
    protected function renderHtml($ad)
    {
        $tpl   = '<html><style>%s</style><body><div class="content">%s</div></body>';
        $html  = $ad->script;
        $style = 'body { margin: 0; overflow: hidden; padding: 0; text-align:'
            . ' center; } img { max-width: 100% }';

        return sprintf($tpl, $style, $html);
    }

    /**
     * Returns the HTML code for an image-based advertisement.
     *
     * @param Advertisement $ad  The advertisement object.
     * @param Photo         $img The image object.
     *
     * @return string The HTML code for the image-based advertisement.
     */
    protected function renderImage($ad, $img)
    {
        $publicId = date('YmdHis', strtotime($ad->created)) .
            sprintf('%06d', $ad->pk_advertisement);

        $params = [
            'category' => $img->category_name,
            'width'    => $img->width,
            'height'   => $img->height,
            'src'      => SITE_URL . 'media/' . INSTANCE_UNIQUE_NAME
                . '/images' . $img->path_file . $img->name,
            'url'      => $this->get('router')
                ->generate('frontend_ad_redirect', [
                    'id' => $publicId
                ]),
        ];

        return $this->get('core.template.admin')
            ->fetch('advertisement/helpers/safeframe/image.tpl', $params);
    }

    /**
     * Returns the HTML code for a OpenX advertisement.
     *
     * @param Advertisement $ad       The advertisement object.
     * @param string        $category The current category.
     *
     * @return string The HTML code for the OpenX advertisement.
     */
    protected function renderOpenX($ad, $category)
    {
        $params = [
            'id'       => $ad->id,
            'category' => $category,
            'openXId'  => $ad->params['openx_zone_id'],
            'url'      => $this->get('setting_repository')
                ->get('revive_ad_server')['url']
        ];

        return $this->get('core.template.admin')
            ->fetch('advertisement/helpers/safeframe/openx.tpl', $params);
    }
}
