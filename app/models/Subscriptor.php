<?php
/**
 * Defines the Subscriptor class
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
 * Class Privilege
 *
 * Class to manage privileges
 *
 * @package    Model
 **/
class Subscriptor
{
    /**
     * The subscriptor id
     *
     * @var int
     **/
    public $id        = null;

    /**
     * The email of the subscriptor
     *
     * @var string
     **/
    public $email     = null;

    /**
     * The name of the subscriptor
     *
     * @var string
     **/
    public $name      = null;

    /**
     * The firstname of the user
     *
     * @var string
     **/
    public $firstname = null;

    /**
     * The last name of the user
     *
     * @var string
     **/
    public $lastname  = null;

    /**
     * status=0 - (mail se le envio pero aun no le dio al link del correo)
     * status=1 - (tras recibir el mail, el usuario ha clicado en el link y se ha aceptado)
     * status=2 - (El administrador ha aceptado la solicitud)
     * status=3 - (El administrador ha deshabilitado el usuario)
     **/
    public $status = null;

    /**
     * Flag to check if user will receive the newsletter
     **/
    public $subscription = null;

    /**
     * The list of errors
     *
     * @var string
     **/
    public $_errors = array();

    /**
     * The database table where the users are saved
     *
     * @var string
     **/
    private $tableName = '`pc_users`';

    /**
     * Constructor
     *
     * @param int $id the subscriptor id
     *
     * @return void
     **/
    public function __construct($id = null)
    {
        if (!is_null($id)) {
            $this->read($id);
        }
    }

    /**
     * Creates a new subscriptor given an array of data
     *
     * @param array $data the array of data
     *
     * @return boolean true if the subscriptor was created
     **/
    public function create($data)
    {
        $data['status'] = (!isset($data['status']))? 0: $data['status'];

        // WARNING!!! By default, subscription=1
        $data['subscription'] =
            (isset($data['subscription']))? $data['subscription']: 1;

        // By default first and last name are ""
        $data['firstname'] = (isset($data['firstname']))? $data['firstname']: "";
        $data['lastname'] = (isset($data['lastname']))? $data['lastname']: "";

        $sql = 'INSERT INTO ' . $this->tableName . ' (
                  `email`, `name`, `firstname`, `lastname`,
                 `status`, `subscription`) VALUES
                ( ?,?,?,?, ?,?)';
        $values = array( $data['email'],
                         $data['name'], $data['firstname'],$data['lastname'],
                         $data['status'], $data['subscription'] );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            return false;
        }

        $this->id = $GLOBALS['application']->conn->Insert_ID();

        dispatchEventWithParams('newsletter_subscriptor.create', array('subscriptor' => $this));

        return true;
    }

    /**
     * Loads the subscriptor instance given the subscriptor id
     *
     * @param int $id the subscriptor id
     *
     * @return Subscriptor the object instance
     **/
    public function read($id)
    {
        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE pk_pc_user = ?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));

        if (!$rs) {
            return null;
        }

        $this->load($rs->fields);

        return $this;
    }

    /**
     * Returns the Subscriptor object overloaded
     *
     * @param array $properties the list of properties to overload
     *
     * @return Subscriptor
     **/
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

        // Special properties
        if (isset ($this->pk_pc_user)) {
            $this->id = $this->pk_pc_user;
        } else {
            $this->id = null;
        }
    }

    /**
     * Updates a subscriptor given an array of data
     *
     * @param array   $data      the array of data
     * @param boolean $isBackend whether this action is called from backend
     *
     * @return boolean true if the subscriptor was updated
     **/
    public function update($data, $isBackend = false)
    {
        if ($isBackend) {
            $sql = 'UPDATE ' . $this->tableName
                 . ' SET `subscription`=?, `status`=?,'
                 . ' `email`=?, `name`=?, `firstname`=?, `lastname`=?  ';
        } else {
            $sql = 'UPDATE '.$this->tableName. ' SET `subscription`= ?, `status`=?';
        }

        $sql .= ' WHERE pk_pc_user=' . intval($data['id']);

        $data['subscription'] = (isset($data['subscription']))? $data['subscription']: 1;
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
            return false;
        }

        dispatchEventWithParams('newsletter_subscriptor.update', array('subscriptor' => $this));

        return true;
    }

    /**
     * Fetches an user given an email
     *
     * @param string $email the user email
     *
     * @return Subscriptor the object instance
     **/
    public function getUserByEmail($email)
    {
        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE `email`=?';
        $rs  = $GLOBALS['application']->conn->Execute($sql, array($email));

        if ($rs === false) {
            return null;
        }

        $this->load($rs->fields);

        return $this;
    }

    /**
     * Fetches all the users given an search criteria
     *
     * @param string $filter    the SQL WHERE clause
     * @param int    $limit     how many users to fetch
     * @param string $_order_by the ORDER BY clause
     *
     * @return boolean true if the subscriptor was created
     **/
    public function getUsers($filter = null, $limit = null, $_order_by = 'name')
    {
        $items = array();
        $_where = '1=1';
        if (!is_null($filter)) {
            $_where = $filter;
        }

        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE ' . $_where;
        $sql .= ' ORDER BY ' . $_order_by;

        if (!empty($limit)) {
            $sql .= ' LIMIT ' . $limit;
        }

        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
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

    /**
     * Removes permanently a subscriptor
     *
     * @param int $id the subscriptor id to delete
     *
     * @return boolean true if the subscriptor was deleted
     **/
    public function delete($id)
    {
        $sql = 'DELETE FROM ' . $this->tableName . ' WHERE pk_pc_user=?';
        $values = array(intval($id));

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            return false;
        }

        dispatchEventWithParams('newsletter_subscriptor.delete', array('subscriptor' => $this));

        return true;
    }

    /**
     * Sets the status to a given value
     *
     * @param int $id the subscriptor id
     * @param int $status the status value
     *
     * @return boolean true if the subscriptor status property was changed
     **/
    public function setStatus($id, $status)
    {
        $sql = 'UPDATE ' . $this->tableName
             . ' SET `status`='.$status.' WHERE pk_pc_user='.intval($id);

        if ($GLOBALS['application']->conn->Execute($sql)===false) {
            return false;
        }

        dispatchEventWithParams('newsletter_subscriptor.update', array('subscriptor' => $this));

        return true;
    }

    /**
     * Checks if exists a user with an email registered
     *
     * @param string $email the email address
     *
     * @return boolean true if the subscriptor is already registered
     **/
    public function existsEmail($email)
    {
        $sql = 'SELECT count(*) AS num FROM `pc_users` WHERE email = ?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array($email));

        if (!$rs) {
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
     *
     * @return boolean
    */
    public function mUpdateProperty($id, $property, $value = null)
    {
        $sql = 'UPDATE '.$this->tableName.' SET `'.$property.'`=? WHERE pk_pc_user=?';
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

        // dispatchEventWithParams('newsletter_subscriptor.update', array('subscriptor' => $this));

        if (!$rs) {
            return false;
        }

        return true;
    }

    /**
     * Returns the number of subscriptors given a search criteria
     *
     * @param string $where the WHERE clause
     *
     * @return int
     **/
    public function countUsers($where = null)
    {
        $sql = 'SELECT count(*) FROM ' . $this->tableName;
        if (!is_null($where)) {
            $sql .= ' WHERE ' . $where;
        }

        $rs = $GLOBALS['application']->conn->GetOne($sql);
        if ($rs === false) {
            return 0;
        }

        return $rs;
    }
}
