<?php

namespace Framework\ORM\Entity;

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
