<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Onm\Settings as s;

/**
 * Advertisement class
 *
 * Handles all the CRUD operations with advertisement content.
 * The class use MethodCacheManager for better performance.
 */
class Advertisement extends Content
{
    /**
     * The category that all the advertisements belongs to
     *
     * @var int
     */
    const ADVERTISEMENT_CATEGORY = 2;

    /**
     * the advertisement id
     *
     * @var int
     */
    public $pk_advertisement = null;

    /**
     * TODO: To be replaced by the property 'positions'
     * The type of advertisement
     *
     * @var int
     */
    public $type_advertisement = null;

    /**
     * List of categories that this advertisement will be available
     *
     * @var string
     */
    public $fk_content_categories = [];

    /**
     * The related image id to this ad
     *
     * @var int
     */
    public $img = null;

    /**
     * The position of the advertisement
     *
     * @var int
     */
    public $path = null;

    /**
     * The url that this advertisment links to
     *
     * @var string
     */
    public $url = null;

    /**
     * The type of measure for this ad (views, clicks, data range)
     *
     * @var string
     */
    public $type_medida = null;

    /**
     * TODO: maybe this is replicated with num_clic_count
     * Number of user clicks in this advertismenet
     *
     * @var int
     */
    public $num_clic = null;

    /**
     * Number of user clicks in this advertisement
     *
     * @var int
     */
    public $num_clic_count = null;

    /**
     * Number of views for this advertisement
     *
     * @var int
     */
    public $num_view = null;

    /**
     * Whether overlap flash events when rendering this advertisement
     *
     * @var boolean
     */
    public $overlap = null;

    /**
     * The list of positions this ad is assigned to
     *
     * @var array
     */
    public $positions = null;

    /**
     * The script content of this advertisement
     *
     * @varstring
     */
    public $script = null;

    /**
     * Whether this advertisement has a script content
     *
     * @var boolean
     */
    public $with_script = null;

    /**
     * In interstitial advertisements this is the amount of time that it will
     * be shown to the user
     *
     * @var int
     */
    public $timeout = null;

    /**
     * Whether this advertisement has a flash image
     *
     * @var boolean
     */
    public $is_flash = null;

    /**
     * Initializes the Advertisement class
     *
     * @param int $id ID of the Advertisement
     *
     * @return Advertisement the instance of the advertisement class
     */
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('Advertisement');

        parent::__construct($id);
    }

    /**
     * Load object properties
     *
     * @param array $properties
     *
     * @return void
     */
    public function load($properties)
    {
        parent::load($properties);

        // FIXME: review that this property is not used ->img
        $this->img = $this->path;

        // Initialize the categories array of this advertisement
        if (!is_array($this->fk_content_categories) && !is_null($this->fk_content_categories)) {
            $this->fk_content_categories = explode(',', $this->fk_content_categories);
        }

        // Check if it contains a flash element
        $this->is_flash = 0;
        if ($this->with_script == 0) {
            $img = getService('entity_repository')->find('Photo', $this->path);
            if (!empty($img) && $img->type_img == "swf") {
                $this->is_flash = 1;
            }
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

        return $this;
    }

    /**
     * Get an instance of a particular ad from its ID
     *
     * @param int $id the ID of the Advertisement
     *
     * @return Advertisement the instance for the Ad
     */
    public function read($id)
    {
        // If no valid id then return
        if (((int) $id) <= 0) {
            return;
        }

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM contents LEFT JOIN contents_categories ON pk_content = pk_fk_content '
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
            if ($positions === false) {
                return false;
            }

            $rs['positions'] = array_map(function ($el) {
                return $el['position_id'];
            }, $positions);
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());
            return false;
        }

        if (array_key_exists('script', $rs)) {
            // Decode base64 if isn't decoded yet
            $isBase64 = base64_decode($rs['script']);
            if ($isBase64) {
                $rs['script'] = $isBase64;
            }
        }

        $this->load($rs);

        if (is_string($this->params)) {
            $advertisement->params = unserialize($this->params);

            if (!is_array($advertisement->params)) {
                $advertisement->params = [];
            }
        }

        // Return instance to method chaining
        return $this;
    }

    /**
     * Create and save into database the ad instance from one array
     *
     * @param array $data the needed data for create a new ad.
     *
     * @return Advertisement
     */
    public function create($data)
    {
        $conn = getService('dbal_connection');
        $conn->beginTransaction();

        parent::create($data);

        if (!empty($data['script'])) {
            $data['script'] = base64_encode($data['script']);
        }

        if (!isset($data['with_script'])) {
            $data['with_script'] = 0;
        }

        $data['pk_advertisement'] = $data['id'] = $this->id;
        $data['overlap']          = (isset($data['overlap'])) ? $data['overlap'] : 0;
        $data['timeout']          = (isset($data['timeout'])) ? $data['timeout'] : null;
        $data['type_medida']      = (!empty($data['type_medida'])) ? $data['type_medida'] : null;

        try {
            $rs = getService('dbal_connection')->insert(
                'advertisements',
                [
                    'pk_advertisement'      => $data['pk_advertisement'],
                    'fk_content_categories' => $data['categories'],
                    'path'                  => $data['img'],
                    'url'                   => $data['url'],
                    'type_medida'           => $data['type_medida'],
                    'num_clic'              => (int) $data['num_clic'],
                    'num_clic_count'        => 0, // num_clic_count
                    'num_view'              => (int) $data['num_view'],
                    'with_script'           => (int) $data['with_script'],
                    'script'                => $data['script'],
                    'overlap'               => (int) $data['overlap'],
                    'timeout'               => (int) $data['timeout'],
                ]
            );

            foreach (array_unique($data['positions']) as $position) {
                $rs = getService('dbal_connection')->insert(
                    'advertisements_positions',
                    [
                        'advertisement_id' => $data['id'],
                        'position_id'      => (int) $position,
                    ]
                );
            }

            $conn->commit();

            // $this->load($data);

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
     * @return Advertisement Return the instance to chaining method
     */
    public function update($data)
    {
        $conn = getService('dbal_connection');
        $conn->beginTransaction();

        // TODO: Remove when dispatching events from custom contents
        $this->old_position = $this->positions;

        parent::update($data);

        if (!empty($data['script'])) {
            $data['script'] = base64_encode($data['script']);
        }

        $data['overlap']     = (isset($data['overlap'])) ? $data['overlap'] : 0;
        $data['timeout']     = (isset($data['timeout'])) ? $data['timeout'] : null;
        $data['with_script'] = (isset($data['with_script'])) ? $data['with_script'] : 0;
        $data['type_medida'] = (!empty($data['type_medida'])) ? $data['type_medida'] : null;

        try {
            $rs = getService('dbal_connection')->update(
                'advertisements',
                [
                    'fk_content_categories' => $data['categories'],
                    'path'                  => $data['img'],
                    'url'                   => $data['url'],
                    'type_medida'           => $data['type_medida'],
                    'num_clic'              => (int) $data['num_clic'],
                    'num_view'              => (int) $data['num_view'],
                    'with_script'           => (int) $data['with_script'],
                    'script'                => $data['script'],
                    'overlap'               => (int) $data['overlap'],
                    'timeout'               => (int) $data['timeout'],
                ],
                [ 'pk_advertisement' => (int) $data['id'] ]
            );

            $rs = getService('dbal_connection')->delete(
                'advertisements_positions',
                [ 'advertisement_id' => $data['id'] ]
            );

            foreach (array_unique($data['positions']) as $position) {
                $rs = getService('dbal_connection')->insert(
                    'advertisements_positions',
                    [
                        'advertisement_id' => $data['id'],
                        'position_id'      => (int) $position,
                    ]
                );
            }

            $conn->commit();

            $this->load($data);

            return $this;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());
            return false;
        }
    }

    /**
     * Deletes one advertisement from db given an id.
     *
     * @param string $id the id of the ad to delete from db.
     *
     * @return void
     *
     */
    public function remove($id)
    {
        if ((int) $id <= 0) {
            return false;
        }

        parent::remove($id);

        try {
            $rs = getService('dbal_connection')
                ->delete("advertisements", [ 'pk_advertisement' => $id ]);

            if (!$rs) {
                return false;
            }
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Get url of advertisement
     *
     * @param int $id Advertisement Id
     *
     * @return string
     */
    public function getUrl($id)
    {
        // If no valid id then return
        if (((int) $id) <= 0) {
            return;
        }

        // Try to minimize the database overload if this object was preloaded
        // or doesn't fit the rules
        if (isset($this) && isset($this->url) && ($this->id == $id)) {
            return $this->url;
        }

        try {
            // Fetch data for the ad from the database
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT url FROM `advertisements` WHERE `advertisements`.`pk_advertisement`=?',
                [ $id ]
            );

            if (!$rs) {
                return null;
            }
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());
            return null;
        }

        return $rs['url'];
    }

    /**
     * Returns the list of sizes for Google DFP.
     *
     * @param array $sizes The list of sizes for the current add.
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
     * @param array $params The item parameters.
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
     * @return void
     */
    public static function setNumClics($id)
    {
        // If no valid id then return
        if (((int) $id) <= 0) {
            return;
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
            dispatchEventWithParams('content.update-set-num-views', [ 'content' => $ad ]);

            return true;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());
            return false;
        }
    }

    /**
     * Renders the advertisment given a set of parameters
     *
     * @param array $params list of parameters for rendering the advertisement
     * @param Template $tpl the Template class instance
     *
     * @return string the final html for the ad
     */
    public function render($params)
    {
        // Don't render any non default ads if module is not activated
        if (!getService('core.security')->hasExtension('ADS_MANAGER')
            && (!isset($this->default_ad) || $this->default_ad != 1)
        ) {
            return '';
        }

        // With multiple positions we cannot rely on the type_advertisement=37
        // any more so I'm telling the renderer that this is a floating banner
        $params['floating'] = true;

        $adsRenderer = getService('core.renderer.advertisement');

        return $adsRenderer->render($this, $params);
    }
}
