<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Kiosko extends Content
{
    /**
     * The kiosko id
     *
     * @var int
     */
    public $pk_kiosko = null;

    /**
     * The name of the kiosko
     *
     * @var string
     */
    public $name = null;

    /**
     * The path to the kiosko file
     *
     * @var string
     */
    public $path = null;

    /**
     * The publishing date of the kiosko
     *
     * @var string
     */
    public $date = null;

    /**
     * Whether if this kiosko is marked as favorite or not
     *
     * @var boolean
     */
    public $favorite = 0;

    /**
     * The price of this kiosko
     *
     * @var int
     */
    public $price = 0;

    /**
     * The type of kiosko
     *
     * @var string
     */
    public $type = 0;

    /**
      * Initializes the Kiosko.
      *
      * @param integer $id The kiosko id.
      */
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('Kiosko');
        $this->content_type           = 14;
        $this->content_type_name      = 'kiosko';

        parent::__construct($id);
    }

    /**
     * Overloads the object properties with an array of the new ones.
     *
     * @param array $properties The list of properties to load.
     */
    public function load($properties)
    {
        $properties['thumbnail'] =
            str_replace('.pdf', '.jpg', $properties['path']);

        parent::load($properties);
    }

    /**
     * Loads the kiosko data given an id.
     *
     * @param integer $id The kiosko id.
     *
     * @return Kiosko The current kiosko.
     */
    public function read($id)
    {
        // If no valid id then return
        if (((int) $id) <= 0) {
            return false;
        }

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM contents LEFT JOIN content_category ON pk_content = content_id '
                . 'LEFT JOIN kioskos ON pk_content = pk_kiosko WHERE pk_content = ?',
                [ $id ]
            );

            if (!$rs) {
                return false;
            }

            $this->load($rs);

            return $this;
        } catch (\Exception $e) {
            getService('error.log')->error(
                'Error while fetching Kiosko: ' . $e->getMessage()
            );

            return false;
        }
    }

    /**
     * Creates a new kiosko from data.
     *
     * @param array $data The kiosko data.
     *
     * @return boolean True if the object was stored.
     */
    public function create($data)
    {
        $data['price'] = isset($data['price']) ? $data['price'] : 0;
        $data['type']  = isset($data['type']) ? $data['type'] : 0;

        $conn = getService('dbal_connection');

        $conn->beginTransaction();

        try {
            parent::create($data);

            $conn->insert('kioskos', [
                'pk_kiosko' => (int) $this->id,
                'name'      => $data['name'] ?? null,
                'path'      => $data['path'],
                'date'      => $data['date'],
                'price'     => $data['price'],
                'type'      => $data['type']
            ]);

            $conn->commit();

            return $this;
        } catch (\Exception $e) {
            $conn->rollback();

            getService('error.log')->error(
                'Error while creating Kiosko: ' . $e->getMessage()
            );

            return false;
        }
    }

    /**
     * Updates the kiosko information given an array of data.
     *
     * @param array $data The new data for the kiosko.
     *
     * @return boolean true If the kiosko was updated.
     */
    public function update($data)
    {
        $conn = getService('dbal_connection');

        $conn->beginTransaction();

        try {
            parent::update($data);

            $conn->update('kioskos', [
                'name'      => $data['name'] ?? null,
                'date'      => $data['date'],
                'path'      => $data['path'],
                'price'     => $data['price'] ?? 0,
                'type'      => $data['type'] ?? 0
            ], [ 'pk_kiosko' => (int) $data['id'] ]);

            $conn->commit();

            return true;
        } catch (\Exception $e) {
            $conn->rollback();

            getService('error.log')->error(
                'Error while updating Kiosko: ' . $e->getMessage()
            );

            return false;
        }
    }

    /**
     * Returns the list of months grouped by years.
     *
     * @return array The list of months grouped by year.
     */
    public function getMonthsByYears()
    {
        $items = [];
        $sql   = 'SELECT DISTINCT MONTH(date) as month, YEAR(date) as year'
            . ' FROM `kioskos` ORDER BY year DESC, month DESC';

        $rs = getService('dbal_connection')->fetchAll($sql);

        foreach ($rs as $value) {
            $items[$value['year']][] = $value['month'];
        }

        return $items;
    }
}
