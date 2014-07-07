<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Onm;

use Onm\Cache\CacheInterface;
use Onm\Database\DbalWrapper;
use Repository\EntityManager;

/**
* Perform searches in Database related with one content
*/
class MachineSearcher
{
    public function __construct(DbalWrapper $databaseConnection, EntityManager $entityManager, CacheInterface $cacheHandler, $cachePrefix)
    {
        $this->cache       = $cacheHandler;
        $this->dbConn      = $databaseConnection;
        $this->cachePrefix = $cachePrefix;
        $this->er          = $entityManager;
    }

    /**
     * Returns a list of contents related with a content type and category
     *
     * @param string $contentTypeName  content types required
     * @param string $filter           advanced SQL filter for contents
     * @param int    $numberOfElements number of results
     *
     * @return  array two dimensional array with the content properties of each content
     */
    public function searchSuggestedContents($contentTypeName, $filter = '', $numberOfElements = 4)
    {
        $cacheKey = $this->cachePrefix.'_suggested_contents_'.md5(implode(',', func_get_args()));
        $result = $this->cache->fetch($cacheKey);

        if (!is_array($result)) {
            $filter = (empty($filter) ? "" : " AND ".$filter);

            // Generate content type table name
            $contentTable = tableize($contentTypeName);

            // Generate where clause for filtering fk_content_type
            $selectedContentTypesSQL = $this->parseTypes($contentTypeName);

            $numberOfElements = (int) $numberOfElements;
            if ($numberOfElements < 1) {
                $numberOfElements = 4;
            }

            $sql = "SELECT content_type_name, pk_content FROM contents"
                    ." WHERE `contents`.`content_status` = 1 AND `contents`.`in_litter` = 0 "
                    .$selectedContentTypesSQL
                    .$filter
                    ." ORDER BY created DESC LIMIT ". $numberOfElements;

            try {
                $rs = $this->dbConn->fetchAll($sql);

                foreach ($rs as $content) {
                    $contentProps []= array($content['content_type_name'], $content['pk_content']);
                }

                if (count($contentProps) < 1) {
                    return array();
                }

                $contents = $this->er->findMulti($contentProps);

                // TODO: nasty hack to convert content objects to the old array way
                $result = array();
                foreach ($contents as $content) {
                    $result []= get_object_vars($content);
                }
            } catch (Exception $e) {
                return array();
            }

            $cm = new \ContentManager();
            $result = $cm->getInTime($result);

            $er = getService('entity_repository');
            foreach ($result as &$content) {
                if (array_key_exists('img2', $content) && $content['img2'] != '0') {
                    $content['image'] = $er->find('Photo', $content['img2']);
                } elseif (array_key_exists('img1', $content) && $content['img1'] != '0') {
                    $content['image'] = $er->find('Photo', $content['img1']);
                }

                $content['uri'] = \Uri::generate(
                    'article',
                    array(
                        'id'       => $content['pk_content'],
                        'date'     => date('YmdHis', strtotime($content['created'])),
                        'category' => $content['catName'],
                        'slug'     => StringUtils::get_title($content['title']),
                    )
                );
            }

            $this->cache->save($cacheKey, $result, 300);
        }

        return $result;
    }

    /**
     * Parsea la cadena fuente comprobando posibles operaciones lógicas.
     *
     * @param string $szSource Cadena a parsear.
     *
     * @return array list of types to search
     *
     */
    private function parseTypes($szSource)
    {
        $szSource = trim($szSource);
        if (($szSource == '') || ($szSource == null) || ($szSource == ' ')) {
            return 'TRUE';
        }

        $contentTypeNames = explode(',', $szSource);

        $contentTypesSQL = array();
        foreach ($contentTypeNames as $contentTypeName) {
            $contentTypesSQL []= "`content_type_name` = '".trim($contentTypeName)."'";
        }
        $contentTypesSQL = " AND (". implode(' OR ', $contentTypesSQL).") ";

        return $contentTypesSQL;
    }
}
