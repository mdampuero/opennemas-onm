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
     */
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
     */
    public $table = null;

    /**
     * Contains the Pager object instance, usefull for paginate contents
     *
     * @var \Pager
     */
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
     */
    public function __construct($contentType = null)
    {
        // Lowercase table name and content type with the name of the class
        if (!is_null($contentType)) {
            $this->init($contentType);
        }

        $this->cache = new MethodCacheManager($this, [ 'ttl' => 30 ]);
    }

    /**
     * Initializes the table and content_type properties from a content type name
     *
     * @param string $contentType the content type name
     *
     * @return void
     */
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
     */
    public function loadObject($contentsData, $contentType)
    {
        $items = [];
        foreach ($contentsData as $contentData) {
            $contentType = classify($contentType);

            $obj = new $contentType();
            $obj->load($contentData);

            $items[] = $obj;
        }

        return $items;
    }

    /**
     * Filters and removes blocked contents from the list of contents.
     *
     * @param array $contents The list of contents.
     *
     * @return array
     */
    public function filterBlocked($contents)
    {
        $subscriptions = getService('api.service.subscription')
            ->setCount(false)
            ->getList('enabled = 1');

        $ids = array_map(function ($a) {
            return $a->pk_user_group;
        }, array_filter($subscriptions['items'], function ($a) {
            return in_array(230, $a->privileges);
        }));

        return array_filter($contents, function ($a) use ($ids) {
            return empty($a->subscriptions)
                || empty(array_intersect($ids, $a->subscriptions));
        });
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
     */
    public function find($contentType, $filter = null, $orderBy = 'ORDER BY 1', $fields = '*')
    {
        $table       = tableize($contentType);
        $contentType = underscore($contentType);

        $where = '`contents`.`content_type_name` = "' . $contentType . '"'
            . 'AND `contents`.`in_litter` = 0';

        if (!is_null($filter)) {
            // se busca desde la litter.php
            if ($filter == 'in_litter=1') {
                $where = $filter;
            } else {
                $where = ' `contents`.`in_litter`=0 AND ' . $filter;
            }
        }

        try {
            $rs = getService('dbal_connection')->fetchAll(
                "SELECT $fields FROM contents JOIN $table ON pk_content = pk_$contentType"
                . " WHERE " . $where . ' ' . $orderBy
            );

            return $this->loadObject($rs, $contentType);
        } catch (\Exception $e) {
            getService('error.log')->error(
                $e->getMessage() . ' Stack Trace: ' . $e->getTraceAsString()
            );

            return [];
        }
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
     */
    public function findAll($contentType, $filter = null, $orderBy = 'ORDER BY 1', $fields = '*')
    {
        $table       = tableize($contentType);
        $contentType = underscore($contentType);

        $where = '`contents`.`in_litter`=0';
        if (!is_null($filter)) {
            // se busca desde la litter.php
            if ($filter == 'in_litter=1') {
                $where = $filter;
            } else {
                $where = ' `contents`.`in_litter`=0 AND ' . $filter;
            }
        }

        try {
            $rs = getService('dbal_connection')->fetchAll(
                'SELECT ' . $fields
                . ' FROM `contents`, `' . $table . '`, `contents_categories` '
                . ' WHERE ' . $where
                . ' AND `contents`.`pk_content`= `' . $table . '` . `pk_' . $contentType . '` '
                . ' AND `contents`.`pk_content`= `contents_categories`.`pk_fk_content` ' . $orderBy
            );

            return $this->loadObject($rs, $contentType);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return [];
        }
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
        $contents = [];

        $cache      = getService('cache');
        $contentIds = $cache->fetch('frontpage_elements_map_' . $categoryID);

        if (!is_array($contentIds) || count($contentIds) <= 0) {
            // Fetch the list of contents for the current frontpage and its metadata
            // We need to get articles in frontpage too in order to mark them as in_frontpage
            $contentIds = $this->getContentIdsInHomePageWithIDs(
                [ (int) $categoryID, 0 ]
            );

            $cache->save('frontpage_elements_map_' . $categoryID, $contentIds);
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

        if (is_array($contentIds) && count($contentIds) > 0) {
            $er = getService('entity_repository');

            // Retrieve contents from cache
            $contentsMap = array_map(function ($content) {
                return [ $content['content_type'], $content['content_id'] ];
            }, $contentIds);

            $contentsRaw = $er->findMulti($contentsMap);
            $contentsRaw = $this->checkAndCleanFrontpageSize($contentsRaw);

            // iterate over all found contents to hydrate them
            foreach ($contentIds as $element) {
                // Only add elements for the requested category id
                if ($element['frontpage_id'] != $categoryID) {
                    continue;
                }

                $content = array_filter($contentsRaw, function ($contentRaw) use ($element) {
                    return $contentRaw->id == $element['content_id'];
                });

                $content = array_pop($content);

                // add all the additional properties related with positions and params
                if (is_object($content) && $content->in_litter == 0) {
                    // We have to clone the content as this is a reference to object,
                    // not a copy of itself.
                    $contentToUse = clone $content;

                    $contentToUse->load([
                        'placeholder' => $element['placeholder'],
                        'position'    => $element['position'],
                    ]);

                    if (!empty($element['params'])) {
                        if (is_array($content->params) && $content->params > 0) {
                            $contentToUse->params = array_merge(
                                $content->params,
                                (array) $element['params']
                            );
                        } else {
                            $contentToUse->params = $element['params'];
                        }
                    }

                    $contentToUse->in_frontpage = in_array($element['content_id'], $contentsInFrontpage);

                    $contents[] = $contentToUse;
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
    public function getContentIdsInHomePageWithIDs($categories = [])
    {
        // Initialization of variables
        $contents = [];

        if (count($categories) == 0) {
            return $contents;
        }

        $conn = getService('orm.manager')->getConnection('instance');

        $categoriesSQL = implode(', ', $categories);

        $sql = 'SELECT * FROM content_positions '
          . 'WHERE `fk_category` IN (' . $categoriesSQL . ') '
          . 'ORDER BY position ASC ';

        $rs = $conn->fetchAll($sql);

        foreach ($rs as $content) {
            $contents[] = [
                'content_id'   => $content['pk_fk_content'],
                'frontpage_id' => $content['fk_category'],
                'position'     => $content['position'],
                'placeholder'  => $content['placeholder'],
                'params'       => unserialize($content['params']),
                'content_type' => $content['content_type'],
            ];
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
        $contents = [];

        $em = getService('entity_repository');

        // iterate over all found contents and initialize them
        foreach ($contentsArray as $element) {
            $content = $em->find($element['content_type'], $element['id']);

            // only add it to the final results if is not in litter
            if ($content->in_litter == 0) {
                $content->load([
                    'placeholder' => $element['placeholder'],
                    'position'    => $element['position'],
                ]);

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
     * Save the content positions for elements in a given category
     *
     * @param int $categoryID the id of the category we want to save positions into
     * @param array $elements an array with the id, placeholder, position
     *
     * @return boolean, if all went good this will be true and viceversa
     */
    public static function saveContentPositionsForHomePage($categoryID, $frontpageVersionId, $elements = [])
    {
        $positions   = [];
        $contentIds  = [];
        $returnValue = false;

        if (empty($elements)) {
            return $returnValue;
        }

        $conn = getService('orm.manager')->getConnection('instance');

        // Foreach element setup the sql values statement part
        foreach ($elements as $element) {
            $contentIds[] = $element['id'];
            $positions[]  = [
                $conn->quote($element['id'], \PDO::PARAM_INT),
                $conn->quote($categoryID, \PDO::PARAM_INT),
                $conn->quote($element['position'], \PDO::PARAM_INT),
                $conn->quote($element['placeholder'], \PDO::PARAM_STR),
                $conn->quote($element['content_type'], \PDO::PARAM_STR),
                $conn->quote($frontpageVersionId, \PDO::PARAM_INT),
            ];
        }

        try {
            $conn->beginTransaction();

            // Clean all the contents for this category after insert the new ones

            self::clearContentPositionsForHomePageOfCategory($categoryID, $frontpageVersionId, $conn);

            // construct the final sql statement and execute it
            $stmt = 'INSERT INTO content_positions (pk_fk_content, fk_category,'
                  . ' position, placeholder, content_type, frontpage_version_id) '
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

            getService('application.log')->error(
                'User ' . getService('core.user')->username
                . ' (' . getService('core.user')->id
                . ') updated frontpage of category ' . $categoryID . ' with error message: '
                . $e->getMessage()
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
    public static function dropSuggestedFlagFromContentIdsArray($contentIds, $conn = false)
    {
        $conn = getService('orm.manager')->getConnection('instance');

        if (is_array($contentIds) && (count($contentIds) > 0)) {
            $contentIdsSQL = implode(', ', $contentIds);
            $values        = [ date("Y-m-d H:i:s") ];

            $sql = 'UPDATE contents '
                 . 'SET `frontpage`=0, `changed`=? '
                 . 'WHERE `pk_content` IN (' . $contentIdsSQL . ')';
            if ($conn->executeUpdate($sql, $values) === false) {
                return false;
            }

            /* Notice log of this action */
            getService('application.log')->notice(
                'User ' . getService('core.user')->username
                . ' (' . getService('core.user')->id
                . ') has executed action drop suggested flag at ' . $contentIdsSQL . ' ids'
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
    public static function clearContentPositionsForHomePageOfCategory($categoryID, $frontpageVersionId, $conn = false)
    {
        $conn = getService('orm.manager')->getConnection('instance');

        // clean actual contents for the homepage of this category
        $sql  = 'DELETE FROM content_positions WHERE ';
        $sql .= empty($frontpageVersionId) ?
            '`fk_category` = ' . $categoryID :
            '`fk_category` = ' . $categoryID . ' AND frontpage_version_id IN (' .
            $frontpageVersionId . ', 0)';
        $conn->executeUpdate($sql);

        getService('application.log')->info(
            'User ' . getService('core.user')->username
            . ' (' . getService('core.user')->id
            . ') clear contents frontpage of category ' . $categoryID
        );

        return true;
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
        if (!is_array($array) || empty($array)) {
            return $array;
        }

        $cur           = 1;
        $stack[1]['l'] = 0;
        $stack[1]['r'] = count($array) - 1;

        do {
            $l = $stack[$cur]['l'];
            $r = $stack[$cur]['r'];
            $cur--;
            do {
                $i   = $l;
                $j   = $r;
                $tmp = $array[(int) (($l + $r) / 2)];

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

        return $array;
    }

    /**
     * Gets the earlier starttime of scheduled contents from a contents array
     *
     * @param array $contents Array of Contents.
     *
     * @return string The minor starttime of scheduled contents or null
     */
    public static function getEarlierStarttimeOfScheduledContents($contents)
    {
        $current = date('Y-m-d H:i:s');
        $expires = null;
        foreach ($contents as $content) {
            if ($content->starttime > $current
                && (empty($expires)
                    || $content->starttime < $expires)
            ) {
                $expires = $content->starttime;
            }
        }

        return $expires;
    }

    /**
     * Gets the earlier starttime of scheduled contents from a contents array
     *
     * @param array $contents Array of Contents.
     *
     * @return string The minor starttime of scheduled contents or null
     */
    public static function getEarlierEndtimeOfScheduledContents($contents)
    {
        $current = date('Y-m-d H:i:s');
        $expires = null;
        foreach ($contents as $content) {
            if ($content->endtime > $current
                && (empty($expires)
                    || $content->endtime < $expires)
            ) {
                $expires = $content->endtime;
            }
        }

        return $expires;
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
            return ($ucfirst === true) ? ucfirst($contentID) : strtolower($contentID);
        }

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                "SELECT path FROM attachments WHERE `pk_attachment`=?",
                [ $contentID ]
            );

            return ($ucfirst === true) ? ucfirst($rs['path']) : $rs['path'];
        } catch (\Exception $e) {
            getService('error.log')->error(
                $e->getMessage() . ' Stack Trace: ' . $e->getTraceAsString()
            );

            return false;
        }
    }

    /**
     * This function returns an array of objects $contentType of the most viewed
     * in the last few days indicated.
     *
     * @param string $contentType type of content
     * @param boolean $notEmpty If there are no results regarding the days
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
     *
     * @param int $page The page to show
     *
     * @return array of objects $contentType
     *
     * @throws Exception
     */
    public function getMostViewedContent(
        $contentType,
        $notEmpty = false,
        $category = 0,
        $author = 0,
        $days = 2,
        $num = 9,
        $all = false,
        $page = 1
    ) {
        $em = getService('entity_repository');

        $date = new \DateTime();
        $date->sub(new \DateInterval('P' . $days . 'D'));
        $date = $date->format('Y-m-d H:i:s');
        $now  = date('Y-m-d H:i:s');

        $criteria = [
            'join' => [
                [
                    'table'               => 'content_views',
                    'type'                => 'left',
                    'contents.pk_content' => [
                        [
                            'value' => 'content_views.pk_fk_content',
                            'field' => true
                        ]
                    ]
                ]
            ],
            'content_type_name' => [['value' => $contentType]],
            'in_litter'         => [['value' => 0]],
            'starttime'         => [
                'union' => 'AND',
                [ 'value' => $date, 'operator' => '>=' ],
                [ 'value' => $now, 'operator' => '<' ]
            ],
            'endtime'           => [
                'union' => 'OR',
                [ 'value' => '0000-00-00 00:00:00'],
                [ 'value'  => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => $now, 'operator' => '>' ]
            ],
        ];

        $order = ['content_views.views' => 'desc'];
        if ($category) {
            $category = getService('category_repository')->find($category);

            if ($category) {
                $category = $category->name;
            }

            $criteria['category_name'] = [['value' => $category]];
        }

        if ($author) {
            $criteria['fk_author'] = [['value' => $author]];
        }

        if (!$all) {
            $criteria['content_status'] = [['value' => 1]];
        }

        $contents = $em->findBy($criteria, $order, $num, $page);

        // Repeat with starttime filter changed
        if (count($contents) == 0) {
            $criteria['starttime'] = [['value' => $now, 'operator' => '<']];

            $contents = $em->findBy($criteria, $order, $num, $page);
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
        $all = false,
        $page = 1
    ) {
        $offset = ($page - 1) * $maxElements;

        try {
            $rs = getService('dbal_connection')->fetchAll(
                "SELECT COUNT(comments.content_id) as num_comments, contents.*, articles.*
                FROM contents, comments, articles
                WHERE contents.pk_content = comments.content_id
                AND contents.pk_content = articles.pk_article
                AND contents.content_status=1
                AND starttime >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                GROUP BY contents.pk_content
                ORDER BY num_comments DESC, contents.starttime DESC
                LIMIT ? OFFSET ?",
                [ $days, $maxElements, $offset ]
            );

            $contents = [];
            foreach ($rs as $row) {
                $content = new $contentType();
                $content->load($row);

                $contents[$content->pk_content] = [
                    'pk_content' => $content->pk_content,
                    'num'        => $content->num_comments,
                    'title'      => $content->title,
                    'permalink'  => $content->slug,
                    'uri'        => $content->uri
                ];
            }

            return $contents;
        } catch (\Exception $e) {
            getService('error.log')->error(
                $e->getMessage() . ' Stack Trace: ' . $e->getTraceAsString()
            );

            return false;
        }
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
        // TODO: Review algorithm
        $table = tableize($contentType);

        $tables   = '`contents`, `' . $table . '`, `ratings` ';
        $whereSQL = '`contents`.in_litter=0 ';
        if (!$all) {
            $whereSQL .= ' AND `contents`.`content_status`=1 ';
        }

        $daysFilterSQL     = 'AND  `contents`.starttime>=DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY) ';
        $tablesRelationSQL = ' AND `contents`.pk_content=`' . $table . '`.pk_' . strtolower($contentType) .
            ' AND `ratings`.pk_rating=`contents`.pk_content ';
        $orderBySQL        = ' ORDER BY `contents`.`content_status` DESC, `ratings`.total_votes DESC ';
        $limitSQL          = 'LIMIT ' . $num;

        if (isset($author) && !is_null($author) && intval($author) > 0) {
            if ($contentType == 'Opinion') {
                $whereSQL .= 'AND `opinions`.fk_author=' . $author . ' ';
            } else {
                $whereSQL .= 'AND `contents`.fk_author=' . $author . ' ';
            }
        }

        if (intval($category) > 0) {
            $tables .= ', `contents_categories` ';

            $tablesRelationSQL .= ' AND  `contents_categories`.pk_fk_content = `contents`.pk_content '
                . 'AND `contents_categories`.pk_fk_content_category=' . $category . ' ';
        }

        $sql = 'SELECT * FROM ' . $tables
            . ' WHERE ' . $whereSQL . $daysFilterSQL . $tablesRelationSQL
            . $orderBySQL . $limitSQL;

        try {
            $rs = getService('dbal_connection')->fetchAll($sql);

            if (is_null($rs) || count($rs) < 4) {
                $rs = getService('dbal_connection')->fetchAll(
                    'SELECT * FROM ' . $tables
                    . ' WHERE ' . $whereSQL . $tablesRelationSQL
                    . $orderBySQL . $limitSQL
                );
            }

            if (empty($rs) || !is_array($rs)) {
                return [];
            }

            return $this->loadObject($rs, $contentType);
        } catch (\Exception $e) {
            getService('error.log')->error(
                $e->getMessage() . ' Stack Trace: ' . $e->getTraceAsString()
            );

            return false;
        }
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
    public function getAllMostViewed($notEmpty = false, $category = 0, $days = 2, $num = 6, $all = false)
    {
        $em = getService('entity_repository');

        $now = new \DateTime();
        $now = $now->format('Y-m-d H:i:s');

        $date = new \DateTime();
        $date->sub(new \DateInterval('P' . $days . 'D'));
        $date = $date->format('Y-m-d H:i:s');

        $criteria = [
            'join' => [
                [
                    'table'               => 'content_views',
                    'type'                => 'left',
                    'contents.pk_content' => [
                        [
                            'value' => 'content_views.pk_fk_content',
                            'field' => true
                        ]
                    ]
                ]
            ],
            'fk_content_type' => [[ 'value' => [ 1,3,4,7,9,11 ], 'operator' => 'IN' ]],
            'in_litter'       => [[ 'value' => 0 ]],
            'starttime'       => [[ 'value' => $date, 'operator' => '>=' ]],
            'endtime'         => [
                'union' => 'OR',
                [ 'value' => '0000-00-00 00:00:00' ],
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => $now, 'operator' => '>' ],
            ],
        ];

        $order = [ 'content_views.views' => 'desc' ];

        if ($category) {
            $category = getService('category_repository')->find($category);

            if ($category) {
                $category = $category->name;
            }

            $criteria['category_name'] = [ [ 'value' => $category ] ];
        }

        if (!$all) {
            $criteria['content_status'] = [ [ 'value' => 1 ] ];
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
            'content_status=1 AND content_status=1 AND frontpage=1 AND in_home=2',
            'ORDER BY  created DESC,  title ASC '
        );

        return $contents;
    }

    /**
     * Filter content objects by starttime and endtime
     *
     * @param array  $items Array of Content objects
     *
     * @return array Items filtered
    */
    public function getInTime($items)
    {
        $filtered = [];
        if (is_array($items)) {
            $filtered = array_filter(
                $items,
                function ($item) {
                    if (is_object($item)) {
                        return $item->isInTime();
                    } else {
                        return self::isInTime($item['starttime'], $item['endtime']);
                    }
                }
            );
        }

        return array_values($filtered);
    }

    /**
     * Check if a content is in time for publishing
     *
     * @param string $starttime the initial time from it will be available
     * @param string $endtime   the initial time until it will be available
     *
     * @return boolean
     */
    public static function isInTime($starttime = null, $endtime = null)
    {
        $start       = !empty($starttime) ? strtotime($starttime) : null;
        $end         = !empty($endtime) ? strtotime($endtime) : null;
        $currentTime = time();

        // If $start and $end not defined or they are equals  => is in time
        if ((empty($start) && empty($end))
            || $start == $end
        ) {
            return true;
        }

        // only setted $end -> check endttime
        if (empty($start)) {
            return $currentTime < $end;
        }

        // only setted $start -> check startime
        if (empty($end) || $end <= 0) {
            return $currentTime > $start;
        }

        // $start < $currentTime < $end
        return ($currentTime < $end) && ($currentTime > $start);
    }

    /**
     * Filter content objects by  available and not inlitter.
     *
     * @param array $items Array of Content objects
     *
     * @return array Items filtered
     */
    public function getAvailable($items)
    {
        $filtered = [];
        if (!is_array($items)) {
            return [];
        }

        foreach ($items as $item) {
            if (is_object($item)) {
                if ($item->content_status == 1 && $item->in_litter == 0) {
                    $filtered[] = $item;
                }

                continue;
            }

            if ($item['content_status'] == 1 && $item['in_litter'] == 0) {
                $filtered[] = $item;
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
    public function count($contentType, $filter = null, $categoryID = null)
    {
        $table       = tableize($contentType);
        $contentType = underscore($contentType);

        $whereSQL = 'AND in_litter=0';
        if (!is_null($filter)) {
            $whereSQL = ' AND ' . $filter;
        }

        if (intval($categoryID) > 0) {
            $sql = 'SELECT COUNT(contents.pk_content) '
                 . 'FROM `contents_categories`, `contents`, ' . $table . '  '
                 . ' WHERE `contents_categories`.`pk_fk_content_category`=' . $categoryID
                 . '  AND pk_content=`' . $table . '`.`pk_' . $contentType
                 . '` AND  `contents_categories`.`pk_fk_content` = `contents`.`pk_content` '
                 . $whereSQL;
        } else {
            $sql = 'SELECT COUNT(contents.pk_content) AS total '
                . 'FROM `contents`, `' . $table . '` '
                . 'WHERE `contents`.`pk_content`=`' . $table . '`.`pk_' . $contentType . '` '
                . $whereSQL;
        }

        try {
            $rs = getService('dbal_connection')->fetchAssoc($sql);

            return (is_array($rs) && array_key_exists('total', $rs)) ? (int) $rs['total'] : 0;
        } catch (\Exception $e) {
            getService('error.log')->error(
                $e->getMessage() . ' Stack Trace: ' . $e->getTraceAsString()
            );

            return false;
        }
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
     */
    public function find_by_category($contentType, $categoryID, $filter = null, $orderBy = 'ORDER BY 1')
    {
        if ($categoryID <= 0) {
            return [];
        }

        $table       = tableize($contentType);
        $contentType = underscore($contentType);

        $whereSQL = 'AND in_litter=0';

        if (!is_null($filter) && $filter == 'in_litter=1') {
            $whereSQL = $filter;
        } elseif (!is_null($filter)) {
            $whereSQL = ' in_litter=0 AND ' . $filter;
        }

        if (intval($categoryID) > 0) {
            $sql = 'SELECT * FROM contents_categories, contents, ' . $table . '  '
                 . 'WHERE ' . $whereSQL
                 . ' AND `contents_categories`.`pk_fk_content_category`=' . $categoryID
                 . ' AND `contents`.`pk_content`=`' . $table . '`.`pk_' . $contentType . '` '
                 . ' AND  `contents_categories`.`pk_fk_content` = `contents`.`pk_content` '
                 . $orderBy;
        } else {
            return [];
        }

        try {
            $rs = getService('dbal_connection')->fetchAll($sql);

            return $this->loadObject($rs, $contentType);
        } catch (\Exception $e) {
            getService('error.log')->error(
                $e->getMessage() . ' Stack Trace: ' . $e->getTraceAsString()
            );

            return false;
        }
    }

    /**
     * Returns title, catName and slugs of last headlines from subcategories of a given category
     *
     * @return array a list of content information (not the object itself)
     */
    public function findHeadlines()
    {
        $items = [];

        $sql = 'SELECT `contents`.`title`, `contents`.`pk_content` ,'
            . '       `contents`.`created` ,  `contents`.`slug` ,'
            . '       `contents`.`starttime` , `contents`.`endtime`,'
            . '       `contents_categories`.`pk_fk_content_category` AS `category_id`'
            . ' FROM `contents`'
            . ' LEFT JOIN contents_categories '
            . '     ON (`contents`.`pk_content`=`contents_categories`.`pk_fk_content`)'
            . ' WHERE `contents`.`content_status` =1'
            . '    AND `contents`.`frontpage` =1'
            . '    AND `contents`.`fk_content_type` =1'
            . '    AND `contents`.`in_litter` =0'
            . ' ORDER BY `starttime` DESC ';

        try {
            $rs = getService('dbal_connection')->fetchAll($sql);

            $ccm = ContentCategoryManager::get_instance();
            foreach ($rs as $row) {
                $items[] = [
                    'title'          => $row['title'],
                    'catName'        => $ccm->getName($row['category_id']),
                    'slug'           => $row['slug'],
                    'created'        => $row['created'],
                    'category_title' => $ccm->getTitle($ccm->getName($row['category_id'])),
                    'id'             => $row['pk_content'],
                    /* to filter in getInTime() */
                    'starttime'      => $row['starttime'],
                    'endtime'        => $row['endtime']
                ];
            }

            return $this->getInTime($items);
        } catch (\Exception $e) {
            getService('error.log')->error(
                $e->getMessage() . ' Stack Trace: ' . $e->getTraceAsString()
            );

            return [];
        }
    }

    /**
     * Returns title, catName, slugs, dates and images of last headlines
     *
     * @param boolean $frontIncluded description not available
     *
     * @return array a list of content information (not the object itself)
     */
    public function findHeadlinesWithImage($frontIncluded = false)
    {
        try {
            $rs = getService('dbal_connection')->fetchAll(
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
                ORDER BY `created` DESC LIMIT 400'
            );

            $ccm = ContentCategoryManager::get_instance();

            $contentIdsInFrontpage = [];
            if ($frontIncluded) {
                $ids = array_map(function ($item) {
                    return $item['pk_content'];
                }, $rs);

                $contentIds            = implode(', ', $ids);
                $contentIdsInFrontpage = getService('dbal_connection')->fetchAll(
                    'SELECT pk_fk_content FROM content_positions WHERE pk_fk_content IN ('
                    . $contentIds . ') AND fk_category=0'
                );
                $contentIdsInFrontpage = array_map(function ($item) {
                    return $item['pk_fk_content'];
                }, $contentIdsInFrontpage);
            }

            $items = [];
            foreach ($rs as $row) {
                if (!$frontIncluded || ($frontIncluded && in_array($row['pk_content'], $contentIdsInFrontpage))) {
                    $items[] = [
                        'title'          => $row['title'],
                        'catName'        => $ccm->getName($row['category_id']),
                        'slug'           => $row['slug'],
                        'created'        => $row['created'],
                        'category_title' => $ccm->getTitle($ccm->getName($row['category_id'])),
                        'id'             => $row['pk_content'],
                        'starttime'      => $row['starttime'],
                        'endtime'        => $row['endtime'],
                        'img1'           => $row['img1'],
                        'img2'           => $row['img2'],
                    ];
                }
            }

            $items = $this->getInTime($items);
            return $items;
        } catch (\Exception $e) {
            getService('error.log')->error(
                $e->getMessage() . ' Stack Trace: ' . $e->getTraceAsString()
            );

            return [];
        }
    }

    /**
     * Returns the title, catName and slugs of last headlines from a given category
     *
     * @param string $filter the SQL WHERE sentence to filter the contents
     * @param string $orderBy the ORDER BY sentence to sort the contents
     *
     * @return the list of opinions
     */
    public function getOpinionArticlesWithAuthorInfo($filter = null, $orderBy = 'ORDER BY 1')
    {
        $whereSQL = 'in_litter=0';
        if (!is_null($filter) && $filter == 'in_litter=1') {
            $whereSQL = $filter;
        } elseif (!is_null($filter)) {
            $whereSQL = $filter . ' AND in_litter=0';
        }

        try {
            $rs = getService('dbal_connection')->fetchAll(
                'SELECT contents.pk_content, contents.position, users.avatar_img_id,
                    opinions.pk_opinion as id, users.name, users.bio, contents.title,
                    contents.slug, opinions.type_opinion, contents.body,
                    contents.changed, contents.created, contents.with_comment,
                    contents.starttime, contents.endtime
                FROM contents, opinions
                LEFT JOIN users ON (users.id=opinions.fk_author)
                WHERE `contents`.`fk_content_type`=4
                AND contents.pk_content=opinions.pk_opinion
                AND ' . $whereSQL . ' ' . $orderBy
            );

            foreach ($rs as &$row) {
                $row['path_img'] = \Photo::getPhotoPath($row['avatar_img_id']);
            }

            return $rs;
        } catch (\Exception $e) {
            getService('error.log')->error(
                $e->getMessage() . ' Stack Trace: ' . $e->getTraceAsString()
            );

            return [];
        }
    }

    /**
     * Fetches available content types.
     *
     * @return array an array with each content type with id, name and title.
     */
    public static function getContentTypes()
    {
        $contentTypes = [
            [
                'pk_content_type' => 1,
                'name'            => 'article',
                'title'           => _('Article')
            ],
            [
                'pk_content_type' => 2,
                'name'            => 'advertisement',
                'title'           => _('Advertisement')
            ],
            [
                'pk_content_type' => 3,
                'name'            => 'attachment',
                'title'           => _('File')
            ],
            [
                'pk_content_type' => 4,
                'name'            => 'opinion',
                'title'           => _('Opinion')
            ],
            [
                'pk_content_type' => 5,
                'name'            => 'event',
                'title'           => _('Event')
            ],
            [
                'pk_content_type' => 6,
                'name'            => 'comment',
                'title'           => _('Comment')
            ],
            [
                'pk_content_type' => 7,
                'name'            => 'album',
                'title'           => _('Album')
            ],
            [
                'pk_content_type' => 8,
                'name'            => 'photo',
                'title'           => _('Image')
            ],
            [
                'pk_content_type' => 9,
                'name'            => 'video',
                'title'           => _('Video')
            ],
            [
                'pk_content_type' => 10,
                'name'            => 'special',
                'title'           => _('Special')
            ],
            [
                'pk_content_type' => 11,
                'name'            => 'poll',
                'title'           => _('Poll')
            ],
            [
                'pk_content_type' => 12,
                'name'            => 'widget',
                'title'           => _('Widget')
            ],
            [
                'pk_content_type' => 13,
                'name'            => 'static_page',
                'title'           => _('Static page')
            ],
            [
                'pk_content_type' => 14,
                'name'            => 'kiosko',
                'title'           => _('Kiosko')
            ],
            [
                'pk_content_type' => 15,
                'name'            => 'book',
                'title'           => _('Book')
            ],
            [
                'pk_content_type' => 16,
                'name'            => 'schedule',
                'title'           => _('Agenda')
            ],
            [
                'pk_content_type' => 17,
                'name'            => 'letter',
                'title'           => _('Letter to editor')
            ],
            [
                'pk_content_type' => 18,
                'name'            => 'frontpage',
                'title'           => _('Frontpage')
            ],
        ];

        return $contentTypes;
    }

    /**
     * Returns the list of content types for the modules activated
     *
     * @return array the list of content types
     */
    public static function getContentTypesFiltered()
    {
        $contentTypes         = \ContentManager::getContentTypes();
        $contentTypesFiltered = [];

        foreach ($contentTypes as $contentType) {
            switch ($contentType['name']) {
                case 'advertisement':
                    $moduleName = 'ads';
                    break;
                case 'attachment':
                    $moduleName = 'file';
                    break;
                case 'photo':
                    $moduleName = 'image';
                    break;
                case 'static_page':
                    $moduleName = 'static_pages';
                    break;
                default:
                    $moduleName = $contentType['name'];
                    break;
            }

            $moduleName = strtoupper($moduleName . '_MANAGER');

            if (getService('core.security')->hasExtension($moduleName)) {
                $contentTypesFiltered[$contentType['name']] = $contentType['title'];
            }
        }

        return $contentTypesFiltered;
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
     * @return boolean|string the content type name
     */
    public static function getContentTypeNameFromId($id, $ucfirst = false)
    {
        if (empty($id)) {
            return false;
        }

        if (is_numeric($id)) {
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
     * Returns the content objects from a list of content ids
     *
     * @param array $contentIds list of content ids to fetch
     *
     * @return array the list of content objecst
     */
    public function getContents($contentIds)
    {
        $contents = [];

        if (is_array($contentIds) && count($contentIds) > 0) {
            foreach ($contentIds as $contentId) {
                if ($contentId <= 0) {
                    continue;
                }

                $content = \Content::get($contentId);
                if (isset($content->pk_content) && $content->pk_content == $contentId) {
                    $contents[] = $content;
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
     */
    public static function getRelatedImagesForContentsWithIDs($relatedImagesIDs)
    {
        // If the given ids is an unique element transform it to an array.
        if (!is_array($relatedImagesIDs) && !empty($relatedImagesIDs)) {
            $relatedImagesIDs = [ $relatedImagesIDs ];
        }

        // If the related images id array is empty just return an empty array
        if (!(count($relatedImagesIDs) > 0)) {
            return [];
        }

        // Fetch the images from SQL
        $relatedImagesSQL = implode(',', $relatedImagesIDs);
        $cm               = new ContentManager();
        $images           = $cm->find('Photo', "pk_content IN ($relatedImagesSQL)");

        return $images;
    }

    /**
     * Returns an array of related contents for one content given its id
     *
     * @param int $contentID the id of the content to get its related content
     *
     * @return array list of related content
     */
    public function getRelatedContentFromContentID($contentID)
    {
        $ccm = new ContentCategoryManager();

        // Fetch relations
        $relations = getService('related_contents')->getRelations($contentID, 'frontpage');

        if (count($relations) == 0) {
            return [];
        }

        $contentObjects = getService('entity_repository')->findMulti($relations);

        // Filter out not ready for publish contents.
        $relatedContent = [];
        foreach ($contentObjects as $content) {
            if (!$content->isReadyForPublish()) {
                continue;
            }

            $content->category_name = $ccm->getName($content->category);

            $relatedContent[] = $content;
        }

        return $relatedContent;
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
        $cache = getService('cache');

        $cacheKey        = CACHE_PREFIX . '_' . urlencode($url);
        $externalContent = $cache->fetch($cacheKey);

        if (!$externalContent) {
            $c = curl_init($url);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
            $externalContent = curl_exec($c);
            $cache->save($cacheKey, $externalContent, 300);
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
     */
    public function getLatestComments($count = 6)
    {
        $contents = [];

        $sql = 'SELECT DISTINCT(comments.content_id), comments.date as comment_date,'
            . ' comments.body as comment_body, comments.author as comment_author,'
            . ' comments.id as comment_id, contents.* FROM comments, contents '
            . 'WHERE contents.pk_content = comments.content_id '
            . 'AND contents.fk_content_type = 1 AND contents.in_litter <> 1 '
            . 'AND comments.status = ? ORDER BY comments.date DESC LIMIT ?';

        try {
            $rs = getService('dbal_connection')->fetchAll(
                $sql,
                [ \Comment::STATUS_ACCEPTED, $count ]
            );

            foreach ($rs as $contentData) {
                $content = new \Article();
                $content->load($contentData);
                $content->comment        = $contentData['comment_body'];
                $content->pk_comment     = $contentData['comment_id'];
                $content->comment_author = $contentData['comment_author'];

                $contents[$content->pk_comment] = $content;
            }

            return $contents;
        } catch (\Exception $e) {
            getService('error.log')->error(
                $e->getMessage() . ' Stack Trace: ' . $e->getTraceAsString()
            );

            return [];
        }
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
        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                "SELECT pk_content FROM `contents` WHERE pk_content = ?",
                [ $oldID ]
            );

            if (is_null($rs)) {
                return false;
            }

            return $rs['pk_content'];
        } catch (\Exception $e) {
            getService('error.log')->error(
                $e->getMessage() . ' Stack Trace: ' . $e->getTraceAsString()
            );

            return false;
        }
    }

    /**
     * Checks and cleans articles and opinions from frontpage when the frontpage
     * limit is reached.
     *
     * @param  array $contentIds The array of contents to check.
     *
     * @return array The array of cleaned contents.
     */
    public function checkAndCleanFrontpageSize($contents)
    {
        $contentsNotAdvertisements = array_filter($contents, function ($content) {
            return $content->content_type_name !== 'advertisement';
        });

        $elementsToRemove = count($contentsNotAdvertisements) - self::$frontpage_limit;

        // Remove first from placeholder_0_0
        if ($elementsToRemove > 0) {
            // Sort by content_id
            usort($contents, function ($a, $b) {
                if ($a->id == $b->id) {
                    return 0;
                }

                return ($a->starttime < $b->starttime) ? -1 : 1;
            });

            foreach ($contents as $key => $content) {
                if ($content->content_type_name === 'article'
                    || $content->content_type_name === 'opinion'
                ) {
                    unset($contents[$key]);
                    $elementsToRemove--;
                }
            }

            getService('session')->getFlashBag()->add(
                'error',
                _('Some elements were removed because this frontpage had too many contents.')
            );
        }

        return $contents;
    }

    /**
     * Returns a list of metaproperty values from a list of contents
     *
     * @param string $property the property name to fetch
     *
     * @return boolean true if it is in the category
     */
    public static function getMultipleProperties($propertyMap)
    {
        if (empty($propertyMap)) {
            return [];
        }

        $map = $values = [];
        foreach ($propertyMap as $property) {
            $map[]    = '(fk_content=? AND `meta_name`=?)';
            $values[] = $property[0];
            $values[] = $property[1];
        }

        try {
            $rs = getService('dbal_connection')->fetchAll(
                'SELECT `fk_content`, `meta_name`, `meta_value` '
                . 'FROM `contentmeta` WHERE (' . implode(' OR ', $map) . ')',
                $values
            );

            return $rs;
        } catch (\Exception $e) {
            getService('error.log')->error(
                $e->getMessage() . ' Stack Trace: ' . $e->getTraceAsString()
            );

            return false;
        }
    }

    /**
     * Sets a metaproperty for the actual content
     *
     * @param string $id the id of the content
     * @param string $property the name of the property
     * @param mixed $value     the value of the property
     *
     * @return boolean true if the property was setted
     */
    public static function setContentMetadata($id, $property, $value)
    {
        if (is_null($id) || empty($property)) {
            return false;
        }

        try {
            getService('dbal_connection')->executeUpdate(
                "INSERT INTO contentmeta (`fk_content`, `meta_name`, `meta_value`)"
                . " VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `meta_value`=?",
                [ $id, $property, $value, $value ]
            );

            return true;
        } catch (\Exception $e) {
            getService('error.log')->error(
                $e->getMessage() . ' Stack Trace: ' . $e->getTraceAsString()
            );

            return false;
        }
    }
}
