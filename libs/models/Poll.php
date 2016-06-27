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
        // If no valid id then return
        if (((int) $id) <= 0) return;

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM contents LEFT JOIN contents_categories ON pk_content = pk_fk_content '
                .'LEFT JOIN polls ON pk_content = pk_poll WHERE pk_content=?',
                [ $id ]
            );

            if (!$rs) {
                return false;
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }

        $this->load($rs);
        $this->items = $this->getItems($this->id);

        return $this;
    }

    /**
     * Overloads the object properties with an array of the new ones
     *
     * @param array $properties the list of properties to load
     *
     * @return void
     **/
    public function load($properties)
    {
        parent::load($properties);

        if (array_key_exists('pk_poll', $properties)) {
            $this->pk_poll = $properties['pk_poll'];
        }
        if (array_key_exists('subtitle', $properties)) {
            $this->subtitle = $properties['subtitle'];
        }
        if (array_key_exists('total_votes', $properties)) {
            $this->total_votes = $properties['total_votes'];
        }
        if (array_key_exists('visualization', $properties)) {
            $this->visualization = $properties['visualization'];
        }
        if (array_key_exists('used_ips', $properties)) {
            $this->used_ips      = unserialize($properties['used_ips']);
        }

        $this->status = 'opened';
        if (is_string($this->params)) {
            $this->params = unserialize($this->params);
        }
        if (is_array($this->params)
            && array_key_exists('closetime', $this->params)
            && (!empty($this->params['closetime']))
            && ($this->params['closetime'] != date('00-00-00 00:00:00'))
            && ($this->params['closetime'] < date('Y-m-d H:i:s'))
        ) {
            $this->status = 'closed';
        }
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

        $conn = getService('dbal_connection');
        try {
            // Save poll items
            if (is_array($data['item']) && !empty($data['item'])) {
                foreach ($data['item'] as $item) {
                    $conn->insert(
                        'poll_items',
                        [
                            'fk_pk_poll' => $this->id,
                            'item'       => $item->item,
                            'metadata'   => \Onm\StringUtils::getTags($item->item)
                        ]
                    );
                }
            }

            $conn->insert(
                'polls',
                [
                    'pk_poll'       => (int) $this->id,
                    'subtitle'      => $data['subtitle'],
                    'total_votes'   => 0,
                    'visualization' => $data['visualization'],
                ]
            );

            return $this;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
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

        $conn = getService('dbal_connection');
        try {
            if (!$data['item']) {
                $data['item'] = [];
            }

            $conn->executeUpdate(
                "DELETE FROM poll_items WHERE fk_pk_poll =?",
                [ (int) $this->id ]
            );

            // Save poll items
            $total = 0;
            foreach ($data['item'] as $key => &$item) {
                $conn->insert(
                    'poll_items',
                    [
                        'pk_item'    => (int) $item->pk_item,
                        'fk_pk_poll' => (int) $this->id,
                        'item'       => $item->item,
                        'votes'      => $item->votes,
                    ]
                );
                $total += $item->votes;
            }

            // Update the poll info
            $conn->update(
                'polls',
                [
                    'subtitle'      => $data['subtitle'],
                    'visualization' => $data['visualization'],
                    'total_votes'   => $total,
                ],
                [ 'pk_poll' => $data['id'] ]
            );

            $this->total   = $total;
            $this->pk_poll = $data['id'];

            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
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

        try {
            $rs = getService('dbal_connection')->delete(
                'polls',
                [ 'pk_poll' => $id ]
            );

            $rs = getService('dbal_connection')->delete(
                'poll_items',
                [ 'fk_pk_poll' => $id ]
            );

        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Returns the list of poll answers given the poll id
     *
     * @param int $pkPoll the poll id
     *
     * @return array the list of poll answers
     **/

    public function getItems($pkPoll)
    {
        $items = [];
        try {
            $rs = getService('dbal_connection')->fetchAll(
                'SELECT * FROM poll_items WHERE fk_pk_poll =? ORDER BY pk_item',
                [ $pkPoll ]
            );

            $i     = 0;
            $total = 0;
            foreach ($rs as $item) {
                $items[] = [
                    'pk_item'  => $item['pk_item'],
                    'item'     => $item['item'],
                    'votes'    => isset($item['votes']) ? $item['votes'] : 0,
                    'metadata' => $item['metadata']
                ];

                $total += $item['votes'];
            }

            foreach ($items as &$item) {
                $item['percent'] = 0;
                if (!empty($item['votes'])) {
                    $item['percent'] = sprintf(
                        "%.2f",
                        round($item['votes'] / $total, 4) * 100
                    );
                }
            }

            return $items;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return [];
        }
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
        $this->used_ips = $this->addCount($this->used_ips, $ip);
        if (!$this->used_ips) {
            return false;
        }

        $this->total_votes++;

        $conn = getService('dbal_connection');
        try {
            $conn->executeUpdate(
                "UPDATE poll_items SET `votes`=`votes`+1 WHERE pk_item=?",
                [ $pkItem ]
            );

            $rs = $conn->executeUpdate(
                "UPDATE polls SET `total_votes`=?, `used_ips`=? WHERE pk_poll=?",
                [
                    $this->total_votes,
                    serialize($this->used_ips),
                    $this->id
                ]
            );

            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Adds a new ip to the arrays vote list
     *
     * @param array $ips_count
     * @param string $ip
     *
     * @return array
     **/
    public function addCount($ips_count, $ip)
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

    /**
     * Renders the poll
     *
     * @param arrray $params parameters for rendering the content
     *
     * @return string the generated HTML
     **/
    public function render($params)
    {
        $tpl = new Template(TEMPLATE_USER);

        $tpl->assign('item', $this);
        $tpl->assign('cssclass', $params['cssclass']);

        try {
            $html = $tpl->fetch('frontpage/contents/_poll.tpl');
        } catch (\Exception $e) {
            $html = _('Poll not available');
        }

        return $html;
    }
}
