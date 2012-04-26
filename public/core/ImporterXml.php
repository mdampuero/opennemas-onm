<?php

/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Onm\Settings as s,
    Onm\Message  as m;



/**
 * Class to import news from XML files
 *
 * @package    Onm
 * @subpackage Import
 * @author     Sandra Pereira <sandra@openhost.es>, 2011
 * @version
 */

class ImporterXml {

   // the instance object
   static private $instance = null;

   public $ignoreds = NULL;
   public $alloweds = NULL;
   public $labels = NULL;
   public $schema = NULL;
   public $data = NULL;


      /**
     * Initializes the object and initializes configuration
     *
     * @return void
     *
     */
    public function __construct($config = array())
    {
        $this->schema = s::get('xml_file_schema');

        $this->labels = array_values($this->schema);

        $ignoreds = explode(',', $this->schema['ignored']);
        foreach($ignoreds as $lab) {
            $this->ignoreds[] = trim($lab);
        }

        $allowed = explode(',', $this->schema['important']);
        foreach($allowed as $lab) {
            $this->alloweds[] = trim($lab);
        }

        $this->data = array();
        foreach ($this->schema as $k=>$v) {
            if($v != 'ignored') {
                $this->data[$k] ='';
            }
        }

    }


   static public function getInstance($config = NULL)
   {

        if (!self::$instance instanceof self)
        {
            self::$instance = new self($config);

        }
        return self::$instance;

   }

   static public function importXML($XMLFile)
   {
        try{
            $simple = simplexml_load_file($XMLFile);

        } catch (Exception $e){
            m::add( _( "Can't read file. Please check xml file...") );
            exit();
        }

        return $simple;
   }


   public function checkLabels($label) {

       foreach($this->schema as $value=>$pattern) {

            if($label == $pattern)
                return $value;
       }
       return false;

    }

    public function checkBeIgnored($text) {

        if (!empty($text) && (in_array($text, $this->ignoreds) || in_array($text, $this->labels) ) ) {
            return '';
        }else{
            return $text. ' ';
        }
    }



    static function parseXMLtoArray($eleto) {

        $json = json_encode($eleto);
        $array = json_decode($json,TRUE);

        return $array;
    }


    public function parseNodes($array) {

        $tag = '';
        $end = '';
        $texto ='';
        if(!empty($array)) {

            foreach($array as $key=>$value) {
                if($key =='@attributes')  {
                    $label = $this->checkAttributes($value);

                    if((is_array($value) &&
                        array_key_exists('class', $value)
                        && $this->checkBeImportant($value['class']))
                        || (!is_array($value)
                        && $this->checkBeImportant($value['class']))) {

                            $tag = '<b>';
                            $end = '</b> <br>';
                    } else {
                        $tag = '';
                        $end = ' ';
                    }

                    if(!empty($label)) {

                        $point = next($array);

                        if(is_object($point) || is_array($point) ) {

                            $this->data[$label] .= $tag.$this->parseNodes($point).$end;
                        } else {

                            $this->data[$label] .= $tag. $this->checkBeIgnored($point) .$end;

                        }
                    }
                } elseif(!in_array($key, $this->ignoreds) ) {
                   $label = $this->checkLabels($key);

                } else {
                   return '';
                }

                if( !empty($label)) {

                    if(!is_object($value) && !is_array($value)) {
                        $texto = (string)$value;

                        $this->data[$label]  .= $this->checkBeIgnored($texto);

                    } else {

                        $this->data[$label]  .= $this->parseNodes($value);
                    }
                } else {
                    if(!empty($tag)) {
                        $texto .= $tag;
                    }
                    if(is_object($value) || is_array($value)) {

                        $texto .=   $this->parseNodes($value);

                    }else{

                        $texto .= ' <br>'. $this->checkBeIgnored($value);
                    }
                    if(!empty($tag)) {
                        $texto .= $end;
                    }
                }
            }
        }

        $texto = $this->checkBeIgnored($texto);

        return $texto;

    }

    public function checkAttributes($value) {


        $label='';

        if((is_object($value) || is_array($value)) ) {

            foreach($value as $n=>$val) {

                if(!empty($val) &&  (!in_array($n, $this->ignoreds) ) ) {
                   $label = $this->checkAttributes($val);
                }
            }
        } else {

            if(!empty($value)) {
                $label = $this->checkLabels($value);
            }
        }

        return $label;
    }

    public function checkBeImportant($value) {

        if((!is_object($value) && !is_array($value)) ) {

            if (in_array($value, $this->alloweds)) {

                return true;
            }
        }
        return false;
    }


    public function getXMLData($docXML) {

        //Clear data
        $this->data = array();
        foreach ($this->schema as $k=>$v) {
            if($v != 'ignored') {
                $this->data[$k] ='';
            }
        }
        $values = self::parseXMLtoArray($docXML);

        $this->data['pk_author'] = $_SESSION['userid'];



        $this->data['content_status']=0;
        $this->data['available']=0;
        $this->data['frontpage']=0;

        $this->data['img1']=""; $this->data['img1_footer']="";
        $this->data['img2']="";$this->data['img2_footer']="";
        $this->data['fk_video']=""; $this->data['footer_video']="";
        $this->data['fk_video2']=""; $this->data['footer_video2']="";
        $this->data['ordenArti']="";$this->data['ordenArtiInt']="";

        $this->parseNodes($values);

        if(empty($this->data['title_int']))
            $this->data['title_int'] = $this->data['title'];

        if(empty($this->data['summary']))
            $this->data['summary'] = strip_tags(substr($this->data['body'],0, strpos($this->data['body'], '.') ).'.');

        if(!empty($this->data['category_name'])) {
            $ccm = ContentCategoryManager::get_instance();
            $current_category = strtolower(StringUtils::normalize_name( $this->data['category_name'] ));
            $this->data['category'] = $ccm->get_id($current_category);

        } else {
            $this->data['category']=  20;
        }

        $this->data['metadata'] =  StringUtils::get_tags($this->data['title']);



        return ($this->data);
    }

}
