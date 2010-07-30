<?php
/* -*- Mode: PHP; tab-width: 4 -*- */
/**
 * OpenNeMas project
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   OpenNeMas
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Keyword_Model
 *
 * @package    Keyword
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Keyword.php 1 2010-07-30 18:16:56Z  $
 */


class Keyword_Model_Keyword {
    /**
     * Keyword properties
     */

    /**
     * @var int Identifier of class
     */
    public $pk_keyword = null;

    /**#@+
     * Object value
     *
     * @access public
     * @var string
     */
    public $word = null;
    public $value = null;
    public $type = null;

    /**
     * @var MethodCacheManager Handler to call method cached
     */
    public $cache = null;

    /**
     * @var ADOConnection
     */
    private $conn = null;

    /**
     * Singleton instance
     *
     * @var Keyword
     */
    private static $_instance = null;


    /**
     * Constructor
     *
     * @param null|int  pk_keyword identifier
     */
    public function __construct($pk_keyword=null) {
        $this->cache = new MethodCacheManager($this, array('ttl' => 330));

        if( Zend_Registry::isRegistered('conn') ) {
            $this->conn  = Zend_Registry::get('conn');
        }

        if(!is_null($pk_keyword)) {
            $this->read($pk_keyword);
        }
    }

    /**
     * Use this class
     *
     * @return Keyword
     */
    public function getInstance() {
        if(is_null(self::$_instance)) {
            self::$_instance = new Keyword_Model_Keyword;
        }

        return self::$_instance;
    }

    /**
     * Create a new object
     *
     * @param array $data
     * @return boolean
     */
    public function create( $data ) {
        //¿¿¿??? Prepare array data with default values if it's necessary
        String_Utils::disabled_magic_quotes( &$data );

        $fields = array('word', 'value', 'type');

        try {
            $pk_keyword = SqlHelper::bindAndInsert('keywords', $fields, $data);
        } catch(Exception $e) {
            return false;
        }
        return $pk_keyword;
    }

    /**
     * Load properties for "this" object from $properties
     *
     * @param sarray $properties
     */

    public function loadProperties($assocProps, $object=null) {
        if(is_null($object)) {
            $object = $this;
        }

        foreach($assocProps as $prop => $val) {
            if(property_exists($object, $prop)) {
                $object->{$prop} = $val;
            }
        }
    }

    /**
     * Read a new content
     *
     * @param array $pk_keyword
     * @return boolean
     */
    public function read($pk_keyword) {

        $sql = 'SELECT * FROM `keywords` WHERE `pk_keyword` = ?';
        $rs = $this->conn->Execute( $sql, array($pk_content) );

        if ( false === $rs) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);

            return false;
        }

        // Load object properties
        $this->loadProperties( $rs->fields );

        return $this;
    }

    /**
     *
     * @throws OptimisticLockingException
     * @param array $data
     */
    public function update($data) {
        // Prepare array data with default values if it's necessary
        // Clear  magic_quotes
        String_Utils::disabled_magic_quotes( &$data );

        $fields = array('word', 'value', 'type');
        try {
            SqlHelper::bindAndUpdate('keywords', $fields, $data, 'pk_keyword = ' . $data['pk_keyword']);
        } catch(Exception $e) {
            return false;
        }

        return $data['pk_keyword'];
    }


    /**
     * Remove content of database
     *
     * @param int $pk_keyword
     * @return int Affected rows
     */
    public function delete($pk_keyword) {
        $pk_keyword = filter_var($pk_keyword, FILTER_VALIDATE_INT);
        $filter     = '`pk_keyword`=' . $pk_keyword;

        return SqlHelper::delete('keywords', $filter);
    }


    /**
     * Get list of terms to substitute
     *
     * @return array Terms
     */
    public function getList($filter=null) {

        $sql = 'SELECT * FROM `keywords`';
        if(!is_null($filter)) {
            $sql = 'SELECT * FROM `keywords` WHERE ' . $filter;
        }
        $rs = $this->conn->Execute($sql);

        $terms = array();
        if ( false === $rs) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);

            return false;

        }else {
            while(!$rs->EOF) {
                $obj = new Keyword_Model_Keyword();
                $obj->loadProperties( $rs->fields );

                $terms[] = $obj;

                $rs->MoveNext();
            }
        }

        return $terms;
    }



    /**
     *
     */
    public function replaceTerms($text, $terms) {
        if(mb_detect_encoding($text) == "UTF-8") {
            $text = ' '.($text).' ';
        } else {
            $text = ' '.utf8_decode($text).' '; // spaces necessary to evaluate first and last pattern matching
        }

        if(!function_exists('longestFirst')) {
            function longestFirst($a, $b) {
                if (strlen($a->word) == strlen($b->word)) {
                    return 0;
                }
                return (strlen($a->word) < strlen($b->word)) ? 1 : -1;
            }
        }
        usort($terms, "longestFirst");

        foreach($terms as $term) {
            $method = 'cb_' . strtolower($term->type);
            if(method_exists($this, $method)) {
                $replacement = $this->$method($term->word, $term->value);

                // WARNING: utf8
                $regexp = '(\W)' .
                        '(' . preg_quote($term->word) . '|' .
                        preg_quote(htmlentities(utf8_decode($term->word), ENT_COMPAT)) .
                        ')(?!(</a>|&|"))(\W)';

                $regexp = '/' . preg_replace('@/@', '\/', $regexp) . '/';

                $text = preg_replace($regexp, '\1' . $replacement . '\4', $text);
            }
        }

        return trim($text);
    }


    /* Callbacks to execute replacement */
    public function cb_search($word, $value) {
        $text = '<a href="' . SITE_URL . 'search/%s" title="%s" class="keyword">%s</a>';

        if(empty($value)) {
            $value = $word;
        }
        $origin = $word;

        // optimize search
        $value = preg_replace('/[\+"\'\-\*&%]/', ' ', $value);
        $value = preg_replace('/[ ][ ]+/', ' ', $value);
        $value = '"' .trim($value) . '"';
        $value = urlencode($value);

        $title = 'Search more info';
        if(Zend_Registry::isRegistered('Zend_Translate')) {
            $translator = Zend_Registry::get('Zend_Translate');
            $title = $translator->_($title);
        }

        return sprintf($text, $value, $title . ': ' . htmlentities($origin, ENT_COMPAT, 'UTF-8'), $word);
    }


    public function cb_url($word, $value) {
        $title = 'Go to';
        if(Zend_Registry::isRegistered('Zend_Translate')) {
            $translator = Zend_Registry::get('Zend_Translate');
            $title = $translator->_($title);
        }

        $text = '<a href="%s" title="' . $title . ' %s" class="keyword">%s</a>';

        return sprintf($text, $value, $value, $word);
    }


    public function cb_email($word, $value) {
        $matches = array();
        preg_match('/^(?P<cuenta>[^@]+)@(?P<dominio>[^\.]+)\.(?P<tld>.*?)$/', $value, $matches);
        $text =<<< MAIL_LINK
<a href="mailto:{$matches['cuenta']}&#64;{$matches['dominio']}&#46;{$matches['tld']}" title="%s" class="keyword">%s</a>
MAIL_LINK;

        $title = 'Contact with';
        if(Zend_Registry::isRegistered('Zend_Translate')) {
            $translator = Zend_Registry::get('Zend_Translate');
            $title = $translator->_($title);
        }

        return sprintf($text, $title . ': '.$word, $word);
    }




}