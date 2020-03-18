<?php

namespace Common\ORM\Core\Exception;

class InvalidQueryException extends \Exception
{
    /**
     * Initializes the InvalidQueryException.
     */
    public function __construct($token)
    {
        $message = sprintf(_("You have a syntax error near '%s'"), $token);

        parent::__construct($message);
    }
}
