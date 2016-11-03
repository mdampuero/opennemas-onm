<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Migration\Component\Translator;

/**
 * The MigrationTranslator provides methods to translate contents when executing
 * a migration from a data source.
 */
class MigrationTranslator extends Translator
{
    /**
     * {@inheritdoc}
     */
    public function loadTranslations()
    {
        $values = $this->conn->fetchAll('SELECT * FROM translation_ids');

        foreach ($values as $value) {
            $this->addTranslation(
                $value['pk_content_old'],
                $value['pk_content'],
                $value['type'],
                $value['slug']
            );
        }
    }

    /**
     * Persist all translations to the target data source.
     */
    public function persist()
    {
        if (empty($this->translations)) {
            return;
        }

        foreach ($this->translations as $translation) {
            $values[] = $translation['source_id'];
            $values[] = $translation['target_id'];
            $values[] = $translation['type'];
            $values[] = $translation['slug'];
        }

        $sql = 'REPLACE INTO translation_ids VALUES '
            . trim(str_repeat('(?,?,?,?),', count($this->translations)), ',');

        $this->conn->executeQuery($sql, $values);
    }
}
