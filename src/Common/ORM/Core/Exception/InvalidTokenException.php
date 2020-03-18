<?php

namespace Common\ORM\Core\Exception;

class InvalidTokenException extends \Exception
{
    /**
     * Initializes the InvalidTokenException.
     *
     * @param string $token  The token.
     * @param string $entity The entity name.
     */
    public function __construct($token, $entity = '')
    {
        $message = sprintf(_("The token '%s' is not valid"), $token);

        if (!empty($entity)) {
            $message = sprintf(
                _("The token '%s' is not valid for entities of type '%s'"),
                $token,
                $entity
            );
        }

        parent::__construct($message);
    }
}
