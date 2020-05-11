<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Core\Schema;

use Common\Data\Core\DataObject;
use Common\ORM\Core\Validation\Validable;

class Schema extends DataObject implements Validable
{
    /**
     * {@inheritdoc}
     */
    public function getClassName()
    {
        return 'Schema';
    }
}
