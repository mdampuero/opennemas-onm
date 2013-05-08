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
     * Creates an order object instance
     *
     * @return void
     **/
    public function __construct()
    {
        return $this;
    }

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
     * Returns the list of orders
     *
     * @return array
     **/
    public static function find()
    {
        $sql = "SELECT * FROM orders WHERE type='paywall' ORDER BY created DESC";
        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            \Application::logDatabaseError();

            return array();
        }

        $order = array();
        while (!$rs->EOF) {
            $order = new \Order();

            $order->id             = $rs->fields['id'];
            $order->user_id        = $rs->fields['user_id'];
            $order->content_id     = $rs->fields['content_id'];
            $order->created        = $rs->fields['created'];
            $order->payment_id     = $rs->fields['payment_id'];
            $order->payment_status = $rs->fields['id'];
            $order->payment_method = $rs->fields['id'];
            $order->type           = $rs->fields['type'];
            $order->params         = @unserialize($element['params']);

            $orders []= $order;

            $rs->MoveNext();
        }

        return $orders;
    }
}
