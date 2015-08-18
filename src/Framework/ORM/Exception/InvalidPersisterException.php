<?php

namespace Framework\ORM\Exception;

class InvalidPersisterException extends \Exception
{
    /**
     * Initializes the exception with a custom message.
     *
     * @param string $class The classname of the invalid persister.
     */
    public function __construct($class)
    {
        $message = "The persister \"$class\" does not exist.";

        parent::__construct($message);
    }
}
