<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
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
