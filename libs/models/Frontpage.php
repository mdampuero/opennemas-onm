<?php
/**
 * Defintes the Frontpage class
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
 * Handles newspaper library
 *
 * @package    Model
 **/
class Frontpage
{
    /**
     * The frontpage id
     *
     * @var int
     **/
    public $pk_frontpage = null;

    /**
     * The frontpage date
     *
     * @var string
     **/
    public $date = null;

    /**
     * The frontpage version
     *
     * @var int
     **/
    public $version = null;

    /**
     * The list of the frontpage contents
     *
     * @var array
     **/
    public $content_positions = [];

    /**
     * Whether this frontpage is promoted
     *
     * @var boolean
     **/
    public $promoted = 0;

    /**
     * Whether the frontpage is a frontpage day
     *
     * @var boolean
     **/
    public $day_frontpage = 0;

    /**
     * Miscelanous params of this frontpage
     *
     * @var array
     **/
    public $params = null;

    /**
     * Initializes the Frontpage instance
     *
     * @param int $id
     *
     * @return void
     */
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('Frontpage');

        return $this->read($id);
    }

    /**
     * Creates a frontpage given an array of data
     *
     * @param array $data the frontpge data
     *
     * @return bool If create in database
     */
    public function create($data)
    {
        $data['content_status'] = 1;
        $data['position']       = 1;

        if (is_null($data['category'])) {
            return false;
        }
        $date          = (!isset($data['date']) || empty($data['date']))? date("Ymd") : $data['date'];
        $category      = $data['category'];
        $contents      = (!isset($data['contents']) || empty($data['contents']))? null: serialize($data['contents']);
        $params        = (!isset($data['params']) || empty($data['params']))? null: serialize($data['params']);
        $version       = (empty($data['version']))? 0: $data['version'];
        $promoted      = (empty($data['promoted'])) ? null : intval($data['promoted']);
        $day_frontpage = (empty($data['day_frontpage'])) ? null: intval($data['day_frontpage']);

        $resp = $GLOBALS['application']->conn->GetOne(
            'SELECT pk_frontpage FROM `frontpages` WHERE category = ? AND date= ?',
            array($category,$date)
        );

        if ($resp) {
            $promoted = "1";
            $sql = "UPDATE frontpages SET  `content_positions`=?,,
                                           `version` =?,
                                           `promoted` =?,
                                           `day_frontpage` =?,
                                           `params` =?
                                            WHERE pk_frontpage = ".$resp;

            $values = array($contents, $version, $promoted, $day_frontpage, $params);
        } else {
            $promoted = "2";
            $sql = "INSERT INTO frontpages (`date`,`category`,`content_positions`,
                                            `version`, `promoted`, `day_frontpage`,
                                            `params`)
                    VALUES (?,?,?, ?,?,?, ?)";
            $values = array(
                $date, $category,$contents,
                $version, $promoted, $day_frontpage,
                $params
            );
        }

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            return false;
        }

        return true;
    }

    /**
     * Reads an specific frontpage given its id
     *
     * @param  int       $id Object ID
     *
     * @return Frontpage the frontpage object instance
     */
    public function read($id)
    {
        // If no valid id then return
        if (((int) $id) <= 0) return;

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM frontpages WHERE pk_frontpage = ?',
                [ $id ]
            );

            if (!$rs) {
                return false;
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }

        $this->load($rs);

        return $this;
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

        $this->id = $this->pk_frontpage;
        $this->content_positions = unserialize($this->content_positions);
        $this->params = unserialize($this->params);
        $this->fk_content_type = 18;

        return $this;
    }

    /**
     * Read, get a specific frontpage
     *
     * @param  int    $date     date of calendar
     * @param  int    $category category in menu element
     * @param  int    $version  version of the frontpage
     *
     * @return boolean
     */

    public function getFrontpage($date, $category = 0)
    {
        // if category = 0 => home
        if (is_null($category)
            && is_null($date)
        ) {
              return false;
        }

        $sql = "SELECT * FROM `frontpages` WHERE `date`=? AND `category`=?";
        $values = array($date, $category);

        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if ($rs === false) {
            return false;
        }

        $this->load($rs->fields);

        return $this;
    }

     /**
     * Read, get a specific frontpage
     *
     * @param  int    $date     date of calendar
     *
     * @return Widget Return instance to chaining method
     */

    public function getCategoriesWithFrontpage($date)
    {
        if (is_null($date)) {
            return false;
        }

        $sql = "SELECT category FROM `frontpages` WHERE `date`=?";
        $values = array($date);

        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if ($rs === false) {
            return false;
        }
        $items = array();
        while (!$rs->EOF) {
            $items[] = $rs->fields['category'];
            $rs->MoveNext();
        }

        return $items;
    }
}
