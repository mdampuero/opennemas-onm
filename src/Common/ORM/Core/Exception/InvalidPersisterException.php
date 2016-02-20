<?php

namespace Common\ORM\Core\Exception;

class InvalidPersisterException extends \Exception
{
    /**
     * Initializes the exception with a custom message.
     *
     * @param string $class  The classname of the invalid persister.
     * @param string $source The source name.
     */
    public function __construct($class, $source)
    {
        $message = _('The persister \'%s\' does not exist in %s');

        parent::__construct(sprintf($message, $class, $source));
    }
}
