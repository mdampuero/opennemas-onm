<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
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
 *
 * @package Onm
 * @subpackage Model
 **/
class Advertisement extends Content
{
    /**
     * The category that all the advertisements belongs to
     *
     * @var int
     **/
    const ADVERTISEMENT_CATEGORY = 2;

    /**
     * the advertisement id
     *
     * @var int
     **/
    public $pk_advertisement = null;

    /**
     * The type of advertisement
     *
     * @var int
     **/
    public $type_advertisement = null;

    /**
     * List of categories that this advertisement will be available
     *
     * @var string
     **/
    public $fk_content_categories = [];

    /**
     * The related image id to this ad
     *
     * @var int
     **/
    public $img  = null;

    /**
     * The position of the advertisement
     *
     * @var int
     **/
    public $path = null;

    /**
     * The url that this advertisment links to
     *
     * @var string
     **/
    public $url            = null;

    /**
     * The type of measure for this ad (views, clicks, data range)
     *
     * @var string
     **/
    public $type_medida    = null;

    /**
     * TODO: maybe this is replicated with num_clic_count
     * Number of user clicks in this advertismenet
     *
     * @var int
     **/
    public $num_clic       = null;

    /**
     * Number of user clicks in this advertisement
     *
     * @var int
     **/
    public $num_clic_count = null;

    /**
     * Number of views for this advertisement
     *
     * @var int
     **/
    public $num_view       = null;

    /**
     * Whether overlap flash events when rendering this advertisement
     *
     * @var boolean
     **/
    public $overlap        = null;

    /**
     * The script content of this advertisement
     *
     * @varstring
     **/
    public $script      = null;

    /**
     * Whether this advertisement has a script content
     *
     * @var boolean
     **/
    public $with_script = null;

    /**
     * In interstitial advertisements this is the amount of time that it will
     * be shown to the user
     *
     * @var int
     **/
    public $timeout     = null;

    /**
     * Whether this advertisement has a flash image
     *
     * @var boolean
     **/
    public $is_flash = null;

    /**
     * Initializes the Advertisement class
     *
     * @param int $id ID of the Advertisement
     *
     * @return Advertisement the instance of the advertisement class
     **/
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
     **/
    public function load($properties)
    {
        parent::load($properties);

        // FIXME: review that this property is not used ->img
        $this->img = $this->path;

        // Initialize the categories array of this advertisement
        if (!is_array($this->fk_content_categories)) {
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

        if (is_null($this->params) || !array_key_exists('restriction_devices', $this->params) || (array_key_exists('restriction_devices', $this->params) && empty($this->params['restriction_devices']))) {
            $this->params['restriction_devices'] = [
                'phone'   => 1,
                'tablet'  => 1,
                'desktop' => 1,
            ];
        }

        if (is_null($this->params) || !array_key_exists('restriction_usergroups', $this->params) || (array_key_exists('restriction_usergroups', $this->params) && empty($this->params['restriction_usergroups']))) {
            $this->params['restriction_usergroups'] = [];
        }

        return $this;
    }

    /**
     * Get an instance of a particular ad from its ID
     *
     * @param int $id the ID of the Advertisement
     *
     * @return Advertisement the instance for the Ad
     **/
    public function read($id)
    {
        // If no valid id then return
        if (((int) $id) <= 0) return;

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM contents LEFT JOIN contents_categories ON pk_content = pk_fk_content '
                .'LEFT JOIN advertisements ON pk_content = pk_advertisement WHERE pk_content=?',
                [ $id ]
            );

            if (!$rs) {
                return false;
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
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

        // Return instance to method chaining
        return $this;
    }

    /**
     * Create and save into database the ad instance from one array
     *
     * @param array $data the needed data for create a new ad.
     *
     * @return Advertisement
     **/
    public function create($data)
    {
        parent::create($data);

        if (!empty($data['script'])) {
            $data['script'] = base64_encode($data['script']);
        }

        if (!isset($data['with_script'])) {
            $data['with_script'] = 0;
        }

        $data['pk_advertisement'] = $data['id'] = $this->id;
        $data['overlap'] = (isset($data['overlap']))? $data['overlap']: 0;
        $data['timeout'] = (isset($data['timeout']))? $data['timeout']: null;
        $data['type_medida'] =
            (isset($data['type_medida']))? $data['type_medida']: 'NULL';

        try {
            $rs = getService('dbal_connection')->insert(
                'advertisements',
                [
                    'pk_advertisement'      => $data['pk_advertisement'],
                    'type_advertisement'    => (int) $data['type_advertisement'],
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

            // $this->load($data);

            return $this;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Updates the data of the ad from one array.
     *
     * @param array $data
     *
     * @return Advertisement Return the instance to chaining method
     **/
    public function update($data)
    {
        // TODO: Remove when dispatching events from custom contents
        $this->old_position = $this->type_advertisement;

        parent::update($data);

        if (!empty($data['script'])) {
            $data['script'] = base64_encode($data['script']);
        }

        $data['overlap']     = (isset($data['overlap']))? $data['overlap']: 0;
        $data['timeout']     = (isset($data['timeout']))? $data['timeout']: null;
        $data['with_script'] = (isset($data['with_script']))? $data['with_script']: 0;
        $data['type_medida'] = (isset($data['type_medida']))? $data['type_medida']: 'NULL';

        try {
            $rs = getService('dbal_connection')->update(
                'advertisements',
                [
                    'type_advertisement'    => (int) $data['type_advertisement'],
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

            $this->load($data);

            return $this;
        } catch (\Exception $e) {
            error_log($e->getMessage());
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
     **/
    public function remove($id)
    {
        if ((int) $id <= 0) {
            return false;
        }

        parent::remove($id);

        try {
            $rs = getService('dbal_connection')->delete(
                "advertisements", [ 'pk_advertisement' => $id ]
            );

            if (!$rs) {
                return false;
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
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
     **/
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
            error_log($e->getMessage());
            return null;
        }

        return $rs['url'];
    }

    /**
     * Function that retrieves the name of the placeholder given
     * the type_advertisemnt
     * For example type=503  => name=publi-gallery-inner
     *
     * @param  string $advType
     * @return string $name_advertisement
     **/
    public function getNameOfAdvertisementPlaceholder($advType)
    {
        if ($advType > 0 && $advType < 100) {
            return 'publi-portada';
        } elseif ($advType > 100 && $advType < 200) {
            return 'publi-interior';
        } elseif ($advType > 200 && $advType < 300) {
            return 'publi-video';
        } elseif ($advType > 300 && $advType < 400) {
            return 'publi-video-interior';
        } elseif ($advType > 400 && $advType < 500) {
            return 'publi-gallery';
        } elseif ($advType > 500 && $advType < 600) {
            return 'publi-gallery-inner';
        } elseif ($advType > 600 && $advType < 700) {
            return 'publi-opinion';
        } elseif ($advType > 700 && $advType < 800) {
            return 'publi-opinion-interior';
        } elseif ($advType > 800 && $advType < 900) {
            return 'publi-poll';
        } elseif ($advType > 900 && $advType < 1000) {
            return 'publi-poll-inner';
        } elseif ($advType > 1000 && $advType < 1100) {
            return 'publi-newsletter';
        }
    }

    /**
     * Increase by one click the number of clicks given an advertisement id
     *
     * @param int $id the id of the advertisement to increase num_count
     *
     * @return void
     **/
    public static function setNumClics($id)
    {
        // If no valid id then return
        if (((int) $id) <= 0) return;

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
            dispatchEventWithParams('content.update-set-num-views', array('content' => $ad));

            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public static function findForPositionIdsAndCategoryPlain($types = [], $category = 0)
    {
        $banners = [];

        // If advertisement types aren't passed return earlier
        if (!is_array($types) || count($types) <= 0) {
            return $banners;
        }

        // Check category
        $category = (empty($category) || ($category=='home')) ? 0 : $category;

        if (!getService('core.security')->hasExtension('ADS_MANAGER')) {
            // Fetch ads from static file
            $advertisements = include APP_PATH.'config/ads/onm_default_ads.php';

            foreach ($advertisements as $ad) {
                if (in_array($ad->type_advertisement, $types) &&
                    (
                        in_array($category, $ad->fk_content_categories) ||
                        in_array(0, $ad->fk_content_categories)
                    )
                ) {
                    $banners []= $ad;
                }
            }
        } else {
            // Get string of types separated by commas
            $types = '(' . implode('|', $types) . '){1}';
            $types = sprintf('"^%s($|,)|,\s*%s\s*,|(^|,)\s*%s$"', $types, $types, $types);

            // Generate sql with or without category
            if ($category !== 0) {
                $config = s::get('ads_settings');
                if (isset($config['no_generics'])
                    && ($config['no_generics'] == '1')
                ) {
                    $generics = '';
                } else {
                    $generics = ' OR fk_content_categories=0';
                }
                $catsSQL = 'AND (advertisements.fk_content_categories LIKE \'%'.$category.'%\' '.$generics.') ';
            } else {
                $catsSQL = 'AND advertisements.fk_content_categories=0 ';
            }

            try {
                $sql = "SELECT pk_advertisement as id FROM advertisements "
                    . "WHERE advertisements.type_advertisement REGEXP $types "
                    . " $catsSQL ORDER BY id";

                $conn = getService('dbal_connection');
                $result = $conn->fetchAll($sql);
            } catch (\Exception $e) {
                return $banners;
            }

            if (count($result) <= 0) {
                return $banners;
            }

            $result = array_map(function ($element) {
                return array('Advertisement', $element['id']);
            }, $result);

            $adManager = getService('advertisement_repository');
            $advertisements = $adManager->findMulti($result);

            foreach ($advertisements as $advertisement) {
                // Dont use this ad if is not in time
                if (!is_object($advertisement)
                    || $advertisement->content_status != 1
                    || $advertisement->in_litter != 0
                ) {
                    continue;
                }

                if (is_string($advertisement->params)) {
                    $advertisement->params = unserialize($advertisement->params);
                    if (!is_array($advertisement->params)) {
                        $advertisement->params = [];
                    }
                }

                // If the ad doesn't belong to the given category or home, skip it
                if (!in_array($category, $advertisement->fk_content_categories)
                    && !in_array(0, $advertisement->fk_content_categories)
                ) {
                    continue;
                }

                $banners []= $advertisement;
            }
        }

        return $banners;
    }

    /**
     * Get advertisement for a given type and category
     *
     * @param array  $types    Types of advertisement
     * @param string $category Category of advertisement
     *
     * @return array $finalBanners of Advertisement objects
     **/
    public static function findForPositionIdsAndCategory($types = array(), $category = 'home')
    {
        $banners = $finalBanners = [];

        // If advertisement types aren't passed return earlier
        if (!is_array($types) || count($types) <= 0) {
            return $banners;
        }

        // Check category
        $category = (empty($category) || ($category=='home')) ? 0 : $category;

        // Remove floating banners
        if (($key = array_search('37', $types)) !== false) {
            unset($types[$key]);
        }

        if (!getService('core.security')->hasExtension('ADS_MANAGER')) {
            // Fetch ads from static file
            $advertisements = include APP_PATH.'config/ads/onm_default_ads.php';

            foreach ($advertisements as $ad) {
                if (in_array($ad->type_advertisement, $types) &&
                    (
                        in_array($category, $ad->fk_content_categories) ||
                        in_array(0, $ad->fk_content_categories)
                    )
                ) {
                    $banners[$ad->type_advertisement][] = $ad;
                }
            }
        } else {
            // Get string of types separated by commas
            $types = implode(',', $types);

            // Generate sql with or without category
            if ($category !== 0) {
                $config = s::get('ads_settings');
                if (isset($config['no_generics'])
                    && ($config['no_generics'] == '1')
                ) {
                    $generics = '';
                } else {
                    $generics = ' OR fk_content_categories=0';
                }
                $catsSQL = 'AND (advertisements.fk_content_categories LIKE \'%'.$category.'%\' '.$generics.') ';
            } else {
                $catsSQL = 'AND advertisements.fk_content_categories=0 ';
            }

            try {
                $sql = "SELECT pk_advertisement as id FROM advertisements "
                  ."WHERE advertisements.type_advertisement IN (".$types.") "
                  .$catsSQL.' ORDER BY id';

                $conn = getService('dbal_connection');
                $result = $conn->fetchAll($sql);
            } catch (\Exception $e) {
                return $banners;
            }

            if (count($result) <= 0) {
                return $banners;
            }

            $result = array_map(function ($element) {
                return array('Advertisement', $element['id']);
            }, $result);

            $adManager = getService('advertisement_repository');
            $advertisements = $adManager->findMulti($result);

            foreach ($advertisements as $advertisement) {
                // Dont use this ad if is not in time
                if (!is_object($advertisement)
                    || (!$advertisement->isInTime()
                        && $advertisement->type_medida == 'DATE')
                    || $advertisement->content_status != 1
                    || $advertisement->in_litter != 0
                ) {
                    continue;
                }

                // TODO: Introduced in May 20th, 2014. This code avoids to restart memcached for
                // already stored ad objects. This should be removed after caches will be regenerated
                // if (!is_array($advertisement->fk_content_categories)) {
                //     $advertisement->fk_content_categories = explode(',', $advertisement->fk_content_categories);
                // }

                if (is_string($advertisement->params)) {
                    $advertisement->params = unserialize($advertisement->params);
                    if (!is_array($advertisement->params)) {
                        $advertisement->params = array();
                    }
                }

                // If the ad doesn't belong to the given category or home, skip it
                if (!in_array($category, $advertisement->fk_content_categories)
                    && !in_array(0, $advertisement->fk_content_categories)
                ) {
                    continue;
                }

                $banners [$advertisement->type_advertisement][] = $advertisement;
            }
        }

        if (!empty($banners)) {
            $homeBanners = array();
            $categoryBanners = array();
            // Perform operations for each advertisement type
            foreach ($banners as $adType => $advs) {
                // Initialize banners arrays
                $homeBanners[$adType] = array();
                $categoryBanners[$adType] = array();
                $finalBanners[$adType] = array();
                if (count($advs) > 1) {
                    foreach ($advs as $ad) {
                        if (in_array(0, $ad->fk_content_categories)) {
                            array_push($homeBanners[$adType], $ad); // Home banners
                            if (in_array($category, $ad->fk_content_categories)) {
                                array_push($categoryBanners[$adType], $ad); // Category+Home banners
                            }
                        } else {
                            array_push($categoryBanners[$adType], $ad); // Category banners
                        }
                    }
                    // If this ad-type don't has any banner, get all from home
                    if (empty($categoryBanners[$adType])) {
                        $key = array_rand($homeBanners[$adType]);
                        $finalBanners[$adType] = $homeBanners[$adType][$key];
                    } else {
                        $key = array_rand($categoryBanners[$adType]);
                        $finalBanners[$adType] = $categoryBanners[$adType][$key];
                    }
                } else {
                    // If this ad-type only has one ad, add it to array
                    $finalBanners[$adType] = array_pop($advs);
                }
            }
        }

        return $finalBanners;
    }

    /**
     * Renders the advertisment given a set of parameters
     *
     * @param array $params list of parameters for rendering the advertisement
     * @param Template $tpl the Template class instance
     *
     * @return string the final html for the ad
     **/
    public function render($params)
    {
        // Don't render any non default ads if module is not activated
        if (!getService('core.security')->hasExtension('ADS_MANAGER')
            && (!isset($this->default_ad) || $this->default_ad != 1)
        ) {
            return '';
        }

        $html  = '<div class="oat"%s data-type="%s"%s%s></div>';
        $id    = '';
        $type  = $this->type_advertisement;
        $style = ' style="display:table;"';
        $width = '';

        // Style for advertisements via renderbanner
        if (array_key_exists('width', $params)) {
            $width = sprintf(
                ' data-width="%d"',
                (int) $params['width']
            );
        }

        // Style for floating advertisements in frontpage manager
        if ($this->type_advertisement == 37) {
            $id .= ' data-id="' . $this->pk_content . '" ';
        }

        return sprintf($html, $id, $type, $width, $style);
    }
}
