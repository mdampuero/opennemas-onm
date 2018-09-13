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
 */
class ContentCategory implements \JsonSerializable
{
    /**
     * Category id
     *
     * @var int
     */
    public $pk_content_category = null;

    /**
     * The parent category id
     *
     * @var int
     */
    public $fk_content_category = null;

    /**
     * The path to the category logo/image
     *
     * @var string
     */
    public $img_path = null;

    /**
     * The color of the category
     *
     * @var string
     */
    public $color = null;

    /**
     * The name of the category
     *
     * @var string
     */
    public $name = null;

    /**
     * The human readable category name
     *
     * @var string
     */
    protected $title = null;

    /**
     * Whether if this category is in menu
     *
     * @var boolean
     */
    public $inmenu = null;

    /**
     * Position in menu
     *
     * @var int
     */
    public $posmenu = null;

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
     */
    public $internal_category = null;

    /**
     * Misc params for this category
     *
     * @var array
     */
    public $params = null;

    /**
     * Initializes the Category class.
     *
     * @param string $id the id of the category.
     */
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
     */
    public function load($properties)
    {
        $propertiesAux = $properties;
        if (is_object($properties)) {
            $propertiesAux = get_object_vars($properties);
        }

        if (array_key_exists('pk_content_category', $propertiesAux)) {
            $this->id = (int) $properties['pk_content_category'];
        }

        foreach ($properties as $k => $v) {
            if (is_numeric($k)) {
                continue;
            }

            if (in_array($k, self::getL10nKeys())) {
                $aux        = @unserialize($v);
                $this->{$k} = (is_bool($aux)) ? $v : $aux;
                continue;
            }

            $this->{$k} = $v;
        }

        if (!empty($this->params) && is_string($this->params)) {
            $this->params = @unserialize($this->params);
        }

        // Force integer on inrss param
        $this->params['inrss'] = is_array($this->params)
            && array_key_exists('inrss', $this->params)
            && $this->params['inrss'] == 0 ? 0 : 1;
    }

    /**
     * Magic function to get uninitialized object properties.
     *
     * @param string $name the name of the property to get.
     *
     * @return mixed the value for the property
     */
    public function __get($name)
    {
        if (in_array($name, $this->getL10nKeys())) {
            if (getService('core.locale')->getContext() !== 'backend') {
                return getService('data.manager.filter')
                    ->set($this->{$name})
                    ->filter('localize')
                    ->get();
            }

            return getService('data.manager.filter')
                ->set($this->{$name})
                ->filter('unlocalize')
                ->get();
        }

        return $this->{$name};
    }

    /**
     * Changes a property value.
     *
     * @param string $name  The property name.
     * @param mixed  $value The property value.
     */
    public function __set($name, $value)
    {
        if (getService('core.instance')->hasMultilanguage()
            && in_array($name, $this->getL10nKeys())
        ) {
            $value = getService('data.manager.filter')
                ->set($value)
                ->filter('unlocalize')
                ->get();
        }

        $this->{$name} = $value;
    }

    /**
     * Returns all content information when serialized.
     *
     * @return array The content information.
     */
    public function jsonSerialize()
    {
        $data = get_object_vars($this);

        foreach ($this->getL10nKeys() as $key) {
            $data[$key] = $this->__get($key);
        }

        return $data;
    }

    /**
     * Fetches all the information of a category into the object.
     *
     * @param string $id the category id.
     *
     * @return null|boolean
     */
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

            $this->id = $this->pk_content_category;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());
            return false;
        }
    }

    /**
     * Creates a category from given data.
     *
     * @param array $data the data for the category.
     *
     * @return boolean true if all went well
     */
    public function create($data)
    {
        // Generate slug for category
        // $data['name'] = \Onm\StringUtils::generateSlug($data['title']);

        if (!getService('core.instance')->hasMultilanguage()) {
            $aux           = new stdClass();
            $aux->title    = $data['title'];
            $aux           = getService('data.manager.filter')->set($aux)
                ->filter('localize', [
                    'keys'   => \ContentCategory::getL10nKeys(),
                    'locale' => getService('core.locale')->setContext('frontend')->getLocale()
                ])->get();
            $data['title'] = $aux->title;
        } else {
            // Serialize language fields
            array_map(function ($field) use (&$data) {
                $data[$field] = serialize($data[$field]);
            }, self::getL10nKeys());
        }

        // Serialize params
        $data['params'] = serialize($data['params']);

        // Check if slug already exists and add number
        $ccm = new ContentCategoryManager();
        if ($ccm->exists($data['name'])) {
            $i    = 1;
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

            dispatchEventWithParams('category.create', [ 'category' => $this ]);

            return true;
        } catch (Exception $e) {
            getService('error.log')->error($e->getMessage());
            return false;
        }
    }

    /**
     * Updates the information for the category.
     *
     * @param array $data the information to update the category.
     *
     * @return boolean true if all went well
     */
    public function update($data)
    {
        $data['params'] = serialize($data['params']);
        $data['name']   = $this->name;

        if (!getService('core.instance')->hasMultilanguage()) {
            $aux           = new stdClass();
            $aux->title    = $data['title'];
            $aux           = getService('data.manager.filter')->set($aux)
                ->filter('localize', [
                    'keys'   => \ContentCategory::getL10nKeys(),
                    'locale' => getService('core.locale')->setContext('frontend')->getLocale()
                ])->get();
            $data['title'] = $aux->title;
        } else {
            // Serialize language fields
            array_map(function ($field) use (&$data) {
                $data[$field] = serialize($data[$field]);
            }, self::getL10nKeys());
        }

        if ($data['logo_path'] == '1') {
            $data['logo_path'] = $this->logo_path;
        }

        $data['color'] = (isset($data['color'])) ? $data['color'] : $this->color;

        $conn = getService('dbal_connection');
        try {
            $conn->beginTransaction();
            $rs = $conn->update(
                'content_categories',
                [
                    'title'               => $data['title'],
                    'name'                => $data['name'],
                    'inmenu'              => (int) $data['inmenu'],
                    'fk_content_category' => (int) $data['subcategory'],
                    'internal_category'   => (int) $data['internal_category'],
                    'logo_path'           => $data['logo_path'],
                    'color'               => $data['color'],
                    'params'              => $data['params']
                ],
                [ 'pk_content_category' => $data['id'] ]
            );

            if ($data['subcategory']) {
                // We look at subcategories and wee add them to their parent
                $rs = $conn->update(
                    'content_categories',
                    [ 'fk_content_category' => $data['subcategory'] ],
                    [ 'fk_content_category' => $data['id'] ]
                );
            }

            $conn->commit();
            dispatchEventWithParams('category.update', ['category' => $this]);

            return true;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getTraceAsString());
            $conn->rollBack();
            return false;
        }
    }

    /**
     * Deletes one category from its id.
     *
     * @param string $id the id of the category.
     *
     * @return boolean true if the category was deleted successfully
     */
    public function delete($id)
    {
        try {
            $rs = getService('dbal_connection')->delete(
                'content_categories',
                [ 'pk_content_category' => $id ]
            );

            if ($rs === false) {
                return false;
            }

            dispatchEventWithParams('category.delete', [ 'category' => $this ]);

            return true;
        } catch (\Exception $e) {
            $logger = getService('error.log');
            $logger->error($e->getMessage() . ' ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Deletes all the contents for one category given the category id.
     *
     * @return boolean true if all the contents was deleted sucessfully
     */
    public function deleteContents()
    {
        $conn = getService('dbal_connection');

        // Get the list of content ids in category
        try {
            $rs = $conn->fetchAll(
                'SELECT pk_fk_content FROM contents_categories WHERE pk_fk_content_category=?',
                [ $this->pk_content_category ]
            );

            $contentsArray = array_map(function ($item) {
                return $item['pk_fk_content'];
            }, $rs);
        } catch (\Exception $e) {
            $logger = getService('error.log');
            $logger->error($e->getMessage() . ' ' . $e->getTraceAsString());
            return false;
        }

        if (empty($contentsArray)) {
            return true;
        }

        $contents = implode(', ', $contentsArray);

        // Prepare sqls to execute
        $sqls[] = 'DELETE FROM contents  WHERE `pk_content` IN (' . $contents . ')';
        $sqls[] = 'DELETE FROM articles WHERE `pk_article` IN (' . $contents . ')';
        $sqls[] = 'DELETE FROM advertisements WHERE `pk_advertisement` IN (' . $contents . ')';
        $sqls[] = 'DELETE FROM albums WHERE `pk_album` IN (' . $contents . ')';
        $sqls[] = 'DELETE FROM albums_photos WHERE `pk_album` IN (' . $contents . ')  '
            . 'OR `pk_photo` IN (' . $contents . ')';
        $sqls[] = 'DELETE FROM comments WHERE `content_id` IN (' . $contents . ')';
        $sqls[] = 'DELETE FROM votes WHERE `pk_vote` IN (' . $contents . ')';
        $sqls[] = 'DELETE FROM ratings WHERE `pk_rating` IN (' . $contents . ')';
        $sqls[] = 'DELETE FROM polls WHERE `pk_poll` IN (' . $contents . ')';
        $sqls[] = 'DELETE FROM poll_items WHERE `fk_pk_poll` IN (' . $contents . ')';
        $sqls[] = 'DELETE FROM related_contents '
            . 'WHERE `pk_content1` IN (' . $contents . ') OR `pk_content2` IN (' . $contents . ')';
        $sqls[] = 'DELETE FROM kioskos WHERE `pk_kiosko` IN (' . $contents . ')';
        $sqls[] = 'DELETE FROM static_pages WHERE `pk_static_page` IN (' . $contents . ')';
        $sqls[] = 'DELETE FROM content_positions WHERE `pk_fk_content` IN (' . $contents . ')';
        $sqls[] = 'DELETE FROM contentmeta WHERE `fk_content` IN (' . $contents . ')';

        $conn->beginTransaction();

        try {
            foreach ($sqls as $sql) {
                $conn->executeUpdate($sql);
            }

            \Video::batchDelete($contentsArray);
            \Attachment::batchDelete($contentsArray);
            $conn->commit();

            return true;
        } catch (\Exception $e) {
            $logger = getService('error.log');
            $logger->error($e->getMessage() . ' ' . $e->getTraceAsString());
            $conn->rollBack();
            return false;
        }
    }

    /**
     * TODO: Rename the db column to available
     * Changes the menu status (shown, hidden) for the category.
     *
     * @param string $status the status to set to the category.
     */
    public function setAvailable($status)
    {
        if ($this->pk_content_category == null) {
            return false;
        }

        try {
            $rs = getService('dbal_connection')->update(
                'content_categories',
                [ 'inmenu' => (int) $status ],
                [ 'pk_content_category' => $this->pk_content_category ]
            );

            if ($rs === false) {
                return false;
            }

            dispatchEventWithParams('category.update', [ 'category' => $this ]);
            return true;
        } catch (\Exception $e) {
            $logger = getService('error.log');
            $logger->error($e->getMessage() . ' ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Changes the rss status (shown, hidden) for the category.
     *
     * @param string $status the status to set to the category rss.
     */
    public function setInRss($status)
    {
        if ($this->pk_content_category == null) {
            return false;
        }

        try {
            if (!is_array($this->params)) {
                $this->params = [];
            }

            $this->params['inrss'] = $status;
            $this->params          = serialize($this->params);

            getService('dbal_connection')->update(
                'content_categories',
                [ 'params' => $this->params ],
                [ 'pk_content_category' => $this->pk_content_category ]
            );

            dispatchEventWithParams('category.update', [ 'category' => $this ]);
            return true;
        } catch (\Exception $e) {
            $logger = getService('error.log');
            $logger->error($e->getMessage() . ' ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Returns the list of properties that support multiple languages.
     *
     * @return array The list of properties that can be localized to multiple
     *               languages.
     */
    public static function getL10nKeys()
    {
        return [ 'title' ];
    }
}
