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
 **/

/**
 * Handles all CRUD actions over Action.
 *
 * @package Model
 **/
class Action
{
    /**
     * Loads the action information given its id
     *
     * @param int $id the order id
     *
     * @return order the order object instance
     **/
    public function get($id)
    {
        $sql = 'SELECT * FROM action_counters WHERE id = ?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array(intval($id)));

        if (!$rs) {
            return null;
        }

        $this->id          = $rs->fields['id'];
        $this->date        = $rs->fields['date'];
        $this->counter     = $rs->fields['counter'];
        $this->action_name = $rs->fields['action_name'];

        return $this;
    }

    /**
     * Saves into database a new order given a set of data
     *
     * @param array $data The data to save to the database
     *
     * @return boolean true if the order was saved
     **/
    public function set($data)
    {

        $queryData = array(
            $data['action_name'],
            $data['counter'],
        );

        $sql = 'INSERT INTO action_counters
                    (`action_name`, `counter`)
                VALUES (?,?)';
        $rs = $GLOBALS['application']->conn->Execute($sql, $queryData);

        if (!$rs) {
            return false;
        }

        return true;
    }

    /**
     * Returns the list of action_counters
     *
     * @return array
     **/
    public static function find($filter = '', $config = array())
    {
        $defaultParams = array(
            'order' => 'date DESC',
        );
        $order = $where = '';

        $config = array_merge($defaultParams, $config);

        if (!empty($filter)) {
            $where = 'WHERE '.$filter;
        }

        if (!empty($config['order'])) {
            $order = 'ORDER BY '.$config['order'];
        }

        $sql = "SELECT * FROM action_counters $where $order";
        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            return array();
        }


        $actions = array();
        while (!$rs->EOF) {
            $action = new \Action();

            $action->id   = $rs->fields['id'];

            $action->action_name = $rs->fields['action_name'];
            $action->date        = \DateTime::createFromFormat(
                'Y-m-d H:i:s',
                $rs->fields['date'],
                new \DateTimeZone('UTC')
            );

            $actions[]= $action;

            $rs->MoveNext();
        }

        return $actions;
    }

    /**
     * Returns the total of counters
     *
     * @return int
     **/
    public static function sum($filter = '', $config = array())
    {
        $where = '';

        if (!empty($filter)) {
            $where = 'WHERE '.$filter;
        }
        $sql = "SELECT sum(counter) as total FROM action_counters $where";

        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);

        $rs = $GLOBALS['application']->conn->Execute($sql);
        if (!$rs) {
            return 0;
        }

        return $rs->fields['total'];
    }
}
