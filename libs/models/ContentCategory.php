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
     * Loads properties to object from one array of property names.
     *
     * @param array $properties the list of the properties to load.
     **/
    public function load($properties)
    {
        if (is_array($properties)) {
            if (array_key_exists('pk_content_category', $properties)) {
                $this->id = (int) $properties['pk_content_category'];
            }

            foreach ($properties as $k => $v) {
                if (!is_numeric($k)) {
                    $this->{$k} = $v;
                }
            }
        } elseif (is_object($properties)) {
            $properties = get_object_vars($properties);

            if (array_key_exists('pk_content_category', $properties)) {
                $this->id = (int) $properties['pk_content_category'];
            }

            foreach ($properties as $k => $v) {
                if (!is_numeric($k)) {
                    $this->{$k} = $v;
                }
            }
        }
    }

    /**
     * Fetches all the information of a category into the object.
     *
     * @param string $id the category id.
     **/
    public function read($id)
    {
        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM content_categories WHERE pk_content_category=?',
                [ (int) $id ]
            );

            if (!$rs) {
                return false;
            }

            $this->load($rs);

            if (!empty($this->params) && is_string($this->params)) {
                $this->params = unserialize($this->params);
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
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
        // Generate slug for category
        $data['name'] = \Onm\StringUtils::getTitle(
            \Onm\StringUtils::normalizeName(strtolower($data['title']))
        );

        // Unserialize params
        $data['params'] = serialize($data['params']);

        // Check if slug already exists and add number
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

        try {
            getService('dbal_connection')->insert(
                'content_categories',
                [
                    'name'                => $data['name'],
                    'title'               => $data['title'],
                    'inmenu'              => (int) $data['inmenu'],
                    'fk_content_category' => (int) $data['subcategory'],
                    'internal_category'   => (int) $data['internal_category'],
                    'logo_path'           => $data['logo_path'],
                    'color'               => $data['color'],
                    'params'              => $data['params']
                ]
            );

            $this->pk_content_category = getService('dbal_connection')->lastInsertId();

            dispatchEventWithParams('category.create', array('category' => $this));

            return true;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
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
        if ($data['logo_path'] == '1') {
            $data['logo_path'] = $this->logo_path;
        }
        $data['color'] = (isset($data['color'])) ? $data['color'] : $this->color;

        $conn = getService('dbal_connection');
        $conn->beginTransaction();
        try {
            $rs = $conn->update(
                'content_categories',
                [
                    'title'               => $data['title'],
                    'inmenu'              => (int) $data['inmenu'],
                    'fk_content_category' => (int) $data['subcategory'],
                    'internal_category'   => (int) $data['internal_category'],
                    'logo_path'           => $data['logo_path'],
                    'color'               => $data['color'],
                    'params'              => $data['params']
                ],
                [ 'pk_content_category' => $data['id'] ]
            );

            if (!$rs) {
                $conn->rollBack();
                return false;
            }

            if ($data['subcategory']) {
                // We look at subcategories and wee add them to their parent
                $rs = $conn->update(
                    'content_categories',
                    [ 'fk_content_category' => $data['subcategory'] ],
                    [ 'fk_content_category' => $data['id'] ]
                );

            }

            $conn->commit();
            dispatchEventWithParams('category.update', array('category' => $this));

            return true;
        } catch (\Exception $e) {
            $conn->rollBack();
            error_log($e->getMessage());
            return false;
        }
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
        if (!ContentCategoryManager::isEmptyByCategoryId($id)) {
            return false;
        }

        try {
            $rs = getService('dbal_connection')->delete(
                'content_categories',
                [ 'pk_content_category' => $id ]
            );

            if ($rs === false) {
                return false;
            }

            dispatchEventWithParams('category.delete', array('category' => $this));

            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Deletes all the contents for one category given the category id.
     *
     * @return boolean true if all the contents was deleted sucessfully
     **/
    public function deleteContents()
    {
        $conn = getService('dbal_connection');

        // Get the list of content ids in category
        try {
            $rs = $conn->fetchAll(
                'SELECT pk_fk_content FROM contents_categories WHERE pk_fk_content_category=?',
                [ $this->pk_content_category ]
            );

            $contentsArray = array_map(function($item) {
                return $item['pk_fk_content'];
            }, $rs);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }

        if (empty($contentsArray)) {
            return true;
        }

        // Prepare sqls to execute
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


        $conn->beginTransaction();
        try {
            foreach ($sqls as $sql) {
                $conn->executeUpdate($sql);
            }

            \Photo::batchDelete($contentsArray);
            \Video::batchDelete($contentsArray);
            \Attachment::batchDelete($contentsArray);
            $conn->commit();

            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $conn->rollBack();
            return false;
        }
    }

    /**
     * TODO: Rename the db column to available
     * Changes the menu status (shown, hidden) for the category.
     *
     * @param string $status the status to set to the category.
     **/
    public function setAvailable($status)
    {
        if ($this->pk_content_category == null) {
            return false;
        }

        try {
            getService('dbal_connection')->update(
                'content_categories',
                [ 'inmenu' => (int) $status ],
                [ 'pk_content_category' => $this->pk_content_category ]
            );

            if ($rs === false) {
                return false;
            }

            dispatchEventWithParams('category.update', array('category' => $this));
            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Changes the rss status (shown, hidden) for the category.
     *
     * @param string $status the status to set to the category rss.
     **/
    public function setInRss($status)
    {
        if ($this->pk_content_category == null) {
            return false;
        }

        try {
            if (!is_array($this->params)) {
                $this->params = array();
            }
            $this->params['inrss'] = $status;
            $this->params = serialize($this->params);

            getService('dbal_connection')->update(
                'content_categories',
                [ 'params' => $this->params ],
                [ 'pk_content_category' => $this->pk_content_category ]
            );

            if ($rs === false) {
                return false;
            }

            dispatchEventWithParams('category.update', array('category' => $this));
            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
