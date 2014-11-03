<?php
/**
 * Defines the Onm\Import\ImporterXml class
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Onm_Import
 */
use Onm\Settings as s;
use Onm\Message  as m;
use Onm\StringUtils;

/**
 * Class to import news from XML files
 *
 * @package    Onm_Import
 */
class ImporterXml
{
    // the instance object
    static private $instance = null;

    public $ignoreds         = null;
    public $alloweds         = null;
    public $labels           = null;
    public $schema           = null;
    public $data             = null;

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

        $this->config = $config;

        $ignoreds = explode(',', $this->schema['ignored']);
        foreach ($ignoreds as $lab) {
            $this->ignoreds[] = trim($lab);
        }

        $allowed = explode(',', $this->schema['important']);
        foreach ($allowed as $lab) {
            $this->alloweds[] = trim($lab);
        }

        $this->data = array();
        foreach ($this->schema as $k => $v) {
            if ($v != 'ignored') {
                $this->data[$k] ='';
            }
        }

    }

    public static function getInstance($config = null)
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self($config);
        }

        return self::$instance;
    }

    public static function importXML($xmlFile)
    {
        try {
            $simple = simplexml_load_file($xmlFile);

        } catch (Exception $e) {
            m::add(_("Can't read file. Please check xml file..."));
            return false;
        }

        return $simple;
    }

    public function checkLabels($label)
    {
        foreach ($this->schema as $value => $pattern) {
            if ($label == $pattern) {
                return $value;
            }
        }

        return false;
    }

    public function checkBeIgnored($text)
    {
        if (!empty($text)
            && (in_array($text, $this->ignoreds)
            || in_array($text, $this->labels))
        ) {
            return '';
        } else {
            return $text. ' ';
        }
    }

    public static function parseXMLtoArray($eleto)
    {
        $json  = json_encode($eleto);
        $array = json_decode($json, true);

        return $array;
    }

    public function parseNodes($array)
    {
        if (empty($array)) {
            return false;
        }

        $tag   = '';
        $end   = '';
        $texto = '';
        foreach ($array as $key => $value) {
            if ($key == '@attributes') {
                $label = $this->checkAttributes($value);
                if ((is_array($value)
                    && array_key_exists('class', $value)
                    && $this->checkBeImportant($value['class']))
                    || (!is_array($value)
                    && $this->checkBeImportant($value['class']))
                ) {
                    $tag = '<b>';
                    $end = '</b> <br>';
                } else {
                    $tag = '';
                    $end = ' ';
                }

                if (!empty($label)) {
                    $point = next($array);

                    if (is_object($point) || is_array($point)) {
                        $this->data[$label] = $tag.$this->parseNodes($point).$end;
                    } elseif (empty($this->data[$label])) {
                        $this->data[$label] = $tag. $this->checkBeIgnored($point).$end;
                    }
                }
            } elseif (!in_array($key, $this->ignoreds)) {
                $label = $this->checkLabels($key);
            } else {

                return '';
            }

            if (!empty($label)) {
                if (!is_object($value) && !is_array($value)) {
                    $texto = (string) $value;
                    $this->data[$label]  .= $this->checkBeIgnored($texto);
                } else {
                    $this->data[$label]  .= $this->parseNodes($value);
                }
            } else {
                if (!empty($tag)) {
                    $texto .= $tag;
                }
                if (is_object($value) || is_array($value)) {
                    $texto .=   $this->parseNodes($value);
                } else {
                    $texto .= ' <br>'. $this->checkBeIgnored($value);
                }
                if (!empty($tag)) {
                    $texto .= $end;
                }
            }
        }

        $texto = $this->checkBeIgnored($texto);

        return $texto;

    }

    public function checkAttributes($value)
    {
        $label='';

        if ((is_object($value) || is_array($value))) {

            foreach ($value as $n => $val) {

                if (!empty($val) && (!in_array($n, $this->ignoreds))) {
                    $label = $this->checkAttributes($val);
                }
            }
        } else {

            if (!empty($value)) {
                $label = $this->checkLabels($value);
            }
        }

        return $label;
    }

    public function checkBeImportant($value)
    {
        if ((!is_object($value) && !is_array($value))) {

            if (in_array($value, $this->alloweds)) {

                return true;
            }
        }

        return false;
    }

    public function getXMLData($docXml)
    {
        //Clear data
        $this->data = array();
        foreach ($this->schema as $k => $v) {
            if ($v != 'ignored') {
                $this->data[$k] ='';
            }
        }
        $values = self::parseXMLtoArray($docXml);

        $this->data['pk_author']      = $_SESSION['userid'];
        $this->data['content_status'] = 0;
        $this->data['available']      = 0;
        $this->data['frontpage']      = 0;
        $this->data['fk_video']       ="";
        $this->data['footer_video']   ="";
        $this->data['fk_video2']      ="";
        $this->data['footer_video2']  ="";
        $this->data['ordenArti']      ="";
        $this->data['ordenArtiInt']   ="";

        $this->parseNodes($values);

        $imgImported = null;
        if (!empty( $this->data['img'] )) {
            $originalFileName = urldecode($this->data['img']);
            $originalFileName = StringUtils::cleanFileName($originalFileName);
            $imgImported      = Content::findByOriginaNameInUrn($originalFileName);
        }

        $this->data['img1']        = $imgImported;
        $this->data['img1_footer'] = $this->data['img_footer'];
        $this->data['img2']        = $imgImported;
        $this->data['img2_footer'] = $this->data['img_footer'];
        if (empty($this->data['title_int'])) {
            $this->data['title_int'] = $this->data['title'];
        }

        if (empty($this->data['summary'])) {
            $this->data['summary'] = strip_tags(
                substr($this->data['body'], 0, strpos($this->data['body'], '.')).'.'
            );
        }
        if (empty($this->data['agency'])) {
            $this->data['agency'] = strip_tags($this->data['agency']);
        }

        if (!empty($this->data['category_name'])) {
            $ccm = ContentCategoryManager::get_instance();
            $current_category = strtolower(StringUtils::normalizeName($this->data['category_name']));
            $this->data['category'] = $ccm->get_id($current_category);

        } else {
            $this->data['category']=  20;
        }

        $this->data['metadata'] =  StringUtils::getTags($this->data['title']);

        return ($this->data);
    }
}
