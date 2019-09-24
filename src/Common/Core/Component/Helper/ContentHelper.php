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

use Onm\Cache\CacheInterface;
use Repository\EntityManager;

/**
* Perform searches in Database related with one content
*/
class ContentHelper
{
    /**
     * Initializes the ContentHelper.
     *
     * @param Connection     $databaseConnection The database connection.
     * @param EntityManager  $entityManager      The entity manager.
     * @param CacheInterface $cacheHandler       The cache service.
     */
    public function __construct(
        $databaseConnection,
        EntityManager $entityManager,
        CacheInterface $cacheHandler
    ) {
        $this->cache  = $cacheHandler;
        $this->dbConn = $databaseConnection;
        $this->er     = $entityManager;
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
    public function getSuggested($contentTypeName, $filter = '', $numberOfElements = 4)
    {
        $cacheKey = '_suggested_contents_' . md5(implode(',', func_get_args()));
        $result   = $this->cache->fetch($cacheKey);

        if (!is_array($result)) {
            $filter = (empty($filter) ? "" : " AND " . $filter);

            $numberOfElements = (int) $numberOfElements;
            if ($numberOfElements < 1) {
                $numberOfElements = 4;
            }

            $sql = "SELECT content_type_name, pk_content FROM contents INNER JOIN"
                    . " contents_categories ON pk_content = pk_fk_content"
                    . " WHERE `contents`.`content_status` = 1 AND `contents`.`in_litter` = 0 "
                    . $filter
                    . " ORDER BY created DESC LIMIT " . $numberOfElements;

            try {
                $rs           = $this->dbConn->fetchAll($sql);
                $contentProps = [];
                foreach ($rs as $content) {
                    $contentProps [] = [$content['content_type_name'], $content['pk_content']];
                }

                if (count($contentProps) < 1) {
                    return [];
                }

                $contents = $this->er->findMulti($contentProps);
            } catch (Exception $e) {
                return [];
            }

            $contents = $this->er->findMulti($contentProps);

            $cm       = new \ContentManager();
            $contents = $cm->getInTime($contents);
            $photos   = [];
            $er       = getService('entity_repository');

            foreach ($contents as &$content) {
                if ($content->img1 != 0) {
                    $photos[ $content->img1 ] = $er->find('Photo', $content->img1);
                }
            }

            $result    = [];
            $result[0] = $contents;
            $result[1] = $photos;

            $this->cache->save($cacheKey, $result, 300);
        }

        return $result;
    }
}
