<?php

namespace Common\ORM\Core\Exception;

class EntityNotFoundException extends \Exception
{
    public function __construct($entity, $id, $error = '')
    {
        $message = _('Unable to find entity of type %s with id %s');

        if (!empty($error)) {
            $message .= ': ' . $error;
        }

        parent::__construct(sprintf($message, $entity, $id));
    }
}
