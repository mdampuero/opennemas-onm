<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Advertisement extends Content
{
    /**
     * The advertisement id.
     *
     * @var int
     */
    public $pk_advertisement = null;

    /**
     * TODO: To be replaced by the property 'positions'.
     *
     * The type of advertisement.
     *
     * @var int
     */
    public $type_advertisement = null;

    /**
     * List of categories that this advertisement will be available.
     *
     * @var string.
     */
    public $fk_content_categories = [];

    /**
     * The related image id to this ad.
     *
     * @var int
     */
    public $img = null;

    /**
     * The position of the advertisement.
     *
     * @var int
     */
    public $path = null;

    /**
     * The url that this advertisment links to.
     *
     * @var string
     */
    public $url = null;

    /**
     * The type of measure for this ad (views, clicks, data range).
     *
     * @var string
     */
    public $type_medida = null;

    /**
     * TODO: maybe this is replicated with num_clic_count
     *
     * Number of user clicks in this advertismenet.
     *
     * @var int
     */
    public $num_clic = 0;

    /**
     * Number of user clicks in this advertisement.
     *
     * @var int
     */
    public $num_clic_count = 0;

    /**
     * Number of views for this advertisement.
     *
     * @var int
     */
    public $num_view = 0;

    /**
     * Whether overlap flash events when rendering this advertisement.
     *
     * @var bool
     */
    public $overlap = 0;

    /**
     * The list of positions this ad is assigned to.
     *
     * @var array
     */
    public $positions = [];

    /**
     * The script content of this advertisement.
     *
     * @varstring
     */
    public $script = null;

    /**
     * Whether this advertisement has a script content.
     *
     * @var bool
     */
    public $with_script = 0;

    /**
     * In interstitial advertisements this is the amount of time that it will
     * be shown to the user.
     *
     * @var int
     */
    public $timeout = 0;

    /**
     * Whether this advertisement has a flash image.
     *
     * @var bool
     */
    public $is_flash = false;

    /**
     * Initializes the Advertisement class
     *
     * @param int $id ID of the Advertisement
     */
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('Advertisement');
        $this->content_type           = 2;
        $this->content_type_name      = 'advertisement';

        parent::__construct($id);
    }

    /**
     * {@inheritdoc}
     */
    public function load($properties)
    {
        parent::load($properties);

        // Initialize the categories array of this advertisement
        if (!is_array($this->fk_content_categories)
            && !is_null($this->fk_content_categories)
        ) {
            $this->fk_content_categories =
                explode(',', $this->fk_content_categories);
        }

        // Check if it contains a flash element
        if ($this->with_script == 0) {
            $img = getService('api.service.photo')->getItem($this->path);

            $this->is_flash = !empty($img) && $img->type_img == "swf";
        }

        if (is_null($this->params)
            || !array_key_exists('restriction_devices', $this->params)
            || (array_key_exists('restriction_devices', $this->params)
                && empty($this->params['restriction_devices']))
        ) {
            $this->params['restriction_devices'] = [
                'phone'   => 1,
                'tablet'  => 1,
                'desktop' => 1,
            ];
        }

        if (is_null($this->params)
            || !array_key_exists('restriction_usergroups', $this->params)
            || (array_key_exists('restriction_usergroups', $this->params)
            && empty($this->params['restriction_usergroups']))
        ) {
            $this->params['restriction_usergroups'] = [];
        }

        if (empty($properties['positions'])) {
            $this->positions = [];
        }

        if (base64_decode($this->script)) {
            $this->script = base64_decode($this->script);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function read($id)
    {
        if (empty($id)) {
            return;
        }

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM contents LEFT JOIN content_category ON pk_content = content_id '
                . 'LEFT JOIN advertisements ON pk_content = pk_advertisement WHERE pk_content=?',
                [ $id ]
            );

            if (!$rs) {
                return false;
            }

            $positions = getService('dbal_connection')->fetchAll(
                'SELECT position_id FROM advertisements_positions '
                . 'WHERE advertisement_id=?',
                [ $id ]
            );

            if ($positions) {
                $rs['positions'] = array_map(function ($el) {
                    return $el['position_id'];
                }, $positions);
            }

            $this->load($rs);

            return $this;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function create($data)
    {
        $conn = getService('dbal_connection');
        try {
            $conn->beginTransaction();

            parent::create($data);

            $this->pk_content       = (int) $this->id;
            $this->pk_advertisement = (int) $this->id;

            getService('dbal_connection')->insert('advertisements', [
                'pk_advertisement'      => $this->id,
                'fk_content_categories' => $data['categories'],
                'path'                  => $data['path'],
                'url'                   => $data['url'],
                'num_clic'              => (int) $data['num_clic'],
                'num_clic_count'        => 0, // num_clic_count
                'num_view'              => (int) $data['num_view'],
                'type_medida'           => (!empty($data['type_medida'])) ? $data['type_medida'] : null,
                'with_script'           => (isset($data['with_script'])) ? (int) $data['with_script'] : 0,
                'script'                => (!empty($data['script'])) ? base64_encode($data['script']) : '',
                'overlap'               => (isset($data['overlap'])) ? (int) $data['overlap'] : 0,
                'timeout'               => (isset($data['timeout'])) ? (int) $data['timeout'] : null,

            ]);

            $this->savePositions($this->id, $data['positions']);
            $conn->commit();

            return $this;
        } catch (\Exception $e) {
            $conn->rollback();

            getService('error.log')->error($e->getMessage());

            return false;
        }
    }

    /**
     * Updates the data of the ad from one array.
     *
     * @param array $data
     *
     * @return boolean|Advertisement Return the instance to chaining method
     */
    public function update($data)
    {
        $conn = getService('dbal_connection');

        // TODO: Remove when dispatching events from custom contents
        $this->old_position = $this->positions;

        try {
            $conn->beginTransaction();

            parent::update($data);

            $conn->update('advertisements', [
                'fk_content_categories' => $data['categories'],
                'path'                  => $data['path'],
                'url'                   => $data['url'],
                'num_clic'              => (int) $data['num_clic'],
                'num_view'              => (int) $data['num_view'],
                'type_medida'           => (!empty($data['type_medida'])) ? $data['type_medida'] : null,
                'with_script'           => (isset($data['with_script'])) ? (int) $data['with_script'] : 0,
                'script'                => (!empty($data['script'])) ? base64_encode($data['script']) : '',
                'overlap'               => (isset($data['overlap'])) ? (int) $data['overlap'] : 0,
                'timeout'               => (isset($data['timeout'])) ? (int) $data['timeout'] : null,
            ], [ 'pk_advertisement' => (int) $data['id'] ]);

            $this->removePositions($this->id);
            $this->savePositions($this->id, $data['positions']);
            $conn->commit();

            return $this;
        } catch (\Exception $e) {
            $conn->rollback();

            getService('error.log')->error($e->getMessage());

            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove($id)
    {
        try {
            if (!parent::remove($id)) {
                return false;
            }

            getService('dbal_connection')
                ->delete("advertisements", [ 'pk_advertisement' => $id ]);

            return true;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());
            return false;
        }
    }

    /**
     * Returns the list of sizes for Google DFP.
     *
     * @return string The list of sizes for Google DFP.
     */
    public function getSizes()
    {
        $sizes = $this->normalizeSizes();

        $sizes = array_map(function ($a) {
            return "[ {$a['width']}, {$a['height']} ]";
        }, $sizes);

        return '[ ' . implode(', ', $sizes) . ' ]';
    }

    /**
     * Checks all parameters (old version) and returns the list of sizes.
     *
     * @return array The list of sizes.
     */
    public function normalizeSizes()
    {
        $params = $this->params;

        // New system, sizes with devices
        if (array_key_exists('sizes', $params)) {
            return $params['sizes'];
        }

        if (!array_key_exists('height', $params)
            || !array_key_exists('width', $params)) {
            return [];
        }

        $sizes  = [];
        $totalW = is_array($params['width']) ? count($params['width']) : 1;
        $totalH = is_array($params['height']) ? count($params['height']) : 1;
        $total  = max($totalH, $totalW);

        // Convert non-array values to array
        if (!is_array($params['height'])) {
            $params['height'] = array_fill(0, $total, $params['height']);
        }

        // Convert non-array values to array
        if (!is_array($params['width'])) {
            $params['width'] = array_fill(0, $total, $params['width']);
        }

        for ($i = 0; $i < $total; $i++) {
            $size = [
                'height' => $params['height'][$i],
                'width'  => $params['width'][$i]
            ];

            if ($i < 3) {
                $size['device'] = $i === 0 ? 'desktop' :
                    ($i === 1 ? 'tablet' : 'phone');
            }

            $sizes[] = $size;
        }

        return $sizes;
    }

    /**
     * Increase by one click the number of clicks given an advertisement id
     *
     * @param int $id the id of the advertisement to increase num_count
     *
     * @return boolean
     */
    public static function setNumClics($id)
    {
        // If no valid id then return
        if (((int) $id) <= 0) {
            return null;
        }

        try {
            // Fetch data for the ad from the database
            $rs = getService('dbal_connection')->executeUpdate(
                'UPDATE advertisements SET `num_clic_count`=`num_clic_count`+1 WHERE `pk_advertisement`=?',
                [ $id ]
            );

            if (!$rs) {
                return false;
            }

            // Clean entity repository cache
            $ad = new \Advertisement($id);
            dispatchEventWithParams('content.update-set-num-views', [ 'item' => $ad ]);

            return true;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());
            return false;
        }
    }

    /**
     * Removes positions for the advertisement.
     *
     * @param int $id The advertisement id.
     */
    protected function removePositions($id)
    {
        getService('dbal_connection')
            ->delete('advertisements_positions', [ 'advertisement_id' => $id ]);
    }

    /**
     * Saves the advertisement positions given the id and an array of positions.
     *
     * @param int   $id        The advertisement id.
     * @param array $positions The list of positions.
     *
     * @return bool True if positions were saved.
     */
    protected function savePositions($id, $positions)
    {
        if (empty($positions)) {
            return;
        }

        $conn = getService('dbal_connection');

        foreach ($positions as $position) {
            try {
                $conn->insert('advertisements_positions', [
                    'advertisement_id' => $id,
                    'position_id'      => $position
                ]);
            } catch (\Exception $e) {
                getService('error.log')->error(
                    $e->getMessage() . ' Stack Trace: ' . $e->getTraceAsString()
                );

                return;
            }
        }
    }
}
