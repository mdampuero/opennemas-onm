<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\ORM\Entity;

class Purchase extends Entity
{
    /**
     * Unserializes the client on wake up.
     */
    public function __wakeup()
    {
        if (!empty($this->client) && !is_object($this->client)) {
            $this->client = @unserialize($this->client);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $this->client = $this->client->getData();

        return $this->data;
    }
}
