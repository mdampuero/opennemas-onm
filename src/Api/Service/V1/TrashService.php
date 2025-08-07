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

class TrashService extends OrmService
{
    /**
     * Removes all contents in the trash.
     */
    public function emptyTrash()
    {
        try {
            $response = $this->getList('in_litter = 1');

            if ($response['total'] === 0) {
                throw new ApiException('The trash is already empty', 400);
            }

            $ids = array_map(function ($a) {
                return $a->pk_content;
            }, $response['items']);

            $this->deleteList($ids);

            $this->dispatcher->dispatch($this->getEventName('emptyTrash'));
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), $e->getCode());
        }
    }


    /**
     * Restores a single item from the trash.
     */
    public function patchItem($id, $data)
    {
        parent::patchItem($id, $data);

        if (isset($data['in_litter']) && (int) $data['in_litter'] === 0) {
            $item = $this->getItem($id);
            if ($item->content_type_name === 'video' && $item->type === 'upload') {
                $instance = $this->container->get('core.instance');
                $this->container->get('api.service.video')
                    ->removeFromStorage($id, $instance, 'restore');
            }
        }
    }

    /**
     * Restores a list of items from the trash.
     */
    public function patchList($ids, $data)
    {
        $updated = parent::patchList($ids, $data);

        if (isset($data['in_litter']) && (int) $data['in_litter'] === 0) {
            $items    = $this->getListByIds($ids)['items'];
            $videoIds = [];
            foreach ($items as $item) {
                if ($item->content_type_name === 'video' && $item->type === 'upload') {
                    $videoIds[] = $item->pk_content;
                }
            }
            if (!empty($videoIds)) {
                $instance = $this->container->get('core.instance');
                $this->container->get('api.service.video')
                    ->removeFromStorage($videoIds, $instance, 'restore');
            }
        }

        return $updated;
    }

    /**
     * Permanently deletes a single item from the trash.
     */
    public function deleteItem($id)
    {
        $item = $this->getItem($id);
        if ($item->content_type_name === 'video' && $item->type === 'upload') {
            $instance = $this->container->get('core.instance');
            $this->container->get('api.service.video')
                ->removeFromStorage($id, $instance, 'delete');
        }

        parent::deleteItem($id);
    }

    /**
     * Permanently deletes a list of items from the trash.
     */
    public function deleteList($ids)
    {
        $items    = $this->getListByIds($ids)['items'];
        $videoIds = [];
        foreach ($items as $item) {
            if ($item->content_type_name === 'video' && $item->type === 'upload') {
                $videoIds[] = $item->pk_content;
            }
        }
        if (!empty($videoIds)) {
            $instance = $this->container->get('core.instance');
            $this->container->get('api.service.video')
                ->removeFromStorage($videoIds, $instance, 'delete');
        }

        return parent::deleteList($ids);
    }
}
