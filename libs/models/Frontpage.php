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
 */
class Frontpage
{
    /**
     * The frontpage id
     *
     * @var int
     */
    public $pk_frontpage = null;

    /**
     * The frontpage date
     *
     * @var string
     */
    public $date = null;

    /**
     * The frontpage version
     *
     * @var int
     */
    public $version = null;

    /**
     * The list of the frontpage contents
     *
     * @var array
     */
    public $content_positions = [];

    /**
     * Whether this frontpage is promoted
     *
     * @var boolean
     */
    public $promoted = 0;

    /**
     * Whether the frontpage is a frontpage day
     *
     * @var boolean
     */
    public $day_frontpage = 0;

    /**
     * Miscelanous params of this frontpage
     *
     * @var array
     */
    public $params = null;

    /**
     * Initializes the Frontpage instance
     *
     * @param int $id
     *
     * @return boolean|null|Frontpage
     */
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('Frontpage');

        return $this->read($id);
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
     * Reads an specific frontpage given its id
     *
     * @param  int       $id Object ID
     *
     * @return boolean|null|Frontpage the frontpage object instance
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
     * Creates a frontpage given an array of data
     *
     * @param array $data the frontpge data
     *
     * @return bool If create in database
     */
    public function create($data)
    {
        if (is_null($data['category'])) {
            return false;
        }
        $date          = (!isset($data['date']) || empty($data['date']))? date("Ymd") : $data['date'];
        $category      = $data['category'];
        $contents     = (!isset($data['contents']) || empty($data['contents']))? null: serialize($data['contents']);
        $version      = (empty($data['version'])) ? 0: $data['version'];
        $promoted     = (empty($data['promoted'])) ? null : intval($data['promoted']);
        $dayFrontpage = (empty($data['day_frontpage'])) ? null: intval($data['day_frontpage']);
        $params       = (!isset($data['params']) || empty($data['params']))? null: serialize($data['params']);

        $conn = getService('dbal_connection');
        try {
            $rs = $conn->insert(
                'frontpages',
                [
                    'date'              => $date,
                    'category'          => $category,
                    'content_positions' => $contents,
                    'version'           => $version,
                    'promoted'          => 2,
                    'day_frontpage'     => $dayFrontpage,
                    'params'            => $params
                ]
            );

            return $conn->lastInsertId();
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Updates the frontpage from a data array
     *
     * @param array $data the new data
     *
     * @return void
     * @author
     */
    public function update($data)
    {
        $contents     = (!isset($data['contents']) || empty($data['contents']))? null: serialize($data['contents']);
        $version      = (empty($data['version'])) ? 0: $data['version'];
        $promoted     = (empty($data['promoted'])) ? null : intval($data['promoted']);
        $dayFrontpage = (empty($data['day_frontpage'])) ? null: intval($data['day_frontpage']);
        $params       = (!isset($data['params']) || empty($data['params']))? null: serialize($data['params']);
        try {
            $rs = getService('dbal_connection')->update(
                'frontpages',
                [
                    'content_positions' => $contents,
                    'version'           => $version,
                    'promoted'          => $promoted,
                    'day_frontpage'     => $dayFrontpage,
                    'params'            => $params
                ],
                [
                    'pk_frontpage' => $resp,
                ]
            );
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
