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

/**
* Perform searches in Database related with one content
*/
class MachineSearcher
{

    public function __construct(CacheInterface $cacheHandler)
    {
        $this->cache = $cacheHandler;
    }


    /**
     * Busca en la base de datos todos las noticias sugeridas que cumplan un
     * $where con Available a 1 (Publicados) que sean del tipo indicado en
     * szContentsType y los tag tengan alguna coincidencia con los
     * proporcionados en szSource.
     *
     * @param string $szSourceTags        Cadena con las tags a parsear
     * @param string $szContentsTypeTitle Titulos de los tipos de
     *                                    contenidos en donde buscar.
     * @param string $filter condicion que han de cumplir
     * @param int    $iLimit max number of elements to return
     *
     * Example
     * SELECT pk_content, title, metadata, created, permalink, MATCH ( metadata)
     *  AGAINST ( 'primer, ministro, tailandés, envía, ejército, multitud, mundo ')
     *  AS rel FROM contents, contents_categories WHERE MATCH ( metadata)
     *  AGAINST ( 'primer, ministro, tailandés, envía, ejército, multitud,
     *  mundo IN BOOLEAN MODE') AND ( ( FALSE OR fk_content_type LIKE '1' ))
     *  AND pk_fk_content_category= 12 AND contents.available=1 AND pk_content
     *  = pk_fk_content AND available = 1 AND in_litter = 0 AND pk_content = pk_fk_content
     *  ORDER BY rel DESC, created DESC LIMIT 0, 6
     *
     * @return pk_content de todos los contendios ordenado por el número de coincidencias.
     */
    public function searchSuggestedContents($szSourceTags, $szContentsTypeTitle, $filter, $iLimit)
    {
        if (empty($szSourceTags)) {
            return array();
        }

        $cacheKey = CACHE_PREFIX.'_suggested_contents_'.md5(implode(',', func_get_args()));
        $result = $this->cache->fetch($cacheKey);

        if (!is_array($result)) {

            $filter = (empty($filter) ? "" : " AND ".$filter);

            // Transform the input string to an array
            $szSourceTags = explode(', ', StringUtils::get_tags($szSourceTags));

            // Generate content type table name
            $contentTable = tableize($szContentsTypeTitle);

            // Generate where clause for filtering fk_content_type
            $selectedContentTypesSQL = $this->parseTypes($szContentsTypeTitle);

            // Generate WHERE with REGEXP using the provided tags
            $szSourceTags = rtrim(implode('|', $szSourceTags), '|');
            $whereSQL = "contents.metadata REGEXP '".$szSourceTags."'";

            $szSqlSentence = "SELECT `contents`.*, `contents_categories`.`catName`, $contentTable.*"
                        ."  FROM contents, $contentTable, contents_categories "
                        ." WHERE contents.pk_content=$contentTable.pk_$szContentsTypeTitle"
                        ." AND `contents`.`pk_content` = `contents_categories`.`pk_fk_content`"
                        ." AND `contents`.`content_status` = 1 "
                        ." AND `contents`.`in_litter` = 0 "
                        .$selectedContentTypesSQL
                        .$filter
                        ." AND ". $whereSQL
                        ." GROUP BY `contents`.`title` ORDER BY created DESC LIMIT ". $iLimit;

            $resultSet = $GLOBALS['application']->conn->Execute($szSqlSentence);
            $result = null;
            if (!empty($resultSet)) {
                $result= $resultSet->GetArray();
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

        //Obtener los id de los tipos a traves de su titulo.
        $szContentsType    = trim($szSource);

        $contentTypeNames = explode(',', $szContentsType);

        foreach ($contentTypeNames as $contentTypeName) {
            $contentTypeIds []= \ContentManager::getContentTypeIdFromName($contentTypeName);
        }

        $contentTypesSQL = '';
        if (!empty($contentTypeIds)) {
            foreach ($contentTypeIds as $szId) {
                $contentTypesSQL []= "`fk_content_type` = {$szId}";
            }
            $contentTypesSQL = " AND ( ". implode(' OR ', $contentTypesSQL)." ) ";
        }

        return $contentTypesSQL;
    }
}
