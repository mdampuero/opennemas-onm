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

class VideoService extends ContentService
{
    /**
     * Removes one or multiple items from the storage if they are of type 'upload'.
     *
     * @param mixed $itemPK The primary key, identifier, or an array of identifiers of the items to remove.
     *
     * @return void
     */
    public function removeFromStorage($itemPK)
    {
        $factory = $this->container->get('core.helper.storage_factory');
        $storage = $factory->create();

        $itemPKs = is_array($itemPK) ? $itemPK : [$itemPK];

        foreach ($itemPKs as $pk) {
            $item = $this->getItem($pk);
            if ($item->type === 'upload' && !empty($item->information['relativePath'])) {
                $storage->delete($item->information['relativePath']);
            }
        }
    }
}
