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

use Common\Cache\Core\Cache;
use Repository\EntityManager;

/**
* Perform searches in Database related with one content
*/
class ContentHelper
{
    /**
     * Initializes the ContentHelper.
     *
     * @param EntityManager  $em    The entity manager.
     * @param Cache          $cache The cache service.
     */
    public function __construct(EntityManager $em, Cache $cache)
    {
        $this->cache = $cache;
        $this->em    = $em;
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
        $epp     = (int) $epp < 1 ? 5 : (int) $epp + 1;
        $cacheId = 'suggested_contents_' . md5(implode(',', func_get_args()));
        $items   = $this->cache->get($cacheId);

        if (!empty($items) && !empty($items[0])) {
            return $this->ignoreCurrent($contentId, $items);
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
            $criteria['pk_fk_content_category'] = [ [ 'value' => $categoryId ] ];
        }

        try {
            $contents = $this->em->findBy($criteria, [ 'starttime' => 'desc' ], $epp, 1);

            foreach ($contents as &$content) {
                if (empty($content->img1)) {
                    continue;
                }

                $photo = $this->em->find('Photo', $content->img1);

                if (!empty($photo)) {
                    $photos[$content->img1] = $photo;
                }
            }

            $items = [ $contents, $photos ];

            $this->cache->set($cacheId, $items, 900);
        } catch (\Exception $e) {
            return [];
        }

        return $this->ignoreCurrent($contentId, $items);
    }

    /**
     * Removes the current content and its photo from the list of suggested
     * contents.
     *
     * @param int   $contentId The current content id.
     * @param array $items     The list of suggested contents and photos for a
     *                         category.
     *
     * @return array The list of suggested contents and their photos without
     *               the current content.
     */
    protected function ignoreCurrent($contentId, $items)
    {
        $current = array_filter($items[0], function ($a) use ($contentId) {
            return $a->pk_content == $contentId;
        });

        $items[0] = array_filter($items[0], function ($a) use ($contentId) {
            return $a->pk_content != $contentId;
        });

        if (!empty($current)) {
            if (!empty($current[0]->img1)) {
                unset($items[1][$current[0]->img1]);
            }
        }

        return $items;
    }
}
