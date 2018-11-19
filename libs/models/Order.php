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
 */

/**
 * Handles all CRUD actions over Orders.
 *
 * @package Model
 */
class Order
{
    /**
     * undocumented function
     * @author
     */
    public function __construct($id = null)
    {
        if (!is_null($id)) {
            return $this->read($id);
        }
    }
    /**
     * Loads the order information given its id
     *
     * @param int $id the order id
     *
     * @return order the order object instance
     */
    public function read($id)
    {
        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM orders WHERE id = ?',
                [ intval($id) ]
            );

            if (!$rs) {
                return false;
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }

        $this->id             = $rs['id'];
        $this->user_id        = $rs['user_id'];
        $this->content_id     = $rs['content_id'];
        $this->created        = $rs['created'];
        $this->payment_id     = $rs['payment_id'];
        $this->payment_status = $rs['payment_status'];
        $this->payment_amount = $rs['payment_amount'];
        $this->payment_method = $rs['payment_method'];
        $this->type           = $rs['type'];
        $this->params         = unserialize($rs['params']);

        return $this;
    }

    /**
     * Saves into database a new order given a set of data
     *
     * @param array $data The data to save to the database
     *
     * @return boolean true if the order was saved
     */
    public function create($data)
    {
        try {
            $data['params'] = serialize($data['params']);
            $data['created'] = $data['created']->format('Y-m-d H:i:s');

            $rs = getService('dbal_connection')->insert(
                "orders",
                [
                    'user_id'        => $data['user_id'],
                    'content_id'     => $data['content_id'],
                    'created'        => $data['created'],
                    'payment_id'     => $data['payment_id'],
                    'payment_status' => $data['payment_status'],
                    'payment_amount' => $data['payment_amount'],
                    'payment_method' => $data['payment_method'],
                    'type'           => $data['type'],
                    'params'         => $data['params'],
                ]
            );

            return $this;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Fills the user attribute from the user information
     */
    public function getUser()
    {
        $this->user = new \User($this->user_id);
    }

    /**
     * Returns the list of orders
     *
     * @return array
     */
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
        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                "SELECT * FROM orders $where $order $limit"
            );

            if (!$rs) {
                return [];
            }
            $orders = [];
            foreach ($rs as $orderData) {
                $order = new \Order();

                $order->id             = $orderData['id'];
                $order->user_id        = $orderData['user_id'];
                $order->content_id     = $orderData['content_id'];
                $order->created        = \DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    $orderData['created'],
                    new \DateTimeZone('UTC')
                );
                $order->payment_id     = $orderData['payment_id'];
                $order->payment_status = $orderData['payment_status'];
                $order->payment_amount = $orderData['payment_amount'];
                $order->payment_method = $orderData['payment_method'];
                $order->type           = $orderData['type'];
                $order->params         = @unserialize($orderData['params']);
                $order->getUser();

                // Overload user info to order obj for ordering propouses
                $order->username = $order->user->username;
                $order->name     = $order->user->name;

                $orders []= $order;
            }
            return $orders;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Returns the list of orders
     *
     * @return array
     */
    public static function count($filter = '')
    {
        try {
            $where = '';
            if (!empty($filter)) {
                $where = 'WHERE '.$filter;
            }

            $rs = getService('dbal_connection')->fetchAssoc(
                "SELECT count(id) as count FROM orders $where"
            );

            if (!$rs) {
                return 0;
            }

            return $rs['count'];
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
