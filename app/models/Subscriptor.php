<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Class Privilege
 *
 * Class to manage privileges
 *
 * @package    Onm
 * @subpackage Newsletter
 * @author     Sandra Pereira <sandra@openhost.es>
 **/
class Subscriptor
{
    public $id        = null;

    public $email     = null;
    public $name      = null;
    public $firstname = null;
    public $lastname  = null;

    /**
     * status=0 - (mail se le envio pero aun no le dio al link del correo)
     * status=1 - (tas recibir el mail, el usuario ha clicado en
     *             el link y se ha aceptado)
     * status=2 - (El administrador ha aceptado la solicitud)
     * status=3 - (El administrador ha deshabilitado el usuario)
     **/
    public $status = null;

    /**
     * Flag to check if user will receive the newsletter
     **/
    public $subscription = null;

    public $_errors = array();

    private $_tableName = '`pc_users`';

    private static $_instance    = null;

    /**
     * Constructor
     *
     * @see Privilege::Privilege
     * @param int $id Privilege Id
     **/
    public function __construct($id=null)
    {
        if (!is_null($id)) {
            $this->read($id);
        }
    }

    public function get_instance()
    {
        if ( is_null(self::$_instance) ) {
            self::$_instance = new Subscriptor();

            return self::$_instance;

        } else {

            return self::$_instance;
        }
    }

    public function create($data)
    {
        $data['status'] = (!isset($data['status']))? 0: $data['status'];

        // WARNING!!! By default, subscription=1
        $data['subscription'] =
            (isset($data['subscription']))? $data['subscription']: 1;

        // By default first and last name are ""
        $data['firstname'] = (isset($data['firstname']))? $data['firstname']: "";
        $data['lastname'] = (isset($data['lastname']))? $data['lastname']: "";

        $sql = 'INSERT INTO ' . $this->_tableName . ' (
                  `email`, `name`, `firstname`, `lastname`,
                 `status`, `subscription`) VALUES
                ( ?,?,?,?, ?,?)';
        $values = array( $data['email'],
                         $data['name'], $data['firstname'],$data['lastname'],
                         $data['status'], $data['subscription'] );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return false;
        }

        $this->id = $GLOBALS['application']->conn->Insert_ID();

        return true;
    }

    public function read($id)
    {
        $sql = 'SELECT * FROM ' . $this->_tableName . ' WHERE pk_pc_user = ?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));

        if (!$rs) {
            \Application::logDatabaseError();

            return;
        }

        $this->load($rs->fields);
    }

    public function load($properties)
    {
        if (is_array($properties)) {
            foreach ($properties as $k => $v) {
                if ( !is_numeric($k) ) {
                    $this->{$k} = $v;
                }
            }
        } elseif (is_object($properties)) {
            $properties = get_object_vars($properties);
            foreach ($properties as $k => $v) {
                if ( !is_numeric($k) ) {
                    $this->{$k} = $v;
                }
            }
        }

        // Special properties
        if (isset ($this->pk_pc_user)) {
            $this->id = $this->pk_pc_user;
        } else {
            $this->id = null;
        }
    }

    /**
     *
    */
    public function update($data, $isBackend = false)
    {
        if ($isBackend) {
            $sql = 'UPDATE ' . $this->_tableName
                 . ' SET `subscription`=?, `status`=?,'
                 . ' `email`=?, `name`=?, `firstname`=?, `lastname`=?  ';
        } else {
            $sql = 'UPDATE ' . $this->_tableName
                 . ' SET `subscription`= ?, `status`=?';
        }

        $sql .= ' WHERE pk_pc_user=' . intval($data['id']);

        $data['subscription'] =
            (isset($data['subscription']))? $data['subscription']: 1;
        if (!$isBackend) {
            $values = array($data['subscription'],$data['status']);
        } else {

            $values =   array(
                $data['subscription'],
                $data['status'],
                $data['email'],
                $data['name'],
                $data['firstname'],
                $data['lastname'],
            );
        }
        $this->id = $data['id'];

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return false;
        }

        return true;
    }

    /**
     * Recuperar un usuario por email
    */
    public function getUserByEmail($email)
    {
        $sql = 'SELECT * FROM ' . $this->_tableName . ' WHERE `email`=?';
        $rs  = $GLOBALS['application']->conn->Execute($sql, array($email));

        if ($rs===false) {
            \Application::logDatabaseError();

            return null;
        }

        $this->load($rs->fields);

        return $this;
    }

    public function get_users($filter=null, $limit=null, $_order_by='name')
    {
        $items = array();
        $_where = '1=1';
        if ( !is_null($filter) ) {
            $_where = $filter;
        }

        $sql = 'SELECT * FROM ' . $this->_tableName . ' WHERE ' . $_where;
        $sql .= ' ORDER BY ' . $_order_by;

        if (!is_null($limit)) {
            $sql .= ' LIMIT ' . $limit;
        }

        $rs = $GLOBALS['application']->conn->Execute($sql);
        if ($rs !== false) {
            while (!$rs->EOF) {
                $user = new Subscriptor();
                $user->load($rs->fields);
                $items[] = $user;

                $rs->MoveNext();
            }
        } else {
            return array();
        }

        return $items;
    }

    public function delete($id)
    {
        $sql = 'DELETE FROM ' . $this->_tableName
             . ' WHERE pk_pc_user=?';
        $values = array(intval($id));

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return false;
        }
        return true;
    }

    public function set_status($id, $status)
    {
        $sql = 'UPDATE ' . $this->_tableName
             . ' SET `status`='.$status.' WHERE pk_pc_user='.intval($id);

        if ($GLOBALS['application']->conn->Execute($sql)===false) {
            \Application::logDatabaseError();

            return;
        }
    }

    public function exists_email($email)
    {
        $sql = 'SELECT count(*) AS num '
            . 'FROM `pc_users` WHERE email = "'.$email.'"';
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            \Application::logDatabaseError();

            return;
        }

        return ($rs->fields['num'] > 0);
    }

    /**
     * Multiple update property
     *
     * @param int|array $id
     * @param string    $property
     * @param mixed     $value
    */
    public function mUpdateProperty($id, $property, $value=null)
    {
        $sql = 'UPDATE ' . $this->_tableName
             . ' SET `' . $property . '`=? WHERE pk_pc_user=?';
        if (!is_array($id)) {
            $values = array($value, $id);
            $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        } else {
            $data = array();
            foreach ($id as $item) {
                $data[] = array($item['value'], $item['id']);
            }

            $rs = $GLOBALS['application']->conn->Execute($sql, $data);
        }

        if (!$rs) {
            \Application::logDatabaseError();

            return false;
        }

        return true;
    }

    public function countUsers($where=null)
    {
        $sql = 'SELECT count(*) FROM ' . $this->_tableName;
        if (!is_null($where)) {
            $sql .= ' WHERE ' . $where;
        }

        $rs = $GLOBALS['application']->conn->GetOne($sql);
        if ($rs === false) {
            return 0;
        }

        return $rs;
    }

    public function getPager($items_page=40, $total=null)
    {
        if (is_null($total)) {
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

}
