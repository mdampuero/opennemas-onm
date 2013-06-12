<?php
/**
 * Defines the Poll class
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
 * Handles all CRUD operations over Polls.
 *
 * @package    Model
 **/
class Poll extends Content
{
    /**
     * The poll id
     *
     * @var int
     **/
    public $pk_poll       = null;

    /**
     * The poll subtitle
     *
     * @var string
     **/
    public $subtitle      = null;

    /**
     * The total amount of votes for this poll
     *
     * @var int
     **/
    public $total_votes   = null;

    /**
     * Ips that have voted this poll
     *
     * @var array
     **/
    public $used_ips      = null;

    /**
     * Type of visualization (bars, pie, ...)
     *
     * @var string
     **/
    public $visualization = null;

    /**
     * Initializes the poll instance
     *
     * @param int $id the poll id
     *
     * @return Poll the object instance
     **/
    public function __construct($id = null)
    {
        $this->content_type = 'Poll';
        $this->content_type_l10n_name = _('Poll');

        parent::__construct($id);
    }

    /**
     * Magic method for calculating undefined object properties
     *
     * @param string $name the property name
     *
     * @return mixed the property value
     **/
    public function __get($name)
    {

        switch ($name) {
            case 'uri':
                if (empty($this->category_name)) {
                    $this->category_name = $this->loadCategoryName($this->pk_content);
                }
                $uri =  Uri::generate(
                    'poll',
                    array(
                        'id'   => sprintf('%06d', $this->id),
                        'date' => date('YmdHis', strtotime($this->created)),
                        'slug' => $this->slug,
                        'category' => $this->category_name,
                    )
                );

                return ($uri !== '') ? $uri : $this->permalink;

                break;
            default:
                break;
        }

        return parent::__get($name);
    }

    /**
     * Loads a poll given its id
     *
     * @param int $id the poll id
     *
     * @return Poll the poll instance
     **/
    public function read($id)
    {
        parent::read($id);

        $sql = 'SELECT * FROM polls WHERE pk_poll = ?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));

        if (!$rs) {
            \Application::logDatabaseError();

            return null;
        }

        $this->pk_poll       = $rs->fields['pk_poll'];
        $this->subtitle      = $rs->fields['subtitle'];
        $this->total_votes   = $rs->fields['total_votes'];
        $this->with_comment  = $rs->fields['with_comment'];
        $this->visualization = $rs->fields['visualization'];
        $this->used_ips      = unserialize($rs->fields['used_ips']);

        return $this;
    }

    /**
     * Creates a new poll given an array of data
     *
     * @param array $data the data for the new poll
     *
     * @return boolean true if the poll was created
     **/
    public function create($data)
    {
        parent::create($data);

        if ($data['item']) {
            foreach ($data['item'] as $item) {
                $sql    = 'INSERT INTO poll_items (`fk_pk_poll`, `item`, `metadata`) VALUES (?,?,?)';
                $tags   = StringUtils::get_tags($item);
                $values = array($this->id,$item, $tags);

                if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
                    \Application::logDatabaseError();
                }
            }
        }
        $sql = 'INSERT INTO polls (`pk_poll`, `subtitle`,`total_votes`, `visualization`, `with_comment`)
                VALUES (?,?,?,?,?)';
        $values = array(
            $this->id,
            $data['subtitle'],
            0,
            $data['visualization'],
            $data['with_comment']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return false;
        }

        return true;
    }

    /**
     * Updates a poll from an array of data
     *
     * @param array $data the array of data
     *
     * @return Poll the object instance
     **/
    public function update($data)
    {
        parent::update($data);

        if ($data['item']) {
            //Insertamos
            $keys =  '';
            foreach ($data['item'] as $k => $item) {
                $sql    ='REPLACE INTO poll_items (`pk_item`, `fk_pk_poll`,`item`, `votes`) VALUES (?,?,?,?)';
                $values = array((int) $k, (int) $this->id, $item, $data['votes'][$k]);

                if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
                    \Application::logDatabaseError();
                }
                $keys .= $k.', ';
            }

            $sql ="DELETE FROM poll_items WHERE pk_item NOT IN ({$keys} 0) AND fk_pk_poll =?";
            $values = array((int)$this->id);
            if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
                \Application::logDatabaseError();
            }

        }

        $sql = "UPDATE polls SET `subtitle`=?, `visualization`=?, `with_comment`=?
                        WHERE pk_poll= ?";

        $values = array(
            $data['subtitle'],
            $data['visualization'],
            $data['with_comment'],
            $data['id']
        );
        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return(false);
        }

        $this->pk_poll = $data['id'];

        return $this;
    }

    /**
     * Removes permanently the poll
     *
     * @param int $id the poll id
     *
     * @return boolean true if the poll was removed
     **/
    public function remove($id)
    {
        parent::remove($id);

        $sql = 'DELETE FROM polls WHERE pk_poll ='.($id);

        if ($GLOBALS['application']->conn->Execute($sql)===false) {
            \Application::logDatabaseError();

            return false;
        }
        $sql='DELETE FROM poll_items WHERE fk_pk_poll ='.($id);
        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return false;
        }

        return true;
    }

    /**
     * Returns the list of poll answers given the poll id
     *
     * @param int $pkPoll the poll id
     *
     * @return array the list of poll answers
     **/
    public function get_items($pkPoll)
    {
        $sql = 'SELECT poll_items.pk_item, poll_items.item, poll_items.votes, '
             . 'poll_items.metadata '
             . ' FROM poll_items WHERE fk_pk_poll =?'
             . ' ORDER BY poll_items.pk_item';
        $values = array($pkPoll);
        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        $i=0;
        $total=0;
        $items = array();
        while (!$rs->EOF) {
            $items[$i]['pk_item']  = $rs->fields['pk_item'];
            $items[$i]['item']     = $rs->fields['item'];
            $items[$i]['votes']    = $rs->fields['votes'];
            $items[$i]['metadata'] = $rs->fields['metadata'];
            $total                 += $items[$i]['votes'];
            $rs->MoveNext();
            $i++;
        }

        //TODO: improvement calc percents
        if (!empty($items)) {
            foreach ($items as &$item) {
                $item['percent'] = 0;
                if (!empty($item['votes'])) {
                    $item['percent'] = sprintf(
                        "%.0f",
                        ($item['votes'] * 100 / $total)
                    );
                }
            }
        }

        return $items;
    }

    /**
     * Registers a poll answer vote given the answer id
     *
     * @param int    $pkItem the poll answer
     * @param string $ip     the ip that votes
     *
     * @return boolean true if the vote was registered
     **/
    public function vote($pkItem, $ip)
    {
        $this->used_ips = $this->add_count($this->used_ips, $ip);
        if (!$this->used_ips) {
            return false;
        }

        $this->total_votes++;

        $sql = "UPDATE poll_items SET `votes`=`votes`+1 WHERE pk_item=? ";
        $values = array($pkItem);

        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if ($rs === false) {
            \Application::logDatabaseError();

            return false;
        }

        $sql = "UPDATE polls SET `total_votes`=?, `used_ips`=?
                WHERE pk_poll=?";

        $values = array(
            $this->total_votes,
            serialize($this->used_ips),
            $this->id
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return false;
        }
        return true;
    }

    /**
     * Adds a new ip to the arrays vote list
     *
     * @param array $ips_count
     * @param string $ip
     *
     * @return array
     **/
    public function add_count($ips_count, $ip)
    {
        $ips = array();
        if ($ips_count) {
            foreach ($ips_count as $ip_array) {
                $ips[] = $ip_array['ip'];
            }
        }
        //Se busca si existe algún voto desde la ip
        $kip_count = array_search($ip, $ips);

        if ($kip_count === false) {
            //No se ha votado desde esa ip
            $ips_count[] = array('ip' => $ip, 'count' => 1);
        } else {
            if ($ips_count[$kip_count]['count'] ==50) {
                return false;
            }
            $ips_count[$kip_count]['count']++;
        }

        return $ips_count;
    }
}
