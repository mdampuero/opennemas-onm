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
     * Returns the number of contents for a given tag id or a list of tag ids
     *
     * @param int|array $tagId wether a list of tag ids or only one tag id
     *
     * @return int  the number of contents
     */
    public static function numberOfContent($tagId)
    {
        if (empty($tagId)) {
            return null;
        }

        $sqlTagId = is_array($tagId) ?
            ' IN (' . substr(str_repeat(', ?', count($tagId)), 2) . ')' :
            ' = ?';

        $sql = 'SELECT tag_id, count(1) AS related_content_count FROM `contents_tags` WHERE tag_id ' .
            $sqlTagId .
            'GROUP BY tag_id';
        $rs  = getService('dbal_connection')->fetchAll($sql, $tagId);

        $numberOfContents = [];
        foreach ($rs as $row) {
            $numberOfContents[$row['tag_id']] = $row['related_content_count'];
        }

        return $numberOfContents;
    }

    /**
     * Method to validate a list of tags
     *
     * @param string $languageId id for the language
     * @param mixed  $tags       list of all tags to validate
     *
     * @return mixed List with all tags validate against DB
     */
    public function validateTags($languageId, $tags)
    {
        if (empty($tags)) {
            return [];
        }

        $sqlTags = '';
        $params  = [$languageId];

        if (is_array($tags)) {
            $sqlTags = ' IN (' . substr(str_repeat(', ?', count($tags)), 2) . ')';
            $ts      = getService('api.service.tag');
            $params  = array_merge(
                $params,
                array_map(
                    function ($tag) use ($ts) {
                        return $ts->createSearchableWord($tag);
                    },
                    $tags
                )
            );
        } else {
            $sqlTags  = ' = ?';
            $params[] = $tags;
        }

        $sql = 'SELECT id, name, language_id, slug FROM tags '
            . 'WHERE language_id = ? AND slug ' . $sqlTags . ' LIMIT 25';

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

        return $validateTags;
    }
}
