<?php

namespace Common\ORM\Core\Exception;

/**
 * Exception thrown when the requested metadata does not exist.
 */
class InvalidMetadataException extends \Exception
{
    /**
     * Initializes the exception.
     *
     * @param string $entity The entity name.
     */
    public function __construct($entity)
    {
        $message = _('No metadata found for "%s"');
        $message = sprintf($message, $entity);

        parent::__construct($message);
    }
}
