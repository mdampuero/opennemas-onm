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

            return $this->normalizeAdObject($element);
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
        $module    = $request->query->get('module', 'frontpage');
        $contentId = $request->query->get('contentId', '');

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

        $contents = $this->get('core.renderer.advertisement')
            ->renderSafeFrame($ad, [
                'category'  => $category,
                'extension' => $module,
                'contentId' => $contentId,
            ]);

        return new Response($contents, 200, $headers);
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
        if ($this->get('core.security')->hasExtension('ADS_MANAGER')) {
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
        $id       = 0;
        $excluded = [ 'home', 'opinion', 'blog', 'newsletter' ];

        if (!empty($category) && !in_array($category, $excluded)) {
            $category = $this->get('category_repository')
                ->findOneBy([ 'name' => [ [ 'value' => $category ] ] ]);

            if (empty($category)) {
                return [];
            }

            $id = $category->pk_content_category;
        }

        return $this->get('advertisement_repository')
            ->findByPositionsAndCategory($places, $id);
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
     * Returns a normalized advertisement.
     *
     * @param Advertisement $element The advertisement object.
     *
     * @return StdClass The normalized advertisement.
     */
    protected function normalizeAdObject($element)
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

        // Set ads datetime to UTC (avoid browsers issues with Date)
        $element->starttime = \DateTime::createFromFormat(
            'Y-m-d H:i:s',
            $element->starttime,
            $this->container->get('core.locale')->setContext('frontend')->getTimeZone()
        )->setTimeZone(new \DateTimeZone('UTC'))->format('Y-m-d h:i:s');

        if (!is_null($element->endtime)) {
            $element->endtime = \DateTime::createFromFormat(
                'Y-m-d H:i:s',
                $element->endtime,
                $this->container->get('core.locale')->setContext('frontend')->getTimeZone()
            )->setTimeZone(new \DateTimeZone('UTC'))->format('Y-m-d h:i:s');
        }

        $object = new \stdClass();

        $object->id          = (int) $element->pk_content;
        $object->type        = ((($element->type_advertisement + 50) % 100) == 0) ?
            'interstitial' : 'normal'; // Types: normal, interstitial
        $object->position    = array_map('intval', explode(',', $element->type_advertisement));
        $object->publicId    = date('YmdHis', strtotime($element->created)) .
            sprintf('%06d', $element->pk_advertisement);
        $object->timeout     = (int) $element->timeout;
        $object->starttime   = $element->starttime;
        $object->endtime     = $element->endtime;
        $object->format      = $this->getFormat($element);
        $object->devices     = $element->params['devices'];
        $object->user_groups = $element->params['user_groups'];
        $object->sizes       = $element->normalizeSizes();

        $object->orientation = array_key_exists('orientation', $element->params) ?
            $element->params['orientation'] : 'top';

        $object->target_url = ($object->format == 'image') ? $element->url : '';

        return $object;
    }
}
