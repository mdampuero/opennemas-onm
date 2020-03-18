<?php

namespace Common\ORM\Core\Exception;

/**
 * Exception thrown when the requested converter does not exist.
 */
class InvalidConverterException extends \Exception
{
    /**
     * Initializes the exception.
     *
     * @param string $entity    The entity name.
     * @param string $converter The converter name.
     */
    public function __construct($entity, $converter = null)
    {
        $message = _('No converters found for "%s"');
        $message = sprintf($message, $entity);

        if (!empty($converter)) {
            $message = _('The converter "%s" for "%s" does not exist');
            $message = sprintf($message, $converter, $entity);
        }

        parent::__construct($message);
    }
}
