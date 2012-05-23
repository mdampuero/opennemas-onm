<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Handles all CRUD actions over Authors.
 *
 * @package    Onm
 * @subpackage Model
 * @author     Fran Dieguez <fran@openhost.es>
 **/
class Author
{

    public $pk_author = null;
    public $name      = null;
    public $fk_user   = null;
    public $gender    = null;
    public $politics  = null;
    public $condition = null;
    public $date_nac  = null;

    public $cache    = null;

    // Static members for performance
    static private $_photos   = null;

    private $_defaultValues = array(
        'name'=>'',
        'gender'=>'',
        'blog'=>'',
        'politics'=>'',
        'condition'=>'',
        'date_nac'=>'',
        'titles'=>array(),
    );

    /**
     * Initializes the Author class.
     *
     * @param strin $id the id of the author.
     **/
    public function __construct($id=null)
    {

        // Posibilidad de cachear resultados de métodos
        $this->cache = new MethodCacheManager($this, array('ttl' => 30));

        if (!is_null($id)) {
            $this->read($id);
        }
    }

    /**
     * Creates an author from array and stores it in db
     *
     * @param array $data the data of the author
     *
     * @return bool true if the object was stored
     */
    public function create($data)
    {
        $data = array_merge($this->_defaultValues, $data);

        $sql = "INSERT INTO authors
                (`name`, `fk_user`, `blog`,`politics`, `condition`,`date_nac`)
                VALUES ( ?,?,?,?,?,?)";
        $values = array(
            $data['name'], '0', $data['blog'],
            $data['politics'], $data['condition'], $data['date_nac']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

            return(false);
        }

        //tabla author_imgs
        $this->pk_author = $GLOBALS['application']->conn->Insert_ID();

        if (isset($data['titles'])) {
            $titles = $data['titles'];
            foreach ($titles as $atid => $des) {
                $sql = "INSERT INTO author_imgs
                                    (`fk_author`, `fk_photo`,`path_img`)
                             VALUES (?,?,?)";
                $values = array( $this->pk_author, $atid, $des );

                $rs = $GLOBALS['application']->conn->Execute($sql, $values);
                if (!$rs) {
                    $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
                    $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
                    $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
                }
            }
        }

        return($this->pk_author);
    }

    /**
     * Fetches one Author by its id.
     *
     * @param string $id the author id to get info from.
     **/
    public function read($id)
    {
        $sql = 'SELECT `authors`.`pk_author`, `authors`.`name` ,
                       `authors`.`blog` , `authors`.`politics` ,
                       `authors`.`date_nac` , `authors`.`fk_user` ,
                       `authors`.`condition`
                FROM authors
                WHERE `authors`.`pk_author` = ?';
        $rs  = $GLOBALS['application']->conn->Execute($sql, array($id));

        if (!$rs) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

            return;
        }

        //self::$_authors
        $this->load($rs->fields);
    }

    /**
     * Updates the information of the author given an array of key-values
     *
     * @param array $data the new data to update the author
     **/
    public function update($data)
    {

        $data = array_merge($this->_defaultValues, $data);

        $sql = "UPDATE `authors`
                SET `name`=?, `blog`=?, `politics`=?, `condition`=?
                WHERE pk_author=?";

        $values = array(
            $data['name'], $data['blog'],
            $data['politics'], $data['condition'],
            $data['id']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

            return;
        }

        $this->pk_author = $data['id'];

        //tabla author_imgs
        $titles = $data['titles'];
        if ($titles) {
            foreach ($titles as $atid=>$des) {
                $sql = "INSERT INTO author_imgs
                        (`fk_author`, `fk_photo`,`path_img`) VALUES ( ?,?,?)";
                $values = array( $this->pk_author, $atid, $des );

                $rs = $GLOBALS['application']->conn->Execute($sql, $values);
                if (!$rs) {
                    $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
                    $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
                    $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
                }
            }
        }

        if ($data['del_img']) {
                $tok = strtok($data['del_img'], ",");
                while (($tok !== false) AND ($tok !=" ")) {
                    $sql = "DELETE FROM author_imgs WHERE pk_img=".$tok;
                    $GLOBALS['application']->conn->Execute($sql);
                    $tok = strtok(",");
                }
        }
    }

    /**
     * Removes an album by a given id.
     *
     * @param string $id the album id
     **/
    public function delete($id)
    {
        $sql = 'DELETE FROM authors WHERE pk_author='.($id);

        if ($GLOBALS['application']->conn->Execute($sql)===false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

            return;
        }
        $sql = 'DELETE FROM author_imgs WHERE fk_author='.($id);

        if ($GLOBALS['application']->conn->Execute($sql)===false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

            return;
        }
    }


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
                if ( !is_numeric($k) ) {
                    $this->{$k} = $v;
                }
            }
        }

        $this->id = $this->pk_author;
    }

    /**
     * Finds authors given a sql WHERE and ORDER BY clause.
     *
     * @param string $where   the SQL WHERE clause
     * @param string $orderBy the SQL ORDER BY clause
     *
     * @return array the array of authors that matches the criteria
     **/
    public function find($where, $orderBy='ORDER BY 1')
    {
        $sql =  'SELECT `authors`.`pk_author`, `authors`.`name` ,
                       `authors`.`blog` , `authors`.`politics` ,
                       `authors`.`date_nac` , `authors`.`fk_user` ,
                       `authors`.`condition` FROM authors '.
                'WHERE ? ?';
        $values = array($where, $orderBy);

        $authors = array();

        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if ($rs!==false) {
            while (!$rs->EOF) {
                $obj = new Author();
                $obj->load($rs->fields);

                $authors[] = $obj;

                $rs->MoveNext();
            }
        }

        return $authors;
    }


    /**
     * Returns one dummy object with information about one author
     * given its id.
     *
     * @param string $id the author id.
     *
     * @return stdClass the object with information about the author
     *
     * @throws <b>Exception</b> Explanation of exception.
     **/
    public function get_author($id)
    {
        $sql = 'SELECT `authors`.`pk_author`, `authors`.`name` ,
                       `authors`.`blog` , `authors`.`politics` ,
                       `authors`.`date_nac` , `authors`.`fk_user` ,
                       `authors`.`condition` FROM authors
                WHERE `author`.`fk_user` = ?';
        $rs  = $GLOBALS['application']->conn->Execute($sql, array($id));

        if (!$rs) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

            return;
        }

        $author = new stdClass();
        $author->pk_author    = $rs->fields['pk_author'];
        $author->fk_user    = $rs->fields['fk_user'];
        $author->gender    = $rs->fields['blog'];
        $author->name    = $rs->fields['name'];
        $author->politics    = $rs->fields['politics'];
        $author->condition    = $rs->fields['condition'];
        $author->date_nac    = $rs->fields['date_nac'];

        return $author;
    }


    /**
     * Returns a list of all authors
     *
     * @param string $filter,   the where sql part to filter authors by
     * @param string $_orderBy, the ORDER BY sql part to sort authors with
     * @return mixed, array of all matched authors
     **/
    static public function list_authors($filter=NULL, $_orderBy='ORDER BY 1')
    {

        $items = array();
        $_where = '1=1';
        if ( !is_null($filter) ) $_where = $filter;

        $sql = 'SELECT `authors`.`pk_author`, `authors`.`name` ,
                       `authors`.`blog` , `authors`.`politics` ,
                       `authors`.`date_nac` , `authors`.`fk_user` ,
                       `authors`.`condition`
                FROM `authors` WHERE '.$_where. ' '.$_orderBy ;

        $rs = $GLOBALS['application']->conn->Execute($sql);
        $i  = 0;
        if ($rs) {
            while (!$rs->EOF) {
                $items[$i] = new stdClass;
                $items[$i]->id         = $rs->fields['pk_author'];
                $items[$i]->pk_author     = $rs->fields['pk_author'];
                $items[$i]->fk_user    = $rs->fields['fk_user'];
                $items[$i]->name    = $rs->fields['name'];
                $items[$i]->gender     = $rs->fields['blog'];
                $items[$i]->politics    = $rs->fields['politics'];
                $items[$i]->condition    = $rs->fields['condition'];
                $num = Author::count_author_photos($rs->fields['pk_author']);
                $items[$i]->num_photos    = $num;

                $rs->MoveNext();
                  $i++;
            }
        }

        return( $items );
    }


    /**
     * Returns all the authors that matches given WHERE and ORDER BY clauses.
     *
     * @param string $filter the where criteria.
     *
     * @return array multidimensional array with information about
     *               matching authors
     **/
    public function all_authors($filter=NULL, $_orderBy='ORDER BY 1')
    {

        $items = array();
        $_where = '1=1';
        if ( !is_null($filter) ) {
            $_where = $filter;
        }

        $sql = 'SELECT authors.pk_author, authors.name
                FROM authors
                WHERE '.$_where.' '.$_orderBy;

        $rs = $GLOBALS['application']->conn->Execute($sql);
        $i  = 0;

        while (!$rs->EOF) {
            $items[$i] = new stdClass;
            $items[$i]->id         = $rs->fields['pk_author'];
            $items[$i]->pk_author     = $rs->fields['pk_author'];
            $items[$i]->name    = $rs->fields['name'];

              $rs->MoveNext();
              $i++;
        }

        return( $items );

    }

    /**
     * Returns all the photos asociated to one author given one author id.
     *
     * @param string $id the author id.
     *
     * @return array list of dummy photo objects
     **/
    public function get_photo($id)
    {
        if (is_null(self::$_photos)) {
            $sql = 'SELECT author_imgs.pk_img, author_imgs.path_img,
                           author_imgs.description
                    FROM author_imgs';
            $rs  = $GLOBALS['application']->conn->Execute($sql);

            if ($rs!==false) {
                while (!$rs->EOF) {
                    $photo = new stdClass();
                    $photo->path_img    = $rs->fields['path_img'];
                    $photo->path_file    = $rs->fields['path_img'];
                    $photo->description    = $rs->fields['description'];

                    self::$_photos[ $rs->fields['pk_img'] ] = $photo;
                    $rs->MoveNext();
                }
            }
        }

        if (isset(self::$_photos[$id])) return self::$_photos[$id];

        return null;
    }

    /**
     * Returns all the photos associated to one author given its id.
     *
     * @param string $id the author id.
     *
     * @return array list of dummy photo objects
     **/
    public function get_author_photos($id)
    {
        $sql = 'SELECT author_imgs.fk_author, author_imgs.pk_img,
                       author_imgs.path_img, author_imgs.description
                FROM author_imgs WHERE fk_author = ?';
        $rs  = $GLOBALS['application']->conn->Execute($sql, array($id));

        if (!$rs) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

            return;
        }

        $i = 0;
        $photos = array();
        while (!$rs->EOF) {
            $photos[$i] = new stdClass();

            $photos[$i]->pk_img = $rs->fields['pk_img'];
            $photos[$i]->path_img = $rs->fields['path_img'];
            $photos[$i]->path_file = $rs->fields['path_img'];
            $photos[$i]->description = $rs->fields['description'];
            $photos[$i]->fk_author = $rs->fields['fk_author'];

            $i++;
            $rs->MoveNext();
        }

        return $photos;
    }

    /**
     * Returns the number of photos that belongs to one author given its id.
     *
     * @param string $id the author id.
     *
     * @return int the number of photos
     **/
    static public function count_author_photos($id)
    {
        $sql = 'SELECT COUNT(*) FROM author_imgs WHERE fk_author = '.($id);
        $rs  = $GLOBALS['application']->conn->Execute($sql);

        return($rs->fields['COUNT(*)']);
    }

}
