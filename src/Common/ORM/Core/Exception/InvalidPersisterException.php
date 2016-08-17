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
 * Exception thrown when the requested persister does not exist.
 */
class InvalidPersisterException extends \Exception
{
    /**
     * Initializes the exception.
     *
     * @param string $entity    The entity name.
     * @param string $persister The persister name.
     */
    public function __construct($entity, $persister = null)
    {
        $message = _('No persisters found for "%s"');
        $message = sprintf($message, $entity);

        if (!empty($persister)) {
            $message = _('The persister "%s" for "%s" does not exist');
            $message = sprintf($message, $persister, $entity);
        }

        parent::__construct($message);
    }
}
