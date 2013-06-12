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
        $banners = array();
        $finalBanners = array();

        if (!is_array($types) || count($types) <= 0 && !ADVERTISEMENT_ENABLE) {
            return $banners;
        }

        // Check category
        $category = (empty($category) || ($category=='home')) ? 0 : $category;

        // Get string of types separated by commas
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
            $catsSQL = 'AND (advertisements.fk_content_categories LIKE \'%'.$category.'%\' '.$generics.') ';
        } else {
            $catsSQL = 'AND advertisements.fk_content_categories=0 ';
        }

        $sql = "SELECT * FROM contents, advertisements "
              ."WHERE contents.pk_content = advertisements.pk_advertisement "
              .'AND contents.available=1 AND advertisements.type_advertisement IN ('.$types.') '
              .$catsSQL
              ."ORDER BY contents.created";

        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            \Application::logDatabaseError();

            return $banners;
        }

        $adsData = $rs->GetArray();
        foreach ($adsData as $data) {
            $advertisement = new \Advertisement();
            $advertisement->load($data);

            // Dont use this ad if is not in time
            if (!$advertisement->isInTime()) {
                continue;
            }

            // Initialize the categories array of this advertisement
            $advertisement->fk_content_categories = explode(',', $advertisement->fk_content_categories);

            // If the ad doesn't belong to the given category or home, skip it
            if (!in_array($category, $advertisement->fk_content_categories)
                && !in_array(0, $advertisement->fk_content_categories)
            ) {
                continue;
            }


            $banners []= $advertisement;
        }

        return $banners;
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

        if (defined('ADVERTISEMENT_ENABLE')  && !ADVERTISEMENT_ENABLE) {
            return $output;
        }

        if (array_key_exists('cssclass', $params)
            && isset($params['cssclass'])
        ) {
            $wrapperClass = $params['cssclass'];
        } else {
            $wrapperClass = 'ad_in_column ad_horizontal_marker clearfix';
        }
        if ($params['interstitial'] != true) {
            $params['beforeHTML'] = "<div class=\"$wrapperClass\">";
            $params['afterHTML']  = "</div>";
        }
        $cssclass             = $params['cssclass'];
        $width                = $this->params['width'];
        $height               = $this->params['height'];
        $overlap              = (isset($this->params['overlap']))? $this->params['overlap']: false;
        $isBastardIE          = preg_match('/MSIE /', $_SERVER['HTTP_USER_AGENT']);

        // Extract width and height properties from CSS
        $width  = $params['width'];
        $height = $params['height'];

        if (!is_null($this->params['width'])
            && !is_null($this->params['height'])
        ) {
            $width = $this->params['width'];
            $height = $this->params['height'];
        }

        if ($this->with_script == 1) {
            if (preg_match('/<iframe/', $this->script)) {
                $content = $this->script;
            } else {
                $url = SITE_URL.'ads/get/'
                    .date('YmdHis', strtotime($this->created))
                    .sprintf('%06d', $this->pk_content)  . '.html' ;
                $content = '<iframe src="'.$url.'" style="width:'.$width.'px; height:'.$height.'px"></iframe>';
            }

        } else {
            global $sc;
            $photo = $sc->get('entity_repository')->find('Photo', $this->img);

            // If the Ad is Flash/Image based try to get the width and height fixed
            if (isset($photo)) {
                if (($photo->width <= $width)
                    && ($photo->height <= $height)
                ) {
                    $width  = $photo->width;
                    $height = $photo->height;
                }
            }

            $url = SITE_URL.'ads/'. date('YmdHis', strtotime($this->created))
                  .sprintf('%06d', $this->pk_advertisement).'.html';
            $mediaUrl = MEDIA_IMG_PATH_WEB. $photo->path_file. $photo->name;

            // TODO: controlar los banners swf especiales con div por encima
            if (strtolower($photo->type_img) == 'swf') {

                $flashObject =
                    '<object width="'.$width.'" height="'.$height.'" >
                        <param name="wmode" value="window" />
                        <param name="movie" value="'.$mediaUrl. '" />
                        <param name="width" value="'.$width.'" />
                        <param name="height" value="'.$height.'" />
                        <embed src="'. $mediaUrl. '" width="'.$width.'" height="'.$height.'" '
                            .'SCALE="exactfit" wmode="window"></embed>
                    </object>';

                if (!$overlap && !$this->overlap) {
                    $content =
                        '<a target="_blank" href="'.$url.'" rel="nofollow" '
                        .'style="display:block;cursor:pointer">'.$flashObject.'</a>';
                } else {
                    // CHECK: dropped checking of IE
                    $content = '<div style="position: relative; width: '.$width.'px; height: '.$height.'px;">
                        <div style="left:0px;top:0px;cursor:pointer;background-color:#FFF;'
                            .'filter:alpha(opacity=0);opacity:0;position:absolute;z-index:100;width:'.
                            $width.'px;height:'.$height.'px;"
                            onclick="javascript:window.open(\''.$url.'\', \'_blank\');return false;"></div>';
                }

                $content = '<div style="width:'.$width.'px; height:'.$height.'px;">'.$content.'</div>';
            } else {
                // Image
                $imageObject = '<img src="'. $mediaUrl.'" width="'.$width.'" height="'.$height.'" />';

                $content = '<a target="_blank" href="'.$url.'" rel="nofollow">'.$imageObject.'</a>';
            }
        }

        $output = $params['beforeHTML'].$content.$params['afterHTML'];

        return $output;
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
