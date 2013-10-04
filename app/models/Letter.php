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
     * The letter body
     *
     * @var string
     **/
    public $body              = null;

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
                        'category' => StringUtils::get_title($this->author),
                    )
                );
                //'cartas-al-director/_AUTHOR_/_SLUG_/_DATE__ID_.html'
                return $uri;

                break;
            case 'slug':
                return StringUtils::get_title($this->title);

                break;
            case 'content_type_name':
                return 'Letter';
                break;
            case 'photo':
                return $this->getPhoto();


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
        $data['content_status'] = $data['available'];
        $data['position']   =  1;
        $data['category'] = 0;

        parent::create($data);

        $sql = 'INSERT INTO letters ( `pk_letter`, `author`, `email`, `body`) '.
                    ' VALUES (?,?,?,?)';

        $values = array(
            $this->id,
            $data['author'],
            $data['email'],
            $data['body']
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
        parent::read($id);

        $sql = "SELECT * FROM letters WHERE pk_letter = ? ";

        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));
        if (!$rs) {
            return false;
        }
        $this->load($rs->fields);
        $this->ip = $this->params['ip'];

        $this->loadAllContentProperties();
        if (!empty($this->image)) {
            $this->photo = $this->photo;
        }
        $this->summary;

        return $this;
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
        $data['content_status'] = $data['available'];
        $data['position']   =  1;
        $data['category'] = 0;

        parent::update($data);
        $sql = "UPDATE letters SET `author`=  ?,
                                   `email`=  ?,
                                   `body` = ?
                            WHERE pk_letter = ?";

        $values = array(
            $data['author'],
            $data['email'],
            $data['body'],
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
     * Returns the Photo object that represents the user avatar
     *
     * @return Photo the photo object
     **/
    public function getPhoto()
    {
        return new \Photo($this->image);
    }

    /**
     * Determines if the content of a comment has bad words
     *
     * @param  array $data the data from the comment
     * @return int higher values means more bad words
     **/
    public function hasBadWorsComment($data)
    {

        $text = $data['title'] . ' ' . $data['body'];

        if (isset($data['author'])) {
            $text.= ' ' . $data['author'];
        }
        $weight = StringUtils::getWeightBadWords($text);

        return $weight > 100;
    }

    /**
     * Saves a letter given an array of data
     *
     * @param array $data the new letter information
     *
     * @return string
     **/
    public function saveLetter($data)
    {
        $_SESSION['username'] = $data['author'];
        $_SESSION['userid'] = 'user';
        $letter = new Letter();

        // Prevent XSS attack
        $data = array_map('strip_tags', $data);
        $data['body'] = nl2br($data['body']);

        if ($letter->hasBadWorsComment($data)) {
            return "Su comentario fue rechazado debido al uso "
                ."de palabras malsonantes.";
        }

        $ip = getRealIp();
        $data["params"] = array('ip'=> $ip);

        if ($letter->create($data)) {

            return "Su carta ha sido guardada y está pendiente de publicación.";
        }

        return "Su carta no ha sido guardado.\nAsegúrese de cumplimentar "
            ."correctamente todos los campos.";
    }

    /**
     * Renders the poll
     *
     * @param arrray $params parameters for rendering the content
     * @param Template $smarty the Template object instance
     *
     * @return string the generated HTML
     **/
    public function render($params, $smarty)
    {
        //  if (!isset($tpl)) {
            $tpl = new Template(TEMPLATE_USER);
        //}

        $tpl->assign('item', $this);
        $tpl->assign('cssclass', $params['cssclass']);

        try {
            $html = $tpl->fetch('frontpage/contents/_content.tpl');
        } catch (\Exception $e) {
            $html = 'Letter not available';
        }

        return $html;
    }
}
