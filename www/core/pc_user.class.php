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
 * PC_User
 * 
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: pc_user.class.php 1 2009-10-19 09:36:06Z vifito $
 */
class PC_User
{
    public $id = null;
    public $login = null;
    public $password = null;
    public $email = null;
    public $name = null;
    public $firstname = null;
    public $lastname = null;
    public $address = null;
    public $phone = null;
    public $gender = null;
    public $date_nac = null;
    public $id_user_group = null;
     
    /**
     * status=0 - (mail se le envio pero aun no le dio al link del correo)
     * status=1 - (tas recibir el mail, el usuario ha clicado en el link y se ha aceptado)
     * status=2 - (El administrador ha aceptado la solicitud)
     * status=3 - (El administrador ha deshabilitado el usuario)
     */ 
    public $status = null;
    
    /**
     * Flag to check if user will receive the newsletter
     */
    public $subscription = null;
    
    public $_errors = array();
    
    private $_tableName = '`pc_users`';

    private static $instance    = NULL;

     
    public function PC_User($id=null)
    {
        if(!is_null($id)) {
            $this->read($id);
        }
    }

    public function __construct($id=null)
    {
        $this->PC_User($id);
    }

    function get_instance() {

        if( is_null(self::$instance) ) {
            self::$instance = new PC_User();
            return self::$instance;

        } else {

            return self::$instance;
        }
    }

    public function create($data)
    {
        if( !$this->validate($data) ) {
            return false;
        }
        
        // FIXME: O envío tería que ir ao final cando se confirme a inserción na base de datos
        // Outra cousa distinta do envío é a xeración do código
        $code = $this->sendmail($data); 
        $data['status'] = (!isset($data['status']))? 0: $data['status'];
        
        //Manejar la fecha de nacimiento. 
        $fnac = explode('/', $data['fechaNacimientoDA']);
        // Vén con / para que valide con jsvalidate
                
        $fnac = array_reverse($fnac);
        $data['fechaNacimientoDA'] = implode("-",$fnac);
        
        // FIXME: 
        $data['id_user_group'] = 1; //Provisional
        
        // WARNING!!! By default, subscription=1 --> por requisito Xornal
        $data['subscription'] = (isset($data['subscription']))? $data['subscription']: 1;
        
        // subscription have a default value "1"
        $sql = 'INSERT INTO ' . $this->_tableName . ' (`nick`, `password`, `email`,
                                                       `name`, `firstname`, `lastname`,
                                                       `dni`, `phone`, `movil`,
                                                       `date_nac`, `city`, `country`,
                                                       `fk_user_group`, `status`, `code`,
                                                       `subscription`) VALUES
            (?,?,?, ?,?,?, ?,?,?, ?,?,?, ?,?,?, ?)';
        $values = array( $data['nickDA'], md5($data['passDA']), $data['emailDA'],
                         $data['nombreDA'], $data['apellidoDA'],$data['segApellidoDA'], 
                         $data['dniDA'], $data['telefDA'], $data['movilDA'],
                         $data['fechaNacimientoDA'], $data['poblacionDA'], $data['paisDA'],
                         $data['id_user_group'], $data['status'], $code,
                         $data['subscription']);               
        
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            var_dump($error_msg);
            die();
            
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return FALSE;
        }
        
        $this->id = $GLOBALS['application']->conn->Insert_ID();
        
        return TRUE;
    }

    public function read($id)
    {
        $sql = 'SELECT * FROM ' . $this->_tableName . ' WHERE pk_user = ?';
        $rs = $GLOBALS['application']->conn->Execute( $sql, array($id) );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
  
        $this->load($rs->fields);
    }
    
    // FIXME: check funcionality
    public function load($properties)
    {
        if(is_array($properties)) {
            foreach($properties as $k => $v) {
                if( !is_numeric($k) ) {
                    $this->{$k} = $v;
                }
            }
        } elseif(is_object($properties)) {
            $properties = get_object_vars($properties);
            foreach($properties as $k => $v) {
                if( !is_numeric($k) ) {
                    $this->{$k} = $v;
                }
            }
        }
        
        // Special properties
        $this->id = $this->pk_user;                   
    }            

    /**
     *
    */
    public function update($data, $isBackend=FALSE)
    {                
        if( !$this->validate($data) ) {
            return FALSE;
        }
        
        // Check if date has a format dd/MM/YYYY or YYYY-MM-dd 
        if( preg_match('@/@', $data['fechaNacimientoDA']) ) {
            //Manejar la fecha de nacimiento. 
            $fnac = explode('/', $data['fechaNacimientoDA']);
            // Vén con / para que valide con jsvalidate
            $fnac = array_reverse($fnac);
            $data['fechaNacimientoDA'] = implode("-",$fnac);
        }
        
        // FIXME: 
        $data['id_user_group'] = 1; //Provisional
        
        $values = array();
        
        // Warning!!!: don't update nick nor email values on frontend
        if(isset($data['passDA']) && (strlen ($data['passDA']) > 0)){
            if($isBackend) {
                $sql = 'UPDATE ' . $this->_tableName . ' SET `subscription`=?, `status`=?, `nick`=?, `password`= ?, `email`=?,';
            } else {
                $sql = 'UPDATE ' . $this->_tableName . ' SET `password`= ?,';
            }
            
            $sql .= ' `name`=?, `firstname`=?, `lastname`=?, `movil`=?, `phone`=?, `dni`=?,
                      `date_nac`=?, `city`=?,`country`=?, `fk_user_group`=? WHERE pk_user=?';
            
            if($isBackend) {
                $data['subscription'] = (isset($data['subscription']))? $data['subscription']: 1;
                $values = array($data['subscription'], $data['status'], $data['nickDA'], md5($data['passDA']), $data['emailDA']);
            } else {
                $values = array(md5($data['passDA']));
            }
            
            $values = array_merge($values, array( $data['nombreDA'],
                                       $data['apellidoDA'],
                                       $data['segApellidoDA'],
                                       $data['movilDA'],
                                       $data['telefDA'],
                                       $data['dniDA'],
                                       $data['fechaNacimientoDA'],
                                       $data['poblacionDA'],
                                       $data['paisDA'],
                                       $data['id_user_group'] ));
                                             
        }else{
            if($isBackend) {
                $sql = 'UPDATE ' . $this->_tableName . ' SET `subscription`=?, `status`=?, `nick`=?, `email`=?, ';
            } else {
                $sql = 'UPDATE pc_users SET ';
            }
            
            $sql .= " `name`=?, `firstname`=?, `lastname`=?,
                      `movil`=?, `phone`=?,
                      `dni`=?,`date_nac`=?,
                      `city`=?,`country`=?,
                      `fk_user_group`=?
                WHERE pk_user=".intval($data['id']);
            if($isBackend) {
                $data['subscription'] = (isset($data['subscription']))? $data['subscription']: 1;
                $values = array($data['subscription'], $data['status'], $data['nickDA'], $data['emailDA']);
            }

            $values = array_merge($values, array( $data['nombreDA'],
                                       $data['apellidoDA'],
                                       $data['segApellidoDA'],
                                       $data['movilDA'],
                                       $data['telefDA'],
                                       $data['dniDA'],
                                       $data['fechaNacimientoDA'],
                                       $data['poblacionDA'],
                                       $data['paisDA'],
                                       $data['id_user_group'] ));
        }
        
        $this->id = $data['id'];
        
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return FALSE;
        }
        
        return TRUE;
    }
    
    public function validate( $data )
    {
        $this->_errors = array();
        
        // Lengitud mínima 6 caracteres
        if( isset($data['nickDA']) && !preg_match('/^[a-zA-ZáéíóúñÁÉÍÓÚÑüÜ]{6,}$/i', $data['nickDA']) ) {
            $this->_errors[] = 'Tu nick tiene que tener un mínimo de 6 caracteres (sin espacios ni números).';
        }
        
        if( isset($data['passDA']) && ($data['repPasswordDA'] != $data['passDA']) &&
           ($data['passDA'] == $data['nickDA']) && !preg_match('/^.{6,}$/i', $data['passDA']) ) {
            $this->_errors[] = 'Tu contraseña tiene que tener un mínimo de 6 caracteres, ser igual a la contraseña de confirmación y no puede ser igual que tu nick.';
        }                
        
        if( isset($data['emailDA']) && !preg_match('/^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\._\-]+$/i', $data['emailDA']) ) {
            $this->_errors[] = 'Tu dirección de correo electrónico debe ser válida. Por ejemplo luis@xornal.com';
        }
        
        if( !preg_match('/^[a-zA-ZáéíóúñÁÉÍÓÚÑüÜçÇ \-]+$/i', $data['nombreDA']) ) {
            $this->_errors[] = 'El campo nombre sólo acepta caracteres.';
        }
        
        if( !preg_match('/^[a-zA-ZáéíóúñÁÉÍÓÚÑüÜçÇ \-]+$/i', $data['apellidoDA']) ) {
            $this->_errors[] = 'El campo apellido sólo acepta caracteres.';
        }
        
        if( isset($data['dniDA']) && (strlen($data['dniDA']) > 0)
                && !preg_match('/^[0-9a-z]{2,20}$/i', $data['dniDA']) ) {
                /* && !preg_match('/^[0-9]{2}\.?[0-9]{3}\.?[0-9]{3}\-?[a-z]$/i', $data['dniDA']) ) { */
            $this->_errors[] = 'Este campo debe ser un DNI (12345678L), NIE (X1234567L), pasaporte o DIE válido.';
        }
        
        /* if( isset($data['telefDA']) && (strlen($data['telefDA']) > 0)
                && !preg_match('/^[0-9 \-\+]+$/', $data['telefDA']) ) {
            return FALSE;
        }
        
        if( isset($data['movilDA']) && (strlen($data['movilDA']) > 0)
                && !preg_match('/^[0-9 \-\+]+$/', $data['movilDA']) ) {
            return FALSE;
        } */
        
        if( isset($data['fechaNacimientoDA']) && (strlen($data['fechaNacimientoDA']) > 0)
                && !preg_match('/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{1,4}$/', $data['fechaNacimientoDA']) ) {
            $this->_errors[] = 'Tu nick tiene que tener un mínimo de 6 caracteres (sin espacios ni números).';
        }        
        
        return count($this->_errors) == 0;
    }
    
    /**
     *
    */
    public function change_password($data)
    {
        // Warning!!!: don't update nick nor email values 
        if(isset($data['passDA']) && (strlen ($data['passDA']) > 0)){
            $sql = 'UPDATE ' . $this->_tableName . ' SET `password`= ?
                    WHERE pk_user=?';
            
            $values = array( md5($data['passDA'], $data['id']) );
        }
        $this->id = $data['id'];
        
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
    }
    
    /**
     * Update subscription for a "plan conecta" user
     *
     * @param int $subscription
     * @param int $user_id, optional user id, if it's null then use $this->id
     * @return PC_User Return this instance
     */
    public function updateSubscription($subscription, $user_id=null)
    {
        if(is_null($user_id)) {
            $user_id = $this->id;
        }
        
        $sql = 'UPDATE ' . $this->_tableName . ' SET `subscription`=? WHERE `pk_user`=?';
        
        if($GLOBALS['application']->conn->Execute($sql, array($subscription, $user_id)) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
            return;
        }        
        
        // update value
        $this->subscription = $subscription;
        return $this;
    }
    
    /**
     * Recuperar un usuario por email
    */
    public function getUserByEmail($email)
    {
        $sql = 'SELECT * FROM ' . $this->_tableName . ' WHERE `email`=?';
        $rs  = $GLOBALS['application']->conn->Execute( $sql, array($email) );        
        
        if ($rs===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
            return null;
        }
        
        $this->load($rs->fields);
        return $this;
    }
    
    /**
     * The letter l (lowercase L) and the number 1
     * have been removed, as they can be mistaken
     * for each other.
     */
    public function createRandomPassword($len=7)
    {
        $chars = "abcdefghijkmnopqrstuvwxyz023456789";
        srand((double)microtime()*1000000);
        $i = 0;
        $pass = '' ;
        
        while ($i <= $len) {
            $num = rand() % 33;
            $tmp = substr($chars, $num, 1);
            $pass = $pass . $tmp;
            $i++;
        }
        
        return $pass;
    }    

    public function delete($id)
    {
        $sql = 'DELETE FROM ' . $this->_tableName . ' WHERE pk_user='.intval($id);

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
    }
    
    public function set_status($id, $status)
    {     
        $sql = 'UPDATE ' . $this->_tableName . ' SET `status`='.$status.' WHERE pk_user='.intval($id);
        
        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
            return;
        }
    }

    public function login($data)
    {
        $sql = 'SELECT * FROM ' . $this->_tableName . ' WHERE `email`=?';
        //die( 'sql:'.$sql );
        $rs = $GLOBALS['application']->conn->Execute( $sql, array($data['email']) );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return FALSE;
        }      
        
        //$this->set_values($rs->fields);
        $this->load($rs->fields);        
        
        if($this->password === md5($data['password'])
                && ($this->status > 0)   // Falta correo de aceptación
                && ($this->status != 3)  // Rechazado polo administrador
           )
        {            
            return $this->id;
        }else{            
            return false;
        }
    }
    
    public function get_nick($id)
    {
        $sql = 'SELECT nick FROM ' . $this->_tableName . ' WHERE pk_user = '.intval($id);
        $rs = $GLOBALS['application']->conn->Execute( $sql );
        
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
        
            return;
        }
        
        return($rs->fields['nick']);
    }
    
    /**
     * Para las peticiones Ajax
    */
    public function exists_nick( $nick )
    {
       $sql = 'SELECT count(*) AS num FROM `pc_users` WHERE nick = "'.$nick.'"';
       $rs = $GLOBALS['application']->conn->Execute( $sql );
        
       if (!$rs) {
           $error_msg = $GLOBALS['application']->conn->ErrorMsg();
           $GLOBALS['application']->logger->debug('Error: '.$error_msg);
           $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
           return;
       }
        
       return ($rs->fields['num'] > 0);
    }
    
    
    public function exists_email( $email )
    {
       $sql = 'SELECT count(*) AS num FROM `pc_users` WHERE email = "'.$email.'"';
       $rs = $GLOBALS['application']->conn->Execute( $sql );

       if (!$rs) {
           $error_msg = $GLOBALS['application']->conn->ErrorMsg();
           $GLOBALS['application']->logger->debug('Error: '.$error_msg);
           $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

           return;
       }

       return( ($rs->fields['num'] > 0) );
    }     
     
     
    public function accept($data)
    {
        $sql = 'SELECT pk_user, code FROM ' . $this->_tableName . ' WHERE nick=\''.$data['user'].'\' and code=\''.strval($data['md5code']).'\'';
        //print 'sql:'.$sql;
        $rs = $GLOBALS['application']->conn->Execute( $sql );
        
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return false;
        }
        
        return $rs->fields['pk_user'];        
    }
    
    
    public function get_users($filter=null, $limit=null, $_order_by='name')
    {        
        $items = array();
        $_where = '1=1';
        if( !is_null($filter) ) {
            $_where = $filter;
        }
        
        $sql = 'SELECT * FROM ' . $this->_tableName . ' WHERE ' . $_where;
        $sql .= ' ORDER BY ' . $_order_by;
        
        if(!is_null($limit)) {
            $sql .= ' LIMIT ' . $limit;
        }        
        
        $rs = $GLOBALS['application']->conn->Execute($sql);
        if($rs !== false) {
            while(!$rs->EOF) {
                $user = new PC_User();
                $user->load($rs->fields);
                $items[] = $user;
                
                $rs->MoveNext();
            }
        } else {            
            return array(); // TODO: implement other control error system
        }
        
        return $items;
    }


     function get_all_authors() {
        if( is_null( $this->authors_name ) ) {
            $sql = 'SELECT pk_user, name, nick, email,status FROM `pc_users`';
            $rs = $GLOBALS['application']->conn->Execute( $sql );

            while(!$rs->EOF) {
                $author=new StdClass();
                $author->name = $rs->fields['name'];
                $author->nick = $rs->fields['nick'];
                $author->email = $rs->fields['email'];
                $author->status = $rs->fields['status'];
                $this->authors_name[ $rs->fields['pk_user'] ] = $author;

                $rs->MoveNext();
            }
        }
        return( $this->authors_name );
    }


    public function countUsers($where=null)
    {                
        $sql = 'SELECT count(*) FROM ' . $this->_tableName;
        if(!is_null($where)) {
            $sql .= ' WHERE ' . $where;
        }
        
        $rs = $GLOBALS['application']->conn->GetOne($sql);
        if($rs === false) {
            return 0;
        }
        
        return $rs;
    }
    
    public function getPager($items_page=40, $total=null)
    {
        if(is_null($total)) {
            $total = $this->countUsers();
        }
        
        // Pager
        $pager_options = array(
            'mode'        => 'Sliding',
            'perPage'     => $items_page,
            'delta'       => 4,
            'clearIfVoid' => true,
            'append'      => false,
            'path'        => '',
            'fileName'    => 'javascript:paginate(%d);',
            'urlVar'      => 'page',
            'totalItems'  => $total,
        );
        
        $pager = Pager::factory($pager_options);
        
        return $pager;
    }
    
    /**
     * Multiple update property
     *
     * @param int|array $id
     * @param string $property
     * @param mixed $value
    */
    public function mUpdateProperty($id, $property, $value=null)
    {
        $sql = 'UPDATE ' . $this->_tableName . ' SET `' . $property . '`=? WHERE pk_user=?';
        if(!is_array($id)) {
            $rs = $GLOBALS['application']->conn->Execute($sql, array($value, $id));
        } else {
            $data = array();
            foreach($id as $item) {
                $data[] = array($item['value'], $item['id']);
            }
            
            $rs = $GLOBALS['application']->conn->Execute($sql, $data);
        }
        
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return false;
        }
        
        return true;
    }
    
    /**
     *
    */
    function send_new_pass($email, $pass) {
        $body =<<< BODYMAIL
Solicitud de nueva contraseña en plan conecta del usuario $email
- Contraseña: $pass

Puedes cambiar la contraseña una vez iniciada la sesión en Conect@ en el siguiente enlace: 
@@@SITE_URL@@@conecta/cambio/

---
@@@SITE_URL@@@
BODYMAIL;

        $body = preg_replace('/@@@SITE_URL@@@/', SITE_URL, $body);

        $to = $email;
        
        $mail = new PHPMailer();
        $mail->SetLanguage('es');
        $mail->IsSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;
        
        $mail->Username = MAIL_USER;
        $mail->Password = MAIL_PASS;
        
        $mail->From = MAIL_USER;
        $mail->FromName = utf8_decode('Xornal de Galicia - Conect@');
        $mail->Subject  = utf8_decode("Cambio de contraseña en Conect@");
        $mail->Body = utf8_decode($body);
        
        $mail->AddAddress($to, $to);
        
        $mail->Send();
        
        return($code);        
    }
    
    /**
     *
    */
    function sendmail($data){        
        $t = gettimeofday(); //Sacamos los microsegundos 
        $micro = intval(substr($t['usec'], 0, 5)); //Le damos formato de 5digitos.
        $date = date("YmdHis").$micro;
        $code = md5($date);
        $body =<<< BODYMAIL
Solicitud de Alta en plan conecta de:
- Nombre y Apellidos: {$data['nombreDA']} {$data['apellidoDA']} {$data['segApellidoDA']}
- Teléfono: {$data['telefDA']}
- Móvil: {$data['movilDA']} 
- Población: {$data['poblacionDA']}
- Pais: {$data['paisDA']}

Muchas gracias por registrarte en Conect@ 
Puedes confirmar tu subscripción en el siguiente enlace: 

@@@SITE_URL@@@conecta/rexistro/accept/{$data['nickDA']}/$code.html

Haz clic en el enlace anterior para expresar tu conformidad con respecto a
las condiciones de participación. Si no puedes acceder a la dirección URL anterior completa,
cópiala y pega en la barra de direcciones de tu navegador.

---
@@@SITE_URL@@@
BODYMAIL;

        $body = preg_replace('/@@@SITE_URL@@@/', SITE_URL, $body);

        $to = $data['emailDA'];
        
        $mail = new PHPMailer();
        $mail->SetLanguage('es');
        $mail->IsSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;
        
        $mail->Username = MAIL_USER;
        $mail->Password = MAIL_PASS;
        
        $mail->From = MAIL_USER;
        $mail->FromName = utf8_decode('Xornal de Galicia - Conect@');
        $mail->Subject  = utf8_decode("Confirma tu solicitud de Alta en Conect@");
        $mail->Body = utf8_decode($body);
        
        $mail->AddAddress($to, $to);
        
        $mail->Send();
        
        return($code);
    }
}
