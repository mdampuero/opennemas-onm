<?php
/**
 * Contains the Action class
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
 * Handles all CRUD actions over Action.
 *
 * @package Model
 */
class Action
{
    /**
     * Loads the action information given its id
     *
     * @param int $id the order id
     *
     * @return Action the order object instance
     */
    public function get($id)
    {
        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM action_counters WHERE id = ?',
                [ intval($id) ]
            );

            if (!$rs) {
                return null;
            }
        } catch (\Exception $e) {
            return null;
        }

        $this->id          = $rs['id'];
        $this->date        = $rs['date'];
        $this->counter     = $rs['counter'];
        $this->action_name = $rs['action_name'];

        return $this;
    }

    /**
     * Saves into database a new order given a set of data
     *
     * @param array $data The data to save to the database
     *
     * @return boolean true if the order was saved
     */
    public function set($data)
    {
        try {
            $rs = getService('dbal_connection')->insert(
                'action_counters',
                [
                    "action_name" => $data['action_name'],
                    "counter"     => $data['counter'],
                ]
            );

            if (!$rs) {
                return false;
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Returns the list of action_counters
     *
     * @return array
     */
    public static function find($filter = '', $config = array())
    {
        $config = array_merge(['order' => 'date DESC'], $config);

        $order = $where = '';
        if (!empty($filter)) {
            $where = 'WHERE '.$filter;
        }

        if (!empty($config['order'])) {
            $order = 'ORDER BY '.$config['order'];
        }
        $actions = [];
        try {
            $rs = getService('dbal_connection')->fetchAll(
                "SELECT * FROM action_counters $where $order"
            );

            if (!$rs) {
                return [];
            }

            foreach ($rs as $element) {
                $action = new \Action();
                $action->id          = $element['id'];
                $action->action_name = $element['action_name'];
                $action->date        = \DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    $element['date'],
                    new \DateTimeZone('UTC')
                );
                $actions[] = $action;
            }

            return $actions;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Returns the total of counters
     *
     * @return int
     */
    public static function sum($filter = '')
    {
        $where = '';
        if (!empty($filter)) {
            $where = 'WHERE '.$filter;
        }

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                "SELECT sum(counter) as total FROM action_counters $where"
            );

            if (!$rs) {
                return 0;
            }

            return (int) $rs['total'];
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return 0;
        }
    }
}
