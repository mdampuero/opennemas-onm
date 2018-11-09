<?php
/**
 * Defines the Attachment class
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
 * Tag
 *
 * Handles some operations about Tags
 *
 * @package    Model
 */
class Tag
{
    /**
     * Method to validate a list of tags
     *
     * @param mixed  $tags       list of all tags to validate
     * @param string $languageId id for the language
     *
     * @return mixed List with all tags validate against DB
     */
    public static function getTagsBySlug($tags, $languageId = null)
    {
        if (empty($tags)) {
            return [];
        }

        $sqlTags = '';
        $params  = [];

        if (is_array($tags)) {
            $sqlTags = ' IN (' . substr(str_repeat(', ?', count($tags)), 2) . ')';
            $params  = $tags;
        } else {
            $sqlTags  = ' = ?';
            $params[] = $tags;
        }

        $sql = 'SELECT id, name, language_id, slug FROM tags '
            . 'WHERE slug ' . $sqlTags;

        if (!empty($languageId)) {
            $sql     .= ' AND language_id = ?';
            $params[] = $languageId;
        }
        $sql .= ' LIMIT 25';

        $rs = getService('dbal_connection')->fetchAll($sql, $params);

        $validateTags = [];
        foreach ($rs as $row) {
            $tagAux                    = new Tag();
            $tagAux->id                = $row['id'];
            $tagAux->name              = $row['name'];
            $tagAux->slug              = $row['slug'];
            $tagAux->language_id       = $row['language_id'];
            $validateTags[$tagAux->id] = $tagAux;
        }

        return ['items' => $validateTags];
    }
}
