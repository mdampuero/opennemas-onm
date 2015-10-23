<?php

namespace Framework\ORM\Entity;

class UserNotification extends Entity
{
    /**
     * {@inheritdoc}
     */
    public function getCachedId()
    {
        $id = get_class($this);
        $id = substr($id, strrpos($id, '\\') + 1);
        $id = preg_replace('/([a-z])([A-Z])/', '$1_$2', $id);

        return strtolower($id) . '-' . $this->notification_id . '-'
            . $this->user_id;
    }

    /**
     * {@inheritdoc}
     */
    public function exists()
    {
        return false;
    }
}
