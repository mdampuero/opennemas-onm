<?php
/**
 * Defines the ContentCategory class
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
 * Handles all the categories CRUD actions.
 *
 * @package    Model
 **/
class ContentCategory
{
    /**
     * Category id
     *
     * @var int
     **/
    public $pk_content_category = null;

    /**
     * The parent category id
     *
     * @var int
     **/
    public $fk_content_category = null;

    /**
     * The path to the category logo/image
     *
     * @var string
     **/
    public $img_path            = null;

    /**
     * The color of the category
     *
     * @var string
     **/
    public $color               = null;

    /**
     * The name of the category
     *
     * @var string
     **/
    public $name                = null;

    /**
     * The human readable category name
     *
     * @var string
     **/
    public $title               = null;

    /**
     * Whether if this category is in menu
     *
     * @var boolean
     **/
    public $inmenu              = null;

    /**
     * Position in menu
     *
     * @var int
     **/
    public $posmenu             = null;

    /**
     * Special category for identify types of content
     *
     * $internal_category = 0 categoria es interna (para usar ventajas
     * funciones class ContentCategory) no se muestra en el menu.
     * $internal_category = 1 categoria generica para todos los
     * tipos de contenidos.
     * $internal_category = n corresponde con el content_type
     *
     * @var int
     **/
    public $internal_category   = null;

    /**
     * Misc params for this category
     *
     * @var array
     **/
    public $params              = null;

    /**
     * Initializes the Category class.
     *
     * @param string $id the id of the category.
     **/
    public function __construct($id = null)
    {
        if (!empty($id) && is_numeric($id)) {
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
        if (!isset($data['name'])) {
            $data['name'] = strtolower($data['title']);
            $data['name'] = StringUtils::normalizeName($data['name']);
        }
        $data['logo_path'] =
            (isset($data['logo_path'])) ? $data['logo_path'] : '';
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
        $values = array(
            $data['name'],
            $data['title'],
            $data['inmenu'],
            $data['subcategory'],
            $data['internal_category'],
            $data['logo_path'],
            $data['color'],
            $data['params']
        );

        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if ($rs === false) {
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
        $sql = 'SELECT * FROM content_categories WHERE pk_content_category =?';

        $values = $id;
        $rs = $GLOBALS['application']->conn->Execute($sql, $values);

        if (!$rs) {
            return false;
        }
        $this->pk_content_category = ($id);
        $this->load($rs->fields);
        if (!empty($this->params) && is_string($this->params)) {
            $this->params = unserialize($this->params);
        }
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
        $data['params'] = serialize($data['params']);
        if (empty($data['logo_path'])) {
            $data['logo_path'] = $this->logo_path;
        }
        $data['color'] =
            (isset($data['color'])) ? $data['color'] : $this->color;
        $sql = "UPDATE content_categories SET  `title`=?, `inmenu`=?, ".
                       " `fk_content_category`=?, `internal_category`=?, ".
                       " `logo_path`=?,`color`=?, `params`=? ".
                   " WHERE pk_content_category=" . ($data['id']);

        $values = array(
            $data['title'],
            $data['inmenu'],
            $data['subcategory'],
            $data['internal_category'],
            $data['logo_path'],
            $data['color'],
            $data['params']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            return;
        }

        if ($data['subcategory']) {

            //Miramos sus subcategorias y se las añadimos a su nuevo padre
            $sql = "UPDATE content_categories SET `fk_content_category`=?
                    WHERE fk_content_category=" . ($data['id']);
            $values = array($data['subcategory']);

            $rs = $GLOBALS['application']->conn->Execute($sql, $values);
            if ($rs === false) {
                return false;
            }
        }

        dispatchEventWithParams('category.update', array('category' => $this));

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
        if (ContentCategoryManager::isEmptyByCategoryId($id)) {
            $sql = 'DELETE FROM content_categories WHERE pk_content_category=?';

            $rs = $GLOBALS['application']->conn->Execute($sql, array($id));
            if ($rs === false) {
                return false;
            }

            dispatchEventWithParams('category.update', array('category' => $this));

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
            if (array_key_exists('pk_content_category', $properties)) {
                $this->id = $properties['pk_content_category'];
            }

            foreach ($properties as $k => $v) {
                if (!is_numeric($k)) {
                    $this->{$k} = $v;
                }
            }
        } elseif (is_object($properties)) {
            $properties = get_object_vars($properties);

            if (array_key_exists('pk_content_category', $properties)) {
                $this->id = $properties['pk_content_category'];
            }

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
     * @return boolean true if all the contents was deleted sucessfully
     **/
    public function deleteContents()
    {
        $sql = 'SELECT pk_fk_content FROM contents_categories WHERE pk_fk_content_category=?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array($this->pk_content_category));

        if (!$rs) {
            return false;
        }

        $contentsArray = array();
        while (!$rs->EOF) {
            $contentsArray[] = $rs->fields['pk_fk_content'];
            $rs->MoveNext();
        }

        if (!empty($contentsArray)) {
            $contents = implode(', ', $contentsArray);
            $sqls []= 'DELETE FROM contents  WHERE `pk_content` IN ('.$contents.')';
            $sqls []= 'DELETE FROM articles WHERE `pk_article` IN ('.$contents.')';
            $sqls []= 'DELETE FROM advertisements WHERE `pk_advertisement` IN ('.$contents.')';
            $sqls []= 'DELETE FROM albums WHERE `pk_album` IN ('.$contents.')';
            $sqls []= 'DELETE FROM albums_photos WHERE `pk_album` IN (' . $contents . ')  '
                .'OR `pk_photo` IN ('.$contents.')';
            $sqls []= 'DELETE FROM comments WHERE `content_id` IN ('.$contents.')';
            $sqls []= 'DELETE FROM votes WHERE `pk_vote` IN ('.$contents.')';
            $sqls []= 'DELETE FROM ratings WHERE `pk_rating` IN ('.$contents.')';
            $sqls []= 'DELETE FROM polls WHERE `pk_poll` IN ('.$contents.')';
            $sqls []= 'DELETE FROM poll_items WHERE `fk_pk_poll` IN ('.$contents.')';
            $sqls []= 'DELETE FROM related_contents '
                .'WHERE `pk_content1` IN (' . $contents . ') OR `pk_content2` IN ('.$contents.')';
            $sqls []= 'DELETE FROM kioskos WHERE `pk_kiosko` IN ('.$contents.')';
            $sqls []= 'DELETE FROM static_pages WHERE `pk_static_page` IN ('.$contents.')';
            $sqls []= 'DELETE FROM content_positions WHERE `pk_fk_content` IN ('.$contents.')';
            $sqls []= 'DELETE FROM contentmeta WHERE `fk_content` IN ('.$contents.')';

            foreach ($sqls as $sql) {
                if ($GLOBALS['application']->conn->Execute($sql) === false) {
                    return false;
                }
            }

            \Photo::batchDelete($contentsArray);
            \Video::batchDelete($contentsArray);
            \Attachment::batchDelete($contentsArray);
        }

        return true;
    }

    /**
     * TODO: Change name to setAvailable and rename the db column to available
     * Changes the menu status (shown, hidden) for the category.
     *
     * @param string $status the status to set to the category.
     **/
    public function setInMenu($status)
    {
        if ($this->pk_content_category == null) {
            return false;
        }

        $sql = "UPDATE content_categories SET `inmenu`=? WHERE pk_content_category=?";
        $values = array($status, $this->pk_content_category);

        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if ($rs === false) {
            return false;
        }

        dispatchEventWithParams('category.update', array('category' => $this));
    }

    /**
     * Changes the rss status (shown, hidden) for the category.
     *
     * @param string $status the status to set to the category rss.
     **/
    public function setInRss($status)
    {
        if (!is_array($this->params)) {
            $this->params = array();
        }
        $this->params['inrss'] = $status;
        $this->params = serialize($this->params);

        $sql = "UPDATE content_categories "
             ." SET `params`=?"
             ." WHERE pk_content_category=?";

        $values = array($this->params, $this->pk_content_category);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            return false;
        }

        dispatchEventWithParams('category.update', array('category' => $this));

        return $this;
    }
}
