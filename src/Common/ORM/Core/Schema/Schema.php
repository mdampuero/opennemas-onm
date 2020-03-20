<?php

namespace Common\ORM\Core\Schema;

use Common\ORM\Core\Validation\Validable;
use Opennemas\Data\Core\DataObject;

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
