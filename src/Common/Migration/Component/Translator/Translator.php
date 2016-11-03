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

        $this->translations[] = [
            'source_id' => $sourceId,
            'type'      => $type,
            'target_id' => $targetId,
            'slug'      => $slug
        ];
    }

    /**
     * Checks if a content is already translated.
     *
     * @param string $sourceId The content id in source data source.
     * @param string $type     The content type.
     * @param string $slug     The content slug.
     *
     * @return boolean True if the content is already translated. False
     *                 otherwise.
     */
    public function isTranslated($sourceId, $type = null, $slug = null)
    {
        $translations = array_filter(
            $this->translations,
            function ($a) use ($sourceId, $type, $slug) {
                return $a['source_id'] === $sourceId
                    && (empty($type) || $a['type'] === $type)
                    && (empty($slug) || $a['slug'] === $slug);
            }
        );

        if (!empty($translations)) {
            return true;
        }

        return false;
    }

    /**
     * Returns the content id in the source data source.
     *
     * @param string $targetId The content id in the target data source.
     * @param string $type     The content type.
     * @param string $slug     The content slug.
     *
     * @return string The content id in the source data source.
     */
    public function getSourceId($targetId, $type = null, $slug = null)
    {
        $translations = array_filter(
            $this->translations,
            function ($a) use ($targetId, $type, $slug) {
                return $a['target_id'] === $targetId
                    && (empty($type) || $a['type'] === $type)
                    && (empty($slug) || $a['slug'] === $slug);
            }
        );

        if (empty($translations)) {
            throw new EntityNotTranslatedException();
        }

        $translation = array_shift($translations);

        return $translation['source_id'];
    }

    /**
     * Returns the content id in the target data source.
     *
     * @param string $sourceId The content id in the source data source.
     * @param string $type     The content type.
     * @param string $slug     The content slug.
     *
     * @return string The content id in the target data source.
     */
    public function getTargetId($sourceId, $type = null, $slug = null)
    {
        $translations = array_filter(
            $this->translations,
            function ($a) use ($sourceId, $type, $slug) {
                return $a['source_id'] === $sourceId
                    && (empty($type) || $a['type'] === $type)
                    && (empty($slug) || $a['slug'] === $slug);
            }
        );

        if (empty($translations)) {
            throw new EntityNotTranslatedException();
        }

        $translation = array_shift($translations);

        return $translation['target_id'];
    }

    /**
     * Persists translations to target data source.
     */
    abstract public function loadTranslations();
}
