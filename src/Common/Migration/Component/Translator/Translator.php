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

use Common\Migration\Component\Exception\EntityNotTranslatedException;

/**
 * The Translator class provides methods to track contents during migration
 * process.
 */
abstract class Translator
{
    /**
     * The list of translations.
     *
     * @var array
     */
    protected $translations = [];

    /**
     * Initializes the MigrationTranslator.
     *
     * @param Connection $conn The database connection.
     */
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Adds a new translation to the list.
     *
     * @param string $sourceId The content id in the source data source.
     * @param string $targetId The content id in the target data source.
     * @param string $type     The content type.
     * @param string $slug     The content slug.
     */
    public function addTranslation($sourceId, $targetId, $type = null, $slug = null)
    {
        if (empty($type)) {
            $type = 'default';
        }

        $this->translations[$type][$sourceId] =
            [ 'target_id' => $targetId, 'slug' => $slug ];
    }

    /**
     * Checks if a content is already translated.
     *
     * @param string $sourceId The content id in source data source.
     *
     * @return boolean True if the content is already translated. False
     *                 otherwise.
     */
    public function isTranslated($sourceId, $type = 'default')
    {
        if (array_key_exists($type, $this->translations)
            && array_key_exists($sourceId, $this->translations[$type])
        ) {
            return true;
        }

        return false;
    }

    /**
     * Returns the content id in the source data source.
     *
     * @param string $targetId The content id in the target data source.
     * @param string $type     The content type.
     *
     * @return string The content id in the source data source.
     */
    public function getSourceId($targetId, $type = 'default')
    {
        if (!array_key_exists($type, $this->translations)) {
            throw new EntityNotTranslatedException();
        }

        foreach ($this->translations[$type] as $source => $target) {
            if ($target['target_id'] === $targetId) {
                return $source;
            }
        }

        throw new EntityNotTranslatedException();
    }

    /**
     * Returns the content id in the target data source.
     *
     * @param string $sourceId The content id in the source data source.
     * @param string $type     The content type.
     *
     * @return string The content id in the target data source.
     */
    public function getTargetId($sourceId, $type = 'default')
    {
        if ($this->isTranslated($sourceId, $type)) {
            return $this->translations[$type][$sourceId]['target_id'];
        }

        throw new EntityNotTranslatedException();
    }

    /**
     * Persists translations to target data source.
     */
    abstract public function loadTranslations();
}
