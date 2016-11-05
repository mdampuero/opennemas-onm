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
 * The MigrationTranslator provides methods to translate contents when executing
 * a migration from a data source.
 */
class MigrationTracker extends Tracker
{
    /**
     * {@inheritdoc}
     */
    public function load()
    {
        $sql    = "SELECT * FROM translation_ids WHERE type = '$this->type'";
        $values = $this->conn->fetchAll($sql);

        foreach ($values as $value) {
            parent::add(
                $value['pk_content_old'],
                $value['pk_content'],
                $value['type'],
                $value['slug']
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function add($sourceId, $targetId, $slug = null)
    {
        parent::add($sourceId, $targetId, $slug);

        $values = [ $sourceId, $targetId, $this->type, $slug ];
        $sql    = 'INSERT INTO translation_ids VALUES (?,?,?,?)';

        $this->conn->executeQuery($sql, $values);
    }
}
