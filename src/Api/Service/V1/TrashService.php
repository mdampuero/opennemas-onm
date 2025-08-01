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
}
