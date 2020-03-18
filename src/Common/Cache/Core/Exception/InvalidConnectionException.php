<?php

namespace Common\Cache\Core\Exception;

class InvalidConnectionException extends \Exception
{
    /**
     * Initializes the exception with a custom message.
     *
     * @param string $class The classname of the invalid persister.
     */
    public function __construct($class, $message = '')
    {
        if (empty($message)) {
            $message = _('The cache connection for \'%s\' does not exist');
        }

        parent::__construct(sprintf($message, $class));
    }
}
