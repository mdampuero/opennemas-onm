<?php

namespace Framework\ORM\Core\Exception;

class InvalidRepositoryException extends \Exception
{
    /**
     * Initializes the exception with a custom message.
     *
     * @param string $class  The classname of the invalid repository.
     * @param string $source The source name.
     */
    public function __construct($class, $source)
    {
        $message = _('The repository \'%s\' does not exist in %s');

        parent::__construct(sprintf($message, $class, $source));
    }
}
