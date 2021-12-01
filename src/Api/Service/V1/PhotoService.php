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
use Api\Exception\DeleteItemException;
use Api\Exception\DeleteListException;
use Api\Exception\FileAlreadyExistsException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PhotoService extends ContentService
{
    /**
     * {@inheritdoc}
     */
    protected $defaults = [
        'content_type_name' => 'photo',
        'fk_content_type'   => 8
    ];

    /**
     *{@inheritdoc}
     */
    public function createItem($data = [], $file = null, bool $copy = false)
    {
        if (empty($file)) {
            throw new CreateItemException('No file provided');
        }

        try {
            $ih   = $this->container->get('core.helper.image');
            $date = new \DateTime($data['created'] ?? null);
            $path = $ih->generatePath($file, $date);

            if ($ih->exists($path)) {
                throw new FileAlreadyExistsException();
            }

            $filename         = basename($path);
            $originalFilename = pathinfo(
                $file instanceof UploadedFile ?
                    $file->getClientOriginalName() : $file->getFilename(),
                PATHINFO_FILENAME
            );

            $data = array_merge([
                'content_status' => 1,
                'created'        => $date->format('Y-m-d H:i:s'),
                'changed'        => $date->format('Y-m-d H:i:s'),
                'description'    => $originalFilename,
                'path'           => 'images' . $date->format('/Y/m/d/') . $filename,
                'title'          => $filename,
                'slug'           => $filename,
            ], $data, $ih->getInformation($file->getPathname()));

            $data = $this->assignUser($data, [ 'fk_user_last_editor', 'fk_publisher' ]);

            $data = $this->em->getConverter($this->entity)
                ->objectify(array_merge($this->defaults, $data));

            $item = new $this->class($data);

            $this->validate($item);
            $this->em->persist($item, $this->getOrigin());

            $id = $this->em->getMetadata($item)->getId($item);

            $this->dispatcher->dispatch($this->getEventName('createItem'), [
                'action' => __METHOD__,
                'id'     => array_pop($id),
                'item'   => $item
            ]);

            $ih->move($file, $path, $copy);

            return $item;
        } catch (\Exception $e) {
            $ih->remove($path);
            throw new CreateItemException($e->getMessage(), $e->getCode());
        }
    }

    /**
     *{@inheritdoc}
     */
    public function deleteItem($id)
    {
        try {
            $item = $this->getItem($id);

            $this->container->get('core.helper.image')->remove($item->path);

            parent::deleteItem($id);
        } catch (\Exception $e) {
            throw new DeleteItemException($e->getMessage(), $e->getCode());
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

        foreach ($response['items'] as $item) {
            try {
                $this->container->get('core.helper.image')->remove($item->path);

                $this->em->remove($item, $item->getOrigin());

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
}
