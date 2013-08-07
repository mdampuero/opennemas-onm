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
/**
 * Handles all the CRUD actions over Related contents.
 *
 * @package    Model
 **/
class RelatedContent
{
    /**
     * Content id of the first content
     *
     * @var int
     **/
    public $pk_content1  = null;

    /**
     * Content id of the second content
     *
     * @var int
     **/
    public $pk_content2  = null;

    /**
     * Relation type (inner, home, ...)
     *
     * @var string
     **/
    public $relationship = null;

    /**
     * Information about the relation
     *
     * @var string
     **/
    public $text         = null;

    /**
     * Proxy property for cache purpouses
     *
     * @var MethodCacheManager
     **/
    public $cache        = null;

    /**
     * Position in the list when multiple relations of the same type
     *
     * @var int
     **/
    public $position     = null;

    /**
     * Position in the list of inner
     *
     * @var int
     **/
    public $posinterior  = null;

    /**
     * Whether showing this relation in frontpage
     *
     * @var boolean
     **/
    public $verportada   = null;

    /**
     * Whether showing this relation in inner
     *
     * @var boolean
     **/
    public $verinterior  = null;

    /**
     * Gets the relations for a given id.
     *
     * @param string $contentID the element id.
     *
     * @return void
     **/
    public function __construct($contentID = null)
    {
        if (!is_null($contentID)) {
            $this->read($contentID);
        }
        $this->cache = new MethodCacheManager($this, array('ttl' => 30));
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
     **/
    public function create(
        $contentID,
        $contentID2,
        $position = 1,
        $posint = 1,
        $verport = null,
        $verint = null,
        $relation = null
    ) {

        $sql = "INSERT INTO related_contents
                (`pk_content1`, `pk_content2`, `position`,  `posinterior`,
                `verportada`, `verinterior`, `relationship`) " . "
                VALUES (?,?,?,?,?,?,?)";
        $values = array(
            $contentID, $contentID2, $position,
            $posint, $verport, $verint, $relation
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            return false;
        }

        return true;
    }

    /**
     * Magic method for loading data from array and inject it in the object.
     *
     * @param array $properties the properties to inject inside the object.
     *
     * @return RelationContent the overloaded related content object instance
     **/
    public function load($properties)
    {
        if (is_array($properties)) {
            foreach ($properties as $k => $v) {

                if (!is_numeric($k)) {
                    $this->{$k} = $v;
                }
            }
        } elseif (is_object($properties)) {
            $properties = get_object_vars($properties);
            foreach ($properties as $k => $v) {

                if (!is_numeric($k)) {
                    $this->{$k} = $v;
                }
            }
        }

        return $this;
    }

    /**
     * Get all the relations for a given element.
     *
     * @param string $contentID the id of the element.
     *
     * @return RelatedContent the related content object instance
     **/
    public function read($contentID)
    {
        $sql = 'SELECT * FROM related_contents WHERE pk_content1=?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array($contentID));

        if (!$rs) {
            return false;
        }

        $this->pk_content1 = $rs->fields['pk_content1'];
        $this->pk_content2 = $rs->fields['pk_content2'];
        $this->position    = $rs->fields['position'];
        $this->posinterior = $rs->fields['posinterior'];
        $this->verportada  = $rs->fields['verportada'];
        $this->verinterior = $rs->fields['verinterior'];

        return $this;
    }

    /**
     * Updates relations from an array of elements.
     *
     * @param array $data list of elements with information for relations.
     **/
    public function update($data)
    {
        $sql = "UPDATE related_contents"
                ."   SET `pk_content2`=?, `relationship`=?,"
                ."       `text`=?, `position`=?"
                . "WHERE pk_content1=?";
        $values = array(
            $data['pk_content2'],
            $data['relationship'],
            $data['text'],
            $data['position'],
            $data['id'],
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
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
     **/
    public function delete($contentID)
    {
        $sql = 'DELETE FROM related_contents WHERE pk_content1=?';

        if ($GLOBALS['application']->conn->Execute($sql, array($contentID)) === false) {
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
     **/
    public function deleteAll($contentID)
    {
        $sql = "DELETE FROM related_contents"
               ." WHERE pk_content1=? OR pk_content2=?";

        $values = array($contentID, $contentID);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
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
    public function getRelations($contentID)
    {
        $related = array();

        if ($contentID) {
            $sql = "SELECT pk_content2 FROM related_contents"
                   ." WHERE verportada=\"1\" AND pk_content1=?"
                   ." ORDER BY position ASC";
            $values = array($contentID);
            $rs = $GLOBALS['application']->conn->Execute($sql, $values);

            if ($rs === false) {
                return (array());
            } else {
                while (!$rs->EOF) {
                    $related[] = $rs->fields['pk_content2'];
                    $rs->MoveNext();
                }
            }
        }
        $related = array_unique($related);

        return $related;
    }

    /**
     * Get contents related to $contentID for inner article
     *
     * @param int $contentID Content ID
     *
     * @return array Array of related content IDs
     */
    public function getRelationsForInner($contentID)
    {
        $related = array();

        if ($contentID) {
            $sql = "SELECT DISTINCT pk_content2 FROM related_contents"
                   ." WHERE verinterior=\"1\" AND pk_content1=? "
                   . "ORDER BY posinterior ASC";
            $values = array($contentID);
            $rs = $GLOBALS['application']->conn->Execute($sql, $values);

            if ($rs === false) {
                return (array());
            } else {
                while (!$rs->EOF) {
                    $related[] = $rs->fields['pk_content2'];
                    $rs->MoveNext();
                }
            }
        }
        $related = array_unique($related);

        return $related;
    }

    /**
     * Returns all the relations for a given content
     *
     * @param int $contentID the content where search related contents from
     *
     * @return array Array of content ids
     **/
    public static function getContentRelations($contentID)
    {
        $related = array();

        if ($contentID) {
            $sql = "SELECT pk_content1 FROM related_contents"
                   ." WHERE  pk_content2=? ORDER BY position ASC";
            $values = array($contentID);
            $rs = $GLOBALS['application']->conn->Execute($sql, $values);

            if ($rs !== false) {
                while (!$rs->EOF) {
                    $related[] = $rs->fields['pk_content1'];
                    $rs->MoveNext();
                }
            }
        }
        $related = array_unique($related);

        return $related;
    }

    /**
     * Creates relations between one content and a list of content id
     *
     * @param int $contentID the content ID to relate with others
     * @param array $relations the list of contents to relate with
     *
     * @return boolean true if all went well
     **/
    public function setRelations($contentID, $relations)
    {
        $relations->delete($contentID);

        if ($relations) {
            foreach ($relations as $related) {
                $relations = new RelatedContent();
                $relations->create($contentID, $related);
            }

            return false;
        }

        return true;
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
     **/
    public function setRelationPosition($contentID, $position, $relationID)
    {
        $sql =  "SELECT position FROM related_contents"
                ." WHERE pk_content1=? AND pk_content2 =?  WHERE verportada=1";
        $values = array($contentID, intval($relationID));
        $rs = $GLOBALS['application']->conn->Execute($sql, $values);

        if (isset($rs->fields['position'])) {
            $sql = "UPDATE related_contents "
                    ."SET `verportada`=?, `position`=?"
                    ." WHERE pk_content1=? AND pk_content2=?";
            $values = array(1, $position, $contentID, $relationID);
        } else {
            $sql =  "INSERT INTO related_contents
                    (`pk_content1`, `pk_content2`,
                    `position`,`verportada`) "
                    . " VALUES (?,?,?,?)";
            $values = array($contentID, $relationID, $position, 1);
        }

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
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
     **/
    public function setRelationPositionForInner($contentID, $position, $relationID)
    {
        $rs = $GLOBALS['application']->conn->Execute(
            "SELECT position FROM related_contents WHERE pk_content1=? AND pk_content2 =?",
            array($contentID, intval($relationID))
        );

        if (isset($rs->fields['position'])) {
            $sql =  "UPDATE related_contents "
                    ."SET  `verinterior`=?, `posinterior`=? "
                    ."WHERE pk_content1=? AND pk_content2=?";
            $values = array(1, $position, $contentID, $relationID);
        } else {
            $sql = "INSERT INTO related_contents
                    (`pk_content1`, `pk_content2`, `posinterior`,`verinterior`) "
                    ." VALUES (?,?,?,?)";
            $values = array($contentID, $relationID, $position, 1);
        }

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
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
     **/
    public function setHomeRelations($contentID, $position, $relationID)
    {
        $rs = $GLOBALS['application']->conn->Execute(
            "SELECT position FROM related_contents"
            ." WHERE pk_content1=? AND pk_content2 =? AND verportada=2",
            array($contentID, intval($relationID))
        );

        if (isset($rs->fields['position'])) {
            $sql = "UPDATE related_contents "
                    ."SET `verportada`=?, `position`=?"
                    ." WHERE pk_content1=? AND pk_content2=?";
            $values = array(2, $position, $contentID, $relationID);
        } else {
            $sql =  "INSERT INTO related_contents (`pk_content1`, `pk_content2`,
                                                  `position`,`verportada`) "
                    . " VALUES (?,?,?,?)";
            $values = array($contentID, $relationID, $position, 2);
        }

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            return false;
        }

        return true;
    }

    /**
     * Returns the frontpage relations for a content given its id
     *
     * @param int $contentID the content ID to fetch relations from
     *
     * @return boolean true if the relations were saved
     **/
    public function getHomeRelations($contentID)
    {
        $rs = $GLOBALS['application']->conn->Execute(
            "SELECT DISTINCT pk_content2 FROM related_contents "
            ."WHERE pk_content1=? AND verportada=2 ORDER BY position ASC",
            array($contentID)
        );

        if ($rs === false) {
            return false;
        }

        $related = array();
        while (!$rs->EOF) {
            $related[] = $rs->fields['pk_content2'];
            $rs->MoveNext();
        }

        $related = array_unique($related);

        return $related;
    }
}
