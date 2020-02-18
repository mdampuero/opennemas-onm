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
     * @param Request $request the request object
     *
     * @return JsonResponse The list of advertisements.
     */
    public function getListAction(Request $request)
    {
        $places   = explode(',', $request->query->get('places'));
        $category = $request->query->get('category');

        $advertisements = $this->getItems($places, $category);

        $advertisements = array_map(function ($element) {
            $date = date('Y-m-d H:i:s');

            // Only image and html type ads
            if (empty($element->content_status)
                || (!empty($element->endtime) && $element->endtime < $date)
            ) {
                return null;
            }

            return $this->normalizeAdObject($element);
        }, $advertisements);

        $advertisements = array_filter($advertisements, function ($element) {
            return !is_null($element);
        });

        $headers = [
            'x-cache-for' => '+1 day',
            'x-tags'      => $this->getListTags($places, $advertisements)
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
    public function getItemAction(Request $request, $id)
    {
        $category           = $request->query->get('category', 'home');
        $module             = $request->query->get('module', 'frontpage');
        $contentId          = $request->query->get('contentId', '');
        $advertisementGroup = $request->query->get('advertisementGroup', 'article_inner');

        $ad = $this->getItem($id);

        if (empty($ad) || empty($ad->content_status)) {
            throw new ResourceNotFoundException(
                sprintf('Advertisement with id "%s" doesnt exists', $id),
                404
            );
        }

        $headers = [
            'x-cache-for' => '+1 day',
            'x-tags'      => $this->getItemTags($ad),
        ];

        $contents = $this->get('frontend.renderer.advertisement')
            ->render($ad, [
                'category'           => $category,
                'extension'          => $module,
                'advertisementGroup' => $advertisementGroup,
                'contentId'          => $contentId,
            ]);

        return new Response($contents, 200, $headers);
    }

    /**
     * Returns an advertisement by id.
     *
     * @param integer $id The advertisement id.
     *
     * @return \Advertisement|null The advertisement.
     */
    protected function getItem($id)
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
     * @param string  $category The category name.
     *
     * @return array The list of advertisements.
     */
    protected function getItems($places, $category)
    {
        $id       = 0;
        $excluded = [ 'home', 'opinion', 'blog', 'newsletter' ];

        if (!empty($category) && !in_array($category, $excluded)) {
            try {
                $category = $this->get('api.service.category')
                    ->getItemBySlug($category);

                $id = $category->pk_content_category;
            } catch (\Exception $e) {
                return [];
            }
        }

        return $this->get('advertisement_repository')
            ->findByPositionsAndCategory($places, $id);
    }

    /**
     * Returns the current advertisement format.
     *
     * @param \Advertisement $advertisement The advertisement object.
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

        if ((int) $advertisement->with_script === 4) {
            return 'Smart';
        }

        return 'html';
    }

    /**
     * Returns the list of tags basing on an advertisement.
     *
     * @param \Advertisement $advertisement The advertisement object.
     *
     * @return string The list of tags.
     */
    protected function getItemTags($advertisement)
    {
        $tags = [
            'extension-advertisement',
            'show',
            'content-' . $advertisement->id
        ];

        foreach ($advertisement->positions as $position) {
            $tags[] = 'position-' . $position;
        }

        return implode(',', $tags);
    }

    /**
     * Returns the list of tags basing on positions and advertisements.
     *
     * @param array    $positions      The list of positions.
     * @param array    $advertisements The list of advertisements.
     *
     * @return string The list of tags.
     */
    protected function getListTags($positions, $advertisements)
    {
        $tags = [
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
     * @param \Advertisement $element The advertisement object.
     *
     * @return \StdClass The normalized advertisement.
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

        // Convert starttime to UTC
        $element->starttime = $this->setTimeZoneToUTC($element->starttime);

        // Convert endtime to UTC
        $element->endtime = $this->setTimeZoneToUTC($element->endtime);

        $object = new \stdClass();

        $hasInterstitial = array_filter(
            $element->positions,
            function ($position) {
                return (($position + 50) % 100) === 0;
            }
        );

        $types = [];
        if (!empty($hasInterstitial)) {
            $types[] = 'interstitial';
        }

        $hasNormal = array_filter(
            $element->positions,
            function ($position) {
                return (($position + 50) % 100) !== 0;
            }
        );

        if (!empty($hasNormal)) {
            $types[] = 'normal';
        }

        $object->id          = (int) $element->pk_content;
        $object->type        = implode('+', $types); // Types: normal, interstitial, insterstitial+normal
        $object->position    = $element->positions;
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

        $object->mark = $this->get('frontend.renderer.advertisement')->getMark($element);

        $object->target_url = ($object->format == 'image') ? $element->url : '';

        return $object;
    }

    /**
     * Returns a DateTime object with timezone UTC from a date string or null
     * if the input is not valid to convert.
     *
     * @param string $date The date to convert.
     *
     * @return mixed The datetime converted to UTC or null if the date is empty.
     */
    public function setTimeZoneToUTC($date)
    {
        if (is_null($date) || empty($date)) {
            return null;
        }

        // Convert date to UTC
        try {
            $date = new \DateTime(
                $date,
                $this->container->get('core.locale')->setContext('frontend')->getTimeZone()
            );

            // This is separated because the previous initialization can raise an exception
            $date = $date->setTimeZone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            $date = null;
        }

        return $date;
    }
}
