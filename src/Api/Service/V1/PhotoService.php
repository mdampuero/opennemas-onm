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

            $filename = basename($path);
            $ih->move($file, $path, $copy);
            $originalFilename = pathinfo(
                $file instanceof UploadedFile ?
                    $file->getClientOriginalName() : $file->getFilename(),
                PATHINFO_FILENAME
            );

            $data = array_merge([
                'created'     => $date->format('Y-m-d H:i:s'),
                'changed'     => $date->format('Y-m-d H:i:s'),
                'description' => $originalFilename,
                'path'        => 'images' . $date->format('/Y/m/d/') . $filename,
                'title'       => $filename,
                'slug'        => $filename,
            ], $data, $ih->getInformation($path));

            $data = $this->em->getConverter($this->entity)
                ->objectify(array_merge($this->defaults, $data));

            $item = new $this->class($data);

            $this->validate($item);
            $this->em->persist($item, $this->getOrigin());

            $id = $this->em->getMetadata($item)->getId($item);

            $this->dispatcher->dispatch($this->getEventName('createItem'), [
                'id'   => array_pop($id),
                'item' => $item
            ]);

            return $item;
        } catch (\Exception $e) {
            throw new CreateItemException($e->getMessage(), $e->getCode());
        }
    }
}
