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
use Symfony\Component\HttpFoundation\JsonResponse;

class PhotoService extends ContentOldService
{
    /**
     *{@inheritdoc}
     */
    public function createItem($file = null)
    {
        try {
            $originalFilename = pathinfo(
                $file->getClientOriginalName(),
                PATHINFO_FILENAME
            );

            $photo = new \Photo();
            $id    = $photo->createFromLocalFile($file->getRealPath(), [
                'description' => $originalFilename
            ]);
            $photo = new \Photo($id);
        } catch (\Exception $e) {
            throw new CreateItemException($e->getMessage(), $e->getCode());
        }
        return $photo;
    }
}
