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
     * {@inheritdoc}
     */
    public function createItem($data)
    {
        $data['slug'] = $this->container->get('data.manager.filter')
            ->set($data['name'])
            ->filter('slug')
            ->get();

        return parent::createItem($data);
    }

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
     * Returns a list of tags associated to contents with content type in a
     * list of types.
     *
     * @param array $contentTypes The list of content types.
     *
     * @return array The list of tags.
     */
    public function getListByContentTypes($contentTypes)
    {
        $ids = $this->container->get('orm.manager')
            ->getRepository($this->entity, $this->origin)
            ->getIdsByContentType($contentTypes);

        if (empty($ids)) {
            return [ 'items' => [], 'total' => 0 ];
        }

        return $this->getListByIds($ids);
    }

    /**
     * Returns a list of tags basing on a list of slugs.
     *
     * @param array  $slugs  The list of slugs.
     * @param string $locale The locale id.
     *
     * @return array The list of tags.
     *
     * @throws GetListException If no slugs provided or if there was a problem
     *                          to find items.
     */
    public function getListBySlugs($slugs, $locale = null)
    {
        if (!is_array($slugs) || empty($slugs)) {
            throw new GetListException('Invalid slugs', 400);
        }

        $oql = sprintf('slug in ["%s"]', implode('","', $slugs));

        if (!empty($locale)) {
            $oql .= sprintf(' and language_id = "%s"', $locale);
        }

        return $this->getList($oql);
    }

    /**
     * Returns the number of contents associated to a tag in a list of tags.
     *
     * @param array The list of tags.
     *
     * @return array A list where the key is a tag id and the value is the
     *               number of contents associated to the tag.
     */
    public function getStats($tags)
    {
        if (empty($tags)) {
            return [];
        }

        if (!is_array($tags)) {
            $tags = [ $tags ];
        }

        $ids = array_map(function ($a) {
            return $a->id;
        }, $tags);

        return $this->container->get('orm.manager')
            ->getRepository($this->entity, $this->origin)
            ->getNumberOfContents($ids);
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
        $slugs = $this->container->get('data.manager.filter')
            ->set(explode(' ', $text))
            ->filter('slug')
            ->get();

        $tags = $this->getListBySlugs($slugs, $languageId);

        return array_map(function ($a) {
            return $a->id;
        }, $tags['items']);
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
     * @param array $slugs slugs to check
     *
     * @return array tags for this slugs
     */
    public function getValidateTagBySlug($slugs, $languageId = null)
    {
        if (empty($slugs)) {
            return [];
        }

        $slugs = $this->createSearchableWord($slugs);

        return $this->getListBySlugs($slugs, $languageId);
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

        $findTags = $this->getListBySlugs($arr, $languageId, 1);

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

        $recoverTags = $this->getListBySlugs($slugs, $locale);

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
     * {@inheritdoc}
     */
    public function updateItem($id, $data)
    {
        $data['slug'] = $this->container->get('data.manager.filter')
            ->set($data['name'])
            ->filter('slug')
            ->get();

        parent::updateItem($id, $data);
    }
}
