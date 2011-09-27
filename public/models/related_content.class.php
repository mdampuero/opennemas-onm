<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Handles all the CRUD actions over Related contents.
 *
 * @package    Onm
 * @subpackage Model
 * @author     me
 **/
class Related_content
{
    public $pk_content1 = null;
    public $pk_content2 = null;
    public $relationship = null;
    public $text = null;
    public $cache = null;
    public $position = null;
    public $posinterior = null;
    public $verportada = null;
    public $verinterior = null;

    /**
     * Gets the relations for a given id.
     *
     * @param string $contentID the element id.
     *
     * @return void
     **/
    public function __construct($contentID = null)
    {
        if (!is_null($contentID)) $this->read($contentID);
        $this->cache = new MethodCacheManager($this, array('ttl' => 30));
    }

    /**
     * Creates a relation between two contents given its ids.
     *
     * @param string $contentID the content id.
     * @param string $contentID2 the content id.
     * @param int $position the weight of the relation, for sorting
     * @param int $posint the weight of the relation in inner
     * @param string $verport
     * @param string $verint
     * @param string $relation
     *
     * @return boolean true if relation was created sucessfully.
     **/
    public function create($contentID, $contentID2, $position = 1, $posint = 1,
                           $verport = null, $verint = null, $relation = null)
    {

        $sql = "INSERT INTO related_contents (`pk_content1`, `pk_content2`,
                                              `position`,  `posinterior`,
                                              `verportada`, `verinterior`,
                                              `relationship`) " . "
                VALUES (?,?,?,?,?,?,?)";
        $values = array(
            $contentID, $contentID2, $position,
            $posint, $verport, $verint, $relation
        ); //positions=1


        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: ' . $errorMsg);
            $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;
            return (false);
        }
        return (true);
    }

    /**
     * Magic method for loading data from array and inject it in the object.
     *
     * @param array $properties the properties to inject inside the object.
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
    }

    /**
     * Getches all the relations for a given element.
     *
     * @param string $contentID the id of the element.
     **/
    public function read($contentID)
    {

        $sql = 'SELECT * FROM related_contents WHERE pk_content1=?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array($contentID));

        if (!$rs) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: ' . $errorMsg);
            $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;
            return;
        }
        $this->pk_content1 = $rs->fields['pk_content1'];
        $this->pk_content2 = $rs->fields['pk_content2'];
        $this->position = $rs->fields['position'];
        $this->posinterior = $rs->fields['posinterior'];
        $this->verportada = $rs->fields['verportada'];
        $this->verinterior = $rs->fields['verinterior'];
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
                . "WHERE pk_content1=" . ($data['id']);
        $values = array(
            $data['pk_content2'], $data['relationship'],
            $data['text'], $data['position']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: ' . $errorMsg);
            $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;
            return;
        }
    }

    /**
     * Delete all the relations for a given element.
     *
     * @param string $contentID the element id.
     **/
    public function delete($contentID)
    {
        $sql = 'DELETE FROM related_contents WHERE pk_content1=' . ($contentID);

        if ($GLOBALS['application']->conn->Execute($sql) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: ' . $errorMsg);
            $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;
            return;
        }
    }

    /**
     * Delete all the relations for a given element,
     * relations with other objects id->XXX and other objects with id XXX->id.
     *
     * @param string $contentID the element id.
     **/
    public function delete_all($contentID)
    {
        $sql = "DELETE FROM related_contents"
               ." WHERE pk_content1=? OR pk_content2=?";

        $values = array($contentID, $contentID);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: ' . $errorMsg);
            $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;
            return;
        }
    }

    /**
     * Get contents related to $contentID for frontpage
     *
     * @param int $contentID Content ID
     *
     * @return array Array of related content IDs
     */
    public function get_relations($contentID)
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
    public function get_relations_int($contentID)
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

    public function get_relations_vic($contentID)
    {
        $related = array();

        if ($contentID) {
            $sql = "SELECT pk_content1 FROM related_contents"
                   ." WHERE pk_content2=? ORDER BY position ASC";
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

    static public function get_content_relations($contentID)
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

    //Define relacion entre noticias y entre publi y noticias
    public function set_relations($contentID, $relations)
    {
        $relations->delete($contentID);

        if ($relations) {
            foreach ($relations as $related) {
                $relations = new Related_content();
                $relations->create($contentID, $related);
            }
            return;
        }
    }

    //Cambia la posicion en portada
    public function set_rel_position($contentID, $position, $relationID)
    {
        $sql =  "SELECT position FROM related_contents"
                ." WHERE pk_content1=? AND pk_content2 =?";
        $values = array($contentID, intval($relationID));
        $rs = $GLOBALS['application']->conn->Execute($sql, $values);

        if (isset($rs->fields['position'])) {
            $sql = "UPDATE related_contents "
                    ."SET `verportada`=?, `position`=?"
                    ." WHERE pk_content1=? AND pk_content2=?";
            $values = array(1, $position, $contentID, $relationID);
        } else {
            $sql =  "INSERT INTO related_contents (`pk_content1`, `pk_content2`,
                                                  `position`,`verportada`) "
                    . " VALUES (?,?,?,?)";
            $values = array($contentID, $relationID, $position, 1);
        }

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: ' . $errorMsg);
            $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;
            return;
        }
    }

    //Cambia la posicion en el interior
    public function set_rel_position_int($contentID, $position, $relationID)
    {

        $sql =  "SELECT position FROM related_contents"
                ." WHERE pk_content1=? AND pk_content2 =?" ;
        $values = array($contentID, intval($relationID));
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (isset($rs->fields['position'])) {
            $sql =  "UPDATE related_contents"
                    ." SET  `verinterior`=?, `posinterior`=?"
                    ." WHERE pk_content1=? AND pk_content2=?";
            $values = array(1, $position, $contentID, $relationID);
        } else {
            $sql = "INSERT INTO related_contents (`pk_content1`, `pk_content2`,
                                                  `posinterior`,`verinterior`) "
                    ." VALUES (?,?,?,?)";
            $values = array($contentID, $relationID, $position, 1);
        }

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: ' . $errorMsg);
            $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;
            return;
        }
    }

    public function sortArticles($articles)
    {
        //Hay que coger las cats para que sean indices de los arrays
        $cc = new ContentCategoryManager();

        $allcategorys = $cc->find(
            'inmenu=1 AND internal_category=1 AND fk_content_category=0',
            'ORDER BY posmenu'
        );
        $i = 0;
        foreach ($allcategorys as $prima) {
            $sql =  ' inmenu=1  AND internal_category=1 '
                    .'AND fk_content_category =' . $prima->pk_content_category;
            $subcat[$i] = $cc->find($sql, 'ORDER BY posmenu');
            foreach ($articles as $article) {
                if (($article->category == $prima->pk_content_category)) {
                    $output[$prima->title][] = $article;
                }
            }
            foreach ($subcat[$i] as $prima) {
                foreach ($articles as $article) {

                    if (($article->category == $prima->pk_content_category)) {
                        $output[$prima->title][] = $article;
                    }
                }
            }
            $i++;
        }

        /*
        for ( $counter = 10; $counter <= 20; $counter++)	{
            foreach($articles as $article) {
                if (($article->category == $counter)
                    && ($article->content_status == 1)
                ) {
                    $output[ $article->category_name][] = $article;
                }
            }
        }*/
        return $output;
    }
}
