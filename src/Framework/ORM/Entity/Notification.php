<?php

namespace Framework\ORM\Entity;

class Notification extends Entity
{
    /**
     * {@inheritdoc}
     */
    public function exists()
    {
        return !empty($this->id);
    }
}
