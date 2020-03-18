<?php

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
