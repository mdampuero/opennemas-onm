<?php

namespace Common\ORM\Entity;

use Common\ORM\Core\Entity;

class Invoice extends Entity
{
    /**
     * {@inheritdoc}
     */
    public function exists()
    {
        $id = $this->invoice_id;

        return !empty($id);
    }
}
