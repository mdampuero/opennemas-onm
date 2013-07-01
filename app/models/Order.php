<?php
/**
 * Contains the Order class
 *
 * This file is part of the Onm package.
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Model
 **/

/**
 * Handles all CRUD actions over Orders.
 *
 * @package Model
 **/
class Order
{
    /**
     * Loads the order information given its id
     *
     * @param int $id the order id
     *
     * @return order the order object instance
     **/
    public function read($id)
    {
        $sql = 'SELECT * FROM orders WHERE id = ?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array(intval($id)));

        if (!$rs) {
            \Application::logDatabaseError();

            return;
        }

        $this->id             = $rs->fields['id'];
        $this->user_id        = $rs->fields['user_id'];
        $this->content_id     = $rs->fields['content_id'];
        $this->created        = $rs->fields['created'];
        $this->payment_id     = $rs->fields['payment_id'];
        $this->payment_status = $rs->fields['payment_status'];
        $this->payment_amount = $rs->fields['payment_amount'];
        $this->payment_method = $rs->fields['payment_method'];
        $this->type           = $rs->fields['type'];
        $this->params         = unserialize($rs->fields['params']);

        return $this;
    }

    /**
     * Saves into database a new order given a set of data
     *
     * @param array $data The data to save to the database
     *
     * @return boolean true if the order was saved
     **/
    public function create($data)
    {
        $data['params'] = serialize($data['params']);

        $data['created'] = $data['created']->format('Y-m-d H:i:s');

        $queryData = array(
            $data['user_id'],
            $data['content_id'],
            $data['created'],
            $data['payment_id'],
            $data['payment_status'],
            $data['payment_amount'],
            $data['payment_method'],
            $data['type'],
            $data['params'],
        );

        $sql = 'INSERT INTO orders
                    (`user_id`, `content_id`, `created`, `payment_id`,
                    `payment_status`, `payment_amount`, `payment_method`,
                    `type`, `params`)
                VALUES (?,?,?,?,?,?,?,?,?)';
        $rs = $GLOBALS['application']->conn->Execute($sql, $queryData);

        if (!$rs) {
            \Application::logDatabaseError();

            return false;
        }

        return $this;
    }

    /**
     * Fills the user attribute from the user information
     *
     * @return void
     **/
    public function getUser()
    {
        $this->user = new \User($this->user_id);
    }

    /**
     * Returns the list of orders
     *
     * @return array
     **/
    public static function find($filter = '', $config = array())
    {
        $defaultParams = array(
            'order' => 'created DESC',
            'limit' => 10
        );
        $order = $limit = $where = '';

        $config = array_merge($defaultParams, $config);

        if (!empty($filter)) {
            $where = 'WHERE '.$filter;
        }

        if (!empty($config['order'])) {
            $order = 'ORDER BY '.$config['order'];
        }

        if ($config['limit'] > 0) {
            $limit = 'LIMIT '.$config['limit'];
        }

        $sql = "SELECT * FROM orders $where $order $limit";
        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            \Application::logDatabaseError();

            return array();
        }


        $orders = array();
        while (!$rs->EOF) {
            $order = new \Order();

            $order->id             = $rs->fields['id'];
            $order->user_id        = $rs->fields['user_id'];
            $order->content_id     = $rs->fields['content_id'];
            $order->created        = \DateTime::createFromFormat(
                'Y-m-d H:i:s',
                $rs->fields['created'],
                new \DateTimeZone('UTC')
            );
            $order->payment_id     = $rs->fields['payment_id'];
            $order->payment_status = $rs->fields['payment_status'];
            $order->payment_amount = $rs->fields['payment_amount'];
            $order->payment_method = $rs->fields['payment_method'];
            $order->type           = $rs->fields['type'];
            $order->params         = @unserialize($element['params']);
            $order->getUser();

            $orders []= $order;

            $rs->MoveNext();
        }

        return $orders;
    }

    /**
     * Returns the list of orders
     *
     * @return array
     **/
    public static function count($filter = '', $config = array())
    {
        $where = '';

        if (!empty($filter)) {
            $where = 'WHERE '.$filter;
        }
        $sql = "SELECT count(id) as count FROM orders $where";

        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);

        $rs = $GLOBALS['application']->conn->Execute($sql);
        if (!$rs) {
            \Application::logDatabaseError();

            return 0;
        }


        return $rs->fields['count'];
    }
}
