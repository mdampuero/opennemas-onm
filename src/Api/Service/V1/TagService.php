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
            $oqlNameAux = split('"', $matches[0]);
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
        return \Tag::validateTags($languageId, $tags);
    }


    /**
     *  Method to retrieve the ids for a list of tags. In case some tag not exist
     * the system generate a new tag and upload the data
     *
     * @param array $tags List of tags from we want to retrieve the ids
     *
     * @return array List with all ids for the tags
     */
    public function getTagsIds($tags)
    {
        if (empty($tags['metadata'])) {
            return [];
        }

        $locale  = $this->container->get('core.locale')
            ->getLocale('frontend');
        $tagsArr = explode(',', $tags['metadata']);

        $validTags = $this->validateTags($locale, $tagsArr);

        $clearTagNames = array_map(function ($tag) {
            return $tag->name;
        }, $validTags);

        $newTags = [];
        foreach ($tagsArr as $tagToCheck) {
            if (!in_array($tagToCheck, $clearTagNames)) {
                $newTags[] = $tagToCheck;
            }
        }

        if (!empty($newTags)) {
            foreach ($newTags as $tagName) {
                $tagData             = [
                    'name'        => $tagName,
                    'slug'        => $this->createSearchableWord($tagName),
                    'language_id' => $locale
                ];

                $tag                 = parent::createItem($tagData);
                $validTags[$tag->id] = $tag;
            }
        }
        return array_keys($validTags);
    }
}
