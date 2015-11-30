<?php

namespace Framework\ORM\Entity;

use Framework\ORM\Core\Entity;

class Client extends Entity
{
    /**
     * {@inheritdoc}
     */
    public function exists()
    {
        $id = $this->client_id;

        return !empty($id);
    }
}
