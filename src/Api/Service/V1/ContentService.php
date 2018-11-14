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
    public function getItem($id)
    {
        $item = parent::getItem($id);

        $item->id = $item->pk_content;

        return $item;
    }
}
