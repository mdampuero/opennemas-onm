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
use Common\Core\Component\Validator\Validator;

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
     * Method to validate a text as tags
     *
     * @param mixed $text       Text with all tags
     * @param mixed $languageId Language of the tag
     *
     * @return mixed List with all tags validate against DB
     */
    public function getTagIdsFromStr($text, $languageId = null)
    {
        $wordArr = explode(' ', $text);

        return $this->getTagIdsFromWordArr($wordArr, $languageId);
    }

    /**
     * Method to validate a text as tags
     *
     * @param mixed $text       Text with all tags
     * @param mixed $languageId Language of the tag
     *
     * @return mixed List with all tags validate against DB
     */
    public function getTagIdsFromWordArr($wordArr, $languageId = null)
    {
        if (!is_array($wordArr)) {
            return null;
        }

        $tags = $this->getValidateTagBySlug($wordArr, $languageId);

        if (empty($tags['items'])) {
            return [];
        }

        $returnIds = [];
        foreach ($tags['items'] as $tag) {
            if (in_array($tag->name, $wordArr)) {
                $returnIds[] = $tag->id;
            }
        }

        return $returnIds;
    }

    /**
     * Method to validate a list of tags
     *
     * @param mixed $tags       List of all tags to validate
     * @param mixed $languageId Language of the tag
     *
     * @return mixed List with all tags validate against validation rules
     */
    public function validTags($tags, $languageId = null)
    {
        $tagsAux     = [];
        $tagsToCheck = is_array($tags) ? $tags : [$tags];

        $tagsAux = [];
        foreach ($tagsToCheck as $tag) {
            $tagValidation = $this->container->get('core.validator')->validate(
                [ 'name' => $tag ],
                Validator::BLACKLIST_RULESET_TAGS
            );

            if (!empty($tagValidation)) {
                continue;
            }

            if (empty($this->createSearchableWord($tag))) {
                continue;
            }

            $tagsAux[] = $tag;
        }

        if (is_array($tags)) {
            return $tagsAux;
        }

        return empty($tagsAux) ? null : $tags;
    }

    /**
     *  Get all tags by the exact slug if is valid
     *
     * @param mixed  $slugs      slugs to check
     * @param string $languageId Language id to search for it
     *
     * @return array tags for this slugs
     */
    public function getValidateTagBySlug($slugs, $languageId = null, $limit = 25)
    {
        $arr = $this->validTags($slugs, $languageId);

        if (empty($arr)) {
            return [];
        }

        $slugs = $this->createSearchableWord($arr);


        //return $this->getTagBySlug($slugs, $languageId, $limit);
        return \Tag::getTagsBySlug($slugs, $languageId, $limit);
    }

    /**
     *  Get all tags by the exact slug
     *
     * @param mixed  $slugs      slugs to check
     * @param string $languageId Language id to search for it
     *
     * @return array tags for this slugs
     */
    public function getTagBySlug($slugs, $languageId = null, $limit = 25)
    {
        if (empty($slugs)) {
            return ['items' => []];
        }

        $oql = is_array($slugs) ?
            ' in ("' . implode('", "', $slugs) . '")' :
            ' = "' . $slugs . '"';

        $oql = 'slug' . $oql . ' limit ' . $limit;

        if (!empty($languageId)) {
            $oql = 'language_id = "' . $languageId . '" and ' . $oql;
        }

        return $this->getList($oql);
    }

    /**
     *  Check if the tag is a valid new tag
     *
     * @param string $tag        Tag to check
     * @param string $languageId Language id to search for it
     *
     * @return boolean if the tag is a valid new tag
     */
    public function isValidNewTag($tag, $languageId = null)
    {
        if (!is_string($tag) || empty($tag)) {
            return false;
        }


        $arr = $this->validTags($tag, $languageId);

        if (empty($arr)) {
            return false;
        }

        $arr = $this->createSearchableWord($arr);

        $findTags = $this->getTagBySlug($arr, $languageId, 1);

        foreach ($findTags['items'] as $tagAux) {
            if ($tag == $tagAux->name) {
                return false;
            }
        }

        return true;
    }

    /**
     *  Method to retrieve the ids for a list of tags. In case some tag not exist
     * the system generate a new tag but whithout id
     *
     * @param array $tags List of tags from we want to retrieve the ids
     *
     * @return array List with all ids for the tags
     */
    public function getTagsAndNewTags($locale, $tagsArr)
    {
        if (!is_array($tagsArr)) {
            return null;
        }
        $validTags = $this->validTags($tagsArr, $locale);

        if (empty($validTags)) {
            return [];
        }

        $slugs = $this->createSearchableWord($validTags);


        $recoverTags = \Tag::getTagsBySlug($slugs, $locale);

        if (count($recoverTags['items']) == 25) {
            return $recoverTags['items'];
        }

        $returnTags    = [];
        $clearTagNames = [];
        foreach ($recoverTags['items'] as $value) {
            if (in_array($value->name, $validTags)) {
                $returnTags[]    = $value;
                $clearTagNames[] = $value->name;
            }
        }

        foreach ($validTags as $tagToCheck) {
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
    public function getListByIdsKeyMapped($ids, $locale = null)
    {
        if (empty($ids)) {
            return ['items' => []];
        }
        $tags      = $this->getListByIds($ids);
        $returnArr = [];

        foreach ($tags['items'] as $tag) {
            if (is_null($locale) || $tag->language_id == $locale) {
                $returnArr[$tag->id] = \Onm\StringUtils::convertToUtf8($tag);
            }
        }
        $tags['items'] = $this->responsify($returnArr);
        return $tags;
    }

    /**
     *  Method to retrieve the tags for a list of tag ids
     *
     * @param array $ids List of ids we want to retrieve
     *
     * @return string List of tags fo this tags.
     */
    public function getTagsSepByCommas($ids, $locale = null)
    {
        if (empty($ids)) {
            return ['items' => []];
        }
        $tags = $this->getListByIds($ids);

        $tagsString = '';
        foreach ($tags['items'] as $tag) {
            if (is_null($locale) || $tag->language_id == $locale) {
                $tagsString .= ',' . $tag->name;
            }
        }
        // We remove the first comma
        return substr($tagsString, 1);
    }

    /**
     * Returns an array of tags associated with the list of contents type
     * requested
     *
     * @param array $contentTypesIds ids for the content types
     *
     * @return array list of tags associated with the types of content indicated
     */
    public function getTagsAssociatedCertainContentsTypes($contentTypesIds)
    {
        return $this->container->get('orm.manager')
            ->getRepository($this->entity, $this->origin)
            ->getTagsAssociatedCertainContentsTypes($contentTypesIds);
    }
}
