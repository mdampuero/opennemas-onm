<?php

namespace Framework\FreshBooks\Exception;

class InvalidRepositoryException extends \Exception
{
    /**
     * Initializes the exception with a custom message.
     *
     * @param string $class The classname of the invalid repository.
     */
    public function __construct($class)
    {
        $message = "The repository \"$class\" does not exist.";

        parent::__construct($message);
    }
}
