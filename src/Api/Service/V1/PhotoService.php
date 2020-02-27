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
            $originalFilename = pathinfo(
                $file instanceof UploadedFile ?
                    $file->getClientOriginalName() : $file->getFilename(),
                PATHINFO_FILENAME
            );

            $data = array_merge([
                'changed'        => $date->format('Y-m-d H:i:s'),
                'content_status' => 1,
                'created'        => $date->format('Y-m-d H:i:s'),
                'name'           => $filename,
                'description'    => $originalFilename,
                'path_file'      => $date->format('/Y/m/d/'),
                'title'          => $filename,
            ], $data, $ih->getInformation($path));

            if (!$photo = $item->create($data)) {
                throw new \Exception();
            }

            return $photo;
        } catch (\Exception $e) {
            throw new CreateItemException($e->getMessage(), $e->getCode());
        }
    }


    /**
     * {@inheritdoc}
     */
    public function getList($oql = '')
    {
        try {
            $oql = $this->getOqlForList($oql);

            $repository = $this->em;

            list($criteria, $order, $epp, $page) =
                $this->container->get('core.helper.oql')->getFiltersFromOql($oql);

            $criteria          = preg_replace(
                '/(title) LIKE (".*")/',
                '(${1} LIKE ${2} OR description LIKE ${2})',
                $criteria
            );
            $response['items'] = $repository->findBy($criteria, $order, $epp, $page);

            if ($this->count) {
                $response['total'] = $repository->countBy($criteria);
            }

            $this->localizeList($response['items']);

            $this->dispatcher->dispatch($this->getEventName('getList'), [
                'items' => $response['items'],
                'oql'   => $oql
            ]);

            return $response;
        } catch (\Exception $e) {
            throw new GetListException($e->getMessage(), $e->getCode());
        }
    }
}
