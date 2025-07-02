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
use Api\Exception\DeleteItemException;
use Api\Exception\DeleteListException;
use Symfony\Component\Process\Process;

class VideoService extends ContentService
{


    // public function createItem($data, $file = null)
    // {
    //     if (empty($file)) {
    //         throw new CreateItemException(_('No file provided'));
    //     }

    //     try {
    //         $fh           = $this->container->get('core.helper.video');
    //         $instance     = $this->container->get('core.instance');
    //         $path         = $fh->generatePath($file, new \DateTime($data['created'] ?? null), $data);
    //         $data['path'] = '/' . $fh->getRelativePath($path);

    //         $data['information'] = [
    //             'source' => [
    //                 $fh->getExtension($file) => $instance->getVideosShortPath() . '/' . $fh->getRelativePath($path)
    //             ]
    //         ];
    //         $fh->move($file, $path);
    //         $item = parent::createItem($data);

    //         $process = new Process(
    //             sprintf(
    //                 '/home/opennemas/current/bin/console %s --operation=%s --file=%s --destination=%s --item=%s',
    //                 'app:core:storage',
    //                 'upload',
    //                 $path,
    //                 $instance->getVideosShortPath() . '/' . $fh->getRelativePath($path),
    //                 $item->pk_content
    //             )
    //         );
    //         $process->start();

    //         return $item;
    //     } catch (\Exception $e) {
    //         throw new CreateItemException($e->getMessage(), $e->getCode());
    //     }
    // }

    /**
     * {@inheritdoc}
     */
    public function updateItem($id, $data, $file = null)
    {
        try {
            $item = $this->getItem($id);

            if (!empty($file)) {
                $fh   = $this->container->get('core.helper.attachment');
                $path = $fh->generatePath($file, new \DateTime($data['created'] ?? null));

                $data['path'] = '/' . $fh->getRelativePath($path);

                if ($fh->exists($path)
                    && $item->path !== $data['path']
                ) {
                    throw new FileAlreadyExistsException(
                        _('A file with the same name has already been uploaded today')
                    );
                }

                if (!empty($item->path)) {
                    $fh->remove($item->path);
                }

                $fh->move($file, $path);
                unset($data['_method']);
            }

            return parent::updateItem($id, $data);
        } catch (\Exception $e) {
            throw new UpdateItemException($e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteList($ids)
    {
        if (!is_array($ids)) {
            throw new DeleteListException('Invalid ids', 400);
        }

        try {
            $response = $this->getListByIds($ids);
        } catch (\Exception $e) {
            throw new DeleteListException($e->getMessage(), $e->getCode());
        }

        $items   = [];
        $deleted = array_map(function ($a) {
            return $a->pk_content;
        }, $response['items']);

        $related = $this->getRelatedContents(implode(',', $deleted));

        $fh = $this->container->get('core.helper.attachment');

        foreach ($response['items'] as $item) {
            try {
                $this->em->remove($item, $item->getOrigin());
                if (!empty($item->path)) {
                    $fh->remove($item->path);
                }
                $items[] = $item;
            } catch (\Exception $e) {
                throw new DeleteListException($e->getMessage(), $e->getCode());
            }
        }

        $this->dispatcher->dispatch($this->getEventName('deleteList'), [
            'action'  => __METHOD__,
            'ids'     => $deleted,
            'item'    => $items,
            'related' => $related
        ]);

        return count($deleted);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItem($id)
    {
        try {
            $item = $this->getItem($id);
            $fh   = $this->container->get('core.helper.attachment');

            if (!empty($item->path)) {
                $fh->remove($item->path);
            }

            return parent::deleteItem($id);
        } catch (\Exception $e) {
            throw new DeleteItemException($e->getMessage(), $e->getCode());
        }
    }
}
