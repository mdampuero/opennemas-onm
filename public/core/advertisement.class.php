<?php
/**
 * Advertisement file, contains class to manage site advertiments
 *
 * @package OpenNeMas
 * @version 0.1
 * @author Tomás Vilariño <vifito@openhost.es>
 * @link http://www.openhost.es
 * @copyright Copyright (c) 2009, Openhost S.L.
 */

/**
 * Advertisement class
 *
 * Handles all the CRUD operations with advertisement content.
 * The class use MethodCacheManager for better performance.
 *
 * @package OpenNeMas
 * @version 0.1
 */
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
        450 => "[V] Banner Interticial",

        /* Albums Front banners > 400 */
        401 => "[A] Big banner superior",
        402 => "[A] Banner superior derecho",

        403 => "[A] Banner1 Column Right",
        405 => "[A] Banner1 2Column Right",

        409 => "[A] Big Banner Inferior",
        410 => "[A] Banner Inferior Derecho",

	/* Intersticial banner album inner */
        550 => "[AI] Banner Interticial",

        /* Video Interior banners > 500 */
        501 => "[AI] Big banner superior",
        502 => "[AI] Banner superior Derecho",

        503 => "[AI] Banner Video Button",

        509 => "[AI] Big Banner Inferior",
        510 => "[AI] Banner Inferior Derecho",
 
    );

    /**
     * @access public
     * @var long
     */
    public $pk_advertisement = null;

    /**
     * @access public
     * @var int
     */
    public $type_advertisement = null;

    /**
     * @access public
     * @var int
     */
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
    */
    var $cache = NULL;

    /**
     * @var Advertisement instance, singleton pattern
     */
    static private $instance = null;

    /**
     * @var registry of banners
     */
    protected $registry = array();

    /**
     * Constructor
     *
     * @param int $id, ID of the Advertisement
    */
    function __construct($id=null)
    {
        parent::__construct($id);

        if(is_numeric($id)) {
            $this->read($id);
        }

        // Store this object into the cache manager for better performance
        if( is_null($this->cache) ) {
            $this->cache = new MethodCacheManager($this, array('ttl' => (20)));
        } else {
            $this->cache->set_cache_life(20); // 20 seconds
        }

        // Set the content_type
        // FIXME: this should be into the __construct method of Content class.
        $this->content_type = get_class();
        return $this;
    }

    /**
     * Method to fetch or create the object by the singleton pattern
     *
     * @return Advertisement, instance of Advertisement
    */
    static function getInstance()
    {
        if( is_null(self::$instance) ) {
            $instance = new Advertisement();
            self::$instance = $instance;
            return self::$instance;
        } else {
            return self::$instance;
        }

    }

    /**
     * Create and save into database the ad instance from one array
     *
     * @param array $data, the needed data for create a new ad.
     * @return Advertisement
     */
    function create($data)
    {
        // Clear magic_quotes String_Utils::fixScriptDeclaration & String_Utils::disabled_magic_quotes
        String_Utils::disabled_magic_quotes( $data );

        parent::create($data);

        if(!empty($data['script'])) {
            $data['script'] = base64_encode( $data['script'] );
        }

        if(!isset($data['with_script'])) {
            $data['with_script'] = 0;
        }

        $data['overlap'] = (isset($data['overlap']))? $data['overlap']: 0;
        $data['timeout'] = (isset($data['timeout']))? $data['timeout']: -1;

        $sql = 'INSERT INTO advertisements (`pk_advertisement`, `type_advertisement`, `fk_content_categories`,
                                            `path`, `url`, `type_medida`, `num_clic`, `num_clic_count`,
                                            `num_view`, `with_script`, `script`, `overlap`, `timeout`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

        // $this->id was setted in parent::create($data)
        $values = array($this->id, $data['type_advertisement'], $data['category'],
                        $data['img'], $data['url'], $data['type_medida'], $data['num_clic'],
                        0, $data['num_view'], $data['with_script'], $data['script'], $data['overlap'],
                        $data['timeout']);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return null;
        }

        $rel = new Related_content();
        if(isset($data['selectos'])){
            $pos = 1;
            foreach($data['selectos'] as $relac) {
                $rel->set_rel_position($this->id, $pos, $relac);
                $pos++;
            }
        }

        // Necesarios para evento
        $this->pk_advertisement = $this->id;
        $this->available        = $data['available'];
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
    */
    function read($id)
    {
        parent::read($id); // Read content of Content

        $sql = 'SELECT * FROM advertisements WHERE pk_advertisement = '.($id);
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }

        $this->load( $rs->fields );

        // Return instance to method chaining
        return $this;
    }

    /**
     * Load object properties
     *
     * @param array $properties
    */
    function load($properties)
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
     * @return Advertisement Return the instance to chaining method
    */
    function update($data)
    {
        parent::update($data);

        // Remove magic_quotes, String_Utils::fixScriptDeclaration & String_Utils::disabled_magic_quotes
        String_Utils::disabled_magic_quotes( $data );

        if(!empty($data['script'])){
            //$data['script'] = String_Utils::fixScriptDeclaration($data['script']);
            $data['script'] = base64_encode( $data['script'] );
        }

        $data['overlap'] = (isset($data['overlap']))? $data['overlap']: 0;
        $data['timeout'] = (isset($data['timeout']))? $data['timeout']: 0;
        $data['with_script'] = (isset($data['with_script']))? $data['with_script']: 0;

        $sql = "UPDATE advertisements
                SET `type_advertisement`=?, `fk_content_categories`=?,
                    `path`=?, `url`=?, `type_medida`=?, `num_clic`=?,
                    `num_clic_count`=?, `num_view`=?,`with_script`=?,
                    `script`=?, `overlap`=?, `timeout`=?
                WHERE pk_advertisement=".($data['id']);

        $values = array($data['type_advertisement'], $data['category'], $data['img'],
                        $data['url'], $data['type_medida'], $data['num_clic'],
                        $data['num_clic_count'], $data['num_view'], $data['with_script'],
                        $data['script'], $data['overlap'], $data['timeout'] );

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return null;
        }

        $rel = new Related_content();
        $rel->delete($data['id']);
        if(isset($data['selectos'])){
            $pos=1;
            foreach($data['selectos'] as $relac) {
                $rel->set_rel_position($data['id'], $pos, $relac);
                $pos++;
            }
        }

        // Necesarios para evento
        $this->pk_advertisement = $this->id;
        $this->available        = $data['available'];
        $this->type_advertisement    = $data['type_advertisement'];
        $this->fk_content_categories = $data['category'];

        // Fire event
        $GLOBALS['application']->dispatch('onAfterUpdateAdvertisement', $this);

        return $this;
    }

    function remove($id)
    {
        parent::remove($id);

        $sql = 'DELETE FROM advertisements WHERE pk_advertisement ='.($id);

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
    }

    /**
     * Get url of advertisement
     *
     * @param int $id Advertisement Id
     * @return string
    */
    function getUrl($id)
    {
        // Try to minimize the database overload if this object was preloaded or
        // doesn't fit the rules
        if(isset($this) && isset($this->url) && ($this->id == $id)) {
            return $this->url;
        }

        // Fetch data for the ad from the database
        $sql = 'SELECT url FROM `advertisements` WHERE `advertisements`.`pk_advertisement`=?';
        $rs = $GLOBALS['application']->conn->Execute( $sql, array($id) );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return null;
        }

        return $rs->fields['url'];
    }

    /**
     * Set clicks
     *
     * @param int $id
    */
    static function setNumClics($id)
    {
        $sql = "UPDATE advertisements SET `num_clic_count`=`num_clic_count`+1 WHERE `pk_advertisement`=?";
        $values = array($id);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }

	$ad = new Advertisement($id);

        //No publicado
        if(($ad->type_medida=='CLIC' ) AND ($ad->num_clic <= $ad->num_clic_count)){
            $status = 0;
            parent::set_status($status, 'NULL');
        }
    }

    /**
     * Set num views
     *
     * @param int $id
    */
    static function setNumViews($id=null)
    {
        //FIXME: error Using $this when not in object // comentar con Tomas
        if(is_null($id)) {
            // $id = $this->id;
            return null;
        }

        parent::setNumViews($id);

        if(is_array($id)) {
            foreach($id as $banner) {

                if((isset($banner) && property_exists($banner, 'type_medida')) &&
                    (($banner->type_medida == 'VIEW') AND ($banner->num_view <= $banner->views)))
                {
                    $banner->set_status($status=0, 'NULL');

                }
            }
        } else {
            $ad = new Advertisement($id);
            if(($ad->type_medida == 'VIEW') AND ($ad->num_view <= $ad->views)) {
                parent::set_status($status=0, 'NULL');
            }
        }
    }

    /**
     * Get advertisement for a type and a category
     *
     * @param array $types Types of advertisement
     * @param string $category Category of advertisement
     * @return array Array of Advertisement objects
    */
    function getAdvertisements($types=array(), $category='home')
    {
        $banners = array();

        // FIXME: falla
        if(is_array($types) && count($types)>0 && ADVERTISEMENT_ENABLE) {
            $category = (empty($category) || ($category=='home'))? 0: $category;
            $types = implode(',', $types);

            $cm = new ContentManager();
            if($category!=0) {
                $rsBanner = $cm->find('Advertisement', ' type_advertisement IN ('.$types.') AND available=1 AND
                                                    (fk_content_categories LIKE \'%'.$category.'%\' OR fk_content_categories=0)',
                                        'ORDER BY type_advertisement, created');
            } else {
                $rsBanner = $cm->find('Advertisement', ' type_advertisement IN ('.$types.') AND available=1 AND
                                                    fk_content_categories=0',
                                        'ORDER BY type_advertisement, created');
            }

            // $advertisements is an array of banners, grouped by advertisement type
            $advertisements = array();
            foreach($rsBanner as $adv) {
		
		$adv->fk_content_categories = explode(',', $adv->fk_content_categories);
		
		if(!in_array($category, $adv->fk_content_categories)
		   && $adv->fk_content_categories != array(0))
		{
		    continue;
		}
		
		
                if(!isset($advertisements[$adv->type_advertisement])) {
                    $advertisements[$adv->type_advertisement] = array();
                }
		
		
                // Colocar primeiro os da propia sección
                if($adv->fk_content_categories == array(0)) {
                    array_push($advertisements[$adv->type_advertisement], $adv);
                } else {
                    array_unshift($advertisements[$adv->type_advertisement], $adv);
                }
		
            }	    

            // Perform operations for each advertisement type
            foreach($advertisements as $type_advertisement => $advs) {
                //Select a random banner
                $banners[] = $advs[ array_rand($advs) ];

                // Previous behavior changed to only fetch first banner
                //$banners[] = array_shift($advs);
            }
        }

        return $banners;
    }

    public function getIntersticial($type, $category='home')
    {
        $interstitial = null;

        if(in_array($type, array(50, 150)) && ADVERTISEMENT_ENABLE) {
            $category = (empty($category) || ($category=='home'))? 0: $category;

            $cm = new ContentManager();
            $rsBanner = $cm->find('Advertisement', ' `type_advertisement`=' . $type . ' AND
                                                     `available`=1 AND
                                                     `fk_content_categories`='.$category,
                                        'ORDER BY type_advertisement, created');

            //if(count($rsBanner) == 1) {
            //    $interstitial = $rsBanner[0];
            //}

            $numBanner = array_rand($rsBanner);
            if (!is_null($numBanner)){
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
    */
    function render($banners, $tpl)
    {
        // Extract pk_photos to perform one query
        $pk_photos = array();
        foreach($banners as $banner) {
            if(!empty($banner->path)) {
                $pk_photos[] = $banner->path;
            }
        }
        $banners_selected =array();
        //Get photos
        $cm = new ContentManager();
        $objs = $cm->cache->find('Photo', "pk_content IN ('" . implode("','", $pk_photos) . "')");

        // Array of photos objects, key is pk_content array( 'pk_content' => object )
        $photos = array();
        foreach($objs as $obj) {
            $photos[ $obj->pk_content ] = $obj;
        }

        foreach($banners as $banner) {
            // Save selected banners to process after
            $banners_selected[] = $banner;

            if (isset($banner->type_advertisement) && property_exists($banner, 'type_advertisement')){
                $tpl->assign('banner'.$banner->type_advertisement, $banner);
            }


            // FIXME: This is a workarround until decide what to do into the Content class
            // This will avoid the the notice messages but doesn't keep the code clean.
            // We should change de content class to return values initialized allways.
            (isset($banner->with_script))?($with_script = $banner->with_script):($with_script = null);

            if($with_script) {
                $tpl->assign('script_b'.$banner->type_advertisement, $banner->script);
            } else {

                if (isset($banner->path) && property_exists($banner, 'path')){
                    $adv = $banner->path; // "path" is Photo ID, $banner->img is similar but deprecated
                }
                //Evitar undefined index
                if(isset($photos[$adv])) {
                    $tpl->assign('photo'.$banner->type_advertisement, $photos[$adv]);
                }
            }
        }
        // Update numviews
        Advertisement::setNumViews( $banners_selected );
    }

    /**
     * Emulate smarty method,
     * workaround for Advertisement::render
     *
     * @param string $entry
     * @param mixed $value
    */
    public function assign($entry, $value)
    {
        $this->registry[$entry] = $value;
    }

    /**
     * Fetch a entry from set of banners,
     * workaround for Advertisement::render
     *
     * @param string $entry
     * @return mixed
    */
    public function fetch($entry)
    {
        return (isset($this->registry[$entry]))? $this->registry[$entry]: null;
    }

    /**
     * Fire this event when publish an advertisement and unpublished others
     * banners where type_advertisement is equals
    */
    function onPublish()
    {
        if(!empty($this->available) && (intval($this->available)>0)) {
            // Documentation: http://www.xaprb.com/blog/2006/06/23/how-to-select-from-an-update-target-in-mysql/
            $sql = 'UPDATE `contents` SET `available`=0 WHERE pk_content IN (
                        SELECT `pk_advertisement` FROM (
                            SELECT `advertisements`.*
                            FROM `advertisements`, `contents`, `contents_categories`
                            WHERE `advertisements`.`type_advertisement`='.$this->type_advertisement.' AND
                                `advertisements`.`pk_advertisement`<>'.$this->pk_advertisement.' AND
                                `contents_categories`.`pk_fk_content_category`='.$this->fk_content_categories.' AND
                                `contents`.`pk_content`=`contents_categories`.`pk_fk_content` AND
                                `contents`.`pk_content`=`advertisements`.`pk_advertisement`
                        ) AS temp
                    )';

            $rs = $GLOBALS['application']->conn->Execute($sql, $values);
            if($rs === false) {
                $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
                $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
                return;
            }
        }
    }
}
