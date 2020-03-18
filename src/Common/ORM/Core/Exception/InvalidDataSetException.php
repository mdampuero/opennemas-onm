<?php

namespace Common\ORM\Core\Exception;

/**
 * Exception thrown when the requested dataset does not exist.
 */
class InvalidDataSetException extends \Exception
{
    /**
     * Initializes the exception.
     *
     * @param string $entity    The entity name.
     * @param string $dataset The dataset name.
     */
    public function __construct($entity, $dataset = null)
    {
        $message = _('No datasets found for "%s"');
        $message = sprintf($message, $entity);

        if (!empty($dataset)) {
            $message = _('The dataset "%s" for "%s" does not exist');
            $message = sprintf($message, $dataset, $entity);
        }

        parent::__construct($message);
    }
}
