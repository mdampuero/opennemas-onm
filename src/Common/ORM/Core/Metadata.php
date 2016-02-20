<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Core;

use Framework\Component\Data\DataObject;
use Common\ORM\Core\Validation\Validable;

class Metadata extends DataObject implements Validable
{
    /**
     * {@inheritdoc}
     */
    public function getClassName()
    {
        return 'Metadata';
    }
}
