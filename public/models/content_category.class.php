<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Handles all the categories CRUD actions.
 *
 * @package    Onm
 * @subpackage Model
 * @author     Fran Dieguez <fran@openhost.es>
 **/
class ContentCategory {
    var $pk_content_category = NULL;
    var $fk_content_category = NULL;
    var $img_path = NULL;
    var $color = NULL;
    var $name = NULL; //nombre carpeta

    var $title = NULL; //titulo seccion

    var $inmenu = NULL; // Flag Ver en el menu.

    var $posmenu = NULL;
    var $internal_category = NULL; // flag asignar a un tipo de contenido.

    /* $internal_category = 0 categoria es interna (para usar ventajas funciones class ContentCategory) no se muestra en el menu.

     * $internal_category = 1 categoria generica para todos los tipos de contenidos.
     * $internal_category = n corresponde con el content_type
    */

    /**
     * Initializes the Category class.
     *
     * @param string $id the id of the category.
     **/
    public function __construct($id = null)
    {
        if (is_numeric($id)) {
            $this->read($id);
        }
    }

    /**
     * Creates a category from given data.
     *
     * @param array $data the data for the category.
     *
     * @return boolean true if all went well
     **/
    public function create($data)
    {
        $data['name'] = strtolower($data['title']);
        $data['name'] = String_Utils::normalize_name($data['name']);
        $data['logo_path'] = (isset($data['logo_path'])) ? $data['logo_path'] : '';
        $data['color'] = (isset($data['color'])) ? $data['color'] : '';
        $data['params'] = serialize($data['params']);
        $ccm = new ContentCategoryManager();

        if ($ccm->exists($data['name'])) {
            $i = 1;
            $name = $data['name'];
            while ($ccm->exists($name)) {
                $name = $data['name'] . $i;
                $i++;
            }
            $data['name'] = $name;
        }
        $sql = "INSERT INTO content_categories
                    (`name`, `title`,`inmenu`,`fk_content_category`,
                    `internal_category`, `logo_path`,`color`, `params`)
                VALUES (?,?,?,?,?,?,?,?)";
        $values = array($data['name'], $data['title'], $data['inmenu'], $data['subcategory'], 
            $data['internal_category'], $data['logo_path'], $data['color'], $data['params']);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: ' . $errorMsg);
            $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;
            return false;
        }
        $this->pk_content_category = $GLOBALS['application']->conn->Insert_ID();
        return true;
    }

    /**
     * Fetches all the information of a category into the object.
     *
     * @param string $id the category id.
     **/
    public function read($id)
    {
        $this->pk_content_category = ($id);
        $sql = 'SELECT * FROM content_categories WHERE pk_content_category = ' . $this->pk_content_category;
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: ' . $errorMsg);
            $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;
            return;
        }
        $this->load($rs->fields);
        $this->params = unserialize($this->params);
       
    }

    /**
     * Updates the information for the category.
     *
     * @param array $data the information to update the category.
     *
     * @return boolean true if all went well
     **/
    public function update($data)
    {
        $this->read($data['id']); //Para comprobar si cambio el nombre carpeta
 
        $data['name'] = String_Utils::normalize_name($data['title']);
        $data['params'] = serialize($data['params']);
        if (empty($data['logo_path'])) {
            $data['logo_path'] = $this->logo_path;
        }
        $data['color'] = (isset($data['color'])) ? $data['color'] : $this->color;
        $sql = "UPDATE content_categories SET `name`=?, `title`=?, `inmenu`=?, ".
                       " `fk_content_category`=?, `internal_category`=?, ".
                       " `logo_path`=?,`color`=?, `params`=? ".
                   " WHERE pk_content_category=" . ($data['id']);
        
        $values = array($data['name'], $data['title'], $data['inmenu'], 
                    $data['subcategory'], $data['internal_category'], 
                    $data['logo_path'], $data['color'], $data['params']);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
           
            $GLOBALS['application']->logger->debug('Error: ' . $errorMsg);
            $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;
            return;
        }

        if ($data['subcategory']) {

            //Miramos sus subcategorias y se las añadimos a su nuevo padre
            $sql = "UPDATE content_categories SET `fk_content_category`=?
                    WHERE fk_content_category=" . ($data['id']);
            $values = array($data['subcategory']);

            if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
                $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: ' . $errorMsg);
                $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;
                return;
            }
        }
        return true;
    }

    /**
     * Deletes one category from its id.
     *
     * @param string $id the id of the category.
     *
     * @return boolean true if the category was deleted successfully
     **/
    public function delete($id)
    {
        if (ContentCategoryManager::is_Empty($id)) {
            $sql = 'DELETE FROM content_categories WHERE pk_content_category=' . ($id);

            if ($GLOBALS['application']->conn->Execute($sql) === false) {
                $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: ' . $errorMsg);
                $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Loads properties to object from one array of property names.
     *
     * @param array $properties the list of the properties to load.
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
     * Deletes all the contents for one category given the category id.
     *
     * @param string $id the category id.
     *
     * @return boolean true if all the contents was deleted sucessfully
     **/
    public function empty_category($id)
    {
        $sql = 'SELECT pk_fk_content FROM contents_categories WHERE pk_fk_content_category=' . ($id);
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: ' . $errorMsg);
            $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;
            return false;
        }
        $array_contents = array();
        while (!$rs->EOF) {
            $array_contents[] = $rs->fields['pk_fk_content'];
            $rs->MoveNext();
        }

        if (!empty($array_contents)) {
            $contents = implode(', ', $array_contents);
            $sqls []= 'DELETE FROM contents  WHERE `pk_content` IN (' . $contents . ') ';
            $sqls []= 'DELETE FROM articles  WHERE `pk_article` IN (' . $contents . ') ';
            $sqls []= 'DELETE FROM articles_clone  WHERE `pk_original` IN (' . $contents . ')  OR `pk_clone` IN (' . $contents . ') ';
            $sqls []= 'DELETE FROM advertisements  WHERE `pk_advertisement` IN (' . $contents . ') ';
            $sqls []= 'DELETE FROM albums  WHERE `pk_album` IN (' . $contents . ') ';
            $sqls []= 'DELETE FROM albums_photos  WHERE `pk_album` IN (' . $contents . ')   OR `pk_photo` IN (' . $contents . ') ';
            $sqls []= 'DELETE FROM videos  WHERE `pk_video` IN (' . $contents . ') ';
            $sqls []= 'DELETE FROM photos  WHERE `pk_photo` IN (' . $contents . ') ';
            $sqls []= 'DELETE FROM comments  WHERE `pk_comment` IN (' . $contents . ') ';
            $sqls []= 'DELETE FROM votes  WHERE `pk_vote` IN (' . $contents . ') ';
            $sqls []= 'DELETE FROM ratings  WHERE `pk_rating` IN (' . $contents . ') ';
            $sqls []= 'DELETE FROM attachments  WHERE `pk_attachment` IN (' . $contents . ') ';
            $sqls []= 'DELETE FROM polls  WHERE `pk_poll` IN (' . $contents . ') ';
            $sqls []= 'DELETE FROM poll_items  WHERE `fk_pk_poll` IN (' . $contents . ') ';
            $sqls []= 'DELETE FROM related_contents  WHERE `pk_content1` IN (' . $contents . ')   OR `pk_content2` IN (' . $contents . ') ';
            $sqls []= 'DELETE FROM kioskos  WHERE `pk_kiosko` IN (' . $contents . ') ';
            $sqls []= 'DELETE FROM static_pages  WHERE `pk_static_page` IN (' . $contents . ') ';

            foreach ($sqls as $sql) {
                if ($GLOBALS['application']->conn->Execute($sql) === false) {
                    $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
                    $GLOBALS['application']->logger->debug(
                        'Error: ' . $errorMsg
                    );
                    $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;
                    return;
                }
            }
        }
        return true;
    }

    /**
     * Sets the priority of the category.
     *
     * @param int $weight the weight of the category.
     **/
    public function set_priority($weight)
    {
        if ($this->pk_content_category == NULL) return false;
        $sql = "UPDATE content_categories SET `posmenu`=?
                WHERE pk_content_category=?";
        $values = array($weight, $this->pk_content_category);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: ' . $errorMsg);
            $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;
            return;
        }
    }

    /**
     * Changes the menu status (shown, hidden) for the category.
     *
     * @param string $status the status to set to the category.
     **/
    public function set_inmenu($status)
    {
        if ($this->pk_content_category == NULL)  return false;
        if ($status == 0)   $this->posmenu = 30;

        $sql = "UPDATE content_categories "
                ." SET `inmenu`=?, `posmenu`=?"
                . " WHERE pk_content_category=?";
        $values = array($status, $this->posmenu, $this->pk_content_category);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: ' . $errorMsg);
            $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;
            return;
        }
    }
}
