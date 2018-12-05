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

use Common\ORM\Entity\Tag;
use Common\Core\Component\Validator\Validator;

class ContentService extends OrmService
{
    /**
     * {@inheritdoc}
     */
    public function createItem($data)
    {
        if (array_key_exists('tags', $data) && !empty($data['tags'])) {
            $data['tags'] = array_map(function ($a) {
                return $a['id'];
            }, $data['tags']);
        }

        return parent::createItem($data);
    }

    /**
     * {@inheritdoc}
     */
    public function updateItem($id, $data)
    {
        if (array_key_exists('tags', $data) && !empty($data['tags'])) {
            $data['tags'] = array_map(function ($a) {
                return $a['id'];
            }, $data['tags']);
        }

        return parent::updateItem($id, $data);
    }
}
