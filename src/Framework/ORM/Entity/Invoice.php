<?php

namespace Framework\ORM\Entity;

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
