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
                . ' FROM `contents`, `' . $table . '`, `content_category` '
                . ' WHERE ' . $where
                . ' AND `contents`.`pk_content`= `' . $table . '` . `pk_' . $contentType . '` '
                . ' AND `contents`.`pk_content`= `content_category`.`content_id` ' . $orderBy
            );

            return $this->loadObject($rs, $contentType);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Drops the suggested to frontpage flag from a list of contents given their ids
     *
     * @param array $contentIds the list of content ids to drop the suggested flag
     *
     * @return boolean true if all went well
     */
    public static function dropSuggestedFlagFromContentIdsArray($contentIds)
    {
        if (is_array($contentIds) && !empty($contentIds)) {
            try {
                getService('api.service.content')->patchList($contentIds, [ 'frontpage' => 0 ]);
            } catch (\Exception $e) {
                return false;
            }

            return true;
        }

        return false;
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
                [ 'value'  => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => $now, 'operator' => '>' ]
            ],
        ];

        $order = ['content_views.views' => 'desc'];
        if (!empty($category)) {
            if (!is_array($category)) {
                $category = [$category];
            }

            $criteria['category_id'] = [['value' => $category, 'operator' => 'IN']];
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
            'fk_content_type' => [[ 'value' => [ 1,4,7,9,11 ], 'operator' => 'IN' ]],
            'in_litter'       => [[ 'value' => 0 ]],
            'starttime'       => [[ 'value' => $date, 'operator' => '>=' ]],
            'endtime'         => [
                'union' => 'OR',
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => $now, 'operator' => '>' ],
            ],
        ];

        $order = [ 'content_views.views' => 'desc' ];

        if ($category) {
            $category = getService('api.service.category')->getItem($category);

            $criteria['category_id'] = [
                [ 'value' => $category->id ]
            ];
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
            $filtered = array_filter($items, function ($item) {
                return getService('core.helper.content')->isIntime($item);
            });
        }

        return array_values($filtered);
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
     * @param int    $categoryID the category id to search for
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
                 . 'FROM `content_category`, `contents`, ' . $table . '  '
                 . ' WHERE `content_category`.`category_id`=' . $categoryID
                 . '  AND pk_content=`' . $table . '`.`pk_' . $contentType
                 . '` AND  `content_category`.`content_id` = `contents`.`pk_content` '
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
            $sql = 'SELECT * FROM content_category, contents, ' . $table . '  '
                 . 'WHERE ' . $whereSQL
                 . ' AND `content_category`.`category_id`=' . $categoryID
                 . ' AND `contents`.`pk_content`=`' . $table . '`.`pk_' . $contentType . '` '
                 . ' AND  `content_category`.`content_id` = `contents`.`pk_content` '
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
                'pk_content_type' => 17,
                'name'            => 'letter',
                'title'           => _('Letter to editor')
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
    * Fetches the content for one specific url
    *
    * This is used for getting information from Onm Rest Api
    *
    * @param string $url the url we want to get contents from
    *
    * @param boolean $decodeJson if true apply json_decode before return content
    *
    * @return false the content retrieved by the url
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
        $em       = getService('entity_repository');
        $contents = [];

        $sql = 'SELECT DISTINCT(comments.content_id), comments.date as comment_date,'
            . ' comments.body as comment_body, comments.author as comment_author,'
            . ' comments.id as comment_id, contents.* FROM comments, contents '
            . 'WHERE contents.pk_content = comments.content_id '
            . 'AND contents.fk_content_type = 1 AND contents.in_litter <> 1 '
            . 'AND comments.status = "accepted" ORDER BY comments.date DESC LIMIT ' . $count;

        try {
            $rs = getService('dbal_connection')->fetchAll($sql);

            foreach ($rs as $contentData) {
                $content = new \Content();
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
     * Sets a metaproperty for the actual content.
     *
     * @param string $id       The id of the content.
     * @param string $property The name of the property.
     * @param mixed  $value    The value of the property.
     *
     * @return boolean true If the property was setted.
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
