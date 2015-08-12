<?php

namespace Framework\FreshBooks\Exception;

class EntityNotFoundException extends \Exception
{
    /**
     * Initializes the exception with a custom message.
     *
     * @param string $id The entity id.
     */
    public function __construct($id)
    {
        $message = "The entity with id \"$id\" not found.";

        parent::__construct($message);
    }
}
