<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\ORM\Core;

use Doctrine\DBAL\Schema\Schema as DbalSchema;
use Framework\Component\Data\DataObject;
use Framework\ORM\Core\Validation\Validable;

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
