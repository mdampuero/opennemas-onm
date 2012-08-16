<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
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

    const ADVERTISEMENT_CATEGORY = 2;

    // FIXME: modificado para versión demo
    public static $map = array(
        /* Intersticial banners frontpages */
        50 => "Banner Interticial en portada",

        /* Frontpages banners < 100 */
        1 => "Top Left LeaderBoard",
        2 => "Top Right LeaderBoard",

        3 => "Center Left LeaderBoard",
        4 => "Center Right LeaderBoard",

        5 => "Bottom Left LeaderBoard",
        6 => "Bottom Right LeaderBoard",

        11 => "Button Colunm 1 Position 1",
        12 => "Button Colunm 1 Position 2",
        13 => "Button Colunm 1 Position 3",
        14 => "Button Colunm 1 Position 4",
        15 => "Button Colunm 1 Position 5",
        16 => "Button Colunm 1 Position 6",

        21 => "Button Colunm 2 Position 1",
        22 => "Button Colunm 2 Position 2",
        24 => "Button Colunm 2 Position 4",
        25 => "Button Colunm 2 Position 5",

        31 => "Button Colunm 3 Position 1",
        32 => "Button Colunm 3 Position 2",
        33 => "Button Colunm 3 Position 3",
        34 => "Button Colunm 3 Position 4",
        35 => "Button Colunm 3 Position 5",
        36 => "Button Colunm 3 Position 6",

        /* Intersticial banner noticia interior */
        150 => "[I] Banner Interticial noticia interior",

        /* Noticia Interior banners > 100 */
        101 => "[I] Big banner superior",
        102 => "[I] Banner superior Derecho",

        103 => "[I] Banner Columna Derecha 1",
        104 => "[I] Robapágina",
        105 => "[I] Banner Columna Derecha 2",
        106 => "[I] Banner Columna Derecha 3",
        107 => "[I] Banner Columna Derecha 4",
        108 => "[I] Banner Columna Derecha 5",

        109 => "[I] Big Banner Inferior",
        110 => "[I] Banner Inferior Derecho",

        /* Intersticial banner video front */
        250 => "[V] Banner Interticial",

        /* Videos Front banners > 200 */
        201 => "[V] Big banner superior",
        202 => "[V] Banner superior derecho",
        203 => "[V] Banner Video Button",

        209 => "[V] Big Banner Inferior",
        210 => "[V] Banner Inferior Derecho",

        /* Intersticial banner video inner */
        350 => "[VI] Banner Interticial",

        /* Video Interior banners > 300 */
        301 => "[VI] Big banner superior",
        302 => "[VI] Banner superior Derecho",

        303 => "[VI] Banner Video Button",

        309 => "[VI] Big Banner Inferior",
        310 => "[VI] Banner Inferior Derecho",

        /* Intersticial banner album front */
        450 => "[A] Banner Interticial",

        /* Albums Front banners > 400 */
        401 => "[A] Big banner superior",
        402 => "[A] Banner superior derecho",

        403 => "[A] Banner1 Column Right",
        405 => "[A] Banner1 2Column Right",

        409 => "[A] Big Banner Inferior",
        410 => "[A] Banner Inferior Derecho",

        /* Intersticial banner album inner */
        550 => "[AI] Banner Interticial",

        /* Album Interior banners > 500 */
        501 => "[AI] Big banner superior",
        502 => "[AI] Banner superior Derecho",

        503 => "[AI] Banner Columna Derecha",

        509 => "[AI] Big Banner Inferior",
        510 => "[AI] Banner Inferior Derecho",

       /* Intersticial banner opinion front */
        650 => "[O] Banner Interticial",

        /* Opinions Front banners > 600 */
        601 => "[O] Big banner superior",
        602 => "[O] Banner superior derecho",
        603 => "[O] Banner1 Column Right",
        605 => "[O] Banner1 2Column Right",
        609 => "[O] Big Banner Inferior",
        610 => "[O] Banner Inferior Derecho",

        /* Intersticial banner opinion inner */
        750 => "[OI] Banner Intersticial - Inner (800X600)",

        /* Opinion Interior banners > 700 */
        701 => "[OI] Big Banner Top(I) (728X90)",
        702 => "[OI] Banner Top Right(I) (234X90)",
        703 => "[OI] Banner1 Column Right (I) (300X*)",
        704 => "[OI] Robapágina (650X*)",
        705 => "[OI] Banner2 Column Right(I) (300X*)",
        706 => "[OI] Banner3 Column Right(I) (300X*)",
        707 => "[OI] Banner4 Column Right(I) (300X*)",
        708 => "[OI] Banner5 Column Right(I) (300X*)",
        709 => "[OI] Big Banner Bottom(I) (728X90)",
        710 => "[OI] Banner Bottom Right(I) (234X90)",

          /* Intersticial banner polls front */
        850 => "[E] Banner Interticial",

         /* Polls Front banners > 800 */
        801 => "[E] Big banner superior",
        802 => "[E] Banner superior derecho",

        803 => "[E] Banner1 Column Right",
        805 => "[E] Banner1 2Column Right",

        809 => "[E] Big Banner Inferior",
        810 => "[E] Banner Inferior Derecho",

        /* Intersticial banner poll inner */
        950 => "[EI] Banner Interticial",

        /* Polls  Interior banners > 900 */
        901 => "[EI] Big banner superior",
        902 => "[EI] Banner superior Derecho",

        903 => "[EI] Banner Columna Derecha",

        909 => "[EI] Big Banner Inferior",
        910 => "[EI] Banner Inferior Derecho",

          /* Newsletter  > 1000 */
        1001 => "[B] Big banner superior",

        1009 => "[B] Big Banner Inferior",

    );

    /**
     * @access public
     * @var long
     **/
    public $pk_advertisement = null;

    /**
     * @access public
     * @var int
     **/
    public $type_advertisement = null;

    /**
     * @access public
     * @var int
     **/
    public $fk_content_categories = null;

    public $img  = null;
    public $path = null;

    public $url            = null;
    public $type_medida    = null;
    public $num_clic       = null;
    public $num_clic_count = null;
    public $num_view       = null;
    public $overlap        = null;

    public $script      = null;
    public $with_script = null;
    public $timeout     = null;

    /**
     * @var MethodCacheManager Instance of MethodCacheManager
     **/
    public $cache = null;

    /**
     * @var Advertisement instance, singleton pattern
     **/
    private static $singleton = null;

    /**
     * @var registry of banners
     **/
    protected $_registry = array();

    /**
     * Initializes the Advertisement class
     *
     * @param int $id, ID of the Advertisement
     *
     * return Advertisement the instance of the advertisement class
     **/
    public function __construct($id=null)
    {
        // Fetch information from Content class
        parent::__construct($id);

        if (is_numeric($id)) {
            $this->read($id);
        }

        // Store this object into the cache manager for better performance
        if ( is_null($this->cache) ) {
            $this->cache = new MethodCacheManager($this, array('ttl' => (20)));
        } else {
            $this->cache->setCacheLife(20); // 20 seconds
        }

        // Set the content_type
        // FIXME: this should be into the __construct method of Content class.
        $this->content_type = get_class();

        return $this;
    }

    /**
     * Method to fetch or create the object by the singleton pattern
     *
     * @see Advertisement::__construct()
     **/
    public static function getInstance()
    {
        // Create a unique instance if not available
        if ( is_null(self::$singleton) ) {
            self::$singleton = new Advertisement();
        }

        return self::$singleton;
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
// var_dump($data);die();

        parent::create($data);

        if (!empty($data['script'])) {
            $data['script'] = base64_encode($data['script']);
        }

        if (!isset($data['with_script'])) {
            $data['with_script'] = 0;
        }

        $data['overlap'] = (isset($data['overlap']))? $data['overlap']: 0;
        $data['timeout'] = (isset($data['timeout']))? $data['timeout']: -1;
        $data['type_medida'] =
            (isset($data['type_medida']))? $data['type_medida']: 'NULL';

        $sql = "INSERT INTO advertisements
                    (`pk_advertisement`, `type_advertisement`,
                     `fk_content_categories`, `path`, `url`, `type_medida`,
                     `num_clic`, `num_clic_count`, `num_view`, `with_script`,
                     `script`, `overlap`, `timeout`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // $this->id was setted in parent::create($data)
        $values = array(
            $this->id, $data['type_advertisement'], $data['category'],
            $data['img'], $data['url'], $data['type_medida'], $data['num_clic'],
            0, $data['num_view'], $data['with_script'],
            $data['script'], $data['overlap'], $data['timeout']
        );

        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if ($rs === false) {
            \Application::logDatabaseError();

            return null;
        }

        $rel = new RelatedContent();
        if (isset($data['selectos'])) {
            $pos = 1;
            foreach ($data['selectos'] as $relac) {
                $rel->setRelationPosition($this->id, $pos, $relac);
                $pos++;
            }
        }

        // Needed for onAfterCreateAdvertisement callback
        $this->pk_advertisement      = $this->id;
        $this->available             = $data['available'];
        $this->type_advertisement    = $data['type_advertisement'];
        $this->fk_content_categories = $data['category'];

        // Fire event
        $GLOBALS['application']->dispatch('onAfterCreateAdvertisement', $this);

        return $this;
    }

    /**
     * Get an instance of a particular ad from its ID
     *
     * @param int $id, the ID of the Advertisement
     *
     * @return Advertisement the instance for the Ad
     **/
    public function read($id)
    {
        parent::read($id); // Read content of Content

        $sql = 'SELECT * FROM advertisements WHERE pk_advertisement = '.($id);
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            \Application::logDatabaseError();

            return;
        }

        $this->load($rs->fields);

        // Return instance to method chaining
        return $this;
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
        $this->category = self::ADVERTISEMENT_CATEGORY;
        parent::load($properties);

        $this->script = base64_decode($this->script);
        // FIXME: revisar que non se utilice ->img
        $this->img = $this->path;
    }

    /**
     * Update advertisement
     *
     * Update the data of the ad from one array.
     *
     * @param array $data
     *
     * @return Advertisement Return the instance to chaining method
     **/
    public function update($data)
    {
        parent::update($data);

        if (!empty($data['script'])) {
            //$data['script'] = StringUtils::fixScriptDeclaration($data['script']);
            $data['script'] = base64_encode($data['script']);
        }

        $data['overlap'] = (isset($data['overlap']))? $data['overlap']: 0;
        $data['timeout'] = (isset($data['timeout']))? $data['timeout']: 0;
        $data['with_script'] =
            (isset($data['with_script']))? $data['with_script']: 0;
        $data['type_medida'] =
            (isset($data['type_medida']))? $data['type_medida']: 'NULL';

        $sql = "UPDATE advertisements
                SET `type_advertisement`=?, `fk_content_categories`=?,
                    `path`=?, `url`=?, `type_medida`=?, `num_clic`=?,
                    `num_clic_count`=?, `num_view`=?,`with_script`=?,
                    `script`=?, `overlap`=?, `timeout`=?
                WHERE pk_advertisement=".($data['id']);

        $values = array(
            $data['type_advertisement'], $data['categories'],
            $data['img'], $data['url'], $data['type_medida'],
            $data['num_clic'], $data['num_clic_count'],
            $data['num_view'], $data['with_script'], $data['script'],
            $data['overlap'], $data['timeout']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return null;
        }

        $rel = new RelatedContent();
        $rel->delete($data['id']);
        if (isset($data['selectos'])) {
            $pos=1;
            foreach ($data['selectos'] as $relac) {
                $rel->setRelationPosition($data['id'], $pos, $relac);
                $pos++;
            }
        }

        // Necesarios para evento
        $this->pk_advertisement      = $this->id;
        $this->available             = $data['available'];
        $this->type_advertisement    = $data['type_advertisement'];
        $this->fk_content_categories = $data['category'];

        // Fire event
        $GLOBALS['application']->dispatch('onAfterUpdateAdvertisement', $this);

        return $this;
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
        parent::remove($id);

        $sql = 'DELETE FROM advertisements WHERE pk_advertisement ='.($id);

        if ($GLOBALS['application']->conn->Execute($sql)===false) {
            \Application::logDatabaseError();

            return;
        }
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
        // Try to minimize the database overload if this object was preloaded
        // or doesn't fit the rules
        if (isset($this) && isset($this->url) && ($this->id == $id)) {
            return $this->url;
        }

        // Fetch data for the ad from the database
        $sql = 'SELECT url FROM `advertisements` '
                .'WHERE `advertisements`.`pk_advertisement`=?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));

        if (!$rs) {
            \Application::logDatabaseError();

            return null;
        }

        return $rs->fields['url'];
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
     * @param int $id the id of the advertisement ot increase num_count
     *
     * @return void
     **/
    public static function setNumClics($id)
    {
        $sql = "UPDATE advertisements SET `num_clic_count`=`num_clic_count`+1 "
                ." WHERE `pk_advertisement`=?";
        $values = array($id);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return;
        }

        $ad = new Advertisement($id);

        //No publicado
        if (
            ($ad->type_medida=='CLIC' )
            && ($ad->num_clic <= $ad->num_clic_count)
        ) {
            $status = 0;
            parent::set_status($status, 'NULL');
        }
    }

    /**
     * Set num views
     *
     * @param int $id The id of the advertisement to increase num_views
     *
     * @return void
     **/
    public static function setNumViews($id = null)
    {
        // if $id was not given return null and do nothing
        if (is_null($id)) {
            return null;
        }

        parent::setNumViews($id);

        // if this ad has views limit and it has reached unpublish it
        if (is_array($id)) {
            foreach ($id as $banner) {
                // if this ad has views limit and it has reached unpublish it
                if (isset($banner)
                    && is_object($banner)
                    && property_exists($banner, 'type_medida')
                ) {
                    $banner->unpublishIfMaxViewsReached();
                }
            }
        } else {
            $ad = new Advertisement($id);
            $ad->unpublishIfMaxViewsReached();
        }
    }

    /**
     * Marks an ad as unpublished if has reached its max allowed views
     *
     * @return void
     **/
    public function unpublishIfMaxViewsReached()
    {
        if (($this->type_medida == 'VIEW')
            && ($this->num_view <= $this->views)
        ) {
            parent::set_status(0, 'NULL');
        }
    }

    /**
     * Get advertisement for a given type and category
     *
     * @param array $types Types of advertisement
     * @param string $category Category of advertisement
     *
     * @return array $finalBanners of Advertisement objects
     **/
    public function getAdvertisements($types=array(), $category='home')
    {
        $banners = array();
        $finalBanners = array();

        // See if advertisements are enabled
        // $types must be an array and not empty
        if (is_array($types) && count($types)>0 && ADVERTISEMENT_ENABLE) {
            // Check category
            $category = (empty($category) || ($category=='home'))? 0: $category;

            // Get string of types separeted by coma
            $types = implode(',', $types);

            // Generate sql with or without category
            $cm = new ContentManager();
            if ($category!=0) {
                $rsBanner = $cm->find(
                    'Advertisement',
                    ' type_advertisement IN ('.$types.') AND available=1 AND
                    (fk_content_categories LIKE \'%'.$category.'%\'
                    OR fk_content_categories=0)',
                    'ORDER BY type_advertisement, created'
                );
            } else {
                $rsBanner = $cm->find(
                    'Advertisement',
                    ' type_advertisement IN ('.$types.') AND available=1 AND
                    fk_content_categories=0',
                    'ORDER BY type_advertisement, created'
                );
            }

            // If this banner is not in time don't add it to the final results
            $rsBanner = $cm->getInTime($rsBanner);

            // $advertisements is an array of all banners, grouped by ad type
            $advertisements = array();
            foreach ($rsBanner as $adv) {
                // Get array of types for this advertisement
                $adv->fk_content_categories = explode(',', $adv->fk_content_categories);

                // If the ad don't belongs to category and home, skip it
                if (!in_array($category, $adv->fk_content_categories)
                    && $adv->fk_content_categories != array(0)
                ) {
                    continue;
                }

                // Initialize array of advertisements with type as array key
                if (!isset($advertisements[$adv->type_advertisement])) {
                    $advertisements[$adv->type_advertisement] = array();
                }

                // Check if this advertisement belongs to this category
                $hasCategoryAdvertisement = in_array($category, $adv->fk_content_categories);

                // Check if this advertisement belongs to home
                $hasHomeAdvertisement = in_array(0, $adv->fk_content_categories);

                // If ad belongs to (category) or (category + home)
                if ($hasCategoryAdvertisement || ($hasHomeAdvertisement && $hasCategoryAdvertisement)) {
                    array_push($advertisements[$adv->type_advertisement], $adv);
                } else {
                    // If ad belongs to home but not to category
                    if ($hasHomeAdvertisement) {
                        array_push($advertisements[$adv->type_advertisement], $adv);
                    }
                }
            }

            // Perform operations for each advertisement type
            foreach ($advertisements as $adType => $advs) {
                // Initialize banners arrays
                $banners[$adType] = array();
                $homeBanners[$adType] = array();
                if (count($advs) > 1) {
                    foreach ($advs as $ad) {
                        if ($ad->fk_content_categories != array(0)) {
                            array_push($banners[$adType], $ad); // Category banners
                        } else {
                            array_push($homeBanners[$adType], $ad); // Home banners
                        }
                    }
                    // If this ad-type don't has any banner, get all from home
                    if (empty($banners[$adType])) {
                        $banners[$adType] = $homeBanners[$adType];
                    }
                } else {
                    // If this ad-type only has one ad, add it to array
                    $banners[$adType] = $advs;
                }
            }

            // Generate final banners array with random selection by ad-type
            foreach ($banners as $adv) {
                $finalBanners[] = $adv[array_rand($adv)];
            }
        }

        return $finalBanners;
    }

    /**
     * Return banners for the interstitial position.
     *
     * @param array $type the list of positions to get banners from.
     * @param string $category the category to get banners from.
     *
     * @return array
     **/
    public function getIntersticial($type, $category='home')
    {
        $interstitial = null;

        if (in_array($type, array(50, 150)) && ADVERTISEMENT_ENABLE) {
            $category = (empty($category) || ($category=='home'))? 0: $category;

            $cm = new ContentManager();
            $rsBanner = $cm->find(
                'Advertisement',
                ' `type_advertisement`=' . $type
                .' AND `available`=1'
                .' AND `fk_content_categories`='.$category,
                ' ORDER BY type_advertisement, created'
            );

            $numBanner = array_rand($rsBanner);
            if (!is_null($numBanner)) {
                $interstitial = $rsBanner[$numBanner];
            } else {
                $interstitial = null;
            }
        }

        return $interstitial;
    }

    /**
     * Inject banners into template
     *
     * @param array $banners Array of Advertisement objects
     * @param Smarty $tpl Template
     **/
    public function render($banners, $tpl, $wsUrl = false)
    {
        // Extract pk_photos to perform one query
        $pk_photos = array();
        foreach ($banners as $banner) {
            if (!empty($banner->path)) {
                $pk_photos[] = $banner->path;
            }
        }
        $banners_selected =array();

        //Get photos
        $cm = new ContentManager();
        if (!$wsUrl) {
            $objs = $cm->cache->find(
                'Photo',
                "pk_content IN ('" . implode("','", $pk_photos) . "')"
            );
        } else {
            $objsArray = array();
            foreach ($pk_photos as $photo) {
                $objsArray[] = json_decode(file_get_contents($wsUrl.'/ws/images/id/'.(int)$photo));
            }
            foreach ($objsArray as $item) {
                $content = new Advertisement();
                $content->load($item);
                $objs[] = $content;
            }
        }

        // Array of photos objects,
        // key is pk_content array( 'pk_content' => object )
        $photos = array();
        foreach ($objs as $obj) {
            $photos[ $obj->pk_content ] = $obj;
        }

        foreach ($banners as $banner) {
            // Save selected banners to process after
            $banners_selected[] = $banner;

            if (isset($banner->type_advertisement)
                && property_exists($banner, 'type_advertisement')
            ) {
                $tpl->assign('banner'.$banner->type_advertisement, $banner);
            }

			// FIXME: This is a workarround until decide what to do into
            // the Content class
            // This will avoid the the notice messages but doesn't keep
            // the code clean.
            // We should change de content class to return values
            // always initialized.
            if (isset($banner->with_script)) {
                $with_script = $banner->with_script;
            } else {
                $with_script = null;
            }

            if ($with_script) {
                $tpl->assign(
                    'script_b'.$banner->type_advertisement,
                    $banner->script
                );
            } else {

                if (isset($banner->path) && property_exists($banner, 'path')) {
                    // "path" is Photo ID, $banner->img
                    // is similar but deprecated
                    $adv = $banner->path;
                }
                //Evitar undefined index
                if (isset($photos[$adv])) {
                    $tpl->assign(
                        'photo'.$banner->type_advertisement, $photos[$adv]
                    );
                }
            }
        }

        // Update numviews
        Advertisement::setNumViews($banners_selected);
    }

    /**
     * Emulate smarty method,
     * workaround for Advertisement::render
     *
     * @param string $entry
     * @param mixed $value
     **/
    public function assign($entry, $value)
    {
        $this->_registry[$entry] = $value;
    }

    /**
     * Fetch a entry from set of banners,
     * workaround for Advertisement::render
     *
     * @param string $entry
     * @return mixed
     **/
    public function fetch($entry)
    {
        if (isset($this->_registry[$entry])) {
            $value = $this->_registry[$entry];
        } else {
            $value = null;
        }

        return $value;
    }

    /**
     * Fire this event when publish an advertisement and unpublished others
     * banners where type_advertisement is equals
    */
    public function onPublish()
    {
        if (!empty($this->available) && (intval($this->available)>0)) {
            $sql = 'UPDATE `contents` SET `available`=0 '
                 . 'WHERE pk_content IN (
                    SELECT `pk_advertisement` FROM (
                        SELECT `advertisements`.*
                        FROM `advertisements`, `contents`,
                            `contents_categories`
                        WHERE `advertisements`.`type_advertisement`=?
                        AND `advertisements`.`pk_advertisement`<>?
                        AND `contents_categories`.`pk_fk_content_category`=?
                        AND `contents`.`pk_content`='
                            .'`contents_categories`.`pk_fk_content`
                        AND `contents`.`pk_content`='
                            .'`advertisements`.`pk_advertisement`
                    ) AS temp )';
            $values = array(
                $this->type_advertisement,
                $this->type_advertisement,
                $this->type_advertisement,
            );
            $rs = $GLOBALS['application']->conn->Execute($sql, $values);
            if ($rs === false) {
                \Application::logDatabaseError();

                return;
            }
        }
    }
}
