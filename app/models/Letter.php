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
 * @author     Sandra Pereira <sandra@openhost.es>
 **/
/*
 CREATE TABLE IF NOT EXISTS `letters` (
  `pk_letter` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `author` varchar(250)  DEFAULT NULL,
  `body` text ,
  PRIMARY KEY (`pk_letter`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

 */
class Letter extends Content
{

	var $pk_letter     = NULL;
    var $author        = NULL;
	var $body          = NULL;

    private static $instance    = NULL;


    function __construct($id=null) {

        parent::__construct($id);

        if(is_numeric($id)) {
            $this->read($id);
        }

        $this->content_type = __CLASS__;

    }

    function get_instance() {

        if( is_null(self::$instance) ) {
            self::$instance = new Letter();
            return self::$instance;

        } else {

            return self::$instance;
        }
    }

	public function __get($name) {

        switch ($name) {
            case 'uri': {
				$uri =  Uri::generate('letter',
                            array(
                                'id' => sprintf('%06d',$this->id),
                                'date' => date('YmdHis', strtotime($this->created)),
                                'slug' => $this->slug,
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

    function create($data) {

        $data['content_status'] = $data['available'];
        $data['position']   =  1;
        $data['category'] = 0;

        parent::create($data);

        $sql = 'INSERT INTO letters ( `pk_letter`, `author`, `email`, `body`) '.
                    ' VALUES (?,?,?,?)';

        $values = array( $this->id, $data['author'], $data['email'], $data['body']);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

           return(false);
        }

       return($this->id);
    }

    function read($id) {
        parent::read($id);

        $sql = "SELECT * FROM letters WHERE pk_letter = ? ";

        $rs = $GLOBALS['application']->conn->Execute( $sql, array($id) );
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
      $this->load( $rs->fields );
      $this->ip = $this->params['ip'];

    }


    function update($data) {
        $data['content_status'] = $data['available'];
        $data['position']   =  1;
        $data['category'] = 0;


        parent::update($data);
        $sql = "UPDATE letters SET `author`=  ?,
                                   `email`=  ?,
                                   `body` = ?
                            WHERE pk_letter = ?";

        $values = array($data['author'],$data['email'],$data['body'],$data['id'] );

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }

        $GLOBALS['application']->dispatch('onAfterUpdateLetter', $this);
    }

    function remove($id) { //Elimina definitivamente
        parent::remove($id);

        $sql = 'DELETE FROM letters WHERE pk_letter ='.($id);

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
    }

    /**
     * Determines if the content of a comment has bad words
     *
     * @access public
     * @param mixed $data, the data from the comment
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


    function saveLetter($data) {

        $_SESSION['username'] = $data['author'];
        $_SESSION['userid'] = 'user';
        $letter = new Letter();

        // Prevent XSS attack
        $data = array_map('strip_tags', $data);

        if($letter->hasBadWorsComment($data)) {
            return "Su comentario fue rechazado debido al uso de palabras malsonantes.";
        }

        $ip = Application::getRealIP();
        $data["params"] = array('ip'=> $ip);
        if($letter->create( $data ) ) {
            return "Su carta ha sido guardada y está pendiente de publicación.";
        }

    return "Su carta no ha sido guardado.\nAsegúrese de cumplimentar correctamente todos los campos.";


    }


}
