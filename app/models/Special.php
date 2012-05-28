<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Handles all the CRUD actions over albums.
 *
 * @package    Onm
 * @subpackage Model
 **/

class Special extends Content
{
    /**
     * the special id
     */
    public $pk_special  = null;
    /**
     * the subtitle for this album
     */
    public $subtitle = null;
    /**
     * path for get a pdf file
     */
    public $pdf_path  = null;
    /**
     * the id of the image that is the cover for this album
     */

    public $img1  = null;

    /**
     * Initializes the Special class.
     *
     * @param strin $id the id of the album.
     **/
    public function __construct($id=null)
    {
       parent::__construct($id);

        if (!is_null($id)) {
            $this->read($id);
        }
        $this->content_type = __CLASS__;

        return $this;
    }

  /**
     * Magic function for getting uninitilized object properties.
     *
     * @param string $name the name of the property to get.
     *
     * @return mixed the value for the property
     **/
    public function __get($name)
    {

        switch ($name) {

            case 'uri': {
                if (empty($this->category_name)) {
                    $this->category_name =
                        $this->loadCategoryName($this->pk_content);
                }
                $uri =  Uri::generate(
                    'special',
                    array(
                        'id' => sprintf('%06d',$this->id),
                        'date' => date('YmdHis', strtotime($this->created)),
                        'category' => $this->category_name,
                        'slug' => $this->slug,
                    )
                );

                return ($uri !== '') ? $uri : $this->permalink;

                break;
            }
            case 'slug': {
                return String_Utils::get_title($this->title);
                break;
            }

            case 'content_type_name': {
                $contentTypeName = $GLOBALS['application']->conn->Execute(
                    'SELECT * FROM `content_types` '
                    .'WHERE pk_content_type = "'. $this->content_type
                    .'" LIMIT 1'
                );

                if (isset($contentTypeName->fields['name'])) {
                    $returnValue = $contentTypeName;
                } else {
                    $returnValue = $this->content_type;
                }
                $this->content_type_name = $returnValue;

                return $returnValue;

                break;
            }

            default: {
                break;
            }
        }

        parent::__get($name);
    }

    /**
     * Creates an special from a data array and stores it in db
     *
     * @param array $data the data of the special
     *
     * @return bool true if the object was stored
     */
    public function create($data)
    {
        parent::create($data);

        if (!array_key_exists('pdf_path', $data)) {
            $data['pdf_path']='';
        }

        $sql = "INSERT INTO specials "
             . "(`pk_special`, `subtitle`, `img1`, `pdf_path`)"
             . " VALUES (?,?,?,?)";

        $values = array(
            $this->id,
            $data['subtitle'],
            $data['img1'],
            $data['pdf_path']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return(false);
        }

        if (empty($data['pdf_path'])) {
             $this->saveItems($data);
        }

        return $this->id;
    }

    public function read($id)
    {
        parent::read($id);

        $sql = 'SELECT * FROM specials WHERE pk_special = '.intval($id);
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            \Application::logDatabaseError();

            return;
        }
        $this->id         = $rs->fields['pk_special'];
        $this->pk_special = $rs->fields['pk_special'];
        $this->subtitle   = $rs->fields['subtitle'];
        $this->img1       = $rs->fields['img1'];
        $this->pdf_path   = $rs->fields['pdf_path'];

    }

    public function update($data)
    {
        parent::update($data);

        if (!array_key_exists('pdf_path', $data)) {
            $data['pdf_path'] = '';
        }

        $sql = "UPDATE specials SET `subtitle`=?, `img1`=?,  `pdf_path`=?  ".
                "WHERE pk_special=".intval($data['id']);
        $values = array(  $data['subtitle'], $data['img1'],  $data['pdf_path'] );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return;
        }

        if (empty($data['pdf_path']) ) {
            $this->saveItems($data);
        }

        return true;
    }


    public function remove($id)
    {
        parent::remove($id);

        $sql = 'DELETE FROM specials WHERE pk_special='.intval($id);

        if ($GLOBALS['application']->conn->Execute($sql)===false) {
            \Application::logDatabaseError();

            return;
        }
        $sql = 'DELETE FROM special_contents WHERE fk_special = ' .intval($id);

        if ($GLOBALS['application']->conn->Execute($sql)===false) {
            \Application::logDatabaseError();

            return;
        }
    }

    public function saveItems($data)
    {
        $this->delete_all_contents($data['id']);
        if (isset($data['noticias_left'])) {
            $tok = strtok($data['noticias_left'], ",");
            $name = "";
            $pos = 1;
            $contentType = 'Article';
            while (($tok !== false) AND ($tok !=" ")) {
                // $this->delete_contents($data['id'] ,$tok)  	;
                $this->set_contents($data['id'] , $tok, $pos, $name, $contentType);
                $tok = strtok(",");
                $pos+=2;
            }
        }

        if (isset($data['noticias_right'])) {
            $tok = strtok($data['noticias_right'],",");
            $name = "";
            $pos = 2;
            $contentType = 'Article';
            while (($tok !== false) AND ($tok !=" ")) {
                //   $this->delete_contents($data['id'] ,$tok)  	;
                $this->set_contents($data['id'] , $tok, $pos, $name,  $contentType);
                $tok = strtok(",");
                $pos+=2;
            }
        }
    }


/****************************************************************************/
/**************************  special_contents ********************************/
/****************************************************************************/

    public function get_contents($id)
    {
        if ($id == null) {
            return(false);
        }
        $sql = 'SELECT * FROM `special_contents`'
             . ' WHERE fk_special=? ORDER BY position ASC';
        $rs = $GLOBALS['application']->conn->Execute($sql, array(intval($id)));

        $i = 0;
        $items = array();
        while (!$rs->EOF) {
            $items []= array(
                'fk_content'   => $rs->fields['fk_content'],
                'name'         => $rs->fields['name'],
                'position'     => $rs->fields['position'],
                'visible'      => $rs->fields['visible'],
                'type_content' => $rs->fields['type_content'],
            );
            $rs->MoveNext();
        }

        return $items;
    }


    //Define contenidos dentro de un modulo
    public function set_contents(
        $id,
        $pkContent,
        $position,
        $name,
        $typeContent
    ) {
        if ($id == null) {
            return(false);
        }

       $visible = 1;
       $sql = "INSERT INTO special_contents "
            . "(`fk_special`, `fk_content`,`position`,`name`,`visible`,`type_content`)"
            . " VALUES (?,?,?,?,?,?)";
        $values = array(
            $id,
            $pkContent,
            $position,
            $name,
            $visible,
            $typeContent
        );

        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if ($rs === false) {
            \Application::logDatabaseError();

            return false;
        }

         return true;
    }

    // Elimina contenidos dentro de un modulo
    public function delete_contents($id, $contentId)
    {
        if ($id == null) {
            return false;
        }
        $sql = 'DELETE FROM special_contents WHERE fk_content=? AND fk_special=?';

        $rs = $GLOBALS['application']->conn->Execute($sql,
            array(intval($contentId)), intval($id));
        if ($rs === false) {
            \Application::logDatabaseError();

            return;
        }
    }

    public function delete_all_contents($id)
    {
        if ($id == null) {
            return false;
        }
        $sql = 'DELETE FROM special_contents WHERE  fk_special=?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array(intval($id)));

        if ($rs === false) {
            \Application::logDatabaseError();

            return;
        }
    }

}
