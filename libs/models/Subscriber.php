<?php
/**
 * Defines the Subscriber class
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
 */
class Subscriber
{
    /**
     * The subscriber id
     *
     * @var int
     */
    public $id = null;

    /**
     * The email of the subscriber
     *
     * @var string
     */
    public $email = null;

    /**
     * The name of the subscriber
     *
     * @var string
     */
    public $name = null;

    /**
     * The firstname of the user
     *
     * @var string
     */
    public $firstname = null;

    /**
     * The last name of the user
     *
     * @var string
     */
    public $lastname = null;

    /**
     * status=0 - (mail se le envio pero aun no le dio al link del correo)
     * status=1 - (tras recibir el mail, el usuario ha clicado en el link y se ha aceptado)
     * status=2 - (El administrador ha aceptado la solicitud)
     * status=3 - (El administrador ha deshabilitado el usuario)
     */
    public $status = null;

    /**
     * Flag to check if user will receive the newsletter
     */
    public $subscription = null;

    /**
     * Constructor
     *
     * @param int $id the subscriptor id
     *
     * @return void
     */
    public function __construct($id = null)
    {
        if (!is_null($id)) {
            $this->read($id);
        }
    }

    /**
     * Returns the Subscriber object overloaded
     *
     * @param array $properties the list of properties to overload
     *
     * @return Subscriber
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

        // Special properties
        if (isset($this->pk_pc_user)) {
            $this->id = $this->pk_pc_user;
        } else {
            $this->id = null;
        }
    }

    /**
     * Loads the subscriber instance given the subscriber id
     *
     * @param int $id the subscriber id
     *
     * @return Subscriber the object instance
     */
    public function read($id)
    {
        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM pc_users WHERE pk_pc_user = ?',
                [ $id ]
            );

            $this->load($rs);

            return $this;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    /**
     * Creates a new subscriber given an array of data
     *
     * @param array $data the array of data
     *
     * @return boolean true if the subscriber was created
     */
    public function create($data)
    {
        $data['status']       = (!isset($data['status'])) ? 0 : $data['status'];
        $data['subscription'] = (isset($data['subscription'])) ? $data['subscription'] : 1;
        $data['firstname']    = (isset($data['firstname'])) ? $data['firstname'] : "";
        $data['lastname']     = (isset($data['lastname'])) ? $data['lastname'] : "";

        $conn = getService('dbal_connection');

        try {
            $conn->insert("pc_users", [
                'email'        => $data['email'],
                'name'         => $data['name'],
                'firstname'    => $data['firstname'],
                'lastname'     => $data['lastname'],
                'status'       => $data['status'],
                'subscription' => $data['subscription'],
            ]);

            $this->id = $conn->lastInsertId();

            dispatchEventWithParams('newsletter_subscriptor.create', [ 'subscriptor' => $this ]);

            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Updates a subscriber given an array of data
     *
     * @param array   $data      the array of data
     * @param boolean $isBackend whether this action is called from backend
     *
     * @return boolean true if the subscriber was updated
     */
    public function update($data, $isBackend = false)
    {
        $data['subscription'] = (isset($data['subscription'])) ? $data['subscription'] : 1;

        $newData = [
            'subscription' => $data['subscription'],
            'status'       => $data['status'],
        ];
        if ($isBackend) {
            $newData = array_merge($newData, [
                'email'        => $data['email'],
                'name'         => $data['name'],
                'firstname'    => $data['firstname'],
                'lastname'     => $data['lastname'],
            ]);
        }

        try {
            getService('dbal_connection')->update(
                'pc_users',
                $newData,
                [ 'pk_pc_user' => (int) $data['id'] ]
            );

            $this->id = $data['id'];

            dispatchEventWithParams('newsletter_subscriptor.update', [ 'subscriptor' => $this ]);

            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Removes permanently a subscriber
     *
     * @param int $id the subscriber id to delete
     *
     * @return boolean true if the subscriber was deleted
     */
    public function delete($id)
    {
        if ((int) $id <= 0) {
            return false;
        }

        try {
            $rs = getService('dbal_connection')->delete(
                "pc_users",
                [ 'pk_pc_user' => (int) $id ]
            );

            $this->id = $id;
            dispatchEventWithParams('newsletter_subscriptor.delete', [ 'subscriptor' => $this ]);

            if (!$rs) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Fetches an user given an email
     *
     * @param string $email the user email
     *
     * @return Subscriber the object instance
     */
    public function getUserByEmail($email)
    {
        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                "SELECT * FROM pc_users WHERE email = ?",
                [ $email ]
            );

            $this->load($rs);

            return $this;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Fetches all the users given an search criteria
     *
     * @param string $filter    the SQL WHERE clause
     * @param int    $limit     how many users to fetch
     * @param string $_order_by the ORDER BY clause
     *
     * @return boolean true if the subscriber was created
     */
    public function getUsers($filter = null, $limit = null, $orderBy = 'name')
    {
        $items = [];
        $where = '';
        if (!empty($filter)) {
            $where = ' WHERE ' . $filter;
        }

        $sql = 'SELECT * FROM pc_users ' . $where . ' ORDER BY ' . $orderBy;

        if (!empty($limit)) {
            $sql .= ' LIMIT ' . $limit;
        }

        try {
            $rs = getService('dbal_connection')->fetchAll($sql);

            foreach ($rs as $item) {
                $user = new Subscriber();
                $user->load($item);
                $items[] = $user;
            }

            return $items;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Sets the status to a given value
     *
     * @param int $id the subscriber id
     * @param int $status the status value
     *
     * @return boolean true if the subscriber status property was changed
     */
    public function setStatus($id, $status)
    {
        try {
            getService('dbal_connection')->update(
                "pc_users",
                [ 'status' => $status ],
                [ 'pk_pc_user' => (int) $id ]
            );

            $this->id = $id;
            dispatchEventWithParams('newsletter_subscriptor.update', [ 'subscriptor' => $this ]);

            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Sets the status to a given value
     *
     * @param int $id the subscriber id
     * @param int $status the status value
     *
     * @return boolean true if the subscriber status property was changed
     */
    public function setSubscriptionStatus($id, $status)
    {
        try {
            getService('dbal_connection')->update(
                "pc_users",
                [ 'subscription' => $status ],
                [ 'pk_pc_user' => (int) $id ]
            );

            $this->id = $id;
            dispatchEventWithParams('newsletter_subscriptor.update', [ 'subscriptor' => $this ]);

            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Checks if exists a user with an email registered
     *
     * @param string $email the email address
     *
     * @return boolean true if the subscriber is already registered
     */
    public function existsEmail($email)
    {
        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT count(*) AS num FROM `pc_users` WHERE email = ?',
                [ $email ]
            );

            return (array_key_exists('num', $rs) && $rs['num'] > 0);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Returns the number of subscriber given a search criteria
     *
     * @param string $where the WHERE clause
     *
     * @return int
     */
    public function countUsers($where = null)
    {
        $sql = 'SELECT count(*) as num FROM pc_users';
        if (!empty($where)) {
            $sql .= ' WHERE ' . $where;
        }

        try {
            $rs = getService('dbal_connection')->fetchAssoc($sql);

            return $rs['num'];
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return 0;
        }
    }
}
