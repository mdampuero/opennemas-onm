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

use Api\Exception\CreateItemException;
use Api\Exception\UpdateItemException;

class FileService extends ContentOldService
{
    /**
     * {@inheritdoc}
     */
    public function createItem($data, $file = null)
    {
        if (empty($file)) {
            throw new CreateItemException('No file provided');
        }

        try {
            $item = new $this->class;

            $fh   = $this->container->get('core.helper.file');
            $path = $fh->generatePath($file, $data['created'] ?? null);

            $data['path'] =
                $fh->generateRelativePath($file, $data['created'] ?? null);

            $fh->move($file, $path);

            if (!$id = $item->create($data)) {
                throw new \Exception('Unable to create the item  "%s"');
            }

            $this->dispatcher->dispatch($this->getEventName('createItem'), [
                'id'   => $id,
                'item' => $item
            ]);

            return $item;
        } catch (\Exception $e) {
            throw new CreateItemException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateItem($id, $data, $file = null)
    {
        if (empty($file) && empty($data['path'])) {
            throw new UpdateItemException('No file provided');
        }

        try {
            $item = $this->getItem($id);

            if (!empty($file)) {
                $fh   = $this->container->get('core.helper.file');
                $path = $fh->generatePath($file, $data['created'] ?? null);

                $data['path'] =
                    $fh->generateRelativePath($file, $data['created'] ?? null);

                $fh->remove($item->getRelativePath());
                $fh->move($file, $path);
            }

            if (!$item->update($data)) {
                throw new \Exception(
                    sprintf('Unable to update the item with id "%s"', $id)
                );
            }

            $this->dispatcher->dispatch($this->getEventName('updateItem'), [
                'id'   => $id,
                'item' => $item
            ]);
        } catch (\Exception $e) {
            throw new UpdateItemException($e->getMessage());
        }
    }
}
