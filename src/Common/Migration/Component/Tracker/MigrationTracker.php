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
            $this->add(
                $value['pk_content_old'],
                $value['pk_content'],
                $value['type'],
                $value['slug']
            );
        }
    }

    /**
     * Persist all parsed to the target data source.
     */
    public function persist()
    {
        if (empty($this->parsed)) {
            return;
        }

        foreach ($this->parsed as $translation) {
            $values[] = $translation['source_id'];
            $values[] = $translation['target_id'];
            $values[] = $translation['type'];
            $values[] = $translation['slug'];
        }

        $sql = 'REPLACE INTO translation_ids VALUES '
            . trim(str_repeat('(?,?,?,?),', count($this->parsed)), ',');

        $this->conn->executeQuery($sql, $values);
    }
}
