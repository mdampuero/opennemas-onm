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

    public static $int      = 0;
    private static $instance;

    /*
     * Devuelve una instancia al propio Objeto. Patron singleton.
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

    /*
     * Busca en la base de datos todos los contenidos que sean del
     * tipo indicado en szContentsType y los tag tenga alguna coincidencia
     * con los proporcionados en szSource.
     *
     * @param string $szSource       Cadena fuente a buscar.
     * @param string $szContentsType Tipos de contenidos en donde buscar.
     *
     * @return array array de los pk_content de todos los contendios
     *               ordenado por el numero de coincidencias.
    */
    public function searchRelatedContents(
        $szSourceTags,
        $szContentsTypeTitle,
        $iLimit = null,
        $_where = null
    ) {
        // If $szSourceTags is array
        // convert it in one string of words separated by blank
        if (is_array($szSourceTags)) {
            $szSourceTags = implode(' ', $szSourceTags);
        }
        //Match con contents.title
        $szMatch2 = $this->defineMatchOfSentence2($szSourceTags);

        $szSqlSentence = "SELECT pk_content, available, title, metadata, "
            ."pk_fk_content_category, created, catName, "
            .$szMatch2. " AS rel FROM contents, contents_categories";
        $szSqlWhere = " WHERE " .$szMatch2;
        $szSqlWhere .=  " AND ( " . $this->parseTypes($szContentsTypeTitle) . ") ";
        $szSqlWhere .= "  AND in_litter = 0 AND pk_content = pk_fk_content";
        if ($_where!=null) {
            $szSqlWhere .= "  AND ".$_where;
        }
        $szSqlSentence .= $szSqlWhere;
        $szSqlSentence .= 'ORDER BY rel DESC, created DESC';
        if ($iLimit!=null) {
            $szSqlSentence .= " LIMIT ".$iLimit;
        }
        $resultSet = $GLOBALS['application']->conn->Execute($szSqlSentence);

        // $result= $resultSet->GetArray();
        $i=0;

        $result = false;

        if ($resultSet->fields) {
            while (!$resultSet->EOF) {
                $result[$i]['id']         = $resultSet->fields['pk_content'];
                $result[$i]['pk_content'] = $resultSet->fields['pk_content'];
                $result[$i]['title']      =
                    htmlentities(strip_tags($resultSet->fields['title']), ENT_QUOTES, 'UTF-8', false);
                $result[$i]['pk_fk_content_category'] =
                $resultSet->fields['pk_fk_content_category'];

                if ($resultSet->fields['catName'] == null) {
                    $result[$i]['catName'] = 'OPINIÓN';
                } else {
                    $result[$i]['catName'] = $resultSet->fields['catName'];
                }

                $result[$i]['created']   = $resultSet->fields['created'];
                $result[$i]['rel']       = $resultSet->fields['rel'];
                $result[$i]['available'] = $resultSet->fields['available'];
                $result[$i]['metadata']  = $resultSet->fields['metadata'];

                $resultSet->MoveNext();
                $i++;
            }
        }

        return $result;
    }

    /*
     * Busca en la base de datos todos los contenidos que sean del tipo
     * indicado en szContentsType y los tag tenga alguna coincidencia con
     * los proporcionados en szSource.
     *
     * @param string $szReturnValues Cadena con las columnas a devolver.
     * @param string $szContentsTags Cadena con los tags a buscar
     *                               en los fulltext.
     * @param string $szContentsTypeTitle Titulos de los tipos de contenidos
     *                                    en donde buscar.
     *
     * @return array array de los pk_content de todos los contendios ordenado
     *               por el n�mero de coincidencias.
    */
    public function searchContentsSelect(
        $szReturnValues,
        $szSourceTags,
        $szContentsTypeTitle,
        $iLimit
    ) {
        $szMatch = $this->defineMatchOfSentence($szSourceTags);
        $szSqlSentence = 'SELECT '. $szReturnValues.", ".$szMatch." as _height";
        $szSqlSentence .= " FROM contents ";
        $szSqlSentence .= " WHERE " . $szMatch;
        $szSqlSentence .= " AND ( ".$this->parseTypes($szContentsTypeTitle).")";
        $szSqlSentence .= " ORDER BY _height DESC";
        $szSqlSentence .= " LIMIT " . $iLimit;

        $resultSet = $GLOBALS['application']->conn->Execute($szSqlSentence);
        if ($resultSet!=null) {
            return $resultSet->GetArray();
        }

        return null;
    }

    /*
     * Busca en la base de datos todos los contenidos que sean del tipo
     * indicado en szContentsType y los tag tenga alguna coincidencia con los
     * proporcionados en szSource. Permiete relacionar la tabla contents con
     * otra tabla.
     *
     * @param string $szReturnValues      Cadena con las columnas a devolver.
     * @param string $szContentsTags      Cadena con los tags a buscar en los fulltext.
     * @param string $szContentsTypeTitle Titulos de los tipos de contenidos
     *                                    en donde buscar.
     * @param string $szWhere operaciones logicas a añadir a la parte
     *                        where de la sentencia.
     * @param string $szNewTAble tabla a añadir a la sentencia.
     *
     * Output: pk_content de todos los contendios ordenado por el n�mero de coincidencias.
    */
    public function searchContentsSelectMerge(
        $szReturnValues,
        $szSourceTags,
        $szContentsTypeTitle,
        $szWhere,
        $szNewTable,
        $iLimit
    ) {
        if (!isset($szNewTable) || empty($szNewTable)
            || !isset($szWhere) || empty($szWhere)
        ) {
            return -1;
        }

        //Transform the input string to search like: 'La via del tren' => '+via +tren'
        $szSourceTags = explode(', ', StringUtils::get_tags($szSourceTags));
        $szSourceTags2=array();
        $i = 0;
        foreach ($szSourceTags as $key) {
            $szSourceTags2[$i] = '+'.$key.'*';
            $i++;
        }
        $szSourceTags2 = implode(' ', $szSourceTags2);// Con + obligatorio

        $szMatch0 = $this->defineMatchOfSentence0($szSourceTags2);//Match with both

        $szSqlSentence = 'SELECT '. $szReturnValues . ", " . (($szMatch0)) ." as _height";

        $szSqlSentence .= " FROM contents, " . $szNewTable;
        $szSqlSentence .= " WHERE " . $szMatch0;
        $szSqlSentence .= " AND ( " . $this->parseTypes($szContentsTypeTitle) . ") AND (" . $szWhere . ") ";
        $szSqlSentence .= " ORDER BY _height DESC, created DESC";
        $szSqlSentence .= " LIMIT " . $iLimit;

        $resultSet = $GLOBALS['application']->conn->Execute($szSqlSentence);

        if ($resultSet!=null) {
            return $resultSet->GetArray();
        }

        return null;
    }

    /*
     * Busca en la base de datos todos los contenidos con Available a 1
     * (Publicados) que sean del tipo indicado en szContentsType y los tag
     * tengan alguna coincidencia con los proporcionados en szSource.
     *
     * @param string $szReturnValues      Cadena con las columnas a devolver.
     * @param string $szContentsTags      Cadena con los tags a buscar en los fulltext.
     * @param string $szContentsTypeTitle Titulos de los tipos de contenidos
     *                                    en donde buscar.
     *
     * @return array pk_content de todos los contendios ordenado por el
     *               número de coincidencias.
    */
    public function searchPublishContentsSelect($szReturnValues, $szSourceTags, $szContentsTypeTitle, $iLimit)
    {
        $szMatch = $this->defineMatchOfSentence($szSourceTags);
        $szSqlSentence =
            'SELECT '. $szReturnValues . ", " . $szMatch . " as _height"
            . " FROM contents "
            . " WHERE " . $szMatch
            . " AND ( " . $this->parseTypes($szContentsTypeTitle) . ") "
            . " AND available = 1"
            . " ORDER BY _height DESC"
            . " LIMIT " . $iLimit;

        $resultSet = $GLOBALS['application']->conn->Execute($szSqlSentence);

        if ($resultSet!=null) {
            return $resultSet->GetArray();
        }

        return null;
    }

    /**
     * Busca en la base de datos todos las noticias sugeridas que cumplan un
     * $where con Available a 1 (Publicados) que sean del tipo indicado en
     * szContentsType y los tag tengan alguna coincidencia con los
     * proporcionados en szSource.
     *
     * @param string $szReturnValues      Cadena con las columnas a devolver.
     * @param string $szContentsTags      Cadena con los tags a buscar en los fulltext.
     * @param string $szContentsTypeTitle Titulos de los tipos de
     *                                    contenidos en donde buscar.
     * @param string $filter condicion que han de cumplir
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

        if ( is_null($filter) ) {
            $filter = "1=1";
        }

        //Transform the input string to search like: 'La via del tren' => '+via +tren'
        $szSourceTags = explode(', ', StringUtils::get_tags($szSourceTags));
        $szSourceTags2=array();
        $i = 0;
        foreach ($szSourceTags as $key) {
            $szSourceTags2[$i] = '+'.$key;
            $i++;
        }
        $szSourceTags2 = implode(' ', $szSourceTags2);// Con + obligatorio
        $szSourceTags = implode(' ', $szSourceTags);// Sin+ no obligatorio

        $szMatch = $this->defineMatchOfSentence($szSourceTags2); //Match con metadata
        $szMatch2 = $this->defineMatchOfSentence2($szSourceTags);//Match con contents.title

        $szSqlSentence = "SELECT `contents`.`pk_content`, `contents`.`title`, "
            ."`contents`.`metadata`, `contents`.`created`,  " . (($szMatch))
            .'+'.(($szMatch2)) . " AS rel  FROM contents, contents_categories ";
        //$szSqlWhere  = " WHERE MATCH ( " . cSearch::FULL_TEXT_COLUMN . ")
        // AGAINST ( '" . $szSourceTags . "  IN BOOLEAN MODE') ";
        $szSqlWhere = " WHERE " . $szMatch.' + '. $szMatch2
                    . " AND ( " . $this->parseTypes($szContentsTypeTitle) . ") "
                    . " AND  ".$filter
                    . " AND `contents`.`available` = 1 "
                    . "AND `contents`.`in_litter` = 0 "
                    . "AND `contents`.`pk_content` = `contents_categories`.`pk_fk_content`";
        $szSqlSentence .= $szSqlWhere;
        $szSqlSentence .= " GROUP BY `contents`.`title` ORDER BY created DESC, rel DESC LIMIT ".$iLimit;

        $resultSet = $GLOBALS['application']->conn->Execute($szSqlSentence);
        $result = null;
        if (!empty($resultSet)) {
            $result= $resultSet->GetArray();
        }

        return $result;
    }

    /*
     * Name: 	Paginate
     *
     * Description: pagina los resultado proporcionados por $cItems.
     *
     * Input:
     * 		$cItems: (array) contenidos a paginar.
     *		$szId: (array) Elemento del objeto que tomamos como id. Valor único en el array.
     *		$iPaging: (string) Número de contenidos por página.
     *
     * Output: (array) contendios para mostrar en la pagina actual.
     *
    */

    public static function Paginate(&$PageReturn, $cItems, $szId, $iPaging)
    {
        $items = array();
        $itemsPerPage = (empty($iPaging) && define(ITEMS_PAGE))?ITEMS_PAGE: $iPaging;

        foreach ($cItems as $v) {
            $items[] = $v[$szId];
        }

        $params = array(
            'itemData'              => $items,
            'perPage'               => $itemsPerPage,
            'delta'                 => 1,
            'append'                => true,
            'separator'             => '|',
            'spacesBeforeSeparator' => 1,
            'spacesAfterSeparator'  => 1,
            'clearIfVoid'           => true,
            'urlVar'                => 'page',
            'mode'                  => 'Sliding',
            'linkClass'             => 'pagination',
            'altFirst'              => 'primera p&aacute;gina',
            'altLast'               => '&uacute;ltima p&aacute;gina',
            'altNext'               => 'p&aacute;gina seguinte',
            'altPrev'               => 'p&aacute;gina anterior',
            'altPage'               => 'p&aacute;gina'
        );

        $pager = &Pager::factory($params);
        $data  = $pager->getPageData();

        $aResult = array();
        foreach ($cItems as $v) {
            if (in_array($v[$szId], $data)) {
                $aResult[] = $v; // Array 0-n compatible con sections Smarty
            }
        }
        $PageReturn = $pager;

        return $aResult;
    }

    /*
     * Name: 	defineMatchOfSentence
     *
     * Description: Crea la parte del Match de la sentencia sql que nos proporciona el vector de pesos.
     *
     * Input:
     * 		szSource: (string) Cadena a parsear con los Tags.
     *		szContentsTypeTitle: (string) titulos de los tipos de contenidos a buscar.
     *		szColumn: (string) campo de la tabla en la que buscar los tags de szSource.
     *
     * Output: (String) Parte "WHERE" de la sentencia SQL.
     *
    */
    public function defineMatchOfSentence0($szSourceTags)
    {
        $szSourceTags = trim($szSourceTags);
        $szSqlMatch = " MATCH (" . cSearch::FULL_TEXT_COLUMN0 .
            ") AGAINST ( '" . $szSourceTags . "' IN BOOLEAN MODE)";

        return $szSqlMatch;
    }

    /*
     * Name:    defineMatchOfSentence
     *
     * Description: Crea la parte del Match de la sentencia sql que nos proporciona el vector de pesos.
     *
     * Input:
     *      szSource: (string) Cadena a parsear con los Tags.
     *      szContentsTypeTitle: (string) titulos de los tipos de contenidos a buscar.
     *      szColumn: (string) campo de la tabla en la que buscar los tags de szSource.
     *
     * Output: (String) Parte "WHERE" de la sentencia SQL.
     *
    */
    public function defineMatchOfSentence($szSourceTags)
    {
        $szSourceTags = trim($szSourceTags);
        $szSqlMatch = " MATCH (" . cSearch::FULL_TEXT_COLUMN  .
            ") AGAINST ( '" . $szSourceTags . "' IN BOOLEAN MODE)";

        return $szSqlMatch;
    }
    /*
     * Name: 	defineMatchOfSentence2
     *
     * Description: Crea la parte del Match de la sentencia sql que nos proporciona el vector de pesos.
     *
     * Input:
     * 		szSource: (string) Cadena a parsear con los Tags.
     *		szContentsTypeTitle: (string) titulos de los tipos de contenidos a buscar.
     *		szColumn: (string) campo de la tabla en la que buscar los tags de szSource.
     *
     * Output: (String) Parte "WHERE" de la sentencia SQL.
     *
    */
    private function defineMatchOfSentence2($szSourceTags)
    {
        $szSourceTags = trim($szSourceTags);
        $szSqlMatch = " MATCH (" . cSearch::FULL_TEXT_COLUMN2  .
            ") AGAINST ( '" . $szSourceTags . "' IN BOOLEAN MODE)";

        return $szSqlMatch;
    }
    /*
     * Name: 	parseTypes
     *
     * Description: Parsea la cadena fuente comprobando posibles operaciones lógicas.
     *
     * Input:
     * 		szSource: (string) Cadena a parsear.
     *
     * Output: (Array de String)
     *
    */
    private function parseTypes($szSource)
    {
        $szSource = trim($szSource);
        if (($szSource == '') || ($szSource == null) || ($szSource == ' ')) {
            return 'TRUE';
        }

        $szColumn = 'fk_content_type';
        //Obtener los id de los tipos a traves de su titulo.
        $szContentsTypeId = $this->getPkContentsType($szSource);

        $vWordsTemp = preg_split(cSearch::PARSE_STRING, $szContentsTypeId);

        $szIdTypes  = array();
        foreach ($vWordsTemp as $szId) {
            $szIdTypes []= $szColumn . " LIKE '" . $szId . "'";
        }
        $szIdTypes = "( FALSE OR ". implode(' OR ', $szIdTypes)." )";

        return $szIdTypes;
    }

    /*
     * Name: getPkContentsType
     *
     * Description: Busca en la base de datos todos los pk de la tabla Contents_type cuyo titulo
     *	coincida con los proporcionados en el parametro de entrada.
     *
     * Input: szContentsType.: (string) Cadena fuente con los titulos de los tipos de contenido.
     *
     * Output: pk_contentType de todas las coincidencias con los titulos.
    */
    public function getPkContentsType($szContentsType)
    {
        $szContentsType    = trim($szContentsType);
        $szSqlContentTypes = "SELECT `pk_content_type` FROM `content_types`";
        $vWordsTemp = preg_split(cSearch::PARSE_STRING, strtolower($szContentsType));

        $szSqlContentTypes .= " WHERE FALSE ";
        for ($iIndex=0; $iIndex<sizeof($vWordsTemp); $iIndex++) {
            $szSqlContentTypes .= " OR name LIKE '" . $vWordsTemp[$iIndex] . "'";
        }
        $resultSet = $GLOBALS['application']->conn->Execute($szSqlContentTypes);
        if (!$resultSet) {
            printf(
                "Get Content Types: Error al obtener el record Set.<br/>" .
                "<pre>" . $szSqlContentTypes . "</pre><br/><br/>"
            );

            return null;
        }

        try {
            $resultArray = $resultSet->GetArray();
            $szResult='';
            foreach ($resultArray as $vAux) {
                $szResult .= $vAux[0] . " ";
            }
        } catch (exception $e) {
            printf("Excepcion: " . $e->message);

            return null;
        }

        return trim($szResult);
    }
}

