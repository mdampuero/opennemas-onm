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

class NewsstandService extends ContentService
{
    /**
     * {@inheritdoc}
     */
    public function createItem($data, $file = null, $thumbnail = null)
    {
        if (empty($file)) {
            throw new CreateItemException('No file provided');
        }

        try {
            $data['changed'] = new \DateTime();
            $data['created'] = new \DateTime();
            $data            = $this->assignUser(
                $data,
                [ 'fk_user_last_editor', 'fk_publisher' ]
            );

            $data = $this->em->getConverter($this->entity)
                ->objectify($this->parseData($data));

            $item = new $this->class($data);

            $nh            = $this->container->get('core.helper.newsstand');
            $filePath      = $nh->generatePath($file, $item->created);
            $thumbnailPath = str_replace('pdf', 'jpg', $filePath);

            if ($nh->exists($filePath) || $nh->exists($thumbnailPath)) {
                throw new FileAlreadyExistsException();
            }

            $item->path      = $nh->getRelativePath($filePath);
            $item->thumbnail = str_replace('pdf', 'jpg', $item->path);

            $nh->move($file, $filePath);
            $nh->move($thumbnail, $thumbnailPath);

            $this->validate($item);
            $this->em->persist($item, $this->getOrigin());

            $id = $this->em->getMetadata($item)->getId($item);

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
    public function updateItem($id, $data, $file = null, $thumbnail = null)
    {
        if (empty($file) && empty($data['path'])) {
            throw new UpdateItemException('No file provided');
        }

        try {
            $data = $this->assignUser($data, [ 'fk_user_last_editor' ]);
            $data = $this->em->getConverter($this->entity)
                ->objectify($data);

            $item = $this->getItem($id);
            $item->setData($data);

            if (!empty($file)) {
                $nh            = $this->container->get('core.helper.newsstand');
                $filePath      = $nh->generatePath($file, $item->created);
                $thumbnailPath = str_replace('pdf', 'jpg', $filePath);

                if ($nh->exists($filePath) || $nh->exists($thumbnailPath)) {
                    throw new FileAlreadyExistsException();
                }

                $data['path'] = $nh->getRelativePath($filePath);

                $nh->remove($item->path);
                $nh->remove($item->thumbnail);
                $nh->move($file, $filePath);
                $nh->move($thumbnail, $thumbnailPath);
            }

            $this->validate($item);
            $this->em->persist($item, $this->getOrigin());

            $this->dispatcher->dispatch($this->getEventName('updateItem'), [
                'id'   => $id,
                'item' => $item
            ]);
        } catch (\Exception $e) {
            throw new UpdateItemException($e->getMessage());
        }
    }
}
