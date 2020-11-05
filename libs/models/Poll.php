<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Opennemas\Data\Serialize\Serializer\PhpSerializer;

class Poll extends Content
{
    /**
     * {@inheritdoc}
     */
    protected static $l10nExclusiveKeys = [ 'pretitle' ];

    /**
     * The poll id
     *
     * @var int
     */
    public $pk_poll = null;

    /**
     * The total amount of votes for this poll
     *
     * @var int
     */
    public $total_votes = null;

    /**
     * Ips that have voted this poll
     *
     * @var array
     */
    public $used_ips = null;

    /**
     * Type of visualization (bars, pie, ...)
     *
     * @var string
     */
    public $visualization = null;

    /**
     * The list of items.
     *
     * @var array
     */
    protected $items = [];

    /**
     * The poll pretitle
     *
     * @var string
     */
    protected $pretitle = null;

    /**
     * Initializes the poll instance
     *
     * @param int $id the poll id
     *
     * @return Poll the object instance
     */
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('Poll');
        $this->content_type           = 11;
        $this->content_type_name      = 'poll';

        parent::__construct($id);
    }

    /**
     * {@inheritdoc}
     */
    public function csvSerialize()
    {
        $data   = parent::csvSerialize();
        $ignore = [ 'pk_item', 'metadata' ];

        $data['total_votes'] = $this->total_votes;

        $i = 0;
        foreach ($this->__get('items') as $item) {
            foreach ($item as $key => $value) {
                if (in_array($key, $ignore)) {
                    continue;
                }

                $data[$key . $i] = $value;
            }

            $i++;
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function __get($name)
    {
        switch ($name) {
            case 'items':
                foreach ($this->items as &$item) {
                    $item['item'] = getService('data.manager.filter')
                        ->set($item['item'])
                        ->filter('localize')
                        ->get();
                }

                return $this->items;

            default:
                return parent::__get($name);
        }
    }

    /**
     * Checks if the current poll is already closed.
     *
     * @return bool True if the poll is closed. False otherwise.
     */
    public function isClosed()
    {
        return array_key_exists('closetime', $this->params)
            && !empty($this->params['closetime'])
            && $this->params['closetime'] < date('Y-m-d H:i:s');
    }

    /**
     * {@inheritdoc}
     */
    public function load($properties)
    {
        parent::load($properties);
    }

    /**
     * {@inheritdoc}
     */
    public function read($id)
    {
        if (empty($id)) {
            return;
        }

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM contents LEFT JOIN content_category ON pk_content = content_id '
                . 'LEFT JOIN polls ON pk_content = pk_poll WHERE pk_content=?',
                [ $id ]
            );

            if (!$rs) {
                return false;
            }

            $this->load($rs);
            $this->loadItems($id);

            return $this;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());

            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function create($data)
    {
        $conn = getService('dbal_connection');

        try {
            $conn->beginTransaction();

            parent::create($data);

            $this->pk_content = (int) $this->id;
            $this->pk_poll    = (int) $this->id;

            $conn->insert('polls', [
                'pk_poll'     => (int) $this->id,
                'pretitle'    => $data['pretitle'],
                'total_votes' => 0,
            ]);

            $this->saveItems($this->id, $data['items'] ?? []);
            $conn->commit();

            return $this;
        } catch (\Exception $e) {
            $conn->rollback();

            getService('error.log')->error($e->getMessage());

            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove($id)
    {
        $conn = getService('dbal_connection');

        try {
            $conn->beginTransaction();

            parent::remove($id);

            $conn->delete('polls', [ 'pk_poll' => $id ]);
            $this->removeItems($id);

            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollback();

            getService('error.log')->error($e->getMessage());
        }
    }

    /**
     * Updates a poll from an array of data
     *
     * @param array $data the array of data
     *
     * @return Poll the object instance
     */
    public function update($data)
    {
        foreach ($this->getL10nKeys(true) as $key) {
            if (array_key_exists($key, $data) && is_array($data[$key])) {
                $data[$key] = PhpSerializer::serialize($data[$key]);
            }
        }

        $conn = getService('dbal_connection');

        try {
            $conn->beginTransaction();

            parent::update($data);

            $conn->update('polls', [
                'pretitle'    => $data['pretitle'],
                'total_votes' => $this->total_votes
            ], [ 'pk_poll' => $this->id ]);

            $this->removeItems($this->id);
            $this->saveItems($this->id, $data['items'] ?? []);

            $conn->commit();
            $this->load($data);

            return true;
        } catch (\Exception $e) {
            $conn->rollback();

            getService('error.log')->error($e->getMessage());

            return false;
        }
    }

    /**
     * Increases the votes for a poll basing on the answer id.
     *
     * @param int $id The answer id.
     *
     * @return bool True if vote was saved successfully. False otherwise.
     */
    public function vote(int $id) : bool
    {
        $this->total_votes++;

        $conn = getService('dbal_connection');

        try {
            $conn->executeUpdate(
                "UPDATE poll_items SET `votes`=`votes`+1 WHERE pk_item=?",
                [ $id ]
            );

            $conn->update('polls', [
                'total_votes' => $this->total_votes
            ], [ 'pk_poll' => $this->id ]);

            dispatchEventWithParams('content.update', [ 'item' => $this ]);

            return true;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());

            return false;
        }
    }

    /**
     * Loads all items in the poll.
     *
     * @param int $id The poll id.
     */
    protected function loadItems(int $id) : void
    {
        try {
            $this->items = getService('dbal_connection')->fetchAll(
                'SELECT * FROM poll_items WHERE fk_pk_poll =? ORDER BY pk_item',
                [ $id ]
            );

            $this->parseItems($this->items);
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());

            $this->items = [];
        }
    }

    /**
     * Parses items and calculates total votes and the percentage of votes per
     * item.
     *
     * @param array $items The list of items.
     */
    protected function parseItems(array &$items) : void
    {
        $this->total_votes = 0;

        foreach ($items as &$item) {
            $item['item'] = PhpSerializer::unserialize($item['item']);

            $this->total_votes += $item['votes'];
        }

        foreach ($this->items as &$item) {
            $item['percent'] = 0;

            if (!empty($item['votes']) && !empty($this->total_votes)) {
                $item['percent'] = sprintf(
                    '%.2f',
                    round($item['votes'] / $this->total_votes, 4) * 100
                );
            }
        }
    }

    /**
     * Removes all items for the poll.
     *
     * @param int $id The poll id.
     */
    protected function removeItems(int $id) : void
    {
        getService('dbal_connection')
            ->delete('poll_items', [ 'fk_pk_poll' => $id ]);
    }

    /**
     * Saves items for the poll.
     *
     * @param int   $id    The poll id.
     * @param array $items The list of items.
     */
    protected function saveItems($id, $items)
    {
        if (empty($items)) {
            return;
        }

        $conn = getService('dbal_connection');

        foreach ($items as $item) {
            if (is_array($item['item'])) {
                $item['item'] =
                    PhpSerializer::serialize($item['item']);
            }

            try {
                $conn->insert('poll_items', [
                    'pk_item'    => (int) $item['pk_item'],
                    'fk_pk_poll' => $id,
                    'item'       => $item['item'],
                    'votes'      => $item['votes']
                ]);
            } catch (\Exception $e) {
                getService('error.log')->error(
                    $e->getMessage() . ' Stack Trace: ' . $e->getTraceAsString()
                );

                return;
            }
        }
    }
}
