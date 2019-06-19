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
            if ($this->isTrashEmpty()) {
                throw new ApiException('The trash is already empty', 400);
            }

            $this->em->getRepository($this->entity, $this->origin)
                ->removeContentsInTrash();

            $this->dispatcher->dispatch($this->getEventName('emptyTrash'));
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Checks if the trash is empty.
     *
     * @return boolean True if the trash is empty. False otherwise.
     */
    protected function isTrashEmpty()
    {
        try {
            $contents = $this->em->getRepository($this->entity, $this->origin)
                ->countBy('in_litter = 1');

            return $contents === 0;
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), $e->getCode());
        }
    }
}
