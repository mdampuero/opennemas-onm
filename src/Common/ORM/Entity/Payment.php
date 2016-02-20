<?php

namespace Framework\ORM\Entity;

use Framework\ORM\Core\Entity;

class Payment extends Entity
{
    /**
     * {@inheritdoc}
     */
    public function exists()
    {
        $id = $this->payment_id;

        return !empty($id);
    }
}
