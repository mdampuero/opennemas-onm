<?php
/**
 * Handles all the CRUD operations over Keywords
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Model
 */
use \Onm\Settings as s;

/**
 * Handles all the CRUD operations over Keywords
 *
 * @package    Model
 **/
class PClave
{
    /**
     * The keyword id
     *
     * @var int
     **/
    public $id = null;

    /**
     * The keyword name
     *
     * @var string
     **/
    public $pclave = null;

    /**
     * The keyword value
     *
     * @var string
     **/
    public $value  = null;

    /**
     * The type of the keyword (url, internal search, ...)
     *
     * @var
     **/
    public $tipo   = null;

    /**
     * Handler to call the method cacher
     *
     * @var MethodCacheManager
     */
    public $cache = null;

    /**
     * Read, get a specific object
     *
     * @param  int    $id Object ID
     *
     * @return PClave Return instance to chaining method
     */
    public function read($id)
    {
        $sql = "SELECT * FROM pclave WHERE id=?";

        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));
        if ($rs === false) {
            return null;
        }

        $this->load($rs->fields);

        return $this;
    }

    /**
     * Create a new pclave in database
     *
     * @param  array  $data
     * @return PClave
     */
    public function create($data)
    {
        $sql = "INSERT INTO `pclave` (`pclave`, `value`, `tipo`) "
             . "VALUES (?, ?, ?)";

        $values = array(
            $data['pclave'],
            $data['tipo'],
            $data['value'],
        );

        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if ($rs === false) {
            return null;
        }

        $data['id'] = $GLOBALS['application']->conn->Insert_ID();
        $this->load($data);

        return $this;
    }

    /**
     * Update
     *
     * @param  array   $data Array values
     * @return boolean
     */
    public function update($data)
    {
        $sql = "UPDATE `pclave` "
             . "SET `pclave`=?, `tipo`=?, `value`=? "
             . "WHERE `id`=?";

        $values = array(
            $data['pclave'],
            $data['tipo'],
            $data['value'],
            $data['id'],
        );

        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if ($rs === false) {
            return false;
        }

        return true;
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

        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));
        if ($rs === false) {
            return false;
        }

        return true;
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
     * Get list of terms given a filter
     *
     * @param string $filter the SQL WHERE clause
     *
     * @return array list of terms
     */
    public function find($filter = null)
    {
        $sql = 'SELECT * FROM `pclave`';
        if (!empty($filter)) {
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
     * Replaces the appearances of all the keywords by their replacements
     *
     * @param string $text the text to change
     * @param array $terms the list of terms to replace
     *
     * @return string the changed text
     */
    public function replaceTerms($text, $terms)
    {
        if (mb_detect_encoding($text) == "UTF-8") {
            $text = ' '.($text).' ';
        } else {
            // spaces necessary to evaluate first and last pattern matching
            $text = ' '.utf8_decode($text).' ';
        }

        usort(
            $terms,
            function (
                $a,
                $b
            ) {
                if (strlen($a->pclave) == strlen($b->pclave)) {
                    return 0;
                }

                return (strlen($a->pclave) < strlen($b->pclave)) ? 1 : -1;
            }
        );

        foreach ($terms as $term) {
            $method = 'cb'.ucfirst($term->tipo);
            if (method_exists($this, $method)) {
                $replacement = $this->$method($term->pclave, $term->value);

                // WARNING: utf8
                $regexp = '(\W)(' . preg_quote($term->pclave) . '|' .
                preg_quote(htmlentities(utf8_decode($term->pclave), ENT_COMPAT))
                .')(?!(</a>|&|"))(\W)';

                $regexp = '@' . preg_replace('@/@', '\/', $regexp).'@i';

                $text = preg_replace($regexp, '\1'.$replacement.'\4', $text);
            }
        }

        return trim($text);
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function cbUrl($name, $value)
    {

        return "<a href='$value' title='\\2'>\\2</a>";
    }

    /**
     * Returns the available keyword types
     *
     * @return array the list of types
     **/
    public static function getTypes()
    {
        $types = array(
            'url'       => _('URL'),
            'intsearch' => _('Internal search'),
            'email'     => _('Email')
        );

        return $types;
    }
}
