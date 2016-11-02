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
        $values = [];

        foreach ($this->translations as $type => $translations) {
            foreach ($translations as $sourceId => $target) {
                $values[] = [
                    'pk_content'     => $target['target_id'],
                    'pk_content_old' => $sourceId,
                    'slug'           => $target['slug'],
                    'type'           => $type
                ];
            }
        }

        if (empty($values)) {
            return;
        }

        $this->conn->insert('translation_ids', $values);
    }
}
