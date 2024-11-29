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
use Api\Exception\ApiException;
use Common\Model\Entity\Tag;
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
     * Moves all contents assigned to a tag to another tag.
     *
     * @param integer $id The tag id of the source tag.
     * @param integer $to The tag id of the target tag.
     */
    public function moveItem($id, $to)
    {
        try {
            $source = $this->getItem($id);

            if ($this->isItemEmpty($source)) {
                throw new ApiException('The item is empty', 400);
            }

            $target = $this->getItem($to);

            $moved = $this->em->getRepository($this->entity, $this->origin)
                ->moveContents((int) $id, (int) $to);

            $this->dispatcher->dispatch($this->getEventName('moveItem'), [
                'id'       => $id,
                'item'     => $source,
                'target'   => $target,
                'contents' => $moved
            ]);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Checks if the tag is empty.
     *
     * @param Category $item The tag.
     *
     * @return boolean True if the tag is empty. False otherwise.
     */
    protected function isItemEmpty($item)
    {
        try {
            $contents = $this->em->getRepository($this->entity, $this->origin)
                ->countContents($item->id);

            if (!empty($contents)
                && array_key_exists((int) $item->id, $contents)
                && !empty($contents[$item->id])
            ) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), $e->getCode());
        }
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

        $oql .= !empty($locale)
            ? sprintf(' and (locale is null or locale = "%s")', $locale)
            : ' and locale is null';

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

        $tags = $this->getListByIds($ids);

        $returnArr = [];

        foreach ($tags['items'] as $tag) {
            if (empty($locale)
                || empty($tag->locale)
                || $tag->locale === $locale
            ) {
                $returnArr[$tag->id] = \Onm\StringUtils::convertToUtf8($tag);
            }
        }

        $tags['items'] = $this->responsify($returnArr);
        $tags['total'] = count($tags['items']);

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
            return is_array($a) ? $a['id'] : $a->id;
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

        return $data;
    }

    /**
     * Get all the tags users for report.
     *
     * @return array The list of items.
     */
    public function getReport()
    {
        try {
            return $this->container->get('orm.manager')
                ->getRepository($this->entity, $this->origin)
                ->findTagsWithContentCount();
        } catch (\Exception $e) {
            throw new GetListException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getListByIds($ids, $internal = false)
    {
        if (!is_array($ids)) {
            throw new GetListException('Invalid ids', 400);
        }

        if (empty($ids)) {
            return ['items' => [], 'total' => 0];
        }

        $items = $this->em->getRepository($this->entity, $this->origin)->find($ids);

        if ($internal) {
            $filteredItems = [];
            foreach ($items as $item) {
                $novisible = $item->getStored()['novisible'] ?? null;
                if ($novisible !== 1) {
                    $filteredItems[] = $item;
                }
            }
            $items = $filteredItems;
        }

        $this->localizeList($items);

        $this->dispatcher->dispatch($this->getEventName('getListByIds'), [
            'ids'   => $ids,
            'items' => $items
        ]);

        return ['items' => $items, 'total' => count($items)];
    }
}
