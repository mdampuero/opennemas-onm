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

class PhotoService extends ContentOldService
{
    /**
     *{@inheritdoc}
     */
    public function createItem($file = null, $data = [], bool $copy = false)
    {
        if (empty($file)) {
            throw new CreateItemException('No file provided');
        }

        try {
            $item = new $this->class;
            $ih   = $this->container->get('core.helper.image');
            $date = new \DateTime($data['created'] ?? null);
            $path = $ih->generatePath($file, $date);

            if ($ih->exists($path)) {
                throw new FileAlreadyExistsException();
            }

            $filename = basename($path);
            $ih->move($file, $path, $copy);
            $originalFilename = '';
            if ($file instanceof UploadedFile) {
                $originalFilename = pathinfo(
                    $file->getClientOriginalName(),
                    PATHINFO_FILENAME
                );
            }

            $data = array_merge([
                'changed'        => $date->format('Y-m-d H:i:s'),
                'content_status' => 1,
                'created'        => $date->format('Y-m-d H:i:s'),
                'name'           => $filename,
                'description'    => $originalFilename,
                'path_file'      => $date->format('/Y/m/d/'),
                'title'          => $filename,
            ], $data, $ih->getInformation($path));

            if (!$id = $item->create($data)) {
                throw new \Exception();
            }

            return $id;
        } catch (\Exception $e) {
            throw new CreateItemException($e->getMessage(), $e->getCode());
        }
    }
}
