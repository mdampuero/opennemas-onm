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
use Api\Exception\FileAlreadyExistsException;
use Api\Exception\UpdateItemException;

class AttachmentService extends ContentOldService
{
    /**
     * {@inheritdoc}
     */
    public function createItem($data, $file = null)
    {
        if (empty($file)) {
            throw new CreateItemException(_('No file provided'));
        }

        try {
            $item = new $this->class;

            $fh   = $this->container->get('core.helper.attachment');
            $path = $fh->generatePath($file, new \DateTime($data['created'] ?? null));

            if ($fh->exists($path)) {
                throw new FileAlreadyExistsException(_('A file with the same name has already been uploaded today'));
            }

            $data['path'] = '/' . $fh->getRelativePath($path);

            $fh->move($file, $path);

            if (!$id = $item->create($data)) {
                throw new \Exception();
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
            throw new CreateItemException(_('No file provided'));
        }

        try {
            $item = $this->getItem($id);

            if (!empty($file)) {
                $fh   = $this->container->get('core.helper.attachment');
                $path = $fh->generatePath($file, new \DateTime($data['created'] ?? null));

                $data['path'] = '/' . $fh->getRelativePath($path);

                if ($fh->exists($path)
                    && $item->getRelativePath() !== $data['path']
                ) {
                    throw new FileAlreadyExistsException(_('A file with the same name has already been uploaded today'));
                }

                if (!empty($item->getRelativePath())) {
                    $fh->remove($item->getRelativePath());
                }

                $fh->move($file, $path);
            }

            if (!$item->update($data)) {
                throw new \Exception();
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
