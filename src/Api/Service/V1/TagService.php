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

use Api\Exception\GetListException;
use Common\ORM\Entity\Tag;
use Common\Core\Component\Validator\Validator;

class TagService extends OrmService
{
    /**
     * {@inheritdoc}
     */
    public function createItem($data)
    {
        $data = $this->parse($data);

        return parent::createItem($data);
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
            $oql .= sprintf(' and locale = "%s"', $locale);
        }

        return $this->getList($oql);
    }

    /**
     * Returns a list of tags basing on a string.
     *
     * @param string $str    The string.
     * @param string $locale The locale id.
     *
     * @return array The list of tags.
     */
    public function getListByString($str, $locale = null)
    {
        $fm = $this->container->get('data.manager.filter');

        $tags  = $fm->set($str)->filter('tags')->get();
        $slugs = $fm->set(explode(',', $tags))->filter('slug')->get();

        return $this->getListBySlugs($slugs, $locale);
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
            return [ 'items' => [], 'total' => 0 ];
        }

        $tags      = $this->getListByIds($ids);
        $returnArr = [];

        foreach ($tags['items'] as $tag) {
            if (is_null($locale) || $tag->locale == $locale) {
                $returnArr[$tag->id] = \Onm\StringUtils::convertToUtf8($tag);
            }
        }

        $tags['items'] = $this->responsify($returnArr);

        return $tags;
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
     * {@inheritdoc}
     */
    public function updateItem($id, $data)
    {
        $data = $this->parse($data);

        parent::updateItem($id, $data);
    }

    /**
     * Checks and adds missing fields to the provided data.
     *
     * @param array The data to parse.
     *
     * @return array The parsed data.
     */
    protected function parse($data)
    {
        $slug = $data['name'];

        if (array_key_exists('slug', $data) && !empty($data['slug'])) {
            $slug = $data['slug'];
        }

        $data['slug'] = $this->container->get('data.manager.filter')
            ->set($slug)
            ->filter('slug')
            ->get();

        if (!array_key_exists('locale', $data)
            || empty($data['locale'])
        ) {
            $data['locale'] = $this->container->get('core.locale')
                ->getLocale('frontend');
        }

        return $data;
    }
}
