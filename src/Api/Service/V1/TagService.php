<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Service\V1;

use Common\ORM\Entity\Tag;

class TagService extends OrmService
{

    /**
     * Method to simplificate the tag word for enable a search system
     *
     * @param string $word word for transformation
     *
     * @return string searcheable word.
     */
    public function createSearchableWord($word)
    {
        return \Onm\StringUtils::generateSlug($word, false);
    }

    /**
     * Method for retrieve the number of contents related with some tag
     *
     * @param array list with all number of related contents by tag
     *
     * @return array list with all tags and the number of contest
     */
    public function getNumContentsRel($tagList)
    {
        if (empty($tagList)) {
            return [];
        }

        $tagListIds = $tagList;
        if (!is_array($tagList) && is_object($tagList)) {
            $tagListIds = $tagList['id'];
        } elseif (is_array($tagList) && is_object($tagList[0])) {
            $tagListIds = array_map(
                function ($tag) {
                    return $tag->id;
                },
                $tagList
            );
        }

        return \Tag::numberOfContent($tagListIds);
    }

    /**
     * Method for replace the parameter name by slug
     *
     * @param oql $oql to check and replace the field name by slug
     *
     * @return String new oql with the field name replace
     */
    public function replaceSearchBySlug($oql)
    {
        $oqlAux = $oql;
        if (preg_match('/and\s*name\s*~\s*"?[^"]*"?/', $oql, $matches)) {
            $oqlNameAux = explode('"', $matches[0]);
            if (count($oqlNameAux) == 3) {
                $oqlNameAux[0] = str_replace("name", 'slug', $oqlNameAux[0]);
                $oqlNameAux[1] = '"' . $this->createSearchableWord($oqlNameAux[1]) . '"';
                $oqlNameAux    = implode($oqlNameAux);
                $oqlAux        = str_replace($matches[0], $oqlNameAux, $oql);
            }
        }
        return $oqlAux;
    }

    /**
     * Method to validate a list of tags
     *
     * @param mixed $tags list of all tags to validate
     *
     * @return mixed List with all tags validate against DB
     */
    public function validateTags($languageId, $tags)
    {
        $ts      = $this;
        $tagsAux = $tags;
        if (is_array($tags)) {
            $tagsAux = array_filter($tags, function ($tag) use ($ts) {
                return !empty($ts->createSearchableWord($tag));
            });
        } else {
            if (empty($ts->createSearchableWord($tagsAux))) {
                return null;
            }
        }
        return \Tag::validateTags($languageId, $tagsAux);
    }

    /**
     *  Method to retrieve the ids for a list of tags. In case some tag not exist
     * the system generate a new tag but whithout id
     *
     * @param array $tags List of tags from we want to retrieve the ids
     *
     * @return array List with all ids for the tags
     */
    public function getTagsIds($locale, $tagsArr)
    {
        $validTags = $this->validateTags($locale, $tagsArr);

        $returnTags    = [];
        $clearTagNames = [];
        foreach ($validTags as $value) {
            if (in_array($value->name, $tagsArr)) {
                $returnTags[]    = $value;
                $clearTagNames[] = $value->name;
            }
        }

        $newTags = [];
        foreach ($tagsArr as $tagToCheck) {
            if (!in_array($tagToCheck, $clearTagNames)) {
                $returnTags[] = [
                    'name' => $tagToCheck,
                    'language_id' => $locale
                ];
            }
        }

        return $returnTags;
    }

    /**
     *  Method to retrieve the tags for a list of tag ids
     *
     * @param array $ids List of ids we want to retrieve
     *
     * @return array List of tags fo this tags.
     */
    public function getListByIdsKeyMapped($ids)
    {
        if (empty($ids)) {
            return ['items' => []];
        }
        $tags      = $this->getListByIds($ids);
        $returnArr = [];

        foreach ($tags['items'] as $tag) {
            $returnArr[$tag->id] = \Onm\StringUtils::convertToUtf8($tag);
        }
        $tags['items'] = $this->responsify($returnArr);
        return $tags;
    }
}
