<?php
/**
 * Handles all the CRUD operations over letters.
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
 * Handles all the CRUD operations over letters.
 *
 * @package    Model
 **/
class Letter extends Content
{
    /**
     * The letter id
     *
     * @var int
     **/
    public $pk_letter         = null;

    /**
     * The author id
     *
     * @var int
     **/
    public $author            = null;

    /**
     * Initializes Letter object instance
     *
     * @param int $id the letter id
     *
     * @return void
     **/
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('Letter');

        parent::__construct($id);
    }

    /**
     * Magic method for generating property values
     *
     * @param string $name the property name
     *
     * @return mixed the property value
     **/
    public function __get($name)
    {
        switch ($name) {
            case 'uri':
                $uri =  Uri::generate(
                    'letter',
                    array(
                        'id'       => sprintf('%06d', $this->id),
                        'date'     => date('YmdHis', strtotime($this->created)),
                        'slug'     => $this->slug,
                        'category' => \Onm\StringUtils::getTitle($this->author),
                    )
                );
                //'cartas-al-director/_AUTHOR_/_SLUG_/_DATE__ID_.html'
                return $uri;

                break;
            case 'slug':
                return \Onm\StringUtils::getTitle($this->title);

                break;
            case 'photo':
                return new \Photo($this->image);

                break;

            case 'summary':
                $summary = substr(strip_tags($this->body), 0, 200);
                $pos = strripos($summary, ".");

                if ($pos > 100) {
                    $summary = substr($summary, 0, $pos).".";
                } else {
                    $summary = substr($summary, 0, strripos($summary, " "));
                }

                return $summary;

                break;
            default:
                return parent::__get($name);
                break;
        }
    }

    /**
     * Creates a new letter given an array of data
     *
     * @param array $data the letter information
     *
     * @return int the new letter id, if it was created
     * @return boolean false if the letter was not created
     **/
    public function create($data)
    {
        $data['position']   =  1;
        $data['category'] = 0;

        parent::create($data);

        $sql = 'INSERT INTO letters ( `pk_letter`, `author`, `email`) '.
                    ' VALUES (?,?,?)';

        $values = array(
            $this->id,
            $data['author'],
            $data['email'],
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            return false;
        }

        if (array_key_exists('image', $data) && !empty($data['image'])) {
            $this->setProperty('image', $data['image']);
        }
        if (array_key_exists('url', $data) && !empty($data['url'])) {
            $this->setProperty('url', $data['url']);
        }

        return $this->id;
    }

    /**
     * Loads the letter information given its id
     *
     * @param int $id the letter id
     *
     * @return Letter the letter instance
     **/
    public function read($id)
    {
        // If no valid id then return
        if (((int) $id) <= 0) return;

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM contents LEFT JOIN contents_categories ON pk_content = pk_fk_content '
                .'LEFT JOIN letters ON pk_content = pk_letter WHERE pk_content=?',
                [ $id ]
            );

            if (!$rs) {
                return false;
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }

        $this->load($rs);

        return $this;
    }

    /**
     * Overloads the object properties with an array of the new ones
     *
     * @param array $properties the list of properties to load
     *
     * @return void
     **/
    public function load($properties)
    {
        parent::load($properties);

        if (is_array($this->params) && array_key_exists('ip', $this->params)) {
            $this->ip = $this->params['ip'];
        }

        $this->image = $this->getProperty('image');
        if (!empty($this->image)) {
            $this->photo = $this->image;
        }

        $this->loadAllContentProperties();
    }

    /**
     * Updates the letter information given an array of data
     *
     * @param array $data the data array
     *
     * @return boolean true if the letter was updated
     **/
    public function update($data)
    {
        $data['position'] =  1;
        $data['category'] = 0;

        parent::update($data);
        $sql = "UPDATE letters
                SET `author`=  ?, `email`=  ?
                WHERE pk_letter = ?";

        $values = array(
            $data['author'],
            $data['email'],
            $data['id']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            return false;
        }

        if (array_key_exists('image', $data) && !empty($data['image'])) {
            $this->setProperty('image', $data['image']);
        }
        if (array_key_exists('url', $data) && !empty($data['url'])) {
            $this->setProperty('url', $data['url']);
        }

        return true;
    }

    /**
     * Removes permanently the letter
     *
     * @param int $id the letter id to delete
     *
     * @return boolean true if the letter was deleted
     **/
    public function remove($id)
    {
        parent::remove($id);

        $sql = 'DELETE FROM letters WHERE pk_letter ='.($id);

        if ($GLOBALS['application']->conn->Execute($sql)===false) {
            return;
        }
    }

    /**
     * Determines if the content of a comment has bad words
     *
     * @param  array $data the data from the comment
     * @return int higher values means more bad words
     **/
    public function hasBadWords($data)
    {
        $text = $data['title'] . ' ' . $data['body'];

        if (isset($data['author'])) {
            $text.= ' ' . $data['author'];
        }
        $weight = \Onm\StringUtils::getWeightBadWords($text);

        return $weight > 100;
    }

    /**
     * Renders the poll
     *
     * @param arrray $params parameters for rendering the content
     *
     * @return string the generated HTML
     **/
    public function render($params, $tpl = null)
    {
        $tpl = new Template(TEMPLATE_USER);

        $tpl->assign('item', $this);
        $tpl->assign('cssclass', $params['cssclass']);

        try {
            $html = $tpl->fetch('frontpage/contents/_content.tpl');
        } catch (\Exception $e) {
            $html = '';
        }

        return $html;
    }
}
