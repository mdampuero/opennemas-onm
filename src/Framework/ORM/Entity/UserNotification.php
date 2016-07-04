<?php

namespace Framework\ORM\Entity;

class UserNotification extends Entity
{
    /**
     * Unserializes the user on wake up.
     */
    public function __wakeup()
    {
        if (!empty($this->user) && !is_object($this->user)) {
            $this->user = @unserialize($this->user);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCachedId()
    {
        $id = get_class($this);
        $id = substr($id, strrpos($id, '\\') + 1);
        $id = preg_replace('/([a-z])([A-Z])/', '$1_$2', $id);

        return strtolower($id) . '-' . $this->notification_id . '-'
            . $this->instance_id . '-' . $this->user_id;
    }
}
