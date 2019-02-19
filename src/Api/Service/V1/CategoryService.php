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

use Api\Exception\ApiException;
use Common\ORM\Entity\Tag;
use Common\Core\Component\Validator\Validator;

class CategoryService extends OrmService
{
    /**
     * Removes all contents assigned to the category.
     *
     * @param integer $id The category id.
     */
    public function emptyItem($id)
    {
        try {
            $item = $this->getItem($id);

            if ($this->isItemEmpty($item)) {
                throw new ApiException('The item is already empty', 400);
            }

            $deleted = $this->em->getRepository($this->entity, $this->origin)
                ->removeContents($id);

            $this->dispatcher->dispatch($this->getEventName('emptyItem'), [
                'id'       => $id,
                'item'     => $item,
                'contents' => $deleted
            ]);
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new ApiException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Removes all contents assigned to a list of categories.
     *
     * @param integer $ids The list of category ids.
     *
     * @return integer The number of emptied categories.
     */
    public function emptyList($ids)
    {
        if (!is_array($ids) || empty($ids)) {
            throw new ApiException('Invalid ids', 400);
        }

        try {
            $response = $this->getListByIds($ids);

            $toDelete = array_map(function ($a) {
                return $a->pk_content_category;
            }, $response['items']);

            $deleted = $this->em->getRepository($this->entity, $this->origin)
                ->removeContents($toDelete);

            $this->dispatcher->dispatch($this->getEventName('emptyList'), [
                'ids'      => $ids,
                'items'    => $response['items'],
                'contents' => $deleted
            ]);
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new ApiException($e->getMessage(), $e->getCode());
        }

        return $response['total'];
    }

    /**
     * Returns a category basing on a slug.
     *
     * @param string $slug The category slug.
     *
     * @return Category The category.
     */
    public function getItemBySlug($slug)
    {
        $oql = sprintf('name regexp "(%%\"|^)%s(\"%%|$)"', $slug);

        return $this->getItemBy($oql);
    }

    /**
     * Moves all contents assigned to a category to another category.
     *
     * @param integer $id The category id of the source category.
     * @param integer $to The category id of the target category.
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
            $this->container->get('error.log')->error($e->getMessage());
            throw new ApiException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Moves all contents assigned to a list of categories to another category.
     *
     * @param integer $ids The list of source category ids.
     * @param integer $to  The category id of the target category.
     *
     * @return integer The number of affected categories.
     */
    public function moveList($ids, $to)
    {
        if (!is_array($ids) || empty($ids)) {
            throw new ApiException('Invalid ids', 400);
        }

        try {
            $response = $this->getListByIds($ids);
            $target   = $this->getItem($to);

            $toMove = array_map(function ($a) {
                return $a->pk_content_category;
            }, $response['items']);

            $moved = $this->em->getRepository($this->entity, $this->origin)
                ->moveContents($toMove, (int) $to);

            $this->dispatcher->dispatch($this->getEventName('moveList'), [
                'ids'      => $ids,
                'items'    => $response['items'],
                'target'   => $target,
                'contents' => $moved
            ]);
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new ApiException($e->getMessage(), $e->getCode());
        }

        return $response['total'];
    }

    /**
     * Returns the number of contents associated to a category in a list of
     * categories.
     *
     * @param array The list of categories.
     *
     * @return array A list where the key is a category id and the value is the
     *               number of contents associated to the category.
     */
    public function getStats($items)
    {
        if (empty($items)) {
            return [];
        }

        if (!is_array($items)) {
            $items = [ $items ];
        }

        $ids = array_map(function ($a) {
            return $a->pk_content_category;
        }, $items);

        try {
            return $this->container->get('orm.manager')
                ->getRepository($this->entity, $this->origin)
                ->countContents($ids);
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new ApiException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Checks if the category is empty.
     *
     * @param Category $item The category.
     *
     * @return boolean True if the category is empty. False otherwise.
     */
    protected function isItemEmpty($item)
    {
        try {
            $contents = $this->em->getRepository($this->entity, $this->origin)
                ->countContents($item->pk_content_category);

            if (!empty($contents)
                && array_key_exists((int) $item->pk_content_category, $contents)
                && !empty($contents[$item->pk_content_category])
            ) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new ApiException($e->getMessage(), $e->getCode());
        }
    }
}
