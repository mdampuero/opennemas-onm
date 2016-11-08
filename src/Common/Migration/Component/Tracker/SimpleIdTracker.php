<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Migration\Component\Tracker;

/**
 * The SimpleIdTracker provides methods to track which contents are already
 * migrated when executing a migration from a data source.
 *
 * This tracks the last migrated id so the tracking value has to be numerically
 * sortable.
 */
class SimpleIdTracker extends Tracker
{
    /**
     * The last inserted source id.
     *
     * @var string
     */
    protected $last = 0;

    /**
     * {@inheritdoc}
     */
    public function add($sourceId, $targetId, $slug = null)
    {
        $values = [ $sourceId, $targetId, $this->type, $slug ];
        $sql    = 'INSERT INTO translation_ids VALUES (?,?,?,?)';

        $this->conn->executeQuery($sql, $values);

        $this->last = $sourceId;
    }

    /**
     * {@inheritdoc}
     */
    public function getParsed()
    {
        return $this->last;
    }

    /**
     * {@inheritdoc}
     */
    public function load()
    {
        $sql = "SELECT pk_content_old FROM translation_ids WHERE type = '$this->type'"
            . " ORDER BY pk_content_old DESC LIMIT 1";

        $values = $this->conn->fetchAll($sql);

        if (!empty($values)) {
            $this->last = $values[0]['pk_content_old'];
        }
    }
}
