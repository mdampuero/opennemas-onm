<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Onm\Message as m;
/**
 * Class for managing attached contents.
 *
 * @package    Onm
 * @subpackage Model
 * @author     Fran Dieguez <fran@openhost.es>
 **/
class Attach_content
{
    var $pk_attachment  = NULL;
    var $pk_content  = NULL;
    var $position = NULL;
    var $posinterior = NULL;
    var $verportada = NULL;
    var $vertitulo = NULL;
    var $verinterior = NULL;
    var $titulo = NULL;

    /**
     * Initializes the Application class.
     **/
    public function __construct($id=NULL)
    {
        if (!is_null($id)) {
            $this->read($id);
        }
    }

    /**
     * Magic function to get uninitilized object properties.
     *
     * @param string $name the name of the property to get.
     *
     * @return mixed the value for the property
     **/
    public function create($id, $idRelated, $position,
                           $posint, $verport, $verint, $titulo)
    {

        $sql = "INSERT INTO attachments_contents (`pk_attachment`, `pk_content`,
                                                  `position`,  `posinterior`,
                                                  `verportada`, `verinterior`,
                                                  `titulo`) " .
                "VALUES (?,?,?,?,?,?)";

        $values = array(
            $idRelated, $id, $position, $posint, $verport, $verint, $titulo
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return false;
        }

        return true;
    }

    /**
     * Fetches the relations of an element by its id.
     *
     * @param string $id the album id to get info from.
     **/
    public function read($id)
    {
        $sql = 'SELECT * FROM attachments_contents WHERE pk_content = '.($id);
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return;
        }

        $this->pk_attachment = $rs->fields['pk_attachment'];
        $this->pk_content = $rs->fields['pk_content'];
        $this->position = $rs->fields['position'];
        $this->posinterior = $rs->fields['posint'];
        $this->verportada = $rs->fields['verportada'];
        $this->verinterior = $rs->fields['verinterior'];
        $this->titulo = $rs->fields['titulo'];

    }

    /**
     * Updates the information of the attachment relation given
     * an array of key-values
     *
     * @param array $data the new data to update the album
     **/
    public function update($data)
    {
        $sql = "UPDATE attachments_contents SET `pk_attachment`=?, `position`=?
                WHERE pk_content=".($data['id']);

        $values = array($data['pk_attachment'],$data['position']);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return;
        }

    }

    /**
     * Removes the attachments relations for a given id.
     *
     * @param string $id the attachment id
     **/
    public function delete($id)
    {
        $sql = 'DELETE FROM attachments_contents WHERE pk_content='.($id);

        if ($GLOBALS['application']->conn->Execute($sql)===false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

            return;
        }
    }

    public function att_delete($id,$attachmentID)
    {
        $sql = "DELETE FROM attachments_contents WHERE pk_content=\"?\""
               ." AND pk_attachment=\"?\"";
        $values = array($id, $attachmentID);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return;
        }
    }

    /**
     * Hides the relation in frontpage and inner for an element given its id.
     *
     * @param string $id the id of element.
     **/
    public function hide($id)
    {
        $sql =  "UPDATE attachments_contents "
                ."SET `verportada`=0, `verinterior`=0 WHERE pk_content=?";
        $values = array($id);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return;
        }
    }

    /**
     * Sets the positions for the element in frontpage.
     *
     * @param string $contentID the id of the content.
     *
     * @param string $position the position of the related element
     *
     * @param string $attachmentID the id of the related element
     **/
    public function set_att_position($contentID,$position,$attachmentID)
    {
        $sql =  "SELECT * FROM attachments_contents"
                ." WHERE pk_content=? AND pk_attachment=?";
        $values = array($contentID, $attachmentID);

        $rs = $GLOBALS['application']->conn->Execute($sql);

        if ($rs->fields['pk_attachment']) {
            $sql = "UPDATE attachments_contents"
                    ."SET  `verportada`=?, `position`=?"
                    . " WHERE pk_content=? AND pk_attachment=?" ;
            $values = array(1,$position, $contentID, $attachmentID);
        } else {
            $sql =  "INSERT INTO attachments_contents ( `pk_content`,
                                                      `pk_attachment`,
                                                      `position`,
                                                      `verportada`) "
                    ." VALUES (?,?,?,?)";

            $values = array($contentID, $attachmentID, $position, 1);
        }

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return;
        }

    }


    /**
     * Sets the positions for the element in inner.
     *
     * @param string $contentID the id of the content.
     *
     * @param string $position the position of the related element
     *
     * @param string $attachmentID the id of the related element
     **/
    public function set_att_position_Int($contentID,$position,$attachmentID)
    {
        $sql =  "SELECT * FROM attachments_contents "
                ."WHERE pk_content = " .($contentID)
                ."AND pk_attachment = " .($attachmentID);
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if ($rs->fields['pk_attachment']) {
            $sql = "UPDATE attachments_contents "
                    ."SET  `verinterior`=?, `posinterior`=?"
                    ." WHERE pk_content=".($contentID)
                    ." AND pk_attachment=".($attachmentID) ;
              $values = array(1,$position);
        } else {
            $sql = "INSERT INTO attachments_contents (`pk_content`,
                                                      `pk_attachment`,
                                                      `posinterior`,
                                                      `verinterior`) "
                    ." VALUES (?,?,?,?)";
            $values = array($contentID, $attachmentID,$position,1);
        }

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
             $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
             $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
             $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
             return;
        }

    }

    /**
     * Returns an array with pk_attach related to a given element id
     *
     * @param string $contentID the id of the content.
     *
     * @return array the list of related elements ids
     **/
    public function get_attach($contentID)
    {
        $related = array();

        if ($contentID) {
            $sql =  "SELECT pk_attachment FROM attachments_contents"
                    ." WHERE pk_content=?"
                    ." ORDER BY posinterior ASC";
            $rs = $GLOBALS['application']->conn->Execute(
                $sql,
                array($contentID)
            );

            if ($rs!==false) {
                while (!$rs->EOF) {
                    $related[] = $rs->fields['pk_attachment'];
                    $rs->MoveNext();
                }
            }
        }
        return $related;
    }

    /**
     * Returns an array with pk_attach related to a given element id for
     * frontpage
     *
     * @param string $contentID the id of the content.
     *
     * @return array the list of related elements ids
     **/
    public function get_attach_relations($contentID)
    {
        $related = array();
        if ($contentID) {
            $sql =  "SELECT pk_attachment FROM attachments_contents"
                    ." WHERE verportada=\"1\" AND pk_content=?"
                    ." ORDER BY position ASC";
            $rs = $GLOBALS['application']->conn->Execute(
                $sql,
                array($contentID)
            );

            if ($rs!==false) {
                while (!$rs->EOF) {
                    $related[] = $rs->fields['pk_attachment'];
                    $rs->MoveNext();
                }
            }
        }
        return( $related);

    }

    /**
     * Returns an array with pk_attach related to a given element id for
     * inner
     *
     * @param string $contentID the id of the content.
     *
     * @return array the list of related elements ids
     **/
    function get_attach_relations_int($contentID)
    {
        $related = array();
        if ($contentID) {
            $sql =  "SELECT pk_attachment FROM attachments_contents"
                    ." WHERE verinterior=\"1\" AND pk_content = ?"
                    ." ORDER BY posinterior ASC";
            $rs = $GLOBALS['application']->conn->Execute(
                $sql,
                array($contentID)
            );

            if ($rs !== false) {
                while (!$rs->EOF) {
                    $related[] = $rs->fields['pk_attachment'];
                    $rs->MoveNext();
                }
            }
        }
        return( $related);
    }

    /**
     * Returns an array with pk_attach related to a given element id
     *
     * @param string $contentID the id of the content.
     *
     * @return array the list of related elements ids
     **/
    public function read_rel($id)
    {
        $sql = 'SELECT * FROM attachments_contents
                WHERE pk_attachment = '.($id);
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

            return;
        }

        while (!$rs->EOF) {
            $related[] = $rs->fields['pk_content'];
              $rs->MoveNext();
        }
        return( $related);
    }

}
