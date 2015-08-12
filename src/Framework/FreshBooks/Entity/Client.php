<?php

namespace Framework\FreshBooks\Entity;

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
