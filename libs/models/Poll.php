<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Poll extends Content
{
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
     * {@inheritdoc}
     */
    public static function getL10nKeys()
    {
        return array_merge(parent::getL10nKeys(), [ 'pretitle' ]);
    }

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
        foreach ($this->items as $item) {
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
                if (!getService('core.instance')->hasMultilanguage()
                    || getService('core.locale')->getContext() !== 'backend'
                ) {
                    foreach ($this->items as &$item) {
                        $item['item'] = getService('data.manager.filter')
                            ->set($item['item'])
                            ->filter('localize')
                            ->get();
                    }
                }

                return $this->items;

            default:
                return parent::__get($name);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load($properties)
    {
        parent::load($properties);

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
     * {@inheritdoc}
     */
    public function read($id)
    {
        if (empty($id)) {
            return;
        }

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM contents LEFT JOIN contents_categories ON pk_content = pk_fk_content '
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
     * Renders the poll.
     *
     * @param array $params The list of parameters.
     *
     * @return string The generated HTML.
     */
    public function render($params)
    {
        $tpl = getService('core.template');

        $params['item'] = $this;

        try {
            $html = $tpl->fetch('frontpage/contents/_poll.tpl', $params);
        } catch (\Exception $e) {
            $html = _('Poll not available');
        }

        return $html;
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
        $this->parseItems($data['items']);

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

        foreach ($items as $item) {
            $this->total_votes += $item['votes'];
        }

        foreach ($this->items as &$item) {
            $item['percent'] = 0;

            if (!empty($item['votes'])) {
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
            try {
                $conn->insert('poll_items', [
                    'fk_pk_poll' => $id,
                    'item'       => $item['item'],
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
