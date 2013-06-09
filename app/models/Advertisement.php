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

    // FIXME: modificado para versión demo
    /**
     * List of available ads positions
     *
     * @var array
     **/
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
        37 => "Floating banner",

        9  => "Top Mega-LeaderBoard",
        91 => "Left Skyscraper",
        92 => "Right Skyscraper",

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
        191 => "[I] Left Skyscraper",
        192 => "[I] Right Skyscraper",
        193 => "[I] InBody Skyscraper",

        /* Intersticial banner video front */
        250 => "[V] Banner Interticial",

        /* Videos Front banners > 200 */
        201 => "[V] Big banner superior",
        202 => "[V] Banner superior derecho",
        203 => "[V] Banner Video Button",

        209 => "[V] Big Banner Inferior",
        210 => "[V] Banner Inferior Derecho",
        291 => "[V] Left Skyscraper",
        292 => "[V] Right Skyscraper",
        /* Intersticial banner video inner */
        350 => "[VI] Banner Interticial",

        /* Video Interior banners > 300 */
        301 => "[VI] Big banner superior",
        302 => "[VI] Banner superior Derecho",

        303 => "[VI] Banner Video Button",

        309 => "[VI] Big Banner Inferior",
        310 => "[VI] Banner Inferior Derecho",
        391 => "[VI] Left Skyscraper",
        392 => "[VI] Right Skyscraper",

        /* Intersticial banner album front */
        450 => "[A] Banner Interticial",

        /* Albums Front banners > 400 */
        401 => "[A] Big banner superior",
        402 => "[A] Banner superior derecho",

        403 => "[A] Banner1 Column Right",
        405 => "[A] Banner1 2Column Right",

        409 => "[A] Big Banner Inferior",
        410 => "[A] Banner Inferior Derecho",
        491 => "[A] Left Skyscraper",
        492 => "[A] Right Skyscraper",

        /* Intersticial banner album inner */
        550 => "[AI] Banner Interticial",

        /* Album Interior banners > 500 */
        501 => "[AI] Big banner superior",
        502 => "[AI] Banner superior Derecho",

        503 => "[AI] Banner Columna Derecha",

        509 => "[AI] Big Banner Inferior",
        510 => "[AI] Banner Inferior Derecho",
        591 => "[AI] Left Skyscraper",
        592 => "[AI] Right Skyscraper",

       /* Intersticial banner opinion front */
        650 => "[O] Banner Interticial",

        /* Opinions Front banners > 600 */
        601 => "[O] Big banner superior",
        602 => "[O] Banner superior derecho",
        603 => "[O] Banner1 Column Right",
        605 => "[O] Banner1 2Column Right",
        609 => "[O] Big Banner Inferior",
        610 => "[O] Banner Inferior Derecho",
        691 => "[O] Left Skyscraper",
        692 => "[O] Right Skyscraper",

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
        791 => "[OI] Left Skyscraper",
        792 => "[OI] Right Skyscraper",
        793 => "[OI] InBody Skyscraper",

          /* Intersticial banner polls front */
        850 => "[E] Banner Interticial",

         /* Polls Front banners > 800 */
        801 => "[E] Big banner superior",
        802 => "[E] Banner superior derecho",

        803 => "[E] Banner1 Column Right",
        805 => "[E] Banner1 2Column Right",

        809 => "[E] Big Banner Inferior",
        810 => "[E] Banner Inferior Derecho",
        891 => "[E] Left Skyscraper",
        892 => "[E] Right Skyscraper",

        /* Intersticial banner poll inner */
        950 => "[EI] Banner Interticial",

        /* Polls  Interior banners > 900 */
        901 => "[EI] Big banner superior",
        902 => "[EI] Banner superior Derecho",

        903 => "[EI] Banner Columna Derecha",

        909 => "[EI] Big Banner Inferior",
        910 => "[EI] Banner Inferior Derecho",
        991 => "[EI] Left Skyscraper",
        992 => "[EI] Right Skyscraper",

          /* Newsletter  > 1000 */
        1001 => "[B] Big banner superior",

        1009 => "[B] Big Banner Inferior",

    );

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
    public $fk_content_categories = null;

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
     * The <script> content of this advertisement
     *
     * @varstring
     **/
    public $script      = null;

    /**
     * Whether this advertisement has a <script> content
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
     * Initializes the Advertisement class
     *
     * @param int $id ID of the Advertisement
     *
     * @return Advertisement the instance of the advertisement class
     **/
    public function __construct($id = null)
    {
        // Fetch information from Content class
        parent::__construct($id);

        if (is_numeric($id)) {
            $this->read($id);
        }

        // Set the content_type
        $this->content_type = get_class();

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

        $values = array(
            $this->id,
            $data['type_advertisement'],
            $data['category'],
            $data['img'],
            $data['url'],
            $data['type_medida'],
            $data['num_clic'],
            0, // num_clic_count
            $data['num_view'],
            $data['with_script'],
            $data['script'],
            $data['overlap'],
            $data['timeout']
        );

        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if ($rs === false) {
            \Application::logDatabaseError();

            return null;
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
     * @param int $id the ID of the Advertisement
     *
     * @return Advertisement the instance for the Ad
     **/
    public function read($id)
    {
        parent::read($id); // Read content of Content

        $sql = 'SELECT * FROM advertisements WHERE pk_advertisement = ?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));

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
     * Updates the data of the ad from one array.
     *
     * @param array $data
     *
     * @return Advertisement Return the instance to chaining method
     **/
    public function update($data)
    {
        $data['available'] = $data['content_status'];
        parent::update($data);

        if (!empty($data['script'])) {
            $data['script'] = base64_encode($data['script']);
        }

        $data['overlap']     = (isset($data['overlap']))? $data['overlap']: 0;
        $data['timeout']     = (isset($data['timeout']))? $data['timeout']: 0;
        $data['with_script'] = (isset($data['with_script']))? $data['with_script']: 0;
        $data['type_medida'] = (isset($data['type_medida']))? $data['type_medida']: 'NULL';

        $sql = "UPDATE advertisements
                SET `type_advertisement`=?, `fk_content_categories`=?,
                    `path`=?, `url`=?, `type_medida`=?, `num_clic`=?,
                    `num_view`=?,`with_script`=?,
                    `script`=?, `overlap`=?, `timeout`=?
                WHERE pk_advertisement=".($data['id']);

        $values = array(
            $data['type_advertisement'],
            $data['categories'],
            $data['img'],
            $data['url'],
            $data['type_medida'],
            $data['num_clic'],
            $data['num_view'],
            $data['with_script'],
            $data['script'],
            $data['overlap'],
            $data['timeout']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return null;
        }

        // Necesarios para evento
        $this->pk_advertisement      = $this->id;
        $this->content_status        = $data['content_status'];
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

        $sql = 'DELETE FROM advertisements WHERE pk_advertisement = ?';

        if ($GLOBALS['application']->conn->Execute($sql, array($id))===false) {
            \Application::logDatabaseError();

            return;
        }
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
        $sql =  "UPDATE advertisements "
                ." SET `num_clic_count`=`num_clic_count`+1 "
                ." WHERE `pk_advertisement`=?";
        $values = array($id);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return false;
        }
    }

    /**
     * Get advertisement for a given type and category
     *
     * @param array  $types    Types of advertisement
     * @param string $category Category of advertisement
     *
     * @return array $finalBanners of Advertisement objects
     **/
    public static function findForPositionIdsAndCategory($types = array(), $category = 'home', $wsUrl = null)
    {
        global $sc;

        $banners = array();
        $finalBanners = array();

        if (!is_array($types) || count($types) <= 0 && !ADVERTISEMENT_ENABLE) {
            return $banners;
        }

        // Check category
        $category = (empty($category) || ($category=='home')) ? 0 : $category;

        // Get string of types separeted by coma
        $types = implode(',', $types);

        // Generate sql with or without category
        $cm = new \ContentManager();
        if ($category !== 0) {
            $config = s::get(array('ads_settings'));
            if (isset($config['ads_settings']['no_generics'])
                && ($config['ads_settings']['no_generics'] == '1')
            ) {
                $generics = '';
            } else {
                $generics = ' OR fk_content_categories=0';
            }
            $catsSQL = 'AND (advertisements.fk_content_categories LIKE \'%'.$category.'%\' '.$generics.')';
        } else {
            $catsSQL = 'AND advertisements.fk_content_categories=0';
        }

        $rsBanner = $cm->find(
            'Advertisement',
            ' contents.available=1 AND advertisements.type_advertisement IN ('.$types.')'.
            $catsSQL,
            'ORDER BY contents.created'
        );

        // If this banner is not in time don't add it to the final results
        $rsBanner = $cm->getInTime($rsBanner);

        // Extract pk_photos to perform one query
        foreach ($rsBanner as &$banner) {
            if (!empty($banner->path)) {
                //Get photos
                $cm = new ContentManager();
                if (!$wsUrl) {
                    $banner->image = $sc->get('entity_repository')->find('Photo', $banner->path);
                } else {
                    // Load images from the remote server

                    // $objsArray = array();
                    // foreach ($pk_photos as $photo) {
                    //     $objsArray[] = json_decode(file_get_contents($wsUrl.'/ws/images/id/'.(int) $photo));
                    // }
                    // foreach ($objsArray as $item) {
                    //     $content = new Advertisement();
                    //     $content->load($item);
                    //     $objs[] = $content;
                    // }
                }
            }
        }


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
                    if (in_array(0, $ad->fk_content_categories)) {
                        array_push($homeBanners[$adType], $ad); // Home banners
                        if (in_array($category, $ad->fk_content_categories)) {
                            array_push($banners[$adType], $ad); // Category+Home banners
                        }
                    } else {
                        array_push($banners[$adType], $ad); // Category banners
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

        return $finalBanners;
    }

    /**
     * Return banners for the interstitial position.
     *
     * @param array  $type     the list of positions to get banners from.
     * @param string $category the category to get banners from.
     *
     * @return array
     **/
    public function getIntersticial($type, $category = 'home')
    {
        $interstitial = null;

        if (((($type + 50) % 100) == 0)
            && ADVERTISEMENT_ENABLE
        ) {
            $category = (empty($category) || ($category=='home'))? 0: $category;

            $cm = new ContentManager();
            $rsBanner = $cm->find(
                'Advertisement',
                ' `contents`.`available`=1'
                .' AND `advertisements`.`type_advertisement`=' . $type
                .' AND `advertisements`.`fk_content_categories` LIKE "%'.$category.'%"',
                ' ORDER BY `contents`.created'
            );

            $rsBanner = $cm->getInTime($rsBanner);

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
     * Renders the advertisment given a set of parameters
     *
     * @param array $params list of parameters for rendering the advertisement
     * @param Template $tpl the Template class instance
     *
     * @return string the final html for the ad
     **/
    public function render($params, $tpl = null)
    {
        $output = '';

        $params['beforeHTML'] = "<div class=\"ad_in_column ad_horizontal_marker clearfix\"><div>";
        $params['afterHTML'] = "</div></div>";

        if (defined('ADVERTISEMENT_ENABLE')  && !ADVERTISEMENT_ENABLE) {
            return $output;
        }

        $banner = $this;

        $width  = $this->params['width'];
        $height = $this->params['height'];

        if ($this->with_script == 1) {
            $photo = new \Photo($this->img);
        }

        $photo = new \Photo($this->img);

        // Overlap flash?
        $overlap  = (isset($this->params['overlap']))? $this->params['overlap']: false;
        $isBastardIE = preg_match('/MSIE /', $_SERVER['HTTP_USER_AGENT']);

        if (isset($params['beforeHTML'])) {
            $output .= $params['beforeHTML'];
        }

        // Initial container
        $output .= '<div class="'.$cssclass.'">';

        if ($this->with_script == 1) {
            // Original method
            // $output .= $banner->script;
            // Parallelized method using iframes
            if (preg_match('/<iframe/', $this->script)) {
                $output .= $this->script;
            } else {
                $url = SITE_URL.'ads/get/'
                    . date('YmdHis', strtotime($this->created))
                    .sprintf('%06d', $this->pk_content)  . '.html' ;
                $output .=
                    '<iframe src="'.$url.'" '
                    .'style="width:'.$this->params['width'].'px; '
                    .'height:'.$this->params['height'].'px"></iframe>';
            }

        } elseif (!empty($banner->pk_advertisement)) {

            // TODO: controlar los banners swf especiales con div por encima
            if (strtolower($photo->type_img)=='swf') {

                if (!$overlap && !$banner->overlap) {
                    // Flash object
                    // FIXME: build flash object with all tags and params

                    $output .= '<a target="_blank" href="'
                                .SITE_URL.'ads/'. date('YmdHis', strtotime($banner->created))
                                .sprintf('%06d', $banner->pk_advertisement)
                                .'.html" rel="nofollow" style="display:block;cursor:pointer">';
                    $output .= '<object width="'.$width.'" height="'.$height.'" >
                            <param name="wmode" value="window" />
                            <param name="movie" value="'. MEDIA_IMG_PATH_WEB. $photo->path_file. $photo->name. '" />
                            <param name="width" value="'.$width.'" />
                            <param name="height" value="'.$height.'" />
                            <embed src="'. MEDIA_IMG_PATH_WEB. $photo->path_file. $photo->name. '"
                                width="'.$width.'" height="'.$height.'" SCALE="exactfit" alt="Publicidad '
                                .$banner->title
                                .'" wmode="window"></embed>
                        </object>';
                } else {
                    if (!$isBastardIE) {
                        $output .= '<div style="position: relative; width: 100%; height: '.$height.'px;">
                            <div style="left:0px;top:0px;cursor:pointer;background-color:transparent;'
                                .'position:absolute;z-index:100;width:'.
                                $width.'px;height:'.$height.'px;"
                                onclick="javascript:window.open(\''.SITE_URL.'ads/'
                                .date('YmdHis', strtotime($banner->created)).sprintf('%06d', $banner->pk_advertisement)
                                .'.html\', \'_blank\');return false;"></div>';
                    } else {
                        $output .= '<div style="position: relative; width: '.$width.'px; height: '.$height.'px;">
                            <div style="left:0px;top:0px;cursor:pointer;background-color:#FFF;'
                                .'filter:alpha(opacity=0);opacity:0;position:absolute;z-index:100;width:'.
                                $width.'px;height:'.$height.'px;"
                                onclick="javascript:window.open(\''.SITE_URL.'ads/'
                                .date('YmdHis', strtotime($banner->created))
                                .sprintf('%06d', $banner->pk_advertisement)
                                .'.html\', \'_blank\');return false;"></div>';
                    }

                    $output .= '<div style="position: absolute; z-index: 0; width: '.$width.'px; left: 0px; height: '.$height.'px;">
                            <object width="'.$width.'" height="'.$height.'">
                                <param name="movie" value="'. MEDIA_IMG_PATH_WEB. $photo->path_file. $photo->name. '" />
                                <param name="wmode" value="opaque" />
                                <param name="width" value="'.$width.'" />
                                <param name="height" value="'.$height.'" />
                                <embed src="'. MEDIA_IMG_PATH_WEB. $photo->path_file. $photo->name. '" wmode="opaque"
                                    width="100%" height="100%" alt="Publicidad '. $banner->title. '"></embed>
                            </object>
                        </div>
                      </div>';

                    $output .= '</div>';

                    if (isset($params['afterHTML'])) {
                        $output .= $params['afterHTML'];
                    }


                    return render_output($output, $banner);
                }
            } else {
                // Image
                $output .= '<a target="_blank" href="'.SITE_URL.'ads/'
                        .date('YmdHis', strtotime($banner->created))
                        .sprintf('%06d', $banner->pk_advertisement) .'.html" rel="nofollow">';
                $output .= '<img src="'. MEDIA_IMG_PATH_WEB. $photo->path_file. $photo->name.'"
                        alt="Publicidad '.$banner->title.'" width="'.$width.'" height="'.$height.'" />';
            }

            $output .= '</a>';
        } else {
            // Empty banner, don't return anything
            $output = '';
            return render_output($output, $banner);
        }

        $output .= '</div>';

        // Post content of banner
        if (isset($params['afterHTML'])) {
            $output .= $params['afterHTML'];
        }

        return $output;
    }

    /**
     * Inject banners into template
     *
     * @param array  $banners Array of Advertisement objects
     * @param Smarty $tpl     Template
     * @param string $wsUrl   The external web service url
     **/
    public function renderMultiple($banners, $tpl, $wsUrl = false)
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
                $objsArray[] = json_decode(file_get_contents($wsUrl.'/ws/images/id/'.(int) $photo));
            }
            foreach ($objsArray as $item) {
                $content = new Advertisement();
                $content->load($item);
                $objs[] = $content;
            }
        }

        // Array of photos objects,  key is pk_content array('pk_content' => object)
        $photos = array();
        if (!empty($objs)) {
            foreach ($objs as $obj) {
                $photos[ $obj->pk_content ] = $obj;
            }
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
                        'photo'.$banner->type_advertisement,
                        $photos[$adv]
                    );
                }
            }
        }

        // Update numviews
        self::setNumViews($banners_selected);
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
