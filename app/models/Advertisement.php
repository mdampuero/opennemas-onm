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
    public $map = null;
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
        $this->content_type = get_class();
        parent::__construct($id);

        // Check if it contains a flash element
        $img = getService('entity_repository')->find('Photo', $this->path);
        if ($img->type_img == "swf") {
            $this->is_flash = 1;
        } else {
            $this->is_flash = 0;
        }
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
            $data['categories'],
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
            return null;
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
        parent::read($id); // Read content of Content

        $sql = 'SELECT * FROM advertisements WHERE pk_advertisement = ?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));

        if (!$rs) {
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
            return null;
        }

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
        $sql =  "UPDATE advertisements "
                ." SET `num_clic_count`=`num_clic_count`+1 "
                ." WHERE `pk_advertisement`=?";
        $values = array($id);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
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

        if (!is_array($types) || count($types) <= 0) {
            return $banners;
        }

        // Check category
        $category = (empty($category) || ($category=='home')) ? 0 : $category;

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

        $sql = "SELECT * FROM contents, advertisements "
              ."WHERE contents.pk_content = advertisements.pk_advertisement "
              ."AND contents.in_litter!=1 "
              .'AND contents.content_status=1 AND advertisements.type_advertisement IN ('.$types.') '
              .$catsSQL
              ."ORDER BY contents.created";

        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
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

            $banners [$advertisement->type_advertisement][] = $advertisement;
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
    public function render($params, $tpl = null)
    {
        $output = '';

        if (array_key_exists('cssclass', $params)
            && isset($params['cssclass'])
        ) {
            $wrapperClass = $params['cssclass'].' ad_in_column ad_horizontal_marker clearfix';
        } else {
            $wrapperClass = 'ad_in_column ad_horizontal_marker clearfix';
        }
        if ($this->type_advertisement == 37) {
            //floating ads
            $params['beforeHTML'] = "<div class=\"$wrapperClass\" style=\"text-align: center;\">";
            $params['afterHTML']  = "</div>";
        }

        $width   = $this->params['width'];
        $height  = $this->params['height'];
        $overlap = (isset($this->params['overlap']))? $this->params['overlap']: false;

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
                // Check for external advertisement Script
                if (isset($this->extWsUrl)) {
                    $url = $this->extWsUrl."ads/get/".$this->pk_content;
                } else {
                    $url = url('frontend_ad_get', array('id' => $this->pk_content));
                }

                $content = '<iframe src="'.$url.'" style="width:'.$width.'px; height:'.$height.'px; overflow: hidden;" '.
                ' scrolling="no"></iframe>';
            }

        } elseif ($this->with_script == 2) {
            $content = "<script type='text/javascript' data-id='{$this->id}'><!--// <![CDATA[
OA_show('zone_{$this->id}');
// ]]> --></script>";
        } elseif ($this->with_script == 3) {
            $content = "<div id='zone_{$this->id}' style='width:{$width}px; height:{$height}px;'><script type='text/javascript' data-id='{$this->id}'><!--// <![CDATA[
googletag.cmd.push(function() { googletag.display('zone_{$this->id}'); });
// ]]> --></script></div>";
        } else {
            // Check for external advertisement Flash/Image based
            if (isset($this->extWsUrl)) {
                $cm = new \ContentManager;
                $photo = $cm->getUrlContent($this->extWsUrl."ws/images/id/".$this->img, true);
                $url = $this->extUrl;
                $mediaUrl = $this->extMediaUrl.$photo->path_file. $photo->name;
            } else {
                $photo = getService('entity_repository')->find('Photo', $this->img);
                $url = SITE_URL.'ads/'. date('YmdHis', strtotime($this->created))
                      .sprintf('%06d', $this->pk_advertisement).'.html';
                $mediaUrl = SITE_URL.'media/'.INSTANCE_UNIQUE_NAME.'/images'.$photo->path_file. $photo->name;
            }

            // If the Ad is Flash/Image based try to get the width and height fixed
            if (isset($photo)) {
                if (($photo->width <= $width)
                    && ($photo->height <= $height)
                ) {
                    $width  = $photo->width;
                    $height = $photo->height;
                }
            }

            // TODO: controlar los banners swf especiales con div por encima
            if (strtolower($photo->type_img) == 'swf') {
                if (!$overlap && !$this->overlap) {
                    // Generate flash object with wmode window
                    $flashObject =
                        '<object width="'.$width.'" height="'.$height.'" >
                            <param name="wmode" value="window" />
                            <param name="movie" value="'.$mediaUrl. '" />
                            <param name="width" value="'.$width.'" />
                            <param name="height" value="'.$height.'" />
                            <embed src="'. $mediaUrl. '" width="'.$width.'" height="'.$height.'" '
                                .'SCALE="exactfit" wmode="window"></embed>
                        </object>';

                    $content =
                        '<a target="_blank" href="'.$url.'" rel="nofollow" '
                        .'style="display:block;cursor:pointer">'.$flashObject.'</a>';
                } else {
                    // Generate flash object with wmode transparent
                    $flashObject =
                        '<object width="'.$width.'" height="'.$height.'" >
                            <param name="wmode" value="transparent" />
                            <param name="movie" value="'.$mediaUrl. '" />
                            <param name="width" value="'.$width.'" />
                            <param name="height" value="'.$height.'" />
                            <embed src="'. $mediaUrl. '" width="'.$width.'" height="'.$height.'" '
                                .'SCALE="exactfit" wmode="transparent"></embed>
                        </object>';

                    // CHECK: dropped checking of IE
                    $content = '<div style="position: relative; width: '.$width.'px; height: '.$height.'px;">
                        <div style="left:0px;top:0px;cursor:pointer;background-color:#FFF;'
                            .'filter:alpha(opacity=0);opacity:0;position:absolute;z-index:100;width:'.
                            $width.'px;height:'.$height.'px;"
                            onclick="javascript:window.open(\''.$url.'\', \'_blank\');return false;">
                            </div>'.$flashObject.'</div>';
                }

                $content = '<div style="width:'.$width.'px; height:'.$height.'px; margin: 0 auto;">'.$content.'</div>';
            } else {
                // Image
                $imageObject = '<img src="'. $mediaUrl.'" width="'.$width.'" height="'.$height.'" />';

                $content = '<a target="_blank" href="'.$url.'" rel="nofollow">'.$imageObject.'</a>';
            }
        }

        $output = $params['beforeHTML'].$content.$params['afterHTML'];

        // Increase number of views for this advertisement
        // $this->setNumViews($this->pk_advertisement);

        return $output;
    }

    /**
     * Fire this event when publish an advertisement and unpublished others
     * banners where type_advertisement is equals
    */
    public function onPublish()
    {
        if (!empty($this->content_status) && (intval($this->content_status)>0)) {
            $sql = 'UPDATE `contents` SET `content_status`=0 '
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
                return;
            }
        }
    }
}
