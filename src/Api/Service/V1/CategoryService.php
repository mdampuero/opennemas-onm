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

            $this->em->getRepository($this->entity, $this->origin)
                ->removeContents($id);

            $this->dispatcher->dispatch($this->getEventName('emptyItem'), [
                'id'   => $id,
                'item' => $item,
            ]);
        } catch (\Exception $e) {
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
                return $a->id;
            }, $response['items']);

            $this->em->getRepository($this->entity, $this->origin)
                ->removeContents($toDelete);

            $this->dispatcher->dispatch($this->getEventName('emptyList'), [
                'ids'   => $ids,
                'items' => $response['items'],
            ]);
        } catch (\Exception $e) {
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
        $oql = sprintf('name regexp "(.+\"|^)%s(\".+|$)"', $slug);

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
                return $a->id;
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
            throw new ApiException($e->getMessage(), $e->getCode());
        }

        return $response['total'];
    }

    public function getListWithoutLocalizer($oql = '')
    {
        try {
            $oql = $this->getOqlForList($oql);

            $repository = $this->em->getRepository($this->entity, $this->origin);

            $response = [ 'items' => $repository->findBy($oql) ];

            if ($this->count) {
                $response['total'] = $repository->countBy($oql);
            }

            $this->dispatcher->dispatch($this->getEventName('getList'), [
                'items' => $response['items'],
                'oql'   => $oql
            ]);

            return $response;
        } catch (\Exception $e) {
            throw new GetListException($e->getMessage(), $e->getCode());
        }
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
            return $a->id;
        }, $items);

        try {
            return $this->container->get('orm.manager')
                ->getRepository($this->entity, $this->origin)
                ->countContents($ids);
        } catch (\Exception $e) {
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
     * Retrieves all child category IDs for a given category, including nested descendants.
     *
     * This method queries the database for direct child categories of the provided category
     * and recursively retrieves the IDs of all their descendants. It checks whether the
     * child categories' content should be shown in the parent category based on the
     * "showContentInParent" flag.
     *
     * If the "showContentInParent" flag is set to true for a child category, the method
     * will include that category's ID and also recursively retrieve the IDs of its descendants.
     * Otherwise, the category and its descendants will be skipped.
     *
     * @param Category $item The parent category from which to retrieve child IDs.
     *
     * @return array An array of IDs representing all child and descendant categories that should
     *               have their content displayed in the parent category.
     */
    public function getChildIds($item)
    {
        try {
            $childIds = [];

            // Retrieve the list of child categories based on the parent ID
            $oql      = sprintf(
                'parent_id = %d',
                $item->id
            );
            $response = $this->getList($oql);

            // Iterate through each child category and collect their IDs
            foreach ($response['items'] as $child) {
                if (($child->params["showContentInParent"] ?? false) == true) {
                    // Add the current child's ID to the result array
                    $childIds[] = $child->id;
                    $childIds   = array_merge($childIds, $this->getChildIds($child));
                }
            }

            // Return the array of collected child IDs
            return $childIds;
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), $e->getCode());
        }
    }
}
