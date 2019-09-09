<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Onm;

use Onm\Cache\CacheInterface;
use Repository\EntityManager;

/**
* Perform searches in Database related with one content
*/
class MachineSearcher
{
    /**
     * Initializes the MachineSearcher.
     *
     * @param Connection     $databaseConnection The database connection.
     * @param EntityManager  $entityManager      The entity manager.
     * @param CacheInterface $cacheHandler       The cache service.
     * @param string         $cachePrefix        The cache prefix.
     */
    public function __construct(
        $databaseConnection,
        EntityManager $entityManager,
        CacheInterface $cacheHandler,
        $cachePrefix
    ) {
        $this->cache       = $cacheHandler;
        $this->dbConn      = $databaseConnection;
        $this->cachePrefix = $cachePrefix;
        $this->er          = $entityManager;
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
    public function searchSuggestedContents($contentTypeName, $filter = '', $numberOfElements = 4)
    {
        $cacheKey = $this->cachePrefix . '_suggested_contents_' . md5(implode(',', func_get_args()));
        $result   = $this->cache->fetch($cacheKey);

        if (!is_array($result)) {
            $filter = (empty($filter) ? "" : " AND " . $filter);

            // Generate where clause for filtering fk_content_type
            $selectedContentTypesSQL = $this->parseTypes($contentTypeName);

            $numberOfElements = (int) $numberOfElements;
            if ($numberOfElements < 1) {
                $numberOfElements = 4;
            }

            $sql = "SELECT content_type_name, pk_content FROM contents"
                    . " WHERE `contents`.`content_status` = 1 AND `contents`.`in_litter` = 0 "
                    . $selectedContentTypesSQL
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

                // TODO: nasty hack to convert content objects to the old array way
                $result = [];
                foreach ($contents as $content) {
                    $content->uri = $content->uri;

                    // We have to call jsonSerialize to get all protected properties
                    $vars      = $content->jsonSerialize();
                    $result [] = $vars;
                }
            } catch (Exception $e) {
                return [];
            }

            $cm     = new \ContentManager();
            $result = $cm->getInTime($result);

            $er = getService('entity_repository');
            foreach ($result as &$content) {
                if (array_key_exists('img2', $content) && $content['img2'] != '0') {
                    $content['image'] = $er->find('Photo', $content['img2']);
                } elseif (array_key_exists('img1', $content) && $content['img1'] != '0') {
                    $content['image'] = $er->find('Photo', $content['img1']);
                }
            }

            $this->cache->save($cacheKey, $result, 300);
        }

        return $result;
    }

    /**
     * Parses a string of content types and returns it as SQL statement.
     *
     * @param string $szSource String to parse.
     *
     * @return array List of types to search.
     */
    private function parseTypes($szSource)
    {
        $szSource = trim($szSource);
        if (($szSource == '') || ($szSource == null) || ($szSource == ' ')) {
            return 'TRUE';
        }

        $contentTypeNames = explode(',', $szSource);

        $contentTypesSQL = [];
        foreach ($contentTypeNames as $contentTypeName) {
            $contentTypesSQL [] = "`content_type_name` = '" . trim($contentTypeName) . "'";
        }

        $contentTypesSQL = " AND (" . implode(' OR ', $contentTypesSQL) . ") ";

        return $contentTypesSQL;
    }
}
