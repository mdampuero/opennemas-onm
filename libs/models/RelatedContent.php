<?php
/**
 * Handles all the CRUD actions over Related contents.
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Model
 */
use Onm\Cache\CacheInterface;
use Repository\EntityManager;

/**
 * Handles all the CRUD actions over Related contents.
 *
 * @package    Model
 */
class RelatedContent
{
    /**
     * Content id of the first content
     *
     * @var int
     */
    public $pk_content1 = null;

    /**
     * Content id of the second content
     *
     * @var int
     */
    public $pk_content2 = null;

    /**
     * Relation type (inner, home, ...)
     *
     * @var string
     */
    public $relationship = null;

    /**
     * Information about the relation
     *
     * @var string
     */
    public $text = null;

    /**
     * Position in the list when multiple relations of the same type
     *
     * @var int
     */
    public $position = null;

    /**
     * Position in the list of inner
     *
     * @var int
     */
    public $posinterior = null;

    /**
     * Whether showing this relation in frontpage
     *
     * @var boolean
     */
    public $verportada = null;

    /**
     * Whether showing this relation in inner
     *
     * @var boolean
     */
    public $verinterior = null;

    /**
     * Initializes the RelatedContent.
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
     * Creates a relation between two contents given its ids.
     *
     * @param string $contentID  the content id of the first content
     * @param string $contentID2 the content id of the second content
     * @param int    $position   the weight of the relation, for sorting
     * @param int    $posint     the weight of the relation in inner
     * @param string $verport    true if the relation must be shown in frontpage
     * @param string $verint     true if the relation must be shown in inner
     * @param string $relation   kind of relation to assign
     *
     * @return boolean true if relation was created sucessfully.
     */
    public function create(
        $contentID,
        $contentID2,
        $position = 1,
        $posint = 1,
        $verport = null,
        $verint = null,
        $relation = null
    ) {
        $sql = "INSERT INTO related_contents "
               . "(`pk_content1`, `pk_content2`, `position`, "
               . "`posinterior`, `verportada`, `verinterior`, `relationship`) "
               . "VALUES (?,?,?,?,?,?,?)";

        $values = [
            $contentID, $contentID2, $position,
            $posint, $verport, $verint, $relation
        ];

        if ($this->dbConn->executeUpdate($sql, $values) === false) {
            return false;
        }

        return true;
    }

    /**
     * Updates relations from an array of elements.
     *
     * @param array $data list of elements with information for relations.
     */
    public function update($data)
    {
        $sql = "UPDATE related_contents SET `pk_content2`=?, `relationship`=?,"
               . " `text`=?, `position`=? WHERE pk_content1=?";

        $values = [
            $data['pk_content2'],
            $data['relationship'],
            $data['text'],
            $data['position'],
            $data['id'],
        ];

        if ($this->dbConn->executeUpdate($sql, $values) === false) {
            return false;
        }

        return true;
    }

    /**
     * Delete all the relations for an element given its id.
     *
     * @param string $contentID the element id.
     *
     * @return boolean true if the relations were removed
     */
    public function delete($contentID)
    {
        $sql = 'DELETE FROM related_contents WHERE pk_content1=?';

        if ($this->dbConn->executeUpdate($sql, [$contentID]) === false) {
            return false;
        }

        return true;
    }

    /**
     * Delete all the relations for a given element,
     * relations with other objects id->XXX and other objects with id XXX->id.
     *
     * @param string $contentID the element id.
     *
     * @return boolean true if all went well
     */
    public function deleteAll($contentID)
    {
        $sql    = "DELETE FROM related_contents WHERE pk_content1 = ? OR pk_content2 = ?";
        $values = [ $contentID, $contentID ];

        if ($this->dbConn->executeUpdate($sql, $values) === false) {
            return false;
        }

        return true;
    }

    /**
     * Get contents related to $contentID for frontpage
     *
     * @param int $contentID Content ID
     *
     * @return array Array of related content IDs
     */
    public function getRelations($contentID, $position = 'frontpage', $limit = null)
    {
        $sql = 'SELECT pk_content2, content_type_name FROM related_contents '
            . 'LEFT JOIN contents ON pk_content2 =  pk_content WHERE pk_content1=? ';

        switch ($position) {
            case 'frontpage': // Old getRelations
                $sql .= "AND verportada=1 ";
                break;

            case 'home': // Old getHomeRelations
                $sql .= "AND verportada=2";
                break;

            case 'inner': // Old getRelationsForInner
                $sql .= "AND verinterior=1 ";
                break;

            default:
                break;
        }

        $sql .= " ORDER BY related_contents.position ASC";

        if (!is_null($limit)) {
            $sql = $sql . " LIMIT " . $limit;
        }

        $rs = $this->dbConn->fetchAll($sql, [$contentID]);

        if (!$rs) {
            return [];
        }

        $related = [];
        foreach ($rs as $value) {
            if (!empty($value['content_type_name']) && !empty($value['pk_content2'])) {
                $related[] = [ classify($value['content_type_name']), $value['pk_content2'] ];
            }
        }

        return $related;
    }

    /**
     * Creates a fronpage relation between one content and a list of content id or a given
     * position
     *
     * @param int $contentID the content ID to relate with others
     * @param string $position the position name where relations will be stored
     * @param array $relationID the content ID of the other content to relate with
     *
     * @return boolean true if all went well
     */
    public function setRelationPosition($contentID, $position, $relationID)
    {
        $sql = "INSERT INTO related_contents "
               . "(`pk_content1`, `pk_content2`, `position`, `verportada`) "
               . "VALUES (?,?,?,?)";

        $rs = $this->dbConn->executeUpdate(
            $sql,
            [$contentID, $relationID, $position, 1]
        );

        if (!$rs) {
            return false;
        }

        return true;
    }

    /**
     * Creates a inner relation between one content and a list of content id or a given
     * position
     *
     * @param int $contentID the content ID to relate with others
     * @param string $position the position name where relations will be stored
     * @param array $relationID the content ID of the other content to relate with
     *
     * @return boolean true if all went well
     */
    public function setRelationPositionForInner($contentID, $position, $relationID)
    {
        $sql = "INSERT INTO related_contents "
               . "(`pk_content1`, `pk_content2`, `posinterior`,`verinterior`) "
               . " VALUES (?,?,?,?)";

        $rs = $this->dbConn->executeUpdate(
            $sql,
            [$contentID, $relationID, $position, 1]
        );

        if (!$rs) {
            return false;
        }

        return true;
    }

    /**
     * Sets the relation between two contents for frontpage
     *
     * @param int $contentID the content ID to relate with
     * @param string $position the position name where relations will be stored
     * @param int $relationID the content ID to relate with the first one
     *
     *
     * @return boolean true if the relations were saved
     */
    public function setHomeRelations($contentID, $position, $relationID)
    {
        $sql = "INSERT INTO related_contents "
               . "(`pk_content1`, `pk_content2`, `position`,`verportada`) "
               . " VALUES (?,?,?,?)";

        $rs = $this->dbConn->executeUpdate(
            $sql,
            [$contentID, $relationID, $position, 2]
        );

        if (!$rs) {
            return false;
        }

        return true;
    }

    /**
     * Returns a list of related contents grouped by content id.
     *
     * @param array  $ids      The list of content ids.
     * @param string $category The category name.
     *
     * @return array The list of related contents grouped by content id.
     */
    public function getRelatedContents($ids, $category = 0)
    {
        $verPortada = 1;

        if (empty($ids)) {
            return [];
        }

        if (!is_array($ids)) {
            $ids = [ $ids ];
        }

        if (getService('core.security')->hasExtension('CRONICAS_MODULES')
            && $category === 0
        ) {
            $verPortada = 2;
        }

        $ids = array_filter($ids, function ($id) {
            return !is_null($id);
        });

        $sql = "SELECT pk_content1, pk_content2, position FROM related_contents "
            . "WHERE pk_content1 in (" . implode(',', $ids)
            . ") AND verportada=" . $verPortada . " ORDER BY position ASC";

        $rs = $this->dbConn->executeQuery($sql);

        if (!$rs) {
            return [];
        }

        $related = [];
        foreach ($rs as $value) {
            $related[$value['pk_content1']][] = $value['pk_content2'];
        }

        return $related;
    }
}
