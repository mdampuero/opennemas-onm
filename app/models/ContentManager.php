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
     * @param [type] [varname] [description]
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
             . 'WHERE '.$_where
             . ' AND `contents`.`pk_content`= `'.$this->table
             . '`.`pk_'.$this->content_type.'` '
             . $orderBy;

        $rs = $GLOBALS['application']->conn->Execute($sql);
        $items = $this->loadObject($rs, $contentType);

        return $items;
    }

    /**
     * Old function name of ContentManager::findAll()
     *
     * @param string $contentType the content type to search for
     * @param string $filter the SQL WHERE sentence to filter down contents
     * @param string $orderBy the ORDER BY sentence
     * @param string $fields the list of fields to fetch
     *
     * @deprecated deprecated since version 1.0
     * @see ContentManager::findAll()
     **/
    public function find_all(
        $contentType,
        $filter = null,
        $orderBy = 'ORDER BY 1',
        $fields = '*'
    ) {
        return $this->findAll($contentType, $filter, $orderBy, $fields);
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

        // Fetch the id, placeholder, position, and content_type
        // in this category's frontpage
        // The second parameter is the id for the homepage category
        $contentIds = $this->getContentIdsInHomePageWithIDs(
            array((int) $categoryID, 0)
        );

        $contentsInFrontpage = array_unique(
            array_map(
                function (
                    $content
                ) {
                    if ($content['frontpage_id'] == 0) {
                        return $content['content_id'];
                    } else {
                        return null;
                    }
                },
                $contentIds
            )
        );


        if (is_array($contentIds) && count($contentIds) > 0) {

            // iterate over all found contents and initialize them
            foreach ($contentIds as $element) {
                // Only add elements for the requested category id
                if ($element['frontpage_id'] != $categoryID) {
                    continue;
                }

                $content = new $element['content_type']($element['content_id']);

                // add all the additional properties related with positions
                // and params
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
                    if (in_array($element['content_id'], $contentsInFrontpage)) {
                        $content->in_frontpage = true;
                    } else {
                        $content->in_frontpage = false;
                    }
                    if (\Onm\Module\ModuleManager::isActivated('AVANCED_FRONTPAGE_MANAGER')) {
                        $content->loadAllContentProperties();
                    }
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

        if (count($categories) > 0) {
            $categoriesSQL = implode(', ', $categories);
            $sql = 'SELECT * FROM content_positions '
              .'WHERE `fk_category` IN ('.$categoriesSQL.') '
              .'ORDER BY position ASC ';


            // Fetch the id, placeholder, position, and content_type
            // in this category's frontpage
            $rs = $GLOBALS['application']->conn->Execute($sql);

            while (!$rs->EOF) {
                $contents []= array(
                    'content_id'   => $rs->fields['pk_fk_content'],
                    'frontpage_id' => $rs->fields['fk_category'],
                    'position'     => $rs->fields['position'],
                    'placeholder'  => $rs->fields['placeholder'],
                    'params'       => unserialize($rs->fields['params']),
                    'content_type' => $rs->fields['content_type'],
                );

                $rs->MoveNext();
            }
        }

        // Return the ids array
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

        // iterate over all found contents and initialize them
        foreach ($contentsArray as $element) {
            $content = new $element['content_type']($element['id']);

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

        // Starting the Transaction
        $GLOBALS['application']->conn->StartTrans();

        // Clean all the contents for this category after insert the new ones
        $clean = ContentManager::clearContentPositionsForHomePageOfCategory(
            $categoryID
        );
        if (!$clean) {
            return false;
        }
        $positions = array();
        $contentIds = array();

        if (count($elements) > 0) {
            // Foreach element setup the sql values statement part
            foreach ($elements as $element) {
                $positions[] = array(
                    $element['id'],
                    $categoryID,
                    $element['position'],
                    $element['placeholder'],
                    $element['content_type'],
                );
                $contentIds []= $element['id'];
            }

            // construct the final sql statement and execute it
            $stmt = 'INSERT INTO content_positions (pk_fk_content, fk_category,'
                  . ' position, placeholder, content_type) '
                  . 'VALUES (?,?,?,?,?)';

            $sqlPrep = $GLOBALS['application']->conn->Prepare($stmt);

            $rs = $GLOBALS['application']->conn->Execute($sqlPrep, $positions);


            // Handling if there were some errors into the execution
            if (!$rs) {
                Application::logDatabaseError();
                $returnValue = false;
            } else {
                // Unset suggested flag if saving content positions in frontpage
                if ($categoryID == 0) {
                    self::dropSuggestedFlagFromContentIdsArray($contentIds);
                }
                $returnValue = true;
            }
        }

        // Finishing transaction
        $GLOBALS['application']->conn->CompleteTrans();

        return $returnValue;
    }

    /**
     * Drops the suggested to frontpage flag from a list of contents given their ids
     *
     * @param array $contentIds the list of content ids to drop the suggested flag
     *
     * @return boolean true if all went well
     **/
    public static function dropSuggestedFlagFromContentIdsArray($contentIds)
    {
        if (is_array($contentIds) && (count($contentIds) > 0)) {
            $contentIdsSQL = implode(', ', $contentIds);

            $sql = 'UPDATE contents '
                 . 'SET `frontpage`=0, `fk_user_last_editor`=?, `changed`=? '
                 . 'WHERE `pk_content` IN ('.$contentIdsSQL.')';
            $values = array($_SESSION['userid'], date("Y-m-d H:i:s"));
            $stmt = $GLOBALS['application']->conn->Prepare($sql, $values);

            if ($GLOBALS['application']->conn->Execute($stmt, $values) === false) {
                Application::logDatabaseError();

                return false;
            }

            /* Notice log of this action */
            $logger = Application::getLogger();
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
        $categoryID
    ) {
        // clean actual contents for the homepage of this category
        $sql = 'DELETE FROM content_positions '
              .'WHERE `fk_category`='.$categoryID;
        $rs = $GLOBALS['application']->conn->Execute($sql);

        // return the value and log if there were errors
        if (!$rs) {
            Application::logDatabaseError();
            $returnValue = false;
        } else {
            $returnValue = true;
        }

        return $returnValue;
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
     * Gets the name of one content type by its ID
     *
     * @param int $contentID the id of the content we want to work with
     * @param boolean $ucfirst true if the contentID should be converted with ucfirst
     *
     * @return string the name of the content
     */
    public static function getContentTypeNameFromId(
        $contentID,
        $ucfirst = false
    ) {
        // Raise an error if $contentID is not a number
        if (!is_numeric($contentID)) {
            // Try to uniformize this, cause if $contentID comes from an widget
            // this raises an error cause the contentID is 'Widget'
            // throw new InvalidArgumentException('getContentTypeNameFromId
            // function only accepts integers. Input was: '.$int);
            $return_value = ($ucfirst === true)
                ? ucfirst($contentID) : strtolower($contentID);
        } else {

            // retrieve the name for this id
            $sql = "SELECT name FROM content_types "
                 . "WHERE `pk_content_type`=$contentID";
            $rs = $GLOBALS['application']->conn->Execute($sql);

            if ($rs->_numOfRows < 1) {
                $return_value = false;
            } else {
                $return_value = ($ucfirst === true)
                    ? ucfirst($rs->fields['name']) : $rs->fields['name'];
            }
        }

        return $return_value;

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
        $this->init($contentType);

        $items   = array();
        $_tables = '`contents`, `'.$this->table.'` ';
        $_where  = '`contents`.`in_litter`=0 ';
        if (!$all) {
            //  $_where .= 'AND `contents`.`content_status`=1
            //  AND `contents`.`available`=1 ';
            $_where .= '  AND `contents`.`available`=1 ';
        }
        $_days     = 'AND  `contents`.`created`>=DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY) ';
        $_order_by = 'ORDER BY `contents`.`content_status` DESC, `contents`.`views` DESC LIMIT '.$num;

        if (intval($category) > 0) {
            $_category = 'AND pk_fk_content_category='.$category
            .'  AND  `contents_categories`.`pk_fk_content` = `contents`.`pk_content` ';
            $_tables   .= ', `contents_categories` ';
        } else {
            $_category = '';
        }

        if (intval($author) > 0) {
            if ($contentType=='Opinion') {
                $_author = 'AND `opinions`.`fk_author`='.$author.' ';
            } else {
                $_author = 'AND `opinions`.`fk_author`='.$author.' ';
            }
        } else {
            $_author = '';
        }

        $sql = 'SELECT * FROM '.$_tables
             . 'WHERE '.$_where.$_category.$_author.$_days
             . ' AND `contents`.`pk_content`=`pk_'.$this->content_type.'` '
             . $_order_by;

        $rs = $GLOBALS['application']->conn->Execute($sql);

        if ($rs->_numOfRows<$num && $notEmpty) {
            $sql = 'SELECT * FROM '.$_tables
                 . 'WHERE '.$_where.$_category.$_author
                 . ' AND `contents`.`pk_content`=`pk_'.$this->content_type.'` '
                 . $_order_by;
            $rs = $GLOBALS['application']->conn->Execute($sql);
        }

        $items = $this->loadObject($rs, $contentType);

        return $this->getInTime($items);
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
     * @param integer $num Number of objects that the function returns.
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
        $num = 9,
        $all = false
    ) {
        $this->init($contentType);
        $items = array();

        $_where_slave = ' 1=1 ';
        $_days = 'AND created>=DATE_SUB(CURDATE(), INTERVAL ' .$days.' DAY) ';
        if (!$all) {
            $_where_slave = ' available=1 ';
            $_days = 'AND created>=DATE_SUB(CURDATE(), INTERVAL '.$days.' DAY) ';
        }

        $_comented = 'AND pk_content IN (SELECT DISTINCT(fk_content) FROM comments) ';
        $_limit = 'LIMIT '.$num;

        if (intval($category)>0) {

            $pks = $this->find_by_category(
                $contentType,
                $category,
                $_where_slave.$_days.$_comented
            );
            if (!$all) {
                $pks = $this->getInTime($pks);
            }

            if (count($pks)<$num && $notEmpty) {
                // En caso de que existan menos de 6 contenidos,
                // lo hace referente a los 200 últimos contenidos
                $pks = $this->find_by_category(
                    $contentType,
                    $category,
                    $_where_slave.$_comented,
                    'ORDER BY `contents`.`content_status` DESC, created DESC LIMIT 200',
                    'pk_content, starttime, endtime'
                );
                if (!$all) {
                    $pks = $this->getInTime($pks);
                }
            }
        } else {
            $pks = $this->find(
                $contentType,
                $_where_slave.$_days.$_comented,
                null,
                'pk_content, starttime, endtime'
            );

            if (!$all) {
                $pks = $this->getInTime($pks);
            }

            if (count($pks)< $num && $notEmpty) {
                // En caso de que existan menos de $num contenidos,
                // lo hace referente a los 200 últimos contenidos
                $pks = $this->getInTime(
                    $this->find(
                        $contentType,
                        $_where_slave.$_comented,
                        'ORDER BY created DESC LIMIT 200',
                        'pk_content, starttime, endtime'
                    )
                );
                if (!$all) {
                    $pks = $this->getInTime($pks);
                }
                array_splice($pks, $num);
            }
        }

        $pk_list = '';
        foreach ($pks as $pk) {
            $pk_list .= ' '.$pk->id.',';
        }
        if (strlen($pk_list)==0) {
            return array();
        }
        $pk_list = substr($pk_list, 0, strlen($pk_list)-1);

        $comments = $this->find(
            'Comment',
            'available=1 AND fk_content IN ('.$pk_list.')',
            ' GROUP BY fk_content ORDER BY num DESC LIMIT 0 , 80',
            ' fk_content, count(pk_comment) AS num'
        );

        $pk_list = '';
        foreach ($comments as $comment) {
            $pk_list .= ' '.$comment->fk_content.',';
        }

        if (strlen($pk_list)==0) {
            return array();
        }

        $pk_list = substr($pk_list, 0, strlen($pk_list)-1);
        $items = $this->find(
            $contentType,
            'pk_content IN('.$pk_list.')',
            $_limit,
            '`contents`.`pk_content`, `contents`.`title`, `contents`.`slug`'
        );
        if (empty($items)) {
            return array();
        }

        foreach ($items as $item) {
            $articles[$item->pk_content] = array(
                'pk_content' => $item->pk_content,
                'num'        => 0,
                'title'      => $item->title,
                'permalink'  => $item->slug,
                'uri'        => $item->uri
            );
        }

        foreach ($comments as $comment) {
            if (array_key_exists($comment->fk_content, $articles)) {
                $articles[$comment->fk_content]['num'] = $comment->num;
            }
        }

        uasort(
            $articles,
            function (
                $a,
                $b
            ) {
                if ($a['num'] == $b['num']) {
                    return 0;
                }

                return ($a['num'] > $b['num']) ? -1 : 1;
            }
        );

        return $articles;
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
        $not_empty = false,
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
            $_where .= ' AND `contents`.`available`=1 ';
        }

        $_days = 'AND  `contents`.created>=DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY) ';
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

        if ($rs->_numOfRows<=($num-3) && $not_empty) {
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
     * Returns an array of objects $contentType an the last comment.
     *
     * @param string  $contentType content type to fetch
     * @param boolean $not_empty   If there are no results regarding the days
     *                             indicated, the query is performed on the
     *                             entire bd. For default is false
     * @param integer $category    pk_content_category ok the contents. If value
     *                             is 0, then does not filter by categories. (default is 0)
     * @param integer $num Number of objects that the function returns. (default is 6)
     * @param boolean $all Get all the content regardless of content status and endtime.
     * @return array
     */
    public function getLastComentsContent(
        $contentType,
        $not_empty = false,
        $category = 0,
        $num = 6,
        $all = false
    ) {
        $this->init($contentType);
        $items = array();

        $comments = $this->find(
            'Comment',
            'available=1 ',
            ' GROUP BY fk_content ORDER BY changed DESC LIMIT 0 , 50'
        );

        $pk_list = '';
        $pk_comment_list ='';
        foreach ($comments as $comment) {
            $pk_list .= ' '.$comment->fk_content.',';
            $pk_comment_list .= ' '.$comment->pk_content.',';
        }

        if (strlen($pk_list)==0) {
            return array();
        }

        $pk_list = substr($pk_list, 0, strlen($pk_list)-1);
        $pk_comment_list = substr($pk_comment_list, 0, strlen($pk_comment_list)-1);

        $items = $this->find(
            $contentType,
            'pk_content IN('.$pk_list.')',
            '',
            '`contents`.`pk_content`, `contents`.`title`, `contents`.`slug`'
        );
        if (empty($items)) {
            return array();
        }

        $sql = 'SELECT * FROM `contents` '
             . 'WHERE `pk_content` IN ('.$pk_comment_list.')';
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            Application::logDatabaseError();

            return false;
        } else {
            while (!$rs->EOF) {
                $contents []= $rs->fields;
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }

        $filter    = (isset($filter)) ? $filter : "";
        $_order_by = (isset($_order_by)) ? $_order_by : "";
        $comment_title = $this->find($contentType, $filter, $_order_by);
        foreach ($items as $item) {
            $articles[$item->pk_content] = array(
                'pk_content'    =>$item->pk_content,
                'comment'       =>'',
                'title'         =>$item->title,
                'slug'          =>$item->slug,
                'pk_comment'    =>'',
                'author'        =>'',
                'comment_title' =>''
            );
        }

        foreach ($comments as $comment) {
            if (array_key_exists($comment->fk_content, $articles)) {
                $index = $comment->fk_content;
                foreach ($contents as $cont) {
                    if ($cont[0] == $index) {
                        $articles[$index]['comment_title'] = $cont['title'];
                    }
                }

                $articles[$index]['comment']    = $comment->body;
                $articles[$index]['pk_comment'] = $comment->pk_comment;
                $articles[$index]['author']     = $comment->author;
            }
        }

        return $articles;
    }

    /**
     * Fetches the latest n comments done in the application
     *
     * @param int $num the number of comments to fetch
     *
     * @return array the list of comment objects
     **/
    public function getLatestComments($num = 6)
    {
        $contents = array();

        $sql = 'SELECT *
                FROM contents
                WHERE contents.fk_content_type=1 AND contents.pk_content IN
                      (SELECT fk_content
                       FROM `comments`,contents
                       WHERE comments.pk_comment = contents.pk_content
                       AND contents.available = 1
                       AND contents.content_status = 1
                       ORDER BY pk_comment DESC)
                ORDER BY contents.created DESC
                LIMIT '. $num;

        $latestCommenteSQL = $GLOBALS['application']->conn->Prepare($sql);
        $rs = $GLOBALS['application']->conn->Execute($latestCommenteSQL);
        if (!$rs) {
            \Application::logDatabaseError();
        } else {
            while (!$rs->EOF) {
                $contents []= $rs->fields['pk_content'];
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }

        return $contents;
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
        $items = array();
        $_tables = '`contents`  ';
        $_where = '`contents`.`in_litter`=0 AND `fk_content_type` IN (1,3,4,7,9,11) ';
        if (!$all) {
            $_where .= 'AND `contents`.`content_status`=1 AND `contents`.`available`=1 ';
        }
        $_days = 'AND  `contents`.`changed`>=DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY) ';
        $_order_by = 'ORDER BY `contents`.`views` DESC LIMIT 0 , '.$num;

        if (intval($category) > 0) {
            $_category = 'AND pk_fk_content_category='.$category
                .'  AND  `contents_categories`.`pk_fk_content` = `contents`.`pk_content` ';
            $_tables .= ', `contents_categories` ';
        } else {
            $_category = '';
        }

        $sql = 'SELECT * FROM '.$_tables .
                'WHERE '.$_where.$_category.$_days.
                $_order_by;
        $rs  = $GLOBALS['application']->conn->Execute($sql);

        if ($rs->_numOfRows<$num && $notEmpty) {
            while ($rs->_numOfRows<$num && $days<30) {
                $_days = 'AND  `contents`.`changed`>=DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY) ';

                $sql = 'SELECT * FROM '.$_tables .
                        'WHERE '.$_where.$_category. $_days.
                        ' '.$_order_by;
                $rs = $GLOBALS['application']->conn->Execute($sql);
                $days+=1;
            }
        }

        $items = $this->loadObject($rs, 'content');

        return $this->getInTime($items);
    }

     /**
     * This function returns an array of objects all types of the most voted in
     * the last few days indicated.
     * Objects only have covered the fields pk_content, title, and total_value
     * total_votes
     *
     * @param boolean $notEmpty If there are no results regarding the days
     *                           indicated, the query is performed on the entire
     *                           bd. For default is false
     * @param integer $category pk_content_category ok the contents. If value
     *                           is 0, then does not filter by categories.
     *                           For default is 0.
     * @param integer $days Interval of days on which the consultation
     *                           takes place. For default is 2.
     * @param integer $num Number of objects that the function returns.
     *                           For default is 8.
     * @param boolean $all Get all the content regardless of content
     *                           status.
     * @return array of objects
     */
    public function getAllMostVoted(
        $notEmpty = false,
        $category = 0,
        $days = 2,
        $num = 6,
        $all = false
    ) {
        $items = array();

        $_tables = '`contents`, `ratings` ';
        $_fields = '*';
        $_where = '`contents`.in_litter=0 ';
        if (!$all) {
            $_where .= 'AND `contents`.`content_status`=1 AND `contents`.`available`=1 ';
        }
        $_days = 'AND  `contents`.changed>=DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY) ';
        $_tables_relations = ' AND `ratings`.pk_rating=`contents`.pk_content ';
        $_order_by = 'ORDER BY `ratings`.total_votes DESC ';
        $_limit = 'LIMIT 0 , '.$num;

        if (intval($category)>0) {
            $_tables .= ', `contents_categories` ';
            $_tables_relations .= ' AND  `contents_categories`.pk_fk_content = `contents`.pk_content ' .
                                  'AND `contents_categories`.pk_fk_content_category=' . $category . ' ';
        }

        $sql = 'SELECT ' . $_fields
             . ' FROM ' . $_tables
             . ' WHERE ' . $_where .$_days.$_tables_relations
             . $_order_by
             . $_limit;
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if ($rs->_numOfRows<=$num && $notEmpty) {
            while ($rs->_numOfRows<$num && $days<30) {
                  $days+=2;

                $sql = 'SELECT ' . $_fields . ' FROM ' . $_tables
                     . ' WHERE ' . $_where . $_tables_relations
                     . $_order_by . $_limit;
                $rs = $GLOBALS['application']->conn->Execute($sql);
            }
        }

        $items = $this->loadObject($rs, 'content');

        return $this->getInTime($items);
    }

    /**
     * This function returns an array of objects $content_type of the most
     * commented in the last few days indicated.
     *
     * @param boolean $notEmpty If there are no results regarding the days indicated,
     *                          the query is performed on the entire bd. (default  false)
     * @param integer $category pk_content_category ok the contents.
     *                          If value is 0, then does not filter by categories. (default  0)
     * @param integer $days Interval of days on which the consultation takes place. (default  2)
     * @param integer $num Number of objects that the function returns. (default 8)
     * @param boolean $all Get all the content regardless of content status and endtime.
     *
     * @return array
     */
    public function getAllMostComented(
        $notEmpty = false,
        $category = 0,
        $days = 2,
        $num = 6,
        $all = false
    ) {
        $items = array();

        $_where_slave = '';
        $_days = 'changed>=DATE_SUB(CURDATE(), INTERVAL '.$days.' DAY) ';
        if (!$all) {
            $_where_slave = ' content_status=1 AND available=1 ';
            $_days = 'AND changed>=DATE_SUB(CURDATE(), INTERVAL '.$days.' DAY) ';
        }

        $_comented = 'AND pk_content IN (SELECT DISTINCT(fk_content) FROM comments) ';
        $_limit    = 'LIMIT 0 , '.$num;
        $_order_by = 'ORDER BY changed DESC';

        $_where= $_where_slave.$_days.$_comented;
        if (intval($category)>0) {
            $sql = 'SELECT * FROM contents_categories, contents '
                 . 'WHERE '.$_where
                 . ' AND `contents_categories`.`pk_fk_content_category`=' .$pk_fk_content_category
                 . ' AND `contents_categories`.`pk_fk_content`=`contents`.`pk_content` '
                 . $_order_by;
        } else {
             $sql = 'SELECT * FROM   contents '.
               'WHERE '.$_where.'  ' . $_order_by;
        }
        $rs = $GLOBALS['application']->conn->Execute($sql);
        $pks = $this->loadObject($rs, 'content');

        if (!$all) {
            $pks = $this->getInTime($pks);
        }

        if (count($pks) < 6 && $notEmpty) {
            // En caso de que existan menos de 6 contenidos, lo hace
            // referente a los 200 últimos contenidos
            $sql = 'SELECT * FROM   contents '.
               'WHERE '.$_where_slave.$_comented.'  ' . $_order_by;
            $rs = $GLOBALS['application']->conn->Execute($sql);
            $pks = $this->loadObject($rs, 'content');
            $pks = $this->getInTime($pks);

            if (!$all) {
                $pks = $this->getInTime($pks);
            }
        }

        $pk_list = '';
        foreach ($pks as $pk) {
            $pk_list .= ' '.$pk->id.',';
        }
        if (strlen($pk_list)==0) {
            return array();
        }
        $pk_list = substr($pk_list, 0, strlen($pk_list)-1);
        $sql = 'SELECT fk_content, count(pk_comment) AS num '
             . 'FROM   contents, comments '
             . 'WHERE available=1 AND fk_content IN ('.$pk_list.') '
             . 'GROUP BY fk_content ORDER BY num DESC LIMIT 0 , 8';

        $rs = $GLOBALS['application']->conn->Execute($sql);
        $comments = $this->loadObject($rs, 'Comment');

        $pk_list = '';
        foreach ($comments as $comment) {
            $pk_list .= ' '.$comment->fk_content.',';
        }
        if (strlen($pk_list)==0) {
            return array();
        }
        $pk_list = substr($pk_list, 0, strlen($pk_list)-1);

        $sql = 'SELECT `contents`.`pk_content`, '
             . '`contents`.`title`, `contents`.`slug` '
             . 'FROM contents, comments '
             . 'WHERE available=1 AND pk_content IN ('.$pk_list.')';
        $rs = $GLOBALS['application']->conn->Execute($sql);
        $items = $this->loadObject($rs, 'content');
        if (empty($items)) {
            return array();
        }
        foreach ($items as $item) {
            $articles[$item->pk_content] = array(
                'pk_content' => $item->pk_content,
                'num'        => 0,
                'title'      => $item->title,
                'slug'       => $item->slug
            );
        }
        foreach ($comments as $comment) {
            if (array_key_exists($comment->fk_content, $articles)) {
                $articles[$comment->fk_content]['num'] = $comment->num;
            }
        }

        uasort(
            $articles,
            function (
                $a,
                $b
            ) {
                if ($a['num'] == $b['num']) {
                    return 0;
                }

                return ($a['num'] > $b['num']) ? -1 : 1;
            }
        );

        return $articles;
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
            'content_status=1 AND available=1 AND frontpage=1'.
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
                    if (($item->available==1) && ($item->in_litter==0)) {
                        $filtered[] = $item;
                    }
                } else {
                    if (($item['available']==1) && ($item['in_litter']==0)) {
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
        $items  = array();
        $_where = 'AND in_litter=0';

        if (!is_null($filter)) {
            if (($filter == ' `contents`.`in_litter`=1')
                || ($filter == 'in_litter=1')
            ) {
                $_where = ' AND '.$filter;
            } else {
                $_where .= ' AND '.$filter;
            }
        }

        if (intval($pk_fk_content_category) > 0) {
            $sql = 'SELECT COUNT(contents.pk_content) '
                 . 'FROM `contents_categories`, `contents`, ' . $this->table . '  '
                 . ' WHERE `contents_categories`.`pk_fk_content_category`='
                 . $pk_fk_content_category
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
     * Used for generate the backend listings
     *
     * Genera las consultas de find o find_by_category y la paginacion
     * Devuelve el array con el segmento de contents que se visualizan en la
     * pagina dada.
     *
     * <code>
     * ContentManager::find_pages($contentType, $filter=null,
     *     $_order_by='ORDER BY 1', $page=1, $items_page=10,
     *     $pk_fk_content_category=null);
     * </code>
     *
     * @param int         $contentType            Tipo contenido.
     * @param string|null $filter                 Clausula where.
     * @param string      $_order_by              Orden de visualizacion
     * @param int         $page                   Página a visualizar.
     * @param int         $items_page             Elementos por pagina.
     * @param int|null    $pk_fk_content_category Id de categoria (para
     *                                             find_by_category y si null
     *                                             es find).
     * @param string url the base path used by the pager
     *
     * @return array Array ($items, $pager)
     */
    public function find_pages(
        $contentType,
        $filter = null,
        $_order_by = 'ORDER BY 1',
        $page = 1,
        $items_page = 10,
        $pk_fk_content_category = null,
        $url = null
    ) {
        $this->init($contentType);
        $items = array();
        $_where = '`contents`.`in_litter`=0';

        if (!is_null($filter)) {
            if ($filter == ' `contents`.`in_litter`=1'
               || $filter == 'in_litter=1'
            ) {
                //se busca desde la litter.php
                $_where = $filter;
            } else {
                $_where = ' `contents`.`in_litter`=0 AND '.$filter;
            }
        }
        $countContents=$this->count($contentType, $filter, $pk_fk_content_category);
        if (empty($page)) {
            $page = 1;
        }
        $_limit=' LIMIT '.($page-1)*$items_page.', '.($items_page);

        if (intval($pk_fk_content_category) > 0) {
            $sql = 'SELECT * FROM contents_categories, contents, '.$this->table.'  ' .
                ' WHERE '.$_where.' AND `contents_categories`.`pk_fk_content_category`='.$pk_fk_content_category.
                '  AND `contents`.`pk_content`=`'.$this->table.'`.`pk_'.$this->content_type
                .'` AND  `contents_categories`.`pk_fk_content` = `contents`.`pk_content` '.
                 $_order_by.$_limit;
        } else {
            $sql = 'SELECT * FROM `contents`, `'.$this->table.'` '
                . ' WHERE '.$_where
                . ' AND `contents`.`pk_content`=`'.$this->table.'`.`pk_'.$this->content_type.'` '
                . $_order_by.' '.$_limit;
        }

        $rs = $GLOBALS['application']->conn->Execute($sql);

        $items = $this->loadObject($rs, $contentType);

        $pager_options = array(
            'mode'        => 'Sliding',
            'perPage'     => $items_page,
            'delta'       => 3,
            'clearIfVoid' => true,
            'urlVar'      => 'page',
            'totalItems'  => $countContents,
        );

        if ($url != null) {
            $pager_options['path'] = $url;
        }
        $pager = Pager::factory($pager_options);

        return array($items, $pager);
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
        $debug = false
    ) {
        $this->init($contentType);
        $items  = array();
        $_where = '';

        if (empty($filter)) {
            $filterCount = ' contents.in_litter != 1 ';
            $filter = ' AND '. $filterCount;
        } else {
            $filterCount = $filter;
            $filter = ' AND '. $filter;
        }

        $countContents = $this->count($contentType, $filterCount, $categoryId);

        if ($page == 1) {
            $limit = ' LIMIT '. $numElements;
        } else {
            $limit = ' LIMIT '.($page-1)*$numElements.', '.$numElements;
        }

        if (!is_null($categoryId)) {
            $sql = 'SELECT * FROM contents_categories, contents, '.$this->table.' '
                 . ' WHERE `contents_categories`.`pk_fk_content_category`='.$categoryId
                 . ' AND `contents`.`pk_content`=`'.$this->table.'`.`pk_'.$this->content_type.'`'
                 . ' AND  `contents_categories`.`pk_fk_content` = `contents`.`pk_content` '
                 . $filter
                 . ' '
                 . $orderBy
                 . $limit;
        } else {
            $sql = 'SELECT * FROM `contents`, `'.$this->table.'` '
                 . ' WHERE `contents`.`pk_content`=`'.$this->table.'`.`pk_'.$this->content_type.'` '
                 . $filter
                 . ' '
                 . $orderBy
                 . $limit;
        }
        if ($debug == true) {
            var_dump($sql);
            die();
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
     * Returns a list of content objecst from a given category that matches a search criteria
     *
     * @param string $contentType the type of content to search for
     * @param string $categoryName the name of the category where search for contents in
     * @param string $filter the SQL WHERE sentence to filter the contents
     * @param string $orderBy the ORDER BY sentence to sort the contents
     *
     * @return array a list of objects that matches the search criterias
     **/
    public function find_by_category_name(
        $contentType,
        $categoryName,
        $filter = null,
        $orderBy = 'ORDER BY 1'
    ) {
        // recupera el id de la categoria del array.
        $this->init($contentType);

        $ccm = ContentCategoryManager::get_instance();
        $pk_fk_content_category = $ccm->get_id($categoryName);

        $items  = array();
        $_where = 'in_litter=0';

        if (!is_null($filter)) {
            // se busca desde la litter.php
            if (preg_match('/in_litter=1/i', $filter)) {
                $_where = $filter;
            } else {
                $_where = $filter.' AND in_litter=0';
            }
        }

        $sql = 'SELECT * FROM contents_categories, contents,  '.$this->table.'  '
             . 'WHERE '.$_where
             . ' AND `contents_categories`.`pk_fk_content_category`='.$pk_fk_content_category
             . '  AND `contents`.`pk_content`= `'.$this->table.'`.`pk_'.$this->content_type
             . '` AND `contents_categories`.`pk_fk_content` = `contents`.`pk_content` '
             .$orderBy;

        $rs = $GLOBALS['application']->conn->Execute($sql);
        $items = $this->loadObject($rs, $contentType);

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
            AND `contents`.`available` =1
            AND `contents`.`fk_content_type` =1
            AND `contents`.`in_litter` =0
        ORDER BY `contents`.`placeholder` ASC, `created` DESC ';

        $rs    = $GLOBALS['application']->conn->Execute($sql);
        $ccm   = ContentCategoryManager::get_instance();
        $items = array();
        while (!$rs->EOF) {
            $items[] = array(
                'title'          => $rs->fields['title'],
                'catName'        => $ccm->get_name($rs->fields['category_id']),
                'slug'           => $rs->fields['slug'],
                'created'        => $rs->fields['created'],
                'category_title' =>
                    $ccm->get_title($ccm->get_name($rs->fields['category_id'])),
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
     * @return array a list of content information (not the object itself)
     **/
    public function findHeadlinesWithImage()
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
            AND `contents`.`frontpage` =1
            AND `contents`.`available` =1
            AND `contents`.`fk_content_type` =1
            AND `contents`.`in_litter` =0
        ORDER BY `contents`.`placeholder` ASC, `created` DESC ';

        $rs    = $GLOBALS['application']->conn->Execute($sql);
        $ccm   = ContentCategoryManager::get_instance();
        $items = array();
        while (!$rs->EOF) {
            $items[] = array(
                'title'          => $rs->fields['title'],
                'catName'        => $ccm->get_name($rs->fields['category_id']),
                'slug'           => $rs->fields['slug'],
                'created'        => $rs->fields['created'],
                'category_title' => $ccm->get_title($ccm->get_name($rs->fields['category_id'])),
                'id'             => $rs->fields['pk_content'],
                'starttime'      => $rs->fields['starttime'],
                'endtime'        => $rs->fields['endtime'],
                'img1'           => $rs->fields['img1'],
                'img2'           => $rs->fields['img2'],
            );

            $rs->MoveNext();
        }

        $items = $this->getInTime($items);

        return $items;
    }

    /**
     * Returns title, catName, slugs, dates and images of last headlines
     *
     * @return array a list of content information (not the object itself)
     **/
    public function findHeadlinesWithImage()
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
            AND `contents`.`frontpage` =1
            AND `contents`.`available` =1
            AND `contents`.`fk_content_type` =1
            AND `contents`.`in_litter` =0
        ORDER BY `contents`.`placeholder` ASC, `created` DESC ';

        $rs    = $GLOBALS['application']->conn->Execute($sql);
        $ccm   = ContentCategoryManager::get_instance();
        $items = array();
        while (!$rs->EOF) {
            $items[] = array(
                'title'          => $rs->fields['title'],
                'catName'        => $ccm->get_name($rs->fields['category_id']),
                'slug'           => $rs->fields['slug'],
                'created'        => $rs->fields['created'],
                'category_title' => $ccm->get_title($ccm->get_name($rs->fields['category_id'])),
                'id'             => $rs->fields['pk_content'],
                'starttime'      => $rs->fields['starttime'],
                'endtime'        => $rs->fields['endtime'],
                'img1'           => $rs->fields['img1'],
                'img2'           => $rs->fields['img2'],
            );

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
            'SELECT contents.pk_content, contents.position,
                opinions.pk_opinion as id, authors.name, authors.pk_author,
                authors.condition, contents.title, author_imgs.path_img,
                contents.slug, opinions.type_opinion, opinions.body,
                contents.changed, contents.created, contents.starttime,
                contents.endtime
            FROM contents, opinions
            LEFT JOIN authors ON (authors.pk_author=opinions.fk_author)
            LEFT JOIN author_imgs ON (opinions.fk_author_img=author_imgs.pk_img)
            WHERE `contents`.`fk_content_type`=4
            AND contents.pk_content=opinions.pk_opinion
            AND '.$where.' '
            .$orderBy;

        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs    = $GLOBALS['application']->conn->Execute($sql);

        $items = null;
        if (!empty($rs)) {
            $items = $rs->GetArray();
        }

        return $items ;
    }

    /**
     * Returns the list of content types available in the application
     *
     * @return array a list of content types
     **/
    public function getContentTypes()
    {
        $items = array();
        $sql   = 'SELECT pk_content_type, name, title FROM content_types ';

        $rs    = $GLOBALS['application']->conn->Execute($sql);
        while (!$rs->EOF) {
            $pk_content_type = $rs->fields['pk_content_type'];
            $items[$pk_content_type] = htmlentities($rs->fields['title']);
            $rs->MoveNext();
        }

        return $items;
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
            .'      contents.changed, '
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

         $sql= 'SELECT contents.pk_content as id, contents.title, authors.name,
                       contents.metadata, contents.slug, contents.changed,
                       contents.starttime, contents.endtime
                FROM contents, opinions
                LEFT JOIN authors
                    ON (authors.pk_author=opinions.fk_author)
                LEFT JOIN author_imgs
                    ON (opinions.fk_author_img=author_imgs.pk_img)
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
            Application::logDatabaseError();

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
                && $content->available == 1
                && $content->in_litter == 0
            ) {
                $content->category_name = $ccm->get_name($content->category);
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
            $where =' AND pk_fk_content_category = '.$categoryID;
        }
        // Initialization of variables
        $contents = array();

        $sql = 'SELECT * FROM contents, contents_categories '
              .'WHERE fk_content_type IN (1,3,7,9,10,11,17) '
              .'AND DATE(starttime) = "'.$date.'" '
              .'AND available=1 AND in_litter=0 '
              .'AND pk_fk_content = pk_content '.$where
              .' ORDER BY  fk_content_type ASC, starttime DESC ';

        $rs = $GLOBALS['application']->conn->Execute($sql);

        if ($rs !== false) {
            $contents = array();
            while (!$rs->EOF) {
                if ($rs->fields['fk_content_type'] == 1) {
                    $content = new Article($rs->fields['pk_fk_content']);
                    if (!empty($content->fk_video)) {
                        $content->video = new Video($content->fk_video);

                    } else {
                        if (!empty($content->img1)) {
                            $content->image = new Photo($content->img1);
                        }
                    }
                } else {
                    $content = new Content($rs->fields['pk_fk_content']);
                    $content->content_type = $content->content_type_name;
                }
                $contents[] = $content;

                $rs->MoveNext();
            }

            return $contents;
        }

        return false;
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
        $externalContent = apc_fetch(APC_PREFIX.$url, $success);
        if (!$success) {
            $c  = curl_init($url);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
            $externalContent = curl_exec($c);
            apc_add(APC_PREFIX.$url, $externalContent, 300);
            curl_close($c);
        }

        if ($decodeJson) {
            $content = json_decode($externalContent);
        } else {
            $content = $externalContent;
        }

        return $content;
    }
}
