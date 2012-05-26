<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use \Onm\Settings as s;

/**
 * Handles all the CRUD operations over Keywords
 *
 * @package    Onm
 * @subpackage Model
 * @author     Fran Dieguez <fran@openhost.es>
 **/
class PClave
{
    /**
     * @var int Identifier of class
     */
    public $id = null;

    /**#@+
     * Object value
     *
     * @access public
     * @var string
     */
    public $pclave = null;
    public $value  = null;
    public $tipo   = null;
    /**#@-*/

    /**
     * @var MethodCacheManager Handler to call method cached
     */
    public $cache = null;

    public static $instance=null;
    /**
     * constructor
     *
     * @param int $id
     */
    public function __construct($id=null)
    {
        if (!is_null($id)) {
            $this->read($id);
        }

        $this->cache = new MethodCacheManager($this, array('ttl' => 330));
    }

    public function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new PClave();
        }

        return self::$instance;
    }
    /**
     * Create a new object
     *
     * @param  array  $data
     * @return PClave
     */
    public function create($data)
    {
        // Clear  magic_quotes
        StringUtils::disabled_magic_quotes($data);

        $sql = "INSERT INTO `pclave` (`pclave`, `value`, `tipo`) VALUES (?, ?, ?)";

        $values[] = $data['pclave'];
        $values[] = $data['value'];
        $values[] = $data['tipo'];
        /*$this->sanitize( &$data );*/

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return null;
        }

        $data['id'] = $GLOBALS['application']->conn->Insert_ID();
        $this->load($data);

        return $this;
    }

    /**
     * Read, get a specific object
     *
     * @param  int    $id Object ID
     * @return PClave Return instance to chaining method
     */
    public function read($id)
    {
        $sql = "SELECT * FROM pclave WHERE id=?";

        $values = array($id);

        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if ($rs === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return null;
        }

        $this->load($rs->fields);

        return $this;
    }

    /**
     * Load properties into this instance
     *
     * @param array $properties Array properties
     */
    public function load($properties)
    {
        if (is_array($properties)) {
            foreach ($properties as $k => $v) {
                if (!is_numeric($k)) {
                    $this->{$k} = $v;
                }
            }
        } elseif (is_object($properties)) {
            $properties = get_object_vars($properties);
            foreach ($properties as $k => $v) {
                if (!is_numeric($k)) {
                    $this->{$k} = $v;
                }
            }
        }
    }

    /**
     * Update
     *
     * @param  array   $data Array values
     * @return boolean
     */
    public function update($data)
    {
        // Clear  magic_quotes
        StringUtils::disabled_magic_quotes($data);

        $sql = "UPDATE `pclave` SET `pclave`=?, `tipo`=?, `value`=? WHERE `id`=?";

        $values[] = $data['pclave'];
        $values[] = $data['tipo'];
        $values[] = $data['value'];
        $values[] = $data['id'];

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return false;
        }

        return true;
    }

    /**
     * Save
     *
     * @param array $data Post data
     */
    public function save($data)
    {
        if (empty($data['id'])) {
            $this->create($data);
        } else {
            $this->update($data);
        }
    }

    /**
     * Delete
     *
     * @param  int     $id Identifier
     * @return boolean
     */
    public function delete($id)
    {
        $sql = "DELETE FROM pclave WHERE id=?";
        $values = array($id);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

           return false;
        }

        return true;
    }

    /**
     * Get list of terms to substitute
     *
     * @return array Terms
     */
    public function getList($filter=null)
    {
        $sql = 'SELECT * FROM `pclave`';
        if (!is_null($filter)) {
            $sql = 'SELECT * FROM `pclave` WHERE ' . $filter;
        }
        $rs = $GLOBALS['application']->conn->Execute($sql);

        $terms = array();
        if ($rs !== false) {
            while (!$rs->EOF) {
                $obj = new PClave();
                $obj->load($rs->fields);

                $terms[] = $obj;

                $rs->MoveNext();
            }
        }

        return $terms;
    }



    /**
     *
     */
    public function replaceTerms($text, $terms)
    {
        if (mb_detect_encoding($text) == "UTF-8") {
            $text = ' '.($text).' ';
        } else {
            $text = ' '.utf8_decode($text).' '; // spaces necessary to evaluate first and last pattern matching
        }

        if (!function_exists('longestFirst')) {
            function longestFirst($a, $b)
            {
                if (strlen($a->pclave) == strlen($b->pclave)) {
                    return 0;
                }

                return (strlen($a->pclave) < strlen($b->pclave)) ? 1 : -1;
            }
        }
        usort($terms, "longestFirst");

        foreach ($terms as $term) {
            $method = 'cb_'.$term->tipo;
            if (method_exists($this, $method)) {
                $replacement = $this->$method($term->pclave, $term->value);

                // WARNING: utf8
                $regexp = '(\W)' .
                            '(' . preg_quote($term->pclave) . '|' .
                                  preg_quote(htmlentities(utf8_decode($term->pclave), ENT_COMPAT)) .
                          ')(?!(</a>|&|"))(\W)';

                $regexp = '/' . preg_replace('@/@', '\/', $regexp) . '/';

                $text = preg_replace($regexp, '\1' . $replacement . '\4', $text);
            }
        }

        return trim($text);
    }

    /* Callbacks to execute replacement */
    public function cb_intsearch($pclave, $value)
    {
        $text = '<a href="'.SITE_URL.'search.php?cx='. s::get('google_custom_search_api_key') .'&cof=FORID:10&ie=UTF-8&q=%s' .
            '&destino='.SITE_NAME.'" title="%s">%s</a>';

        if (empty($value)) {
            $value = $pclave;
        }
        $origin = $pclave;

        // optimize search
        $value = preg_replace('/[\+"\'\-\*&%]/', ' ', $value);
        $value = preg_replace('/[ ][ ]+/', ' ', $value);
        $value = '"' .trim($value) . '"';
        $value = urlencode($value);

        return sprintf($text, $value, 'Buscar m&aacute;s entradas '. s::get('site_name') .'en para: ' . htmlentities($origin, ENT_COMPAT, 'UTF-8'), $pclave);
    }

    public function cb_url($pclave, $value)
    {
        //Añadido target="_blank"
        $text = '<a target="_blank" href="%s" title="Ir a %s">%s</a>';

        return sprintf($text, $value, $value, $pclave);
    }

    public function cb_email($pclave, $value)
    {
        $matches = array();
        preg_match('/^(?P<cuenta>[^@]+)@(?P<dominio>[^\.]+)\.(?P<tld>.*?)$/', $value, $matches);
        $text =<<< MAIL_LINK
<a href="mailto:{$matches['cuenta']}&#64;{$matches['dominio']}&#46;{$matches['tld']}" title="%s">%s</a>
MAIL_LINK;

        return sprintf($text, 'Ponerse en contacto con: '.$pclave, $pclave);
    }
}
