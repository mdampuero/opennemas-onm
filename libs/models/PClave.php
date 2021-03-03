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

/**
 * Handles all the CRUD operations over Keywords
 *
 * @package    Model
 */
class PClave
{
    /**
     * The keyword id
     *
     * @var int
     */
    public $id = null;

    /**
     * The keyword name
     *
     * @var string
     */
    public $pclave = null;

    /**
     * The keyword value
     *
     * @var string
     */
    public $value = null;

    /**
     * The type of the keyword (url, internal search, ...)
     *
     * @var
     */
    public $tipo = null;

    /**
     * The content type (required by the automated listings)
     *
     * @var
     */
    public $content_type_name = 'pclave';

    /**
     * Handler to call the method cacher
     *
     * @var MethodCacheManager
     */
    public $cache = null;

    /**
     * Initializes the Pclave and loads by id if provided
     */
    public function __construct($id = null)
    {
        if (is_numeric($id)) {
            $this->read($id);

            return $this;
        }
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

                if ($k == 'pclave') {
                    $this->title = $v;
                }
            }
        } elseif (is_object($properties)) {
            $properties = get_object_vars($properties);
            foreach ($properties as $k => $v) {
                if (!is_numeric($k)) {
                    $this->{$k} = $v;
                }

                if ($k == 'pclave') {
                    $this->title = $v;
                }
            }
        }
    }

    /**
     * Read, get a specific object
     *
     * @param  int    $id Object ID
     *
     * @return PClave Return instance to chaining method
     */
    public function read($id)
    {
        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM pclave WHERE id=?',
                [ $id ]
            );

            $this->load($rs);

            return $this;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());
            return null;
        }
    }

    /**
     * Create a new pclave in database
     *
     * @param  array  $data
     * @return PClave
     */
    public function create($data)
    {
        $conn = getService('dbal_connection');
        try {
            $conn->insert(
                'pclave',
                [
                    'pclave' => $data['pclave'],
                    'value'  => $data['value'],
                    'tipo'   => $data['tipo'],
                ]
            );

            $data['id'] = $conn->lastInsertId();
            $this->load($data);

            return $this;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());
            return null;
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
        $conn = getService('dbal_connection');
        try {
            $conn->update(
                'pclave',
                [
                    'pclave' => $data['pclave'],
                    'value'  => $data['value'],
                    'tipo'   => $data['tipo'],
                ],
                [ 'id' => $data['id'] ]
            );

            return true;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());
            return false;
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
        try {
            $rs = getService('dbal_connection')->delete(
                'pclave',
                [ 'id' => $id ]
            );

            return $rs;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());
            return null;
        }
    }

    /**
     * Get list of terms given a filter
     *
     * @param string $filter the SQL WHERE clause
     *
     * @return array list of terms
     */
    public function find($filter = null, $order = '', $epp = '', $page = '')
    {
        try {
            $sql = 'SELECT * FROM `pclave`';
            if (!empty($filter)) {
                $sql = 'SELECT * FROM `pclave` WHERE ' . $filter;
            }

            $sql .= empty($order) ? '' : ' order by ' . $order;
            $sql .= empty($epp) ? '' : ' limit ' . $epp;
            $sql .= empty($page) ? '' : ' offset ' . ($page - 1) * $epp;

            $rs = getService('dbal_connection')->fetchAll($sql);

            $terms = [];
            foreach ($rs as $element) {
                $obj = new PClave();
                $obj->load($element);

                $terms[] = $obj;
            }

            return $terms;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());
            return [];
        }
    }

    /**
     * Counts terms given a filter.
     *
     * @param array $filter The filter used to search.
     *
     * @return integer The number of found terms.
     */
    public function count($filter)
    {
        $sql = 'SELECT COUNT(*) as total FROM `pclave`';
        if (!empty($filter)) {
            $sql = 'SELECT COUNT(*) as total FROM `pclave` WHERE ' . $filter;
        }

        $result = getService('dbal_connection')->fetchAll($sql);

        return $result[0]['total'];
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
        // Spaces necessary to evaluate first and last pattern matching
        $text = ' ' . $text . ' ';
        if (mb_detect_encoding($text) !== "UTF-8") {
            $text = utf8_decode($text);
        }

        foreach ($terms as $term) {
            // Select keyword type
            switch ($term->tipo) {
                case 'url':
                    $replacement = "<a href='" . $term->value . "'
                                       title='" . $term->pclave . "' target='_blank'>" .
                                       $term->pclave . "</a>";
                    break;
                case 'email':
                    $replacement = "<a href='mailto:" . $term->value . "'
                                       target='_blank'>" . $term->pclave . "</a>";
                    break;
                case 'intsearch':
                    $replacement = "<a href='/tag/" . $term->value . "'
                                       target='_blank'>" . $term->pclave . "</a>";
                    break;
                default:
                    break;
            }

            // The \b matches a word boundary
            $text = preg_replace(
                '@\b' . $term->pclave . '\b@',
                '\1' . $replacement . '\4',
                $text
            );
        }

        return trim($text);
    }

    /**
     * Returns the available keyword types
     *
     * @return array the list of types
     */
    public static function getTypes()
    {
        $types = [
            'url'       => _('URL'),
            'intsearch' => _('Internal search'),
            'email'     => _('Email')
        ];

        return $types;
    }
}
