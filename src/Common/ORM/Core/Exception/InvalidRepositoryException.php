<?php

namespace Common\ORM\Core\Exception;

/**
 * Exception thrown when the requested repository does not exist.
 */
class InvalidRepositoryException extends \Exception
{
    /**
     * Initializes the exception.
     *
     * @param string $entity     The entity name.
     * @param string $repository The repository name.
     */
    public function __construct($entity, $repository = null)
    {
        $message = _('No repositories found for "%s"');
        $message = sprintf($message, $entity);

        if (!empty($repository)) {
            $message = _('The repository "%s" for "%s" does not exist');
            $message = sprintf($message, $repository, $entity);
        }

        parent::__construct($message);
    }
}
