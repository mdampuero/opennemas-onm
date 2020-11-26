<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Common\Core\Component\Helper;

use Opennemas\Cache\Core\Cache;
use Repository\EntityManager;
use Api\Service\Service;

/**
* Perform searches in Database related with one content
*/
class ContentHelper
{
    /**
     * Initializes the ContentHelper.
     *
     * @param EntityManager  $em      The entity manager.
     * @param Service        $service The API service for content.
     * @param Cache          $cache   The cache service.
     */
    public function __construct(EntityManager $em, Service $service, Cache $cache)
    {
        $this->cache   = $cache;
        $this->service = $service;
        $this->em      = $em;
    }

    /**
     * Get the proper cache expire date for scheduled contents.
     *
     * @return mixed The expire cache datetime in "Y-m-d H:i:s" format or null.
     */
    public function getCacheExpireDate()
    {
        $oqlStart = sprintf(
            'content_status = 1 and in_litter != 1 and'
            . ' (starttime !is null and starttime > "%s")'
            . ' order by starttime asc limit 1',
            date('Y-m-d H:i:s')
        );

        $oqlEnd = sprintf(
            'content_status = 1 and in_litter != 1 and'
            . ' (endtime !is null and endtime > "%s")'
            . ' order by endtime desc limit 1',
            date('Y-m-d H:i:s')
        );

        try {
            $start = $this->service->getItemBy($oqlStart);
        } catch (\Exception $e) {
            $start = null;
        }

        try {
            $end = $this->service->getItemBy($oqlEnd);
        } catch (\Exception $e) {
            $end = null;
        }

        if (empty($start) && empty($end)) {
            return null;
        }

        // Get valid date formated or null
        $starttime = !empty($start) && $start->starttime
            ? $start->starttime->format('Y-m-d H:i:s') : null;
        $endtime   = !empty($end) && $end->endtime
            ? $end->endtime->format('Y-m-d H:i:s') : null;

        return min(array_filter([ $starttime, $endtime ]));
    }

    /**
     * Returns a list of contents related with a content type and category.
     *
     * @param string $contentTypeName  Content types required.
     * @param string $filter           Advanced SQL filter for contents.
     * @param int    $numberOfElements Number of results.
     *
     * @return array Array with the content properties of each content.
     */
    public function getSuggested($contentId, $contentTypeName, $categoryId = null, $epp = 4)
    {
        $epp     = (int) $epp < 1 ? 4 : (int) $epp;
        $cacheId = 'suggested_contents_'
            . md5(implode(',', [ $contentTypeName, $categoryId, $epp ]));

        $items = $this->cache->get($cacheId);

        if (!empty($items)) {
            return $this->ignoreCurrent($contentId, $items, $epp);
        }

        $criteria = [
            'content_status'    => [ [ 'value' => 1 ] ],
            'in_litter'         => [ [ 'value' => 0 ] ],
            'content_type_name' => [ [ 'value' => $contentTypeName ] ],
            'starttime'         => [
                'union' => 'OR',
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
            ],
            'endtime'           => [
                'union' => 'OR',
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => date('Y-m-d H:i:s'), 'operator' => '>' ],
            ]
        ];

        if (!empty($categoryId)) {
            $criteria['category_id'] = [ [ 'value' => $categoryId ] ];
        }

        try {
            $items = $this->em->findBy($criteria, [
                'starttime' => 'desc'
            ], $epp + 1, 1);

            $this->cache->set($cacheId, $items, 900);
        } catch (\Exception $e) {
            return [];
        }

        return $this->ignoreCurrent($contentId, $items, $epp);
    }

    /**
     * Returns true if the content is suggested
     *
     * @return boolean true if the content is suggested
     */
    public function isSuggested($item)
    {
        return ($item->frontpage == 1);
    }


    /**
     * Removes the current content from the list of suggested
     * contents.
     *
     * @param int   $contentId The current content id.
     * @param array $items     The list of suggested contents for a category.
     * @param int   $epp       The maximum number of items to return.
     *
     * @return array The list of suggested contents.
     */
    protected function ignoreCurrent($contentId, $items, $epp)
    {
        $current = array_filter($items, function ($a) use ($contentId) {
            return $a->pk_content == $contentId;
        });

        $items = array_slice(array_filter($items, function ($a) use ($contentId) {
            return $a->pk_content != $contentId;
        }), 0, $epp);

        return $items;
    }
}
