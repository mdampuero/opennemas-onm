<?php

namespace Framework\ORM\Exception;

class ClientNotFoundException extends EntityNotFoundException
{
    /**
     * Creates a new exception from id, source and error message.
     *
     * @param integer $id     The client id.
     * @param string  $source The source name.
     * @param string  $error  The error message.
     */
    public function __construct($id, $source, $error = null)
    {
        $message = _('The client with id \'%d\' was not found in %s');

        if (!empty($error)) {
            $message .= ': ' . $error;
        }

        parent::__construct(sprintf($message, $id, $source));
    }
}
