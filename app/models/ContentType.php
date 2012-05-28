<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Handles common operations with content types
 *
 * @package    Onm
 * @subpackage Model
 *
 * @author Alex Rico
 */
class ContentType
{
    /**
     * @var int(10) with id for a content type
     */
    public $pk_content_type = null;

    /**
     * @var string with internal name for content type
     */
    public $name = null;

    /**
     * @var string with readable name for content type
     */
    public $title = null;

    /**
     * @var int(10)
     */
    public $fk_template_default = null;


    /**
     * Initializes the content type for a given id.
     *
     * @param string $id the content type id to initilize.
     **/
    public function __construct($id=null)
    {
        $this->cache = new MethodCacheManager($this, array('ttl' => 30));

        if (!is_null($id)) {
            $this->read($id);
        }
    }

    public function read($id)
    {
        // Fire event onBeforeXxx
        $GLOBALS['application']->dispatch('onBeforeRead', $this);
        if (empty($id)) {
            return false;
        }
        $sql = 'SELECT * FROM content_types WHERE pk_content_type =?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));

        if (!$rs) {
            \Application::logDatabaseError();

            return false;
        }

        $this->load($rs->fields);

        // Fire event onAfterXxx
        $GLOBALS['application']->dispatch('onAfterRead', $this);

    }

    /**
     * Load properties into this instance
     *
     * @param array $properties Array properties
     */
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

    /*
     * Fetches available content types.
     *
     * @return array an array with each content type with id, name and title.
     *
     * @throw Exception if there was an error while fetching all the
     *                  content types
     */
    public static function getAllContentTypes()
    {
        $fetchedFromAPC = false;
        if (extension_loaded('apc')) {
            $resultArray = apc_fetch(APC_PREFIX . "_getContentTypes",
                $fetchedFromAPC);
        }

        // If was not fetched from APC now is turn of DB
        if (!$fetchedFromAPC) {

            $sqlContTypes = "SELECT pk_content_type, name, title "
                               . "FROM content_types";
            $resultSet = $GLOBALS['application']->conn->Execute($sqlContTypes);

            if (!$resultSet) {
                $message = "There was an error while fetching available "
                         . "content types. '$sqlContTypes'.";
                throw new \Exception($message);
            }

            try {
                $resultArray = $resultSet->GetArray();
                $i=0;
                foreach ($resultArray as &$res) {
                    $resultArray[$i]['title'] = htmlentities($res['title']);
                    $resultArray[$i]['2'] = htmlentities($res['2']);
                    $i++;
                }
            } catch (\Exception $e) {
                printf("Excepcion: " . $e->message);

                return null;
            }

            if (extension_loaded('apc')) {
                apc_store(APC_PREFIX . "_getContentTypes", $resultArray);
            }
        }

        return $resultArray;
    }

    /*
     * Find a content type id given the name of one content type.
     *
     * @param  string $name The name of a content type
     * @return int    pk_content_type.
     * @throw  Exception  if there was an error while fetching
     *                    all the content types
     */
    public static function getIdContentType($name)
    {
        $contenTypes = self::getContentTypes();

        foreach ($contenTypes as $types) {
            if ($types['name'] == $name) {
                return $types['pk_content_type'];
            }
        }

        return false;
    }

    /*
     * Get the content type object given the id of one content.
     *
     * @return int pk_content_type.
     * @param  int $id The id of a content
     *
     */
    public static function getContentTypeByContentId($id)
    {
        $sql = 'SELECT fk_content_type FROM contents WHERE pk_content = ?';
        $rs = $GLOBALS['application']->conn->GetOne($sql, $id);

        if (!$rs) {
            \Application::logDatabaseError();

            return false;
        }

        return $rs;
    }
}