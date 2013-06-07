<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Class for handling searching operations
 *
 * @package Onm
 */
class cSearch
{
    // caracteres por el cual separar cada elemento de la cadena.
    const PARSE_STRING      = '/[\s,;]+/';

    const FULL_TEXT_COLUMN  = 'contents.metadata';
    const FULL_TEXT_COLUMN2 = 'contents.title';
    const FULL_TEXT_COLUMN0 = 'contents.title,contents.metadata';

    const ITEMS_PAGE        = 10;

    /**
     * Undocumented. Maybe unused variable
     *
     * @var int
     **/
    public static $int      = 0;

    /**
     * The singleton instance of the cSearch object
     *
     * @var cSearch
     **/
    private static $instance;

    /**
     * Returns an instance of the object. Singleton pattern
     *
     * @return cSearch the singleton object instance
     *
    */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new cSearch();
        }

        return self::$instance;
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
        global $sc;
        $cache = $sc->get('cache');

        $cacheKey = CACHE_PREFIX.'_suggested_contents_'.md5(implode(',', func_get_args()));
        $result = $cache->fetch($cacheKey);

        if (!is_array($result)) {

            $filter = (empty($filter) ? "" : " AND ".$filter);

            // Transform the input string to search like: 'La via del tren' => '+via +tren'
            $szSourceTags = explode(', ', StringUtils::get_tags($szSourceTags));
            $szSourceTags = implode(' ', $szSourceTags);// Sin+ no obligatorio

            $matchSQL =  " MATCH (contents.metadata) AGAINST ( '{$szSourceTags}' IN BOOLEAN MODE)";
            $selectedContentTypesSQL = $this->parseTypes($szContentsTypeTitle);

            $szSqlSentence = "SELECT {$matchSQL} AS rel, `contents`.*, `contents_categories`.`catName`"
                        ."  FROM contents, contents_categories "
                        ." WHERE " . $matchSQL
                        .$selectedContentTypesSQL
                        .$filter
                        ." AND `contents`.`available` = 1 "
                        ." AND `contents`.`in_litter` = 0 "
                        ." AND `contents`.`pk_content` = `contents_categories`.`pk_fk_content`"
                        ." GROUP BY `contents`.`title` ORDER BY created DESC, rel DESC LIMIT ". $iLimit;

            $resultSet = $GLOBALS['application']->conn->Execute($szSqlSentence);
            $result = null;
            if (!empty($resultSet)) {
                $result= $resultSet->GetArray();
            }

            foreach ($result as &$res) {
                $res['uri'] = Uri::generate(
                    'article',
                    array(
                        'id'       => $res['pk_content'],
                        'date'     => date('YmdHis', strtotime($res['created'])),
                        'category' => $res['catName'],
                        'slug'     => StringUtils::get_title($res['title']),
                    )
                );
            }

            $cache->save($cacheKey, $result, 300);
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

        $ids = array();
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
