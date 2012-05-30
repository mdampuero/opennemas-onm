<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Handles all the CRUD operations over letters.
 *
 * @package    Onm
 * @subpackage Model
 **/
class Letter extends Content
{
    public $pk_letter         = null;
    public $author            = null;
    public $body              = null;

    private static $_instance = null;

    public function __construct($id = null)
    {
        parent::__construct($id);

        if (is_numeric($id)) {
            $this->read($id);
        }

        $this->content_type = __CLASS__;

    }

    public function get_instance()
    {
        if ( is_null(self::$_instance) ) {
            self::$_instance = new Letter();

            return self::$_instance;

        } else {

            return self::$_instance;
        }
    }

    public function __get($name)
    {
        switch ($name) {
            case 'uri': {
                $uri =  Uri::generate('letter',
                    array(
                        'id'       => sprintf('%06d',$this->id),
                        'date'     => date('YmdHis', strtotime($this->created)),
                        'slug'     => $this->slug,
                        'category' => StringUtils::get_title($this->author),
                    )
                );
                //'cartas-al-director/_AUTHOR_/_SLUG_/_DATE__ID_.html'

                return $uri;

                break;
            }
            case 'slug': {
                return StringUtils::get_title($this->title);
                break;
            }
            default: {
                break;
            }
        }
    }

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
            \Application::logDatabaseError();

           return false;
        }

       return $this->id;
    }

    public function read($id)
    {
        parent::read($id);

        $sql = "SELECT * FROM letters WHERE pk_letter = ? ";

        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));
        if (!$rs) {
            \Application::logDatabaseError();

            return false;
        }
      $this->load($rs->fields);
      $this->ip = $this->params['ip'];

    }

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
            \Application::logDatabaseError();

            return;
        }

        $GLOBALS['application']->dispatch('onAfterUpdateLetter', $this);
    }

    public function remove($id) { //Elimina definitivamente
        parent::remove($id);

        $sql = 'DELETE FROM letters WHERE pk_letter ='.($id);

        if ($GLOBALS['application']->conn->Execute($sql)===false) {
            \Application::logDatabaseError();

            return;
        }
    }

    /**
     * Determines if the content of a comment has bad words
     *
     * @access public
     * @param  mixed    $data, the data from the comment
     * @return integer, higher values means more bad words
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

    public function saveLetter($data)
    {
        $_SESSION['username'] = $data['author'];
        $_SESSION['userid'] = 'user';
        $letter = new Letter();

        // Prevent XSS attack
        $data = array_map('strip_tags', $data);

        if ($letter->hasBadWorsComment($data)) {
            return "Su comentario fue rechazado debido al uso "
                ."de palabras malsonantes.";
        }

        $ip = Application::getRealIP();
        $data["params"] = array('ip'=> $ip);
        if ($letter->create($data)) {
            return "Su carta ha sido guardada y está pendiente de publicación.";
        }

        return "Su carta no ha sido guardado.\nAsegúrese de cumplimentar "
            ."correctamente todos los campos.";
    }

}
