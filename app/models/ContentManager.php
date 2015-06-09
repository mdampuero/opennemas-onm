<?php
/**
 * Contains the ContentManager class for handling common content operations
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Model
 */

/**
 * Handles common operations with contents
 *
 * @package    Model
 */
class ContentManager
{
    /**
     * When working with an specific content type, this contains the content type
     * name (valid for the aplication)
     *
     * @var string
     **/
    public $content_type = null;

    /**
     * The maximum number of element to show in a frontpage.
     *
     * @var integer
     */
    public static $frontpage_limit = 100;

    /**
     * When working with an specific content type, this contains the table
     * that contains that specific content type
     *
     * @var string
     **/
    public $table = null;

    /**
     * Contains the Pager object instance, usefull for paginate contents
     *
     * @var \Pager
     **/
    public $pager = null;

    /**
     * Initializes the class and assigns the cache instance to itself
     *
     * If a valid content type name is given, it initializes some values for accessing
     * some particular database tables
     *
     * @param string $contentType the content type to work with
     *
     * @return void
     **/
    public function __construct($contentType = null)
    {
        // Lowercase table name and content type with the name of the class
        if (!is_null($contentType)) {
            $this->init($contentType);
        }

        $this->cache = new MethodCacheManager($this, array('ttl' => 30));
    }

    /**
     * Initializes the table and content_type properties from a content type name
     *
     * @param string $contentType the content type name
     *
     * @return void
     **/
    public function init($contentType)
    {
        $this->table        = tableize($contentType);
        $this->content_type = underscore($contentType);
    }

    /**
     * Hydrates the properties from a \AdodbResultSet into a new object
     *
     * @param \AdodbResultSet $rs the adodb result set that contains information
     *                            of the contents to be hydrated
     *
     * @param string $contentType the content type name
     *
     * @return array the content objects with all the information completed
     **/
    public function loadObject($rs, $contentType)
    {
        $items = array();

        if ($rs !== false) {
            while (!$rs->EOF) {
                $contentType = classify($contentType);
                $obj = new $contentType();
                $obj->load($rs->fields);

                $items[] = $obj;

                $rs->MoveNext();
            }
        }

        return $items;
    }

    /**
     * Searches contents in the database given son search params
     *
     * @param string $contentType the content type to search for
     * @param string $filter the SQL WHERE sentence to filter down contents
     * @param string $orderBy the ORDER BY sentence
     * @param string $fields the list of fields to fetch
     *
     * @return array an array of contents with the information completed
     **/
    public function find(
        $contentType,
        $filter = null,
        $orderBy = 'ORDER BY 1',
        $fields = '*'
    ) {
        $this->init($contentType);
        $items = array();

        $_where = '`contents`.`in_litter`=0';

        if (!is_null($filter)) {
            // se busca desde la litter.php
            if ($filter == 'in_litter=1') {
                $_where = $filter;
            } else {
                $_where = ' `contents`.`in_litter`=0 AND '.$filter;
            }
        }

        $sql = 'SELECT '.$fields.' FROM `contents`, `'.$this->table.'` '
             . 'WHERE `contents`.`pk_content`= `'.$this->table. '`.`pk_'.$this->content_type.'`'
             .' AND '.$_where
             .' '.$orderBy;

        $rs = $GLOBALS['application']->conn->Execute($sql);
        $items = $this->loadObject($rs, $contentType);

        return $items;
    }

    /**
     * Returns an array of objects for a given content type and filters
     *
     * @param string $contentType the content type to search for
     * @param string $filter      the SQL string to filter contents
     * @param string $orderBy    SQL string to order results
     * @param string $fields      the list of fields to get
     *
     * @return array the list of content objects
     **/
    public function findAll(
        $contentType,
        $filter = null,
        $orderBy = 'ORDER BY 1',
        $fields = '*'
    ) {
        $this->init($contentType);
        $items = array();

        $where = '`contents`.`in_litter`=0';

        if (!is_null($filter)) {
            //se busca desde la litter.php
            if ($filter == 'in_litter=1') {
                $where = $filter;
            } else {
                $where = ' `contents`.`in_litter`=0 AND '.$filter;
            }
        }

        $sql = 'SELECT '.$fields
             . ' FROM `contents`, `'.$this->table.'`, `contents_categories` '
             . ' WHERE '.$where
             . ' AND `contents`.`pk_content`= `'.$this->table.'`.`pk_'.$this->content_type.'` '
             . ' AND `contents`.`pk_content`= `contents_categories`.`pk_fk_content` '.$orderBy;

        $rs = $GLOBALS['application']->conn->Execute($sql);
        $items = $this->loadObject($rs, $contentType);

        return $items;
    }

    /**
     * Searches for contents given a criteria
     *
     * @param array $params parameters to filter contents with
     *
     * @return array list of contents that matched the criteria
     **/
    public static function search($params = array())
    {
        $defaultParams = array(
            'text'                   => '',
            'content_types_selected' => 'all',
            'page'                   => 1,
            'elements_per_page'      => 20,
            'order'                  => 'contents.created DESC '
        );

        $params = array_merge($defaultParams, $params);

        // Return empty array if the search text is empty
        if (empty($params['text'])) {
            return array();
        }

        // Preparing the search SQL
        $searchSQL = " AND (contents.title LIKE '%{$params['text']}%'"
                    ." OR contents.description LIKE '%{$params['text']}%'"
                    ." OR contents.metadata LIKE '%{$params['text']}%')";

        // Preparing limit
        $limitSQL = '';
        if ($params['page'] <= 1) {
            $limitSQL = ' LIMIT '. $params['elements_per_page'];
        } else {
            $limitSQL = ' LIMIT '.($params['page']-1)*$params['elements_per_page'].', '.$params['elements_per_page'];
        }

        // Preparing the order SQL
        $orderBySQL = ' ORDER BY '.$params['order'];


        // Preparing filter for content types
        $contentTypesFilterSQL = '';
        if ($params['content_types_selected'] != 'all'
            && !empty($params['content_types_selected'])
        ) {
            if (is_string($params['content_types_selected'])) {
                $contentTypesFilterSQL = ' AND `fk_content_type` = '.$params['content_types_selected']. " ";
            } else {
                $contentTypesFilterSQL =
                    ' AND `fk_content_type` IN ('.implode(', ', $params['content_types_selected']). ") ";
            }
        }

        $sql = "SELECT  contents.*,
                        `contents_categories`.`pk_fk_content_category` as category_id,
                        `contents_categories`.`catName`  as category_name "
               ."FROM `contents`, `contents_categories` "
               ."WHERE `contents`.`pk_content`=`contents_categories`.`pk_fk_content` "
               .$contentTypesFilterSQL
               .$searchSQL
               .$orderBySQL
               .$limitSQL;

        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if ($rs === false) {
            return array();
        }

        $contents = array();
        $contentsData = $rs->getArray();
        foreach ($contentsData as $data) {
            $contenType = self::getContentTypeNameFromId($data['fk_content_type']);
            $contentTypeClass = classify($contenType);
            $content = new $contentTypeClass();
            $content->load($data);

            $contents []= $content;
        }

        return $contents;
    }

    /**
     * Fetches all the contents (articles, widgets, etc) for one specific
     * category with its placeholder and position
     *
     * This is used for HomePages, fetches all the contents assigned for it and
     * allows to render an entire homepage
     *
     * @param  int $categoryID the id of the category we want to get contents from
     *
     * @return array of contents
     */
    public function getContentsForHomepageOfCategory($categoryID)
    {
        // Initialization of variables
        $contents = array();

        $cache      = getService('cache');
        $contentIds = $cache->fetch('frontpage_elements_map_'.$categoryID);

        if (!is_array($contentIds) || count($contentIds) <= 0) {
            // Fetch the list of contents for the current frontpage and its metadata
            // We need to get articles in frontpage too in order to mark them as in_frontpage
            $contentIds = $this->getContentIdsInHomePageWithIDs(
                array((int) $categoryID, 0)
            );

            $cache->save('frontpage_elements_map_'.$categoryID, $contentIds);
        }

        // Build an array with contents that exist in the main frontapge
        $contentsInFrontpage = array_unique(
            array_map(
                function ($content) {
                    if ($content['frontpage_id'] == 0) {
                        return $content['content_id'];
                    } else {
                        return null;
                    }
                },
                $contentIds
            )
        );

        // Clear out home frontpage authors
        $contentIds = array_filter(
            $contentIds,
            function ($content) use ($categoryID) {
                return ($content['frontpage_id'] == $categoryID);
            }
        );

        $contentIds = $this->checkAndCleanFrontpageSize($contentIds);

        if (is_array($contentIds) && count($contentIds) > 0) {

            $er = getService('entity_repository');

            // Retrieve contents from cache
            $contentsMap = array_map(function ($content) {
                return array($content['content_type'], $content['content_id']);
            }, $contentIds);

            $contentsRaw = $er->findMulti($contentsMap);
            $er->populateContentMetasInContents($contentsRaw);

            // iterate over all found contents to hydrate them
            foreach ($contentIds as $element) {

                // Only add elements for the requested category id
                if ($element['frontpage_id'] != $categoryID) {
                    continue;
                }

                foreach ($contentsRaw as $contentRaw) {
                    if ($element['content_id'] == $contentRaw->id) {
                        $content = $contentRaw;
                        break;
                    }
                }

                // add all the additional properties related with positions and params
                if (is_object($content) && $content->in_litter == 0) {
                    $content->load(
                        array(
                            'placeholder' => $element['placeholder'],
                            'position'    => $element['position'],
                        )
                    );
                    if (is_array($content->params) && $content->params > 0) {
                        $content->params = array_merge(
                            $content->params,
                            (array) $element['params']
                        );
                    } else {
                        $content->params = $element['params'];
                    }

                    $content->in_frontpage = in_array($element['content_id'], $contentsInFrontpage);

                    $contents[] = $content;
                }
            }
        }

        // Return all the objects of contents initialized
        return $contents;
    }

    /**
     * Fetches content ids (articles, widgets, etc) for one specific
     * category with its placeholder and position
     *
     * This is used for HomePages, fetches all the contents assigned for it and
     * allows to render an entire homepage
     *
     * @param  array $categories list of category ids
     *
     * @return mixed, array of contents
     */
    public function getContentIdsInHomePageWithIDs($categories = array())
    {
        // Initialization of variables
        $contents = array();

        if (count($categories) == 0) {
            return $contents;
        }

        $conn = getService('dbal_connection');

        $categoriesSQL = implode(', ', $categories);
        $sql = 'SELECT * FROM content_positions '
          .'WHERE `fk_category` IN ('.$categoriesSQL.') '
          .'ORDER BY position ASC ';

        $rs = $conn->fetchAll($sql);

        foreach ($rs as $content) {
            $contents []= array(
                'content_id'   => $content['pk_fk_content'],
                'frontpage_id' => $content['fk_category'],
                'position'     => $content['position'],
                'placeholder'  => $content['placeholder'],
                'params'       => unserialize($content['params']),
                'content_type' => $content['content_type'],
            );
        }

        return $contents;
    }

    /**
     * Fetches all the contents (articles, widgets, etc) for one specific
     * category given an array of content ids, position and placeholder
     *
     * This is used from frontpage manager for preview the actual frontpage
     *
     * @param array $contentsArray [ 'id':'xxx', 'position':'xxx', 'placeholder':'xxx', 'params': [] ]
     *
     * @return  array of contents
     */
    public function getContentsForHomepageFromArray($contentsArray)
    {
        // Initialization of variables
        $contents = array();

        $em = getService('entity_repository');

        // iterate over all found contents and initialize them
        foreach ($contentsArray as $element) {
            $content = $em->find($element['content_type'], $element['id']);

            // only add it to the final results if is not in litter
            if ($content->in_litter == 0) {
                $content->load(
                    array(
                        'placeholder' => $element['placeholder'],
                        'position'    => $element['position'],
                    )
                );
                if (is_array($content->params) && $content->params > 0) {
                    $content->params = array_merge(
                        $content->params,
                        (array) $element['params']
                    );
                } else {
                    $content->params = $element['params'];
                }
                $contents[] = $content;
            }
        }

        // Return all the objects of contents initialized
        return $contents;

    }

    /**
     * Fetches all the contents (articles, widgets, etc) for one specific
     * category with its placeholder and position
     *
     * This is used for HomePages, fetches all the contents assigned for it
     * and allows to render an entire homepage
     *
     * @param int $categoryID the id of the category we want to get contents from
     *
     * @return mixed array of contents
     **/
    public function getContentsIdsForHomepageOfCategory($categoryID)
    {

        // Initialization of variables
        $contents = array();
        if (empty($categoryID)) {
            $categoryID = 0;
        }

        $sql = 'SELECT * FROM content_positions '
              .'WHERE `fk_category`='.$categoryID.' '
              .'ORDER BY position ASC ';

        // Fetch the id, placeholder, position, and content_type
        // in this category's frontpage
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if ($rs !== false) {

            // iterate over all found contents and initialize them
            while (!$rs->EOF) {
                if (!class_exists($rs->fields['content_type'])) {
                    $rs->MoveNext();
                    continue;
                }
                $contents []= $rs->fields['pk_fk_content'];

                $rs->MoveNext();
            }
        }

        // Return all the objects of contents initialized
        return $contents;
    }

    /**
     * Save the content positions for elements in a given category
     *
     * @param int $categoryID the id of the category we want to save positions into
     * @param array $elements an array with the id, placeholder, position
     *
     * @return boolean, if all went good this will be true and viceversa
     */
    public static function saveContentPositionsForHomePage(
        $categoryID,
        $elements = array()
    ) {
        $positions = array();
        $contentIds = array();
        $returnValue = false;

        if (empty($elements)) {
            return $returnValue;
        }

        $conn   = getService('dbal_connection');
        $logger = getService('application.log');

        // Foreach element setup the sql values statement part
        foreach ($elements as $element) {
            $positions[] = array(
                $conn->quote($element['id'], \PDO::PARAM_INT),
                $conn->quote($categoryID, \PDO::PARAM_INT),
                $conn->quote($element['position'], \PDO::PARAM_INT),
                $conn->quote($element['placeholder'], \PDO::PARAM_STR),
                $conn->quote($element['content_type'], \PDO::PARAM_STR)
            );
            $contentIds[] = $element['id'];
        }

        try {
            $conn->beginTransaction();

            // Clean all the contents for this category after insert the new ones
            self::clearContentPositionsForHomePageOfCategory($categoryID, $conn);

            // construct the final sql statement and execute it
            $stmt = 'INSERT INTO content_positions (pk_fk_content, fk_category,'
                  . ' position, placeholder, content_type) '
                  . 'VALUES ';

            foreach ($positions as $position) {
                $stmt .= '(' . implode(',', $position) . '),';
            }

            $stmt = trim($stmt, ',');

            $conn->executeUpdate($stmt);

            // Unset suggested flag if saving content positions in frontpage
            if ($categoryID == 0) {
                self::dropSuggestedFlagFromContentIdsArray($contentIds, $conn);
            }

            $conn->commit();
            $returnValue = true;
        } catch (\Exception $e) {
            $conn->rollback();

            $logger->error(
                'User '.$_SESSION['username'].' ('.$_SESSION['userid']
                .') updated frontpage of category '.$categoryID.' with error message: '
                .$e->getMessage()
            );
        }

        return $returnValue;
    }

    /**
     * Drops the suggested to frontpage flag from a list of contents given their ids
     *
     * @param array $contentIds the list of content ids to drop the suggested flag
     *
     * @return boolean true if all went well
     */
    public static function dropSuggestedFlagFromContentIdsArray(
        $contentIds,
        $conn = false
    ) {
        if (!$conn) {
            $conn = getService('dbal_connection');
        }

        if (is_array($contentIds) && (count($contentIds) > 0)) {
            $contentIdsSQL = implode(', ', $contentIds);

            $sql = 'UPDATE contents '
                 . 'SET `frontpage`=0, `changed`=? '
                 . 'WHERE `pk_content` IN ('.$contentIdsSQL.')';
            $values = array(date("Y-m-d H:i:s"));
            // $stmt = $conn->prepare($sql);

            if ($conn->executeUpdate($sql, $values) === false) {
                return false;
            }

            /* Notice log of this action */
            $logger = getService('application.log');
            $logger->notice(
                'User '.$_SESSION['username'].' ('.$_SESSION['userid']
                .') has executed action drop suggested flag at '.$contentIdsSQL.' ids'
            );

            return true;
        }

        return false;
    }

    /**
    * Clear the content positions for elements in a given category
    *
    * @param int $categoryID the id of the category we want
    *                        to clear positions from
    * @return boolean if all went good this will be true and viceversa
    */
    public static function clearContentPositionsForHomePageOfCategory(
        $categoryID,
        $conn = false
    ) {
        if (!$conn) {
            $conn = getService('dbal_connection');
        }

        // clean actual contents for the homepage of this category
        $sql = 'DELETE FROM content_positions WHERE `fk_category` = ' . $categoryID;
        $conn->executeUpdate($sql);

        $logger = getService('application.log');
        $logger->info(
            'User '.$_SESSION['username'].' ('.$_SESSION['userid']
            .') clear contents frontpage of category '.$categoryID
        );
    }

    /**
     * Checks the priority of two objects by its position property
     *
     * @param array $a first object
     * @param array $b second object
     *
     * @return int 0 if both objects has the same property,
     *             1 if the first one is greater
     *             -1 if the second one is greater
     */
    public function sortByPosition($a, $b)
    {
        return ($a->position == $b->position)
            ? 0 : (($a->position > $b->position) ? 1 : -1);
    }

    /**
     * Sorts one array of objects by one of the object's property
     *
     * @param array $array the array of objects
     * @param array $property the property to sort with
     *
     * @return array the sorted $array
     */
    public static function sortArrayofObjectsByProperty($array, $property)
    {
        // Y si el array es vacio ????
        if (count($array) > 0) {
            $cur           = 1;
            $stack[1]['l'] = 0;
            $stack[1]['r'] = count($array)-1;

            do {
                $l = $stack[$cur]['l'];
                $r = $stack[$cur]['r'];
                $cur--;
                do {
                    $i   = $l;
                    $j   = $r;
                    $tmp = $array[(int) (($l+$r)/2)];

                    // split the array in to parts
                    // first: objects with "smaller" property $property
                    // second: objects with "bigger" property $property
                    do {
                        while ($array[$i]->{$property} < $tmp->{$property}) {
                            $i++;
                        } while ($tmp->{$property} < $array[$j]->{$property}) {
                            $j--;
                        }

                        // Swap elements of two parts if necesary
                        if ($i <= $j) {
                            $w         = $array[$i];
                            $array[$i] = $array[$j];
                            $array[$j] = $w;

                            $i++;
                            $j--;
                        }

                    } while ($i <= $j);

                    if ($i < $r) {
                        $cur++;
                        $stack[$cur]['l'] = $i;
                        $stack[$cur]['r'] = $r;
                    }
                    $r = $j;
                } while ($l < $r);
            } while ($cur != 0);
        }

        return $array;
    }

    /**
     * Gets the path of one file type by its ID
     *
     * @param int $contentID the id of the content we want to work with
     * @param boolean $ucfirst true if the contentID should be converted with ucfirst
     *
     * @return string the name of the content
     */
    public static function getFilePathFromId($contentID, $ucfirst = false)
    {
        // Raise an error if $contentID is not a number
        if (!is_numeric($contentID)) {
            // Try to uniformize this, cause if $contentID comes from an widget
            // this raises an error cause the contentID is 'Widget'
            // throw new InvalidArgumentException('getContentTypeNameFromId
            // function only accepts integers. Input was: '.$int);
            $returnValue = ($ucfirst === true)
                ? ucfirst($contentID) : strtolower($contentID);
        } else {
            // retrieve the name for this id
            $sql = "SELECT path FROM attachments "
                 . "WHERE `pk_attachment`=$contentID";
            $rs = $GLOBALS['application']->conn->Execute($sql);

            if ($rs->_numOfRows < 1) {
                $returnValue = false;
            } else {
                $returnValue = ($ucfirst === true)
                    ? ucfirst($rs->fields['path']) : $rs->fields['path'];
            }
        }

        return $returnValue;

    }

    /**
     * This function returns an array of objects $contentType of the most viewed
     * in the last few days indicated.
     *
     * @param string  $contentType type of content
     * @param boolean $notEmpty    If there are no results regarding the days
     *                             indicated, the query is performed on the
     *                             entire bd. For default is false
     * @param integer $category pk_content_category ok the contents. If value
     *                             is 0, then does not filter by categories. For
     *                             default is 0.
     * @param integer $author pk_author of the contnet. If value is 0, then
     *                             does not filter by categories. For default
     *                             is 0.
     * @param integer $days Interval of days on which the request takes
     *                             place. For default is 2.
     * @param integer $num Number of objects that the function returns.
     *                             For default is 8.
     * @param boolean $all Get all the content regardless of content
     *                             status.
     * @return array of objects $contentType
     */
    public function getMostViewedContent(
        $contentType,
        $notEmpty = false,
        $category = 0,
        $author = 0,
        $days = 2,
        $num = 9,
        $all = false
    ) {
        $em = getService('entity_repository');

        $date = new \DateTime();
        $date->sub(new \DateInterval('P' . $days . 'D'));
        $date = $date->format('Y-m-d H:i:s');

        $criteria = array(
            'join' => array(
                array(
                    'table'               => 'content_views',
                    'type'                => 'left',
                    'contents.pk_content' => array(
                        array(
                            'value' => 'content_views.pk_fk_content',
                            'field' => true
                        )
                    )
                )
            ),
            'content_type_name' => array(array('value' => $contentType)),
            'in_litter'         => array(array('value' => 0)),
            'starttime'         => array(array('value' => $date, 'operator' => '>=')),
            'endtime'           => array(
                'union' => 'OR',
                array('value' => '0000-00-00 00:00:00', 'operator' => '='),
                array('value' => $date, 'operator' => '>')
            ),
        );

        $order = array('content_views.views' => 'desc');

        if ($category) {
            $category = getService('category_repository')->find($category);

            if ($category) {
                $category = $category->name;
            }

            $criteria['category_name'] = array(array('value' => $category));
        }

        if ($author) {
            $criteria['fk_author'] = array(array('value' => $author));
        }

        if (!$all) {
            $criteria['content_status'] = array(array('value' => 1));
        }

        $contents = $em->findBy($criteria, $order, $num, 1);

        // Repeat without 'created' filter
        if (count($contents) == 0) {
            unset($criteria['starttime']);
            unset($criteria['endtime']);
            $contents = $em->findBy($criteria, $order, $num, 1);
        }

        return $contents;
    }

    /**
     * This function returns an array of objects $contentType of the most
     * commented in the last few days indicated.
     *
     * @param string  $contentType type of content
     * @param boolean $notEmpty    If there are no results regarding the days
     *                             indicated, the query is performed on the
     *                             entire bd. For default is false
     * @param integer $category pk_content_category ok the contents. If value
     *                             is 0, then does not filter by categories. For
     *                             default is 0.
     * @param integer $days Interval of days on which the consultation
     *                             takes place. For default is 2.
     * @param integer $maxElements Number of objects that the function returns.
     *                             For default is 8.
     * @param boolean $all Get all the content regardless of content
     *                             status and endtime.
     * @return array
     */
    public function getMostComentedContent(
        $contentType,
        $notEmpty = false,
        $category = 0,
        $days = 2,
        $maxElements = 9,
        $all = false
    ) {
        $this->init($contentType);

        $sql = "SELECT COUNT(comments.content_id) as num_comments, contents.*, articles.*
                FROM contents, comments, articles
                WHERE contents.pk_content = comments.content_id
                AND contents.pk_content = articles.pk_article
                AND contents.content_status=1
                AND starttime >= DATE_SUB(CURDATE(), INTERVAL $days DAY)
                GROUP BY contents.pk_content
                ORDER BY num_comments DESC, contents.starttime DESC
                LIMIT ?";

        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $GLOBALS['application']->conn->Execute($sql, array($maxElements));

        $contents = array();
        while (!$rs->EOF) {
            $content = new $contentType();
            $content->load($rs->fields);

            $contents []= $content;

            $rs->MoveNext();
        }

        $contentsArray = array();
        foreach ($contents as $item) {
            $contentsArray[$item->pk_content] = array(
                'pk_content' => $item->pk_content,
                'num'        => $item->num_comments,
                'title'      => $item->title,
                'permalink'  => $item->slug,
                'uri'        => $item->uri
            );
        }

        return $contentsArray;
    }

    /**
     * This function returns an array of objects $contentType of the most voted
     * in the last few days indicated.
     * Objects only have covered the fields pk_content, title, and total_value
     * total_votes
     *
     * @param string  $contentType type of content
     * @param boolean $not_empty   If there are no results regarding the days
     *                             indicated, the query is performed on the
     *                             entire bd. For default is false
     * @param integer $category pk_content_category ok the contents. If value
     *                             is 0, then does not filter by categories.
     *                             For default is 0.
     * @param integer $author pk_author of the contnet. If value is 0,
     *                             then does not filter by categories.
     *                             For default is 0.
     * @param integer $days Interval of days on which the consultation
     *                             takes place. For default is 2.
     * @param integer $num Number of objects that the function returns.
     *                             For default is 8.
     * @param boolean $all Get all the content regardless of content
     *                             status.
     * @return array the contents
     */
    public function getMostVotedContent(
        $contentType,
        $notEmpty = false,
        $category = 0,
        $author = 0,
        $days = 2,
        $num = 8,
        $all = false
    ) {
        $this->init($contentType);
        $items = array();

        $_tables = '`contents`, `' . $this->table . '`, `ratings` ';
        $_fields = ' * ';
        $_where = '`contents`.in_litter=0 ';
        if (!$all) {
            $_where .= ' AND `contents`.`content_status`=1 ';
        }

        $_days = 'AND  `contents`.starttime>=DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY) ';
        $_tables_relations = ' AND `contents`.pk_content=`' . $this->table . '`.pk_' . strtolower($contentType) .
                             ' AND `ratings`.pk_rating=`contents`.pk_content ';
        $_order_by = 'ORDER BY `contents`.`content_status` DESC, `ratings`.total_votes DESC ';
        $_limit = 'LIMIT '.$num;

        if (isset($author) && !is_null($author) && intval($author) > 0) {
            if ($contentType=='Opinion') {
                $_where .= 'AND `opinions`.fk_author='.$author.' ';
            } else {
                $_where .= 'AND `contents`.fk_author='.$author.' ';
            }
        }

        if (intval($category)>0) {
            $_tables .= ', `contents_categories` ';
            $_tables_relations .= ' AND  `contents_categories`.pk_fk_content = `contents`.pk_content ' .
                                  'AND `contents_categories`.pk_fk_content_category=' . $category . ' ';
        }

        $sql = 'SELECT ' . $_fields
             . ' FROM ' . $_tables
             . ' WHERE ' . $_where.$_days.$_tables_relations
             . $_order_by . $_limit;
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if ($rs->_numOfRows<=($num-3) && $notEmpty) {
            $sql = 'SELECT ' . $_fields
                 . ' FROM ' . $_tables
                 . ' WHERE ' . $_where . $_tables_relations
                 . $_order_by . $_limit;
            $rs = $GLOBALS['application']->conn->Execute($sql);
        }

        $items = $this->loadObject($rs, $contentType);

        return $items;
    }

     /**
     * This function returns an array of objects all types of the most viewed
     * in the last few days indicated.
     *
     * @param boolean $notEmpty If there are no results regarding the days
     *                           indicated, the query is performed on the
     *                           entire bd. For default is false
     * @param integer $category    pk_content_category ok the contents. If value
     *                             is 0, then does not filter by categories. (default 0)
     * @param integer $days Interval of days on which the consultation takes place. (default 2)
     * @param integer $num Number of objects that the function returns. (default 6)
     * @param boolean $all Get all the content regardless of content status
     *
     * @return array of objects
     */
    public function getAllMostViewed(
        $notEmpty = false,
        $category = 0,
        $days = 2,
        $num = 6,
        $all = false
    ) {
        $em = getService('entity_repository');

        $now = new \DateTime();
        $now = $now->format('Y-M-d H:i:s');

        $date = new \DateTime();
        $date->sub(new \DateInterval('P' . $days . 'D'));
        $date = $date->format('Y-m-d H:i:s');

        $criteria = array(
            'join' => array(
                array(
                    'table'               => 'content_views',
                    'type'                => 'left',
                    'contents.pk_content' => array(
                        array(
                            'value' => 'content_views.pk_fk_content',
                            'field' => true
                        )
                    )
                )
            ),
            'fk_content_type' => array(array('value' => array(1,3,4,7,9,11), 'operator' => 'IN')),
            'in_litter'       => array(array('value' => 0)),
            'starttime'       => array(array('value' => $date, 'operator' => '>=')),
            'endtime'         => array(
                'union' => 'OR',
                array('value' => '0000-00-00 00:00:00', 'operator' => '='),
                array('value' => $now, 'operator' => '>')
            ),
        );

        $order = array('content_views.views' => 'desc');

        if ($category) {
            $category = getService('category_repository')->find($category);

            if ($category) {
                $category = $category->name;
            }

            $criteria['category_name'] = array(array('value' => $category));
        }

        if (!$all) {
            $criteria['content_status'] = array(array('value' => 1));
        }

        $contents = $em->findBy($criteria, $order, $num, 1);

        // Repeat without 'created' filter
        if (count($contents) == 0) {
            unset($criteria['starttime']);
            unset($criteria['endtime']);
            $contents = $em->findBy($criteria, $order, $num, 1);
        }

        return $contents;
    }

    /**
     * Returns a list of suggested contents for homepage
     *
     * @return array instantiated elements suggested for homepage
     */
    public static function getSuggestedContentsForHomePage()
    {
        $cm       = new ContentManager();
        $contents = $cm->findAll(
            'Article',
            'content_status=1 AND content_status=1 AND frontpage=1'.
            ' AND in_home=2',
            'ORDER BY  created DESC,  title ASC '
        );

        return $contents;
    }

    /**
     * Filter content objects by starttime and endtime
     *
     * @see Content::isInTime()
     * @param array  $items Array of Content objects
     * @param string $time  Time filter, by default is now.
     *                      Syntax: 'YYYY-MM-DD HH:MM:SS'
     *
     * @return array Items filtered
    */
    public function getInTime($items, $time = null)
    {
        $filtered = array();
        if (is_array($items)) {
            foreach ($items as $item) {
                if (is_object($item)) {
                    if ($item->isInTime()) {
                        $filtered[] = $item;
                    }
                } else {
                    $starttime = (!empty($item['starttime']))
                        ? $item['starttime']: '0000-00-00 00:00:00';
                    $endtime   = (!empty($item['endtime']))
                        ? $item['endtime']: '0000-00-00 00:00:00';

                    if (Content::isInTime2($starttime, $endtime, $time)) {
                        $filtered[] = $item;
                    }
                }
            }
        }

        return $filtered;
    }

    /**
     * Filter content objects by  available and not inlitter.
     *
     * @param array $items Array of Content objects
     *
     * @return array Items filtered
     **/
    public function getAvailable($items)
    {
        $filtered = array();
        if (is_array($items)) {
            foreach ($items as $item) {
                if (is_object($item)) {
                    if (($item->content_status==1) && ($item->in_litter==0)) {
                        $filtered[] = $item;
                    }
                } else {
                    if (($item['content_status']==1) && ($item['in_litter']==0)) {
                        $filtered[] = $item;
                    }
                }
            }
        }

        return $filtered;
    }

    /**
     * Counts the available contents given a filter and a category.
     * If no category is provided it searches accross all the categories
     *
     * @param string $contentType the contentType to search for
     * @param string $filter the SQL WHERE sentence to filter contents with
     * @param int $pk_fk_content_category the category id to search for
     *
     * @return int the number of contents that match the filter
     */
    public function count(
        $contentType,
        $filter = null,
        $pk_fk_content_category = null
    ) {
        $this->init($contentType);

        $_where = '';
        if (!is_null($filter)) {
            if (($filter == ' `contents`.`in_litter`=1')
                || ($filter == 'in_litter=1')
            ) {
                $_where = ' AND '.$filter;
            } else {
                $_where .= ' AND '.$filter;
            }
        } else {
            $_where = 'AND in_litter=0';
        }

        if (intval($pk_fk_content_category) > 0) {
            $sql = 'SELECT COUNT(contents.pk_content) '
                 . 'FROM `contents_categories`, `contents`, ' . $this->table . '  '
                 . ' WHERE `contents_categories`.`pk_fk_content_category`='. $pk_fk_content_category
                 . '  AND pk_content=`'.$this->table. '`.`pk_'.$this->content_type
                 . '` AND  `contents_categories`.`pk_fk_content` = `contents`.`pk_content` '
                 . $_where;
        } else {
            $sql = 'SELECT COUNT(contents.pk_content) AS total '
                . 'FROM `contents`, `'.$this->table.'` '
                . 'WHERE `contents`.`pk_content`=`'.$this->table
                . '`.`pk_'.$this->content_type.'` '
                . $_where;
        }

        $rs = $GLOBALS['application']->conn->GetOne($sql);

        return $rs;
    }

    /**
     * Returns a tuple with the total count of contents that matches a filter and
     * and slice of that contents from one offset and with the number of elements
     * requested
     *
     * @param string $contentType the content type to search
     * @param int $categoryId the category id where to search the contents.
     *                        if null is provided it will search in all the categories
     * @param string $filter the SQL WHERE sentence to filter the contents
     * @param string $orderBy the ORDER By sentence
     * @param int $page the offset page where to start the slice
     * @param int $numElements the number of elements that the slice must have
     * @param boolean $debug if true the function will halt and print down the result
     *
     * @return  array a tuple with the count of contents that match the search and the slice
     **/
    public function getCountAndSlice(
        $contentType,
        $categoryId,
        $filter,
        $orderBy,
        $page = 1,
        $numElements = 10,
        $offset = 0,
        $debug = false
    ) {
        $this->init($contentType);

        if (empty($filter)) {
            $filterCount = ' contents.in_litter<>1';
            $filter = ' AND '. $filterCount;
        } else {
            $filterCount = $filter;
            $filter = ' AND '. $filter;
        }

        $countContents = $this->count($contentType, $filterCount, $categoryId);

        if ($page == 1) {
            $limit = $offset.', '.$numElements;
        } else {
            $limit = $offset+(($page-1)*$numElements).', '.$numElements;
        }

        if (intval($categoryId)>0) {
            $sql = 'SELECT * FROM contents_categories, contents, '.$this->table.' '
                 . ' WHERE `contents_categories`.`pk_fk_content_category`='.$categoryId
                 . ' AND `contents`.`pk_content`=`'.$this->table.'`.`pk_'.$this->content_type.'`'
                 . ' AND  `contents_categories`.`pk_fk_content` = `contents`.`pk_content` '
                 . $filter
                 . ' '
                 . $orderBy
                 . ' LIMIT '.$limit;
        } else {
            $sql = 'SELECT * FROM `contents`, `'.$this->table.'` '
                 . ' WHERE `contents`.`pk_content`=`'.$this->table.'`.`pk_'.$this->content_type.'` '
                 . $filter
                 . ' '
                 . $orderBy
                 . ' LIMIT '.$limit;
        }
        if ($debug == true) {
            var_dump($sql);
        }

        $rs = $GLOBALS['application']->conn->Execute($sql);

        $items = $this->loadObject($rs, $contentType);

        return array($countContents, $items);
    }

    /**
     * Returns a list of content objecst from a given category that matches a search criteria
     *
     * @param string $contentType the type of content to search for
     * @param string $pkFkContentCategory the id of the category where search for contents in
     * @param string $filter the SQL WHERE sentence to filter the contents
     * @param string $orderBy the ORDER BY sentence to sort the contents
     *
     * @return array a list of objects that matches the search criterias
     **/
    public function find_by_category(
        $contentType,
        $pkFkContentCategory,
        $filter = null,
        $orderBy = 'ORDER BY 1'
    ) {
        $this->init($contentType);

        $items = array();
        $_where = 'AND in_litter=0';

        if (!is_null($filter)) {
            //se busca desde la litter.php
            if ($filter == 'in_litter=1') {
                $_where = $filter;
            } else {
                $_where = ' in_litter=0 AND '.$filter;
            }
        }

        if (intval($pkFkContentCategory) > 0) {
            $sql = 'SELECT * FROM contents_categories, contents, '.$this->table.'  '
                 . 'WHERE '.$_where
                 . ' AND `contents_categories`.`pk_fk_content_category`='
                 . $pkFkContentCategory
                 . ' AND `contents`.`pk_content`=`' . $this->table . '`.`pk_'.$this->content_type
                 . '` AND  `contents_categories`.`pk_fk_content` = `contents`.`pk_content` '
                 . $orderBy;
        } else {
            return $items ;
        }

        $rs = $GLOBALS['application']->conn->Execute($sql);

        $items=$this->loadObject($rs, $contentType);

        return $items;
    }

    /**
     * Returns title, catName and slugs of last headlines from subcategories of a given category
     *
     * @return array a list of content information (not the object itself)
     **/
    public function findHeadlines()
    {
        $sql =
        'SELECT `contents`.`title`, `contents`.`pk_content` ,
               `contents`.`created` ,  `contents`.`slug` ,
               `contents`.`starttime` , `contents`.`endtime` ,
               `contents_categories`.`pk_fk_content_category` AS `category_id`
        FROM `contents`
        LEFT JOIN contents_categories
            ON (`contents`.`pk_content`=`contents_categories`.`pk_fk_content`)
        WHERE `contents`.`content_status` =1
            AND `contents`.`frontpage` =1
            AND `contents`.`fk_content_type` =1
            AND `contents`.`in_litter` =0
        ORDER BY `starttime` DESC ';

        $rs    = $GLOBALS['application']->conn->Execute($sql);
        $ccm   = ContentCategoryManager::get_instance();
        $items = array();
        while (!$rs->EOF) {
            $items[] = array(
                'title'          => $rs->fields['title'],
                'catName'        => $ccm->getName($rs->fields['category_id']),
                'slug'           => $rs->fields['slug'],
                'created'        => $rs->fields['created'],
                'category_title' =>
                    $ccm->getTitle($ccm->getName($rs->fields['category_id'])),
                'id'             => $rs->fields['pk_content'],

                /* to filter in getInTime() */
                'starttime'      => $rs->fields['starttime'],
                'endtime'        => $rs->fields['endtime']
            );

            $rs->MoveNext();
        }

        $items = $this->getInTime($items);

        return $items;
    }

    /**
     * Returns title, catName, slugs, dates and images of last headlines
     *
     * @param boolean $frontIncluded description not available
     *
     * @return array a list of content information (not the object itself)
     **/
    public function findHeadlinesWithImage($frontIncluded = false)
    {
        $sql =
        'SELECT `contents`.`title`, `contents`.`pk_content` ,
               `contents`.`created` ,  `contents`.`slug` ,
               `contents`.`starttime` , `contents`.`endtime` ,
               `articles`.`img1` , `articles`.`img2` ,
               `contents_categories`.`pk_fk_content_category` AS `category_id`
        FROM `contents`, contents_categories, articles
        WHERE `contents`.`pk_content`=`contents_categories`.`pk_fk_content`
            AND `contents`.`pk_content`=`articles`.`pk_article`
            AND `contents`.`content_status` =1
            AND `contents`.`fk_content_type` =1
            AND `contents`.`in_litter` =0
        ORDER BY `created` DESC LIMIT 400 ';

        $rs    = $GLOBALS['application']->conn->Execute($sql);
        $ccm   = ContentCategoryManager::get_instance();
        $items = array();
        while (!$rs->EOF) {

            if (!$frontIncluded) {
                $sqlAux = 'SELECT count(*) as num FROM content_positions WHERE pk_fk_content=? AND fk_category=0';
                $rsAux  = $GLOBALS['application']->conn->Execute($sqlAux, array($rs->fields['pk_content']));
            }
            if ($rsAux->fields['num'] <= 0 || $frontIncluded) {
                $items[] = array(
                    'title'          => $rs->fields['title'],
                    'catName'        => $ccm->getName($rs->fields['category_id']),
                    'slug'           => $rs->fields['slug'],
                    'created'        => $rs->fields['created'],
                    'category_title' => $ccm->getTitle($ccm->getName($rs->fields['category_id'])),
                    'id'             => $rs->fields['pk_content'],
                    'starttime'      => $rs->fields['starttime'],
                    'endtime'        => $rs->fields['endtime'],
                    'img1'           => $rs->fields['img1'],
                    'img2'           => $rs->fields['img2'],
                );
            }


            $rs->MoveNext();
        }

        $items = $this->getInTime($items);

        return $items;
    }

    /**
     * Returns the title, catName and slugs of last headlines from a given category
     *
     * @param string $filter the SQL WHERE sentence to filter the contents
     * @param string $orderBy the ORDER BY sentence to sort the contents
     *
     * @return the list of opinions
     **/
    public function getOpinionArticlesWithAuthorInfo(
        $filter = null,
        $orderBy = 'ORDER BY 1'
    ) {
        $items = array();
        $where = '1=1  AND in_litter=0';

        if (!is_null($filter)) {
            if ($filter == 'in_litter=1') {
                //se busca desde la litter.php
                $where = $filter;
            }

            $where = $filter.' AND in_litter=0';
        }
        // METER TB LEFT JOIN
        //necesita el as id para paginacion

        $sql =
            'SELECT contents.pk_content, contents.position, users.avatar_img_id,
                opinions.pk_opinion as id, users.name, users.bio, contents.title,
                contents.slug, opinions.type_opinion, contents.body,
                contents.changed, contents.created, contents.with_comment,
                contents.starttime, contents.endtime
            FROM contents, opinions
            LEFT JOIN users ON (users.id=opinions.fk_author)
            WHERE `contents`.`fk_content_type`=4
            AND contents.pk_content=opinions.pk_opinion
            AND '.$where.' '.$orderBy;

        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $GLOBALS['application']->conn->Execute($sql);

        $items = null;
        if (!empty($rs)) {
            $items = $rs->GetArray();
            foreach ($items as &$item) {
                $item['path_img'] = \Photo::getPhotoPath($item['avatar_img_id']);
            }
        }

        return $items ;
    }

    /**
     * Fetches available content types.
     *
     * @return array an array with each content type with id, name and title.
     */
    public static function getContentTypes()
    {
        $contentTypes = array(
            array(
                'pk_content_type' => 1,
                'name'            => 'article',
                'title'           => _('Article')
            ),
            array(
                'pk_content_type' => 2,
                'name'            => 'advertisement',
                'title'           => _('Advertisement')
            ),
            array(
                'pk_content_type' => 3,
                'name'            => 'attachment',
                'title'           => _('File')
            ),
            array(
                'pk_content_type' => 4,
                'name'            => 'opinion',
                'title'           => _('Opinion')
            ),
            array(
                'pk_content_type' => 5,
                'name'            => 'event',
                'title'           => _('Event')
            ),
            array(
                'pk_content_type' => 6,
                'name'            => 'comment',
                'title'           => _('Comment')
            ),
            array(
                'pk_content_type' => 7,
                'name'            => 'album',
                'title'           => _('Album')
            ),
            array(
                'pk_content_type' => 8,
                'name'            => 'photo',
                'title'           => _('Image')
            ),
            array(
                'pk_content_type' => 9,
                'name'            => 'video',
                'title'           => _('Video')
            ),
            array(
                'pk_content_type' => 10,
                'name'            => 'special',
                'title'           => _('Special')
            ),
            array(
                'pk_content_type' => 11,
                'name'            => 'poll',
                'title'           => _('Poll')
            ),
            array(
                'pk_content_type' => 12,
                'name'            => 'widget',
                'title'           => _('Widget')
            ),
            array(
                'pk_content_type' => 13,
                'name'            => 'static_page',
                'title'           => _('Static page')
            ),
            array(
                'pk_content_type' => 14,
                'name'            => 'kiosko',
                'title'           => _('Kiosko')
            ),
            array(
                'pk_content_type' => 15,
                'name'            => 'book',
                'title'           => _('Book')
            ),
            array(
                'pk_content_type' => 16,
                'name'            => 'schedule',
                'title'           => _('Agenda')
            ),
            array(
                'pk_content_type' => 17,
                'name'            => 'letter',
                'title'           => _('Letter to editor')
            ),
            array(
                'pk_content_type' => 18,
                'name'            => 'frontpage',
                'title'           => _('Frontpage')
            ),
        );

        return $contentTypes;
    }

    /**
     * Returns the id of a content type given its name.
     *
     * @param string $name the name of the content type
     *
     * @return int the content type id
     */
    public static function getContentTypeIdFromName($name)
    {
        $contenTypes = \ContentManager::getContentTypes();

        foreach ($contenTypes as $types) {
            if ($types['name'] == $name) {
                return $types['pk_content_type'];
            }
        }

        return false;
    }

    /**
     * Returns the user readable name of a content type given its id.
     *
     * @param int $id the id of the content type
     *
     * @return string the content type title
     */
    public static function getContentTypeTitleFromId($id)
    {
        $contenTypes = \ContentManager::getContentTypes();

        foreach ($contenTypes as $types) {
            if ($types['pk_content_type'] == $id) {
                return $types['title'];
            }
        }

        return false;
    }

    /**
     * Returns the name of a content type given its id.
     *
     * @param int $id the content type id
     * @param string $ucfirst whether to apply the ucfirst function
     *
     * @return string the content type name
     **/
    public static function getContentTypeNameFromId($id, $ucfirst = false)
    {
        if (empty($id)) {
            return false;
        }

        if (!is_numeric($id)) {
            $name = ($ucfirst === true) ? ucfirst($id) : strtolower($id);
        } else {
            $contentTypes = \ContentManager::getContentTypes();
            foreach ($contentTypes as $types) {
                if ($types['pk_content_type'] == $id) {

                    $name = ($ucfirst === true) ? ucfirst($types['name']) : $types['name'];

                    return $name;
                }
            }
        }

        return false;
    }

    /**
    * Returns a bidimensional array with properties of articles
    * from one category.
    *
    * This function is highly optimized for fast quering. Best suitable for
    * Sitemap generation and RSS
    *
    * @param int $categoryID the ID of the category to search from
    * @param string $filter the SQL WHERE sentence to filter contents
    * @param string $orderBy the ORDER BY sentence to order contents
    * @param int $limit the number of contents to retrieve
    *
    * @return mixed array with values of articles.
    */
    public function getArrayOfArticlesInCategory(
        $categoryID,
        $filter = null,
        $orderBy = 'ORDER BY 1',
        $limit = 50
    ) {
        $items  = array();
        $where = '1=1  AND in_litter=0';

        if (!is_null($filter)) {
            if ($filter == 'in_litter=1') {
                $where = $filter;
            }

            $where = $filter . ' AND in_litter=0';
        }

        $sql =  'SELECT contents.pk_content, contents.title, contents.slug, '
            .'      contents_categories.catName, contents.created,'
            .'      contents.changed, contents.params, '
            .'      contents.metadata, contents.starttime, contents.endtime '
            .'FROM  contents, contents_categories '
            .'WHERE contents.pk_content = contents_categories.pk_fk_content '
            .'      AND contents_categories.pk_fk_content_category=?'
            .'      AND '.$where.' '.$orderBy . ' LIMIT '.$limit;

        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $GLOBALS['application']->conn->Execute($sql, array($categoryID));

        $items = $rs->GetArray();

        return $items;
    }

    /**
      * Get authors for sitemap XML
      *
      * @param string $filter the SQL WHERE sentence to filter the contents
      * @param string $orderBy the ORDER BY sentence to order the contents
      *
      * @return array the list of outhors
     */
    public function getOpinionAuthorsPermalinks(
        $filter = null,
        $orderBy = 'ORDER BY 1'
    ) {
        $items = array();
        $_where = '1=1  AND in_litter=0';

        if (!is_null($filter)) {
            if ($filter == 'in_litter=1') {
                //se busca desde la litter.php
                $_where = $filter;
            }

            $_where = $filter.' AND in_litter=0';
        }

        // METER TB LEFT JOIN
        //necesita el as id para paginacion

         $sql= 'SELECT contents.pk_content as id, contents.title, users.name,
                       contents.metadata, contents.slug, contents.changed,
                       contents.starttime, contents.endtime
                FROM contents, opinions
                LEFT JOIN users
                    ON (users.id=opinions.fk_author)
                WHERE `contents`.`fk_content_type`=4
                AND contents.pk_content=opinions.pk_opinion
                AND '.$_where.' '
                .$orderBy;

        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs    = $GLOBALS['application']->conn->Execute($sql);

        $items = $rs->GetArray();

        return $items;
    }

    /**
     * Returns the name of a category for a content given its id
     *
     * @param int $contentId the id of the content
     *
     * @return string the category name
     **/
    public function getCategoryNameByContentId($contentId)
    {
        $sql = 'SELECT pk_fk_content_category, catName FROM `contents_categories` '
             . 'WHERE pk_fk_content = ?';
        $rs  = $GLOBALS['application']->conn->Execute($sql, array($contentId));

        if (!$rs) {
            return false;
        }

        return $rs->fields['catName'];
    }

    /**
     * Returns the content objects from a list of content ids
     *
     * @param array $contentIds list of content ids to fetch
     *
     * @return array the list of content objecst
     **/
    public function getContents($contentIds)
    {
        $contents = array();
        $content = new Content();
        if (is_array($contentIds) && count($contentIds) > 0) {
            foreach ($contentIds as $contentId) {
                if ($contentId <= 0) {
                    continue;
                }

                $content = \Content::get($contentId);
                if (isset($content->pk_content) && $content->pk_content == $contentId) {
                    $contents []= $content;
                }
            }
        }

        return $contents;
    }

    /**
     * Returns an array of image objects given an array/unique_id  of image
     *
     * @param array $relatedImagesIDs the list of content ids to fetch
     *
     * @return array the list of images
     **/
    public static function getRelatedImagesForContentsWithIDs($relatedImagesIDs)
    {
        // If the given ids is an unique element transform it to an array.
        if (!is_array($relatedImagesIDs)
            && !empty($relatedImagesIDs)
        ) {
            $relatedImagesIDs = array($relatedImagesIDs);
        }

        // If the related images id array is empty just return an empty array
        if (!(count($relatedImagesIDs) > 0)) {
            return array();
        }

        // Fetch the images from SQL
        $relatedImagesSQL = implode(',', $relatedImagesIDs);
        $cm               = new ContentManager();
        $images = $cm->find('Photo', "pk_content IN ($relatedImagesSQL)");

        return $images;
    }

    /**
     * Returns an array of related contents for one content given its id
     *
     * @param int $contentID the id of the content to get its related content
     *
     * @return array list of related content
     **/
    public function getRelatedContentFromContentID($contentID)
    {
        $rc  = new RelatedContent();
        $ccm = new ContentCategoryManager();

        $relatedContentIDs = $rc->getRelations($contentID);
        $relatedContent = array();
        foreach ($relatedContentIDs as $contentID) {
            $content = new Content($contentID);
            // Filter by scheduled {{{
            if ($content->isInTime()
                && $content->content_status == 1
                && $content->in_litter == 0
            ) {
                $content->category_name = $ccm->getName($content->category);
                $relatedContent[] = $content;
            }
            // }}}
        }

        return $relatedContent;
    }

    /**
    * Fetches all the contents (articles, widgets, etc) for one specific
    * category with its placeholder and position
    *
    * This is used for HomePages, fetches all the contents assigned for it and
    * allows to render an entire homepage
    *
    * @param string $date the date to fetch contents when
    * @param int $categoryID category id we want to get contents from
    *
    * @return null|array array of contents
    */
    public function getContentsForLibrary($date, $categoryID = 0)
    {
        if (empty($date)) {
            return false;
        }

        $where ='';
        if (!empty($categoryID)) {
            $where = ' AND pk_fk_content_category = '.$categoryID;
        }

        $sql = 'SELECT content_type_name, pk_content FROM contents, contents_categories '
              .'WHERE fk_content_type IN (1,4,7,9) '
              .'AND DATE(starttime) = "'.$date.'" '
              .'AND content_status=1 AND in_litter=0 '
              .'AND pk_fk_content = pk_content '.$where
              .' ORDER BY  fk_content_type ASC, starttime DESC ';

        $rs = $GLOBALS['application']->conn->GetAll($sql);

        if ($rs == false) {
            return false;
        }

        $ids = array();
        foreach ($rs as $item) {
            $ids[] = array($item['content_type_name'], $item['pk_content']);
        }

        // Get contents from repository
        $er = getService('entity_repository');
        $contents = $er->findMulti($ids);

        // Fetch video or image for article and opinions
        foreach ($contents as $content) {
            if (!empty($content->fk_video)) {
                $content->video = $er->find('Video', $content->fk_video);
            } elseif (!empty($content->img1)) {
                $content->image = $er->find('Photo', $content->img1);
            }
        }

        return $contents;

    }

    /**
    * Fetches the content for one specific url
    *
    * This is used for getting information from Onm Rest Api
    *
    * @param $url the url we want to get contents from
    *
    * @param $decodeJson if true apply json_decode before return content
    *
    * @return false | the content retrieved by the url
    */
    public function getUrlContent($url, $decodeJson = false)
    {
        global $kernel;
        $cache = $kernel->getContainer()->get('cache');

        $externalContent = $cache->fetch(CACHE_PREFIX.$url);
        if (!$externalContent) {
            $c  = curl_init($url);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
            $externalContent = curl_exec($c);
            $cache->save(CACHE_PREFIX.$url, $externalContent, 300);
            curl_close($c);
        }

        if ($decodeJson) {
            $content = json_decode($externalContent);
        } else {
            $content = $externalContent;
        }

        return $content;
    }

    /**
     * Fetches the latest n articles commented
     *
     * @param int $count the number of comments to fetch
     *
     * @return array the list of comment objects
     **/
    public function getLatestComments($count = 6)
    {
        $contents = array();

        $sql = 'SELECT DISTINCT comments.content_id,
                       contents.*,
                       comments.body as comment_body, comments.author as comment_author, comments.id as comment_id
                FROM  contents, comments
                WHERE contents.fk_content_type = 1
                  AND contents.in_litter <> 1
                  AND comments.status = ?
                  AND contents.pk_content = comments.content_id
                GROUP BY contents.pk_content
                ORDER BY comments.date DESC
                LIMIT ?';

        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $GLOBALS['application']->conn->Execute($sql, array(\Comment::STATUS_ACCEPTED, $count));

        while (!$rs->EOF) {
            $content = new \Article();
            $content->load($rs->fields);
            $content->comment        =  $rs->fields['comment_body'];
            $content->pk_comment     =  $rs->fields['comment_id'];
            $content->comment_author =  $rs->fields['comment_author'];

            $contents[$content->pk_comment] = $content;
            $rs->MoveNext();
        }

        $rs->Close(); # optional

        return $contents;
    }

    /**
     * Helper function to check existance one element in translation_ids table
     *
     * @param string $content_type the content type to search for
     * @param string $content_id the content id to get
     *
     * @return Content the content of type $content_type and id $content_id
     */
    public static function getOriginalIDForContentTypeAndID($content_type, $content_id)
    {
        $sql = 'SELECT * FROM `translation_ids` WHERE `pk_content_old`=? AND type=? LIMIT 1';

        $_values = array($content_id, $content_type);
        $_sql = $GLOBALS['application']->conn->Prepare($sql);
        $rss = $GLOBALS['application']->conn->Execute($_sql, $_values);

        if (!$rss) {
            $returnValue = false;
        } else {
            if ($rss->_numOfRows > 0) {

                $returnValue =  $rss->fields['pk_content'];

            } else {
                $returnValue = false;
            }
        }

        return $returnValue;

    }


    /**
     * Returns the original ID and content type for a given content id
     *
     * @param string $content_id the content id to get
     *
     * @return array ($content_type and id $content_id)
     */
    public static function getOriginalIdAndContentTypeFromID($content_id)
    {
        $sql = 'SELECT * FROM `translation_ids` WHERE `pk_content_old`=? LIMIT 1';

        $_values = $content_id;
        $_sql = $GLOBALS['application']->conn->Prepare($sql);
        $rss = $GLOBALS['application']->conn->Execute($_sql, $_values);

        if (!$rss) {
            $returnValue = false;
        } else {
            if ($rss->_numOfRows > 0) {
                $returnValue =  array($rss->fields['type'], $rss->fields['pk_content']);

            } else {
                $returnValue = false;
            }
        }

        return $returnValue;
    }

    /**
     * Returns the original ID and content type for a given content slug
     *
     * @param string $slug the slug of the content
     *
     * @return array ($content_type and id $content_id)
     */
    public static function getOriginalIdAndContentTypeFromSlug($slug)
    {
        $sql = 'SELECT * FROM `translation_ids` WHERE `slug`=? LIMIT 1';

        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rss = $GLOBALS['application']->conn->Execute($sql, array($slug));

        if (!$rss) {
            $returnValue = false;
        } else {
            if ($rss->_numOfRows > 0) {
                $returnValue =  array($rss->fields['type'], $rss->fields['pk_content']);

            } else {
                $returnValue = false;
            }
        }

        return $returnValue;
    }



    /**
     * Check if content id exists
     *
     * @param string $oldID the content id to check
     *
     * @return pk_content or false
    */
    public static function searchContentID($oldID)
    {
        $sql       = "SELECT pk_content FROM `contents` WHERE pk_content = ?";
        $value     = array($oldID);
        $contentID = $GLOBALS['application']->conn->GetOne($sql, $value);

        return $contentID;
    }

     /**
     *  Search id in refactor_id table. (used for translate old format ids)
     *
     * @param string $oldID Old id created with mktime
     *
     * @return int id in table refactor_id or false
     *
     */

    public static function searchInRefactorID($oldID)
    {
        $sql = "SELECT pk_content FROM `refactor_ids` "
             . "WHERE pk_content_old = ?";
        $value  = array($oldID);
        $refactorID = $GLOBALS['application']->conn->GetOne($sql, $value);

        if (!empty($refactorID)) {
            $content = new Content($refactorID);
            $content = $content->get($refactorID);

            forward301('/'.$content->uri);
        }

        return $oldID;
    }

    /**
     * Clean id and search if exist in content table.
     * If not found search in refactor_id table. (used for translate old format ids)
     *
     * @param string $dirtyID Vble with date in first 14 digits
     *
     * @return int id in table content or forward to 404
     *
     */
    public static function resolveID($dirtyID)
    {
        $contentID = 0;
        if (!empty($dirtyID)) {
            if (preg_match('@tribuna@', INSTANCE_UNIQUE_NAME)
                || preg_match('@retrincos@', INSTANCE_UNIQUE_NAME)
                || preg_match('@cronicas@', INSTANCE_UNIQUE_NAME)
            ) {
                $contentID = self::searchInRefactorID($dirtyID);
            }

            preg_match("@(?P<dirtythings>\d{1,14})(?P<digit>\d+)@", $dirtyID, $matches);
            $contentID = self::searchContentID((int) $matches["digit"]);
        }

        return $contentID;
    }

    /**
     * Checks and cleans articles and opinions from frontpage when the frontpage
     * limit is reached.
     *
     * @param  array $contentIds The array of contents to check.
     *
     * @return array The array of cleaned contents.
     */
    public function checkAndCleanFrontpageSize($contentIds)
    {
        $elementsToRemove = count($contentIds) - self::$frontpage_limit;

        // Remove first from placeholder_0_0
        if ($elementsToRemove > 0) {
            getService('session')->getFlashBag()->add(
                'error',
                _('Some elements were removed because this frontpage had too many contents.')
            );

            $contentIds = array_filter(
                $contentIds,
                function ($content) use (&$elementsToRemove) {
                    if ($elementsToRemove > 0
                        && $content['placeholder'] === 'placeholder_0_0'
                        && ($content['content_type'] === 'Article'
                            || $content['content_type'] === 'Opinion')
                    ) {
                        $elementsToRemove--;
                        return false;
                    }

                    return true;
                }
            );
        }

        // Remove contents from the end of the array
        if ($elementsToRemove > 0) {
            // Sort by placeholder
            usort($contentIds, function ($a, $b) {
                return strcmp($b['content_id'], $a['content_id']);
            });

            $i = count($contentIds) - 1;
            while ($i > 0 && $elementsToRemove > 0) {
                if ($contentIds[$i]['content_type'] === 'Article'
                    || $contentIds[$i]['content_type'] === 'Opinion'
                ) {
                    unset($contentIds[$i]);
                    $elementsToRemove--;
                }

                $i--;
            }
        }

        return $contentIds;
    }
}
